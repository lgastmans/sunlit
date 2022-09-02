@extends('layouts.app')

@section('title')
    @parent() | Create a Proforma Invoice
@endsection

@section('page-title', 'Create a Proforma Invoice')

@section('content')

<div class="row">
    <div class="col-6">
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <p>{{ __('app.create_title', ['field' => 'proforma invoice']) }} </p>
                </div>
                <x-forms.errors class="mb-4" :errors="$errors" />
                    <form action="{{ route('sale-orders.store') }}" method="POST" class="needs-validation" novalidate>
                        @csrf()
                        <div class="mb-12">
                            <label class="form-label" for="order_number">Order #</label>
                            <input type="hidden" name="order_number" id="order_number" value="{{ $order_number }}">
                            <div class="input-group">
                                <span class="input-group-text">{{ Setting::get('sale_order.prefix') }}</span>
                                <input type="text" class="form-control" name="order_number_count" id="order_number_count" placeholder="" value="{{ $order_number_count }}" readonly required>
                                <span class="input-group-text">{{ Setting::get('sale_order.suffix') }}</span>
                                <div class="invalid-feedback">
                                    {{ __('error.form_invalid_field', ['field' => strtolower('order #') ]) }}
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="dealer-select">Dealer</label>
                            <select class="dealer-select form-control" name="dealer_id" required></select>
                            <div class="invalid-feedback">
                                {{ __('error.form_invalid_field', ['field' => 'dealer' ]) }}
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="warehouse-select">Warehouse</label>
                            <select class="warehouse-select form-control" name="warehouse_id" required></select>
                            <div class="invalid-feedback">
                                {{ __('error.form_invalid_field', ['field' => 'warehouse' ]) }}
                            </div>
                        </div>
                    
                        <button class="btn btn-primary" type="submit">{{ __('app.create_title', ['field' => 'proforma invoice']) }}</button>
                    </form>
                </div>
            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col -->
</div>


@endsection

@section('page-scripts')

    <script>


        var dealerSelect = $(".dealer-select").select2();
        dealerSelect.select2({
            ajax: {
                url: '{{route('ajax.dealers')}}',
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