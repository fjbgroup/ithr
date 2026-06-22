<?php

namespace App\Http\Controllers;

use App\Models\StaffIr;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\AuditLogger;

class IRController extends Controller
{
    public function index(Request $request)
    {
        $search = trim($request->query('q', ''));
        $type_filter = $request->query('type');
        $staff_filter = $request->query('staff_id');

        $query = StaffIr::with(['staff.department']);

        if ($staff_filter) {
            $query->where('staff_id', $staff_filter);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $like = "%$search%";
                $q->where('title', 'like', $like)
                  ->orWhereHas('staff', function($sq) use ($like) {
                      $sq->where('name', 'like', $like)
                        ->orWhere('staff_no', 'like', $like);
                  });
            });
        }

        if ($type_filter && in_array($type_filter, ['Verbal', 'Written'])) {
            $query->where('type', $type_filter);
        }

        $records = $query->orderBy('date', 'DESC')->orderBy('id', 'DESC')->get();

        $totalVerbal = $records->where('type', 'Verbal')->count();
        $totalWritten = $records->where('type', 'Written')->count();

        $preloadStaff = null;
        if ($staff_filter) {
            $preloadStaff = Staff::find($staff_filter);
        }

        return view('ir.index', [
            'records' => $records,
            'search' => $search,
            'type_filter' => $type_filter,
            'staff_filter' => $staff_filter,
            'totalVerbal' => $totalVerbal,
            'totalWritten' => $totalWritten,
            'preloadStaff' => $preloadStaff
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'title' => 'required',
            'date' => 'required|date',
            'type' => 'required|in:Verbal,Written',
        ]);

        StaffIr::create([
            'staff_id' => $request->staff_id,
            'title' => $request->title,
            'date' => $request->date,
            'type' => $request->type,
            'created_by' => Auth::id(),
        ]);

        $irStaff = Staff::find($request->staff_id);
        AuditLogger::log('create', 'ir',
            'Added IR record "' . $request->title . '" (' . $request->type . ') for ' . ($irStaff->name ?? 'staff #' . $request->staff_id) . '.',
            ['staff_id' => $request->staff_id, 'type' => $request->type]
        );

        return redirect()->back()->with('success', 'IR record added successfully');
    }

    public function update(Request $request, $id)
    {
        $record = StaffIr::findOrFail($id);

        $request->validate([
            'title' => 'required',
            'date' => 'required|date',
            'type' => 'required|in:Verbal,Written',
        ]);

        $record->update([
            'title' => $request->title,
            'date' => $request->date,
            'type' => $request->type,
        ]);

        AuditLogger::log('update', 'ir',
            'Updated IR record #' . $id . ' "' . $request->title . '" for ' . ($record->staff->name ?? 'staff #' . $record->staff_id) . '.',
            ['ir_id' => $id]
        );

        return redirect()->back()->with('success', 'IR record updated successfully');
    }

    public function destroy($id)
    {
        $record = StaffIr::with('staff')->findOrFail($id);

        AuditLogger::log('delete', 'ir',
            'Deleted IR record #' . $id . ' "' . $record->title . '" (' . $record->type . ') for ' . ($record->staff->name ?? 'staff #' . $record->staff_id) . '.',
            ['ir_id' => $id]
        );

        $record->delete();
        return redirect()->back()->with('success', 'IR record deleted successfully');
    }

    public function show($id)
    {
        $record = StaffIr::with('staff')->findOrFail($id);
        return response()->json($record);
    }
}
