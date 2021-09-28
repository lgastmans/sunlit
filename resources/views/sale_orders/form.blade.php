@extends('layouts.app')

@section('title')
    @parent() | Create a Sale Order
@endsection

@section('page-title', 'Create a Sale Order')

@section('content')

<div class="row">
    <div class="col-6">
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <p>{{ __('app.create_title', ['field' => 'sale order']) }} </p>
                </div>
                <x-forms.errors class="mb-4" :errors="$errors" />
                    <form action="{{ route('sale-orders.store') }}" method="POST" class="needs-validation" novalidate>
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
                            <label class="form-label" for="dealer-select">Dealer</label>
                            <select class="dealer-select form-control" name="dealer_id" required></select>
                            <div class="invalid-feedback">
                                {{ __('error.form_invalid_field', ['field' => 'dealer' ]) }}
                            </div>
                        </div>
                    
                        <button class="btn btn-primary" type="submit">{{ __('app.create_title', ['field' => 'sale order']) }}</button>
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



    </script>


@endsection