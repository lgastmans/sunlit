<?php

namespace App\Exports;

use App\Models\Product;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

/*
    https://makitweb.com/export-data-in-excel-and-csv-format-with-laravel-excel/
*/

class ProductsExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        //return collect(Product::all());

        return collect(Product::join('categories', 'categories.id', '=', 'products.category_id')
                ->join('suppliers', 'suppliers.id', '=', 'products.supplier_id')
                ->join('taxes', 'taxes.id', '=', 'products.tax_id')
                ->orderBy('products.code', 'ASC')
                ->get(['categories.name as category_name', 'suppliers.company', 'taxes.name as tax_name', 'products.code', 'products.name', 'products.model', 'products.cable_length', 'products.kw_rating', 'products.part_number']
            ));

        //getProducts()
    }

    public function headings(): array {
        return [
           "category", "supplier", "tax", "code","name","model","cable_length", "kw_rating", "part_number"
        ];
    }
}
