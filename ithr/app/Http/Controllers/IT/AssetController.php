<?php

namespace App\Http\Controllers\IT;

use App\Models\IT\InventoryItem;

class AssetController extends Controller
{
    public function show(int $id)
    {
        $asset = InventoryItem::with('creator', 'ewasteItems')->findOrFail($id);
        return view('it.inventory.show', compact('asset'));
    }
}

