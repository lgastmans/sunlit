@extends('layouts.app')

@section('title')
    @parent() | Create a Purchase Order
@endsection

@section('page-title', 'Create a Purchase Order')

@section('content')

<div class="row">
    <div class="col-6">
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <p>{{ __('app.create_title', ['field' => 'purchase order']) }} </p>
                </div>
                <x-forms.errors class="mb-4" :errors="$errors" />
                    <form action="{{ route('purchase-orders.store') }}" method="POST" class="needs-validation" novalidate>
                        @csrf()
                        <div class="mb-3">
                            <div>
                                <label class="form-label" for="order_number">Order #</label>
                                <input type="text" class="form-control" name="order_number" id="order_number" placeholder="" value="{{ old('order_number') }}" required>
                                <div class="invalid-feedback">
                                    {{ __('error.form_invalid_field', ['field' => strtolower('order #') ]) }}
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="supplier-select">Supplier</label>
                            <select class="supplier-select form-control" name="supplier_id"></select>
                            <div class="invalid-feedback">
                                {{ __('error.form_invalid_field', ['field' => 'supplier' ]) }}
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="warehouse-select">Warehouse</label>
                            <select class="warehouse-select form-control" name="warehouse_id"></select>
                            <div class="invalid-feedback">
                                {{ __('error.form_invalid_field', ['field' => 'warehouse' ]) }}
                            </div>
                        </div>
                        <button class="btn btn-primary" type="submit">{{ __('app.create_title', ['field' => 'purchase order']) }}</button>
                    </form>
                </div>
            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col -->
</div>


@endsection

@section('page-scripts')

    <script>
        var supplierSelect = $(".supplier-select").select2();
        supplierSelect.select2({
            ajax: {
                url: '{{route('ajax.suppliers')}}',
                dataType: 'json'
            }
        });

        var warehouseSelect = $(".warehouse-select").select2();
        warehouseSelect.select2({
            ajax: {
                url: '{{route('ajax.warehouses')}}',
                dataType: 'json'
            }
        });

    </script>
@endsection