<?php

namespace App\Http\Controllers\IT;

use App\Models\IT\Brand;
use App\Models\IT\InventoryItem;
use App\Models\IT\NonItAsset;
use App\Services\IT\ActivityLogService;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::orderBy('sort_order')->orderBy('name')->get()->map(function ($b) {
            $b->it_count  = InventoryItem::where('brand', $b->name)->count();
            $b->nit_count = NonItAsset::where('brand', $b->name)->count();
            return $b;
        });

        return view('it.brands.index', compact('brands'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:100']);
        $name = strtoupper(trim($request->name));
        $max  = Brand::max('sort_order') ?? 0;
        Brand::create(['name' => $name, 'sort_order' => $max + 1]);
        ActivityLogService::log('CREATE', 'inventory', 0, 'Added brand: ' . $name);
        return back()->with('success', "Brand '{$name}' added.");
    }

    public function update(Request $request, int $id)
    {
        $request->validate(['name' => 'required|string|max:100']);
        $brand = Brand::findOrFail($id);
        $old   = $brand->name;
        $new   = strtoupper(trim($request->name));
        $brand->update(['name' => $new]);
        if ($old !== $new) {
            InventoryItem::where('brand', $old)->update(['brand' => $new]);
            NonItAsset::where('brand', $old)->update(['brand' => $new]);
        }
        ActivityLogService::log('UPDATE', 'inventory', 0, "Renamed brand: {$old} to {$new}");
        return back()->with('success', 'Brand updated.');
    }

    public function destroy(int $id)
    {
        $brand = Brand::findOrFail($id);
        ActivityLogService::log('DELETE', 'inventory', 0, 'Removed brand: ' . $brand->name);
        $brand->delete();
        return back()->with('success', 'Brand removed.');
    }
}
