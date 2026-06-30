<?php

namespace App\Http\Controllers\WT;

use App\Models\WT\MasterData;
use App\Models\WT\UserActivityLog;
use App\Models\WT\WalkieTalkie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class MasterDataController extends Controller
{
    /**
     * The walkie_talkies column(s) that each category governs. Used to count
     * usage and to block deletion of values that are still referenced.
     */
    private const USAGE_COLUMNS = [
        'model' => ['model'],
        'department' => ['department'],
        'position' => ['position'],
        'ownership_type' => ['ownership_type', 'ownership_type_to_be'],
    ];

    public function index(Request $request)
    {
        $activeTab = $request->query('tab', 'model');
        if (! array_key_exists($activeTab, MasterData::CATEGORIES)) {
            $activeTab = 'model';
        }

        $search = trim((string) $request->query('q', ''));

        $counts = collect(MasterData::CATEGORIES)
            ->keys()
            ->mapWithKeys(fn ($category) => [$category => MasterData::category($category)->visible()->count()])
            ->all();

        $usage = $this->usageCounts($activeTab);

        $rows = MasterData::category($activeTab)
            ->visible()
            ->when($search !== '', fn ($query) => $query->where('value', 'LIKE', "%{$search}%"))
            ->orderBy('value')
            ->get()
            ->map(function (MasterData $row) use ($usage) {
                $row->usage_count = $usage[$row->value] ?? 0;

                return $row;
            });

        return view('wt.admin.master_data.index', [
            'activeTab' => $activeTab,
            'categories' => MasterData::CATEGORIES,
            'counts' => $counts,
            'rows' => $rows,
            'search' => $search,
        ]);
    }

    public function store(Request $request)
    {
        $category = $this->resolveCategory($request);

        $validated = $request->validate([
            'value' => [
                'required',
                'string',
                'max:255',
                Rule::unique('wt_master_data', 'value')->where(fn ($query) => $query->where('category', $category)),
            ],
        ]);

        $value = $this->normalize($validated['value']);

        if (MasterData::isBlockedValue($category, $value)) {
            return redirect()
                ->route('wt.admin.masterData.index', ['tab' => $category])
                ->with('error', '"' . $value . '" is a person name, not a position.');
        }

        MasterData::create([
            'category' => $category,
            'value' => $value,
        ]);

        $this->log('insert', 'Added ' . MasterData::CATEGORIES[$category] . ' "' . $value . '".');

        return redirect()
            ->route('wt.admin.masterData.index', ['tab' => $category])
            ->with('success', MasterData::CATEGORIES[$category] . ' "' . $value . '" added.');
    }

    public function update(Request $request, MasterData $masterData)
    {
        $category = $masterData->category;

        $validated = $request->validate([
            'value' => [
                'required',
                'string',
                'max:255',
                Rule::unique('wt_master_data', 'value')
                    ->where(fn ($query) => $query->where('category', $category))
                    ->ignore($masterData->id),
            ],
        ]);

        $oldValue = $masterData->value;
        $newValue = $this->normalize($validated['value']);

        if ($oldValue !== $newValue) {
            // Cascade the rename to every walkie record that uses the old value.
            $cascaded = 0;
            foreach (self::USAGE_COLUMNS[$category] as $column) {
                $cascaded += WalkieTalkie::where($column, $oldValue)->update([$column => $newValue]);
            }

            $masterData->update(['value' => $newValue]);

            $this->log('update', 'Renamed ' . MasterData::CATEGORIES[$category] . ' "' . $oldValue . '" to "' . $newValue . '". Cascaded to ' . $cascaded . ' walkie record(s).');
        }

        return redirect()
            ->route('wt.admin.masterData.index', ['tab' => $category])
            ->with('success', MasterData::CATEGORIES[$category] . ' updated.');
    }

    public function destroy(MasterData $masterData)
    {
        $category = $masterData->category;
        $value = $masterData->value;

        if ($this->usageCountFor($category, $value) > 0) {
            return redirect()
                ->route('wt.admin.masterData.index', ['tab' => $category])
                ->with('error', 'Cannot delete "' . $value . '" — it is still used by one or more walkie talkies.');
        }

        $masterData->delete();

        $this->log('delete', 'Deleted ' . MasterData::CATEGORIES[$category] . ' "' . $value . '".');

        return redirect()
            ->route('wt.admin.masterData.index', ['tab' => $category])
            ->with('success', MasterData::CATEGORIES[$category] . ' "' . $value . '" deleted.');
    }

    /**
     * Return the walkie talkies that reference a given category value, for the
     * "In Use" drill-down popup.
     */
    public function usage(Request $request)
    {
        $category = $request->query('category');
        $value = $this->normalize((string) $request->query('value', ''));

        abort_unless(array_key_exists($category, MasterData::CATEGORIES), 422, 'Invalid category.');

        $columns = self::USAGE_COLUMNS[$category];

        $records = WalkieTalkie::query()
            ->where(function ($query) use ($columns, $value) {
                foreach ($columns as $column) {
                    $query->orWhere($column, $value);
                }
            })
            ->orderBy('radio_id')
            ->get([
                'walkie_id', 'radio_id', 'serial_number', 'model', 'status',
                'ownership_type', 'ownership', 'department', 'position',
            ])
            ->map(fn (WalkieTalkie $w) => [
                'walkie_id' => $w->walkie_id,
                'radio_id' => $w->radio_id ?: '-',
                'serial_number' => $w->serial_number ?: '-',
                'model' => $w->model ?: '-',
                'status' => $w->status ?: '-',
                'ownership_type' => $w->ownership_type ?: '-',
                'ownership' => $w->ownership ?: '-',
                'department' => $w->department ?: '-',
                'position' => $w->position ?: '-',
            ]);

        return response()->json([
            'category' => $category,
            'label' => MasterData::CATEGORIES[$category],
            'value' => $value,
            'count' => $records->count(),
            'records' => $records,
        ]);
    }

    private function resolveCategory(Request $request): string
    {
        $category = $request->input('category');

        abort_unless(array_key_exists($category, MasterData::CATEGORIES), 422, 'Invalid category.');

        return $category;
    }

    private function normalize(string $value): string
    {
        return strtoupper(trim($value));
    }

    /**
     * Map of value => count of walkie talkies referencing it, for a category.
     */
    private function usageCounts(string $category): array
    {
        $columns = self::USAGE_COLUMNS[$category];
        $counts = [];

        foreach ($columns as $column) {
            $grouped = WalkieTalkie::query()
                ->whereNotNull($column)
                ->where($column, '!=', '')
                ->selectRaw($column . ' as value, COUNT(*) as total')
                ->groupBy($column)
                ->pluck('total', 'value');

            foreach ($grouped as $value => $total) {
                $counts[$value] = ($counts[$value] ?? 0) + (int) $total;
            }
        }

        return $counts;
    }

    private function usageCountFor(string $category, string $value): int
    {
        $total = 0;
        foreach (self::USAGE_COLUMNS[$category] as $column) {
            $total += WalkieTalkie::where($column, $value)->count();
        }

        return $total;
    }

    private function log(string $action, string $details): void
    {
        UserActivityLog::create([
            'user_id' => Auth::guard('wt')->id(),
            'username' => Auth::guard('wt')->user()->username ?? null,
            'event_type' => 'action',
            'event_action' => $action,
            'event_details' => $details,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
