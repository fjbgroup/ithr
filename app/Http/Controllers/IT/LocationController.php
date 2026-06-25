<?php

namespace App\Http\Controllers\IT;

use App\Models\IT\Location;
use App\Models\IT\InventoryItem;
use App\Models\IT\NonItAsset;
use App\Services\IT\ActivityLogService;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::orderBy('sort_order')->orderBy('name')->get()->map(function ($l) {
            $l->it_count  = InventoryItem::where('location', $l->name)->count();
            $l->nit_count = NonItAsset::where('location', $l->name)->count();
            return $l;
        });

        return view('it.locations.index', compact('locations'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:100']);
        $name = strtoupper(trim($request->name));
        $max  = Location::max('sort_order') ?? 0;
        Location::create(['name' => $name, 'sort_order' => $max + 1]);
        ActivityLogService::log('CREATE', 'inventory', 0, 'Added location: ' . $name);
        return back()->with('success', "Location '{$name}' added.");
    }

    public function update(Request $request, int $id)
    {
        $request->validate(['name' => 'required|string|max:100']);
        $location = Location::findOrFail($id);
        $old      = $location->name;
        $new      = strtoupper(trim($request->name));
        $location->update(['name' => $new]);
        if ($old !== $new) {
            InventoryItem::where('location', $old)->update(['location' => $new]);
            NonItAsset::where('location', $old)->update(['location' => $new]);
        }
        ActivityLogService::log('UPDATE', 'inventory', 0, "Renamed location: {$old} to {$new}");
        return back()->with('success', 'Location updated.');
    }

    public function destroy(int $id)
    {
        $location = Location::findOrFail($id);
        ActivityLogService::log('DELETE', 'inventory', 0, 'Removed location: ' . $location->name);
        $location->delete();
        return back()->with('success', 'Location removed.');
    }
}
