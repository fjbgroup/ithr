<?php

namespace App\Http\Controllers\IT;

use App\Models\IT\AssetClass;
use App\Models\IT\Brand;
use App\Models\IT\InventoryItem;
use App\Models\IT\Location;
use App\Models\IT\NonItAsset;
use Illuminate\Http\Request;

class MasterdataController extends Controller
{
    public function index(Request $request)
    {
        $addClassCounts = function ($cls) {
            $cls->it_count  = InventoryItem::where('asset_class', $cls->name)->count();
            $cls->nit_count = NonItAsset::where('asset_class', $cls->name)->count();
            return $cls;
        };

        $itClasses  = AssetClass::where('type', 'it')->orderBy('sort_order')->orderBy('name')->get()->map($addClassCounts);
        $nitClasses = AssetClass::where('type', 'non_it')->orderBy('sort_order')->orderBy('name')->get()->map($addClassCounts);

        $brands = Brand::orderBy('sort_order')->orderBy('name')->get()->map(function ($b) {
            $b->it_count  = InventoryItem::where('brand', $b->name)->count();
            $b->nit_count = NonItAsset::where('brand', $b->name)->count();
            return $b;
        });

        $locations = Location::orderBy('sort_order')->orderBy('name')->get()->map(function ($l) {
            $l->it_count  = InventoryItem::where('location', $l->name)->count();
            $l->nit_count = NonItAsset::where('location', $l->name)->count();
            return $l;
        });

        $tab = in_array($request->get('tab'), ['classes', 'brands', 'locations'])
            ? $request->get('tab')
            : 'classes';

        return view('it.masterdata.index', compact('itClasses', 'nitClasses', 'brands', 'locations', 'tab'));
    }
}
