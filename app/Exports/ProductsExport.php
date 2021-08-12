<?php

namespace App\Exports;

use App\Models\Product;


/*
    useful links for reference

    https://makitweb.com/export-data-in-excel-and-csv-format-with-laravel-excel/
    https://laraveldaily.com/laravel-excel-export-formatting-and-styling-cells/
    https://docs.laravel-excel.com/2.1/reference-guide/formatting.html
*/


use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductsExport implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnFormatting, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return collect(Product::join('categories', 'categories.id', '=', 'products.category_id')
                ->join('suppliers', 'suppliers.id', '=', 'products.supplier_id')
                ->join('taxes', 'taxes.id', '=', 'products.tax_id')
                ->orderBy('products.code', 'ASC')
                ->get(['categories.name as category_name', 'suppliers.company', 'taxes.amount as tax_amount', 'products.code', 'products.name', 'products.model', 'products.cable_length', 'products.kw_rating', 'products.part_number']
            ));
    }

    public function headings(): array {
        return [
           "category", "supplier", "tax", "code","name","model","cable_length", "kw_rating", "part_number"
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
            ($product->tax_amount/10000),
            $product->code,
            $product->name,
            $product->model,
            $product->cable_length,
            $product->kw_rating,
            $product->part_number
        ];
    }

}
