<?php

namespace App\Http\Controllers\IT;

use App\Models\IT\AssetClass;
use App\Models\IT\InventoryItem;
use App\Models\IT\NonItAsset;
use App\Models\IT\EwasteItem;
use App\Services\IT\ActivityLogService;
use Illuminate\Http\Request;

class AssetClassController extends Controller
{
    public function index()
    {
        $addCounts = function ($cls) {
            $cls->it_count  = InventoryItem::where('asset_class', $cls->name)->count();
            $cls->nit_count = NonItAsset::where('asset_class', $cls->name)->count();
            return $cls;
        };

        $itClasses  = AssetClass::where('type', 'it')->orderBy('sort_order')->orderBy('name')->get()->map($addCounts);
        $nitClasses = AssetClass::where('type', 'non_it')->orderBy('sort_order')->orderBy('name')->get()->map($addCounts);

        return view('it.asset-classes.index', compact('itClasses', 'nitClasses'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:50', 'type' => 'required|in:it,non_it', 'sort_order' => 'nullable|integer']);
        $name = strtoupper(trim($request->name));
        $max  = AssetClass::max('sort_order') ?? 0;
        AssetClass::create(['name' => $name, 'type' => $request->type, 'sort_order' => $request->sort_order ?? $max + 1]);
        ActivityLogService::log('CREATE', 'inventory', 0, 'Added asset class: '.$name);
        return back()->with('success', "Class '$name' added.")
                     ->with('scroll_target', $request->type === 'non_it' ? 'nitCard' : 'itCard');
    }

    public function update(Request $request, int $id)
    {
        $request->validate(['name' => 'required|string|max:50', 'type' => 'nullable|in:it,non_it', 'sort_order' => 'nullable|integer']);
        $cls = AssetClass::findOrFail($id);
        $old = $cls->name;
        $new = strtoupper(trim($request->name));
        $cls->update(['name' => $new, 'type' => $request->type ?? $cls->type, 'sort_order' => $request->sort_order ?? $cls->sort_order]);
        if ($old !== $new) {
            InventoryItem::where('asset_class', $old)->update(['asset_class' => $new]);
            NonItAsset::where('asset_class', $old)->update(['asset_class' => $new]);
            EwasteItem::where('asset_class', $old)->update(['asset_class' => $new]);
        }
        ActivityLogService::log('UPDATE', 'inventory', 0, "Renamed class: $old â†’ $new");
        $type = $request->type ?? $cls->type;
        return back()->with('success', "Class updated.")
                     ->with('scroll_target', $type === 'non_it' ? 'nitCard' : 'itCard');
    }

    public function destroy(int $id)
    {
        $cls = AssetClass::findOrFail($id);
        ActivityLogService::log('DELETE', 'inventory', 0, 'Removed class: '.$cls->name);
        $type = $cls->type;
        $cls->delete();
        return back()->with('success', 'Class removed.')
                     ->with('scroll_target', $type === 'non_it' ? 'nitCard' : 'itCard');
    }
}

