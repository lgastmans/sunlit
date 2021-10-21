@extends('layouts.app')

@section('title')
    @parent() | Add a Product
@endsection

@section('page-title', 'Products')

@section('content')

<div class="row">
    <div class="col-9">
        <div class="card">
            <div class="card-body">
                <x-forms.errors class="mb-4" :errors="$errors" />
                    <form action="@if ($product->id) {{ route('products.update', $product->id) }} @else {{ route('products.store') }} @endif" method="POST" class="needs-validation" novalidate>
                        @csrf()
                        @if ($product->id)
                            @method('PUT')
                        @endif
                        <div class="mb-3 row">
                            <div class="col-xl-3">
                                <label class="form-label" for="category-select">Category</label>
                                <select class="category-select form-control" name="category_id">
                                    @if ($product->category)
                                        <option value="{{$product->category->id}}" selected="selected">{{$product->category->name}}</option>
                                    @endif
                                </select>
                                <div class="invalid-feedback">
                                    {{ __('error.form_invalid_field', ['field' => 'category' ]) }}
                                </div>
                            </div>

                            <div class="col-xl-3">
                                <label class="form-label" for="supplier-select">Supplier</label>
                                @if ($product->purchase_order_item_count == 0)
                                <select class="supplier-select form-control" name="supplier_id">
                                    @if ($product->supplier)
                                        <option value="{{$product->supplier->id}}" selected="selected">{{$product->supplier->company}}</option>
                                    @endif
                                </select>
                                @else
                                <input type="text" class="form-control" value="{{$product->supplier->company}}" disabled>
                                    <input type="hidden" value="{{$product->supplier->id}}" name="supplier_id">
                                @endif
                                <div class="invalid-feedback">
                                    {{ __('error.form_invalid_field', ['field' => 'supplier' ]) }}
                                </div>
                            </div>

                            <div class="col-xl-2">
                                <label class="form-label" for="tax-select">Tax</label>
                                <select class="tax-select form-control" name="tax_id">
                                    @if ($product->tax)
                                        <option value="{{$product->tax->id}}" selected="selected">{{$product->tax->name}}</option>
                                    @endif
                                </select>
                                <div class="invalid-feedback">
                                    {{ __('error.form_invalid_field', ['field' => 'tax' ]) }}
                                </div>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <div class="col-xl-2">
                                <x-forms.input label="Code" name="code" value="{{ old('code', $product->code) }}" message="Please provide a code" required="true"/>
                            </div>
                            <div class="col-xl-4">
                                <x-forms.input label="Name" name="name" value="{{ old('name', $product->name) }}" message="Please provide a name" required="true"/>
                            </div>
                            <div class="col-xl-4">
                                <x-forms.input label="Model" name="model" value="{{ old('model', $product->model) }}" message="Please provide a model" required="false"/>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <div class="col-xl-2">
                                <label class="form-label" for="purchase_price">Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text" id="inputGroupPrepend">{{ __('app.currency_symbol_inr')}}</span>
                                    <input class="form-control" id="purchase_price" name="purchase_price" value="{{ old('purchase_price', $product->purchase_price) }}" required="true" data-toggle="input-mask" />
                                    <div class="invalid-feedback">
                                        {{ __('error.form_invalid_field', ['field' => 'percentage']) }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-2">
                                <x-forms.input label="Minimum Quantity" name="minimum_quantity" value="{{ old('minimum_quantity', $product->minimum_quantity) }}" message="Please provide a minimum quantity" required="false"/>
                            </div>
                            <div class="col-xl-2">
                                <x-forms.input label="Cable length" name="cable_length" value="{{ old('cable_length', $product->cable_length) }}" message="Please provide a cable length" required="false"/>
                            </div>
                            <div class="col-xl-2">
                                <x-forms.input label="KW rating" name="kw_rating" value="{{ old('kw_rating', $product->kw_rating) }}" message="Please provide a KW rating" required="false"/>
                            </div>
                            <div class="col-xl-2">
                                <x-forms.input label="Part number" name="part_number" value="{{ old('part_number', $product->part_number) }}" message="Please provide a part number" required="false"/>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <div class="col-xl-2">
                                <x-forms.input label="Cable Length Input" name="cable_length_input" value="{{ old('cable_length_input', $product->cable_length_input) }}" message="Please provide cable length input" required="false"/>
                            </div>
                            <div class="col-xl-2">
                                <x-forms.input label="Cable Length Output" name="cable_length_output" value="{{ old('cable_length_output', $product->cable_length_output) }}" message="Please provide cable length output" required="false"/>
                            </div>
                            <div class="col-xl-2">
                                <x-forms.input label="Actual Weight" name="weight_actual" value="{{ old('weight_actual', $product->weight_actual) }}" message="Please provide Actual Weight" required="false"/>
                            </div>
                            <div class="col-xl-2">
                                <x-forms.input label="Volume Weight" name="weight_volume" value="{{ old('weight_volume', $product->weight_volume) }}" message="Please provide Volume Weight" required="false"/>
                            </div>
                            <div class="col-xl-2">
                                <x-forms.input label="Calculated Weight" name="weight_calculated" value="{{ old('weight_calculated', $product->weight_calculated) }}" message="Please provide Calculated Weight" required="false"/>
                            </div>
                            <div class="col-xl-2">
                                <x-forms.input label="Warranty" name="warranty" value="{{ old('warranty', $product->warranty) }}" message="Please provide Warranty" required="false"/>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <div class="col-xl-12">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control" name="notes" rows="5">{{ old('notes', $product->notes) }}</textarea>
                            </div>
                        </div>
                        <button class="btn btn-primary" type="submit">@if ($product->id) {{ __('app.edit_title', ['field' => 'product']) }} @else {{ __('app.add_title', ['field' => 'product']) }} @endif</button>

                    </form>

            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col -->
</div>
@endsection


@section('page-scripts')

    <script>

        var categorySelect = $(".category-select").select2();
        categorySelect.select2({
            ajax: {
                url: '{{route('ajax.categories')}}',
                dataType: 'json'
            }
        });

        var supplierSelect = $(".supplier-select").select2();
        supplierSelect.select2({
            ajax: {
                url: '{{route('ajax.suppliers')}}',
                dataType: 'json'
            }
        });

        var taxSelect = $(".tax-select").select2();
        taxSelect.select2({
            ajax: {
                url: '{{route('ajax.taxes')}}',
                dataType: 'json'
            }
        });

    </script>

@endsection