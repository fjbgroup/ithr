<?php

namespace App\Http\Traits;

use App\Imports\SimpleToArray;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

trait ImportsExcel
{
    public function importPreview(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate(['file' => 'required|file|mimes:xlsx,xls,csv|max:10240']);

        $sheetIndex = max(0, (int) $request->input('sheet_index', 0));
        $headerRow  = max(1, (int) $request->input('header_row', 1));

        $allSheets  = Excel::toArray(new SimpleToArray, $request->file('file'));
        $sheetNames = array_keys($allSheets);
        $sheetData  = array_values($allSheets)[$sheetIndex] ?? [];
        $rawHeaders = array_values($sheetData)[$headerRow - 1] ?? [];

        $headers = array_values(array_filter(
            array_map(fn($h) => [
                'raw' => (string) $h,
                'key' => Str::slug((string) $h, '_'),
            ], $rawHeaders),
            fn($h) => $h['raw'] !== '' && $h['raw'] !== '0' && $h['key'] !== ''
        ));

        return response()->json([
            'sheets'  => array_values($sheetNames),
            'headers' => $headers,
        ]);
    }
}
