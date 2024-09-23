<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use App\Models\Product;
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

class ProductsExport implements FromCollection, ShouldAutoSize, WithColumnFormatting, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection(): Collection
    {
        return collect(Product::join('categories', 'categories.id', '=', 'products.category_id')
            ->join('suppliers', 'suppliers.id', '=', 'products.supplier_id')
            ->join('taxes', 'taxes.id', '=', 'products.tax_id')
            ->orderBy('products.code', 'ASC')
            ->get(['categories.name as category_name', 'suppliers.company', 'taxes.amount as tax_amount', 'products.part_number', 'products.kw_rating', 'products.notes']
            ));
    }

    public function headings(): array
    {
        return [
            'category', 'supplier', 'tax', 'part_number', 'kw_rating', 'description',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_PERCENTAGE_00,
        ];
    }

    public function map($product): array
    {
        return [
            $product->category_name,
            $product->company,
            ($product->tax_amount / 10000),
            $product->part_number,
            $product->kw_rating,
            $product->notes,
        ];
    }
}
