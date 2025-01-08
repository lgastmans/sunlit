<?php

namespace App\Livewire;

use App\Models\Product;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Themes\Bootstrap5;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class ProductTable extends PowerGridComponent
{
    public string $tableName = 'product-table'; //'product-table-opqtkz-table';

    public function setUp(): array
    {
        $this->showCheckBox();
        
        return [
            PowerGrid::header()
                ->showSearchInput(),
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),            
        ];
    }

    public function datasource(): Builder
    {
        return Product::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('category_id')
            ->add('supplier_id')
            ->add('tax_id')
            ->add('code')
            ->add('name')
            ->add('model')
            ->add('minimum_quantity')
            ->add('purchase_price')
            ->add('kw_rating')
            ->add('part_number')
            ->add('notes')
            ->add('cable_length_input')
            ->add('cable_length_output')
            ->add('weight_actual')
            ->add('weight_volume')
            ->add('weight_calculated')
            ->add('warranty')
            ->add('created_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('Category id', 'category_id'),
            Column::make('Supplier id', 'supplier_id'),
            Column::make('Tax id', 'tax_id'),
            Column::make('Code', 'code')
                ->sortable()
                ->searchable(),

            Column::make('Name', 'name')
                ->sortable()
                ->searchable(),

            Column::make('Model', 'model')
                ->sortable()
                ->searchable(),

            Column::make('Minimum quantity', 'minimum_quantity')
                ->sortable()
                ->searchable(),

            Column::make('Purchase price', 'purchase_price')
                ->sortable()
                ->searchable(),

            Column::make('Kw rating', 'kw_rating')
                ->sortable()
                ->searchable(),

            Column::make('Part number', 'part_number')
                ->sortable()
                ->searchable(),

            Column::make('Notes', 'notes')
                ->sortable()
                ->searchable(),

            Column::make('Cable length input', 'cable_length_input')
                ->sortable()
                ->searchable(),

            Column::make('Cable length output', 'cable_length_output')
                ->sortable()
                ->searchable(),

            Column::make('Weight actual', 'weight_actual')
                ->sortable()
                ->searchable(),

            Column::make('Weight volume', 'weight_volume')
                ->sortable()
                ->searchable(),

            Column::make('Weight calculated', 'weight_calculated')
                ->sortable()
                ->searchable(),

            Column::make('Warranty', 'warranty'),
            Column::make('Created at', 'created_at_formatted', 'created_at')
                ->sortable(),

            Column::make('Created at', 'created_at')
                ->sortable()
                ->searchable(),

            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert('.$rowId.')');
    }

    public function actions(Product $row): array
    {
        return [
            Button::add('edit')
                ->slot('Edit: '.$row->id)
                ->id()
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('edit', ['rowId' => $row->id])
        ];
    }

    /*
    public function actionRules($row): array
    {
       return [
            // Hide button edit for ID 1
            Rule::button('edit')
                ->when(fn($row) => $row->id === 1)
                ->hide(),
        ];
    }
    */
}
