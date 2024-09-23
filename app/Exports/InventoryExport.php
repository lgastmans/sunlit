<?php

namespace App\Exports;

use App\Models\Inventory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
/*
    useful links for reference

    https://makitweb.com/export-data-in-excel-and-csv-format-with-laravel-excel/
    https://laraveldaily.com/laravel-excel-export-formatting-and-styling-cells/
    https://docs.laravel-excel.com/2.1/reference-guide/formatting.html
*/

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

//class InventoryExport implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnFormatting, WithMapping
class InventoryExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping
{
    public function collection(): Collection
    {
        return collect(Inventory::with(['product', 'warehouse'])
            ->select('inventories.*', 'products.code', 'products.name', 'products.minimum_quantity', 'suppliers.company', 'categories.name', 'warehouses.name')
            ->addSelect(DB::raw('(inventories.stock_available + inventories.stock_ordered - inventories.stock_booked) AS projected'))
            ->join('products', 'products.id', '=', 'product_id')
            ->join('warehouses', 'warehouses.id', '=', 'warehouse_id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->join('suppliers', 'suppliers.id', '=', 'products.supplier_id')
            ->orderBy('products.code')
            ->get()
        );
    }

    public function headings(): array
    {
        return [
            'company', 'warehouse', 'category', 'code', 'name', 'available', 'ordered', 'booked', 'projected', 'min qty',
        ];
    }

    /*
        public function columnFormats(): array
        {
            return [
                'C' => NumberFormat::FORMAT_PERCENTAGE_00,
            ];
        }
    */

    public function map($record): array
    {
        return [
            $record->company,
            $record->warehouse->name,
            $record->product->category->name,
            $record->product->code,
            $record->product->name,
            $record->stock_available,
            $record->stock_ordered,
            $record->stock_booked,
            $record->projected,
            $record->minimum_quantity,
        ];
    }
}
