@extends('layouts.app')

@section('page-title', 'Products')

@section('content')

<div class="row">
    <div class="col-6">
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <p>@if ($product->id) {{ __('app.edit_title', ['field' => 'product']) }}: <span class="text-primary">{{ $product->name }}</span> @else {{ __('app.add_title', ['field' => 'product']) }} @endif </p>
                </div>
                <x-forms.errors class="mb-4" :errors="$errors" />
                    <form action="@if ($product->id) {{ route('products.update', $product->id) }} @else {{ route('products.store') }} @endif" method="POST" class="needs-validation" novalidate>
                        @csrf()
                        @if ($product->id)
                            @method('PUT')
                        @endif
                        @if ($product->id)
                            <div class="mb-3">
                                <input type="hidden" name="id" value="{{ old('id', $product->id) }}" />
                            </div>
                        @endif

                        <div class="mb-3">
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

                        <div class="mb-3">
                            <label class="form-label" for="supplier-select">Supplier</label>
                            <select class="supplier-select form-control" name="supplier_id">
                                @if ($product->supplier)
                                    <option value="{{$product->supplier->id}}" selected="selected">{{$product->supplier->company}}</option>
                                @endif
                            </select>
                            <div class="invalid-feedback">
                                {{ __('error.form_invalid_field', ['field' => 'supplier' ]) }}
                            </div>
                        </div>

                        <div class="mb-3">
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

                        <div class="mb-3">
                            <x-forms.input label="Code" name="code" value="{{ old('code', $product->code) }}" message="Please provide a code" required="true"/>
                        </div>
                        <div class="mb-3">
                            <x-forms.input label="Name" name="name" value="{{ old('name', $product->name) }}" message="Please provide a name" required="true"/>
                        </div>
                        <div class="mb-3">
                            <x-forms.input label="Model" name="model" value="{{ old('model', $product->model) }}" message="Please provide a model" required="false"/>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="display_purchase_price">Amount</label>
                            <div class="input-group">
                                <input class="form-control" id="display_purchase_price" name="display_purchase_price" value="{{ old('display_purchase_price', $product->display_purchase_price) }}" required="true" data-toggle="input-mask" data-mask-format="000000.00"/>
                                <span class="input-group-text" id="inputGroupPrepend">&#8377;</span>
                                <div class="invalid-feedback">
                                    {{ __('error.form_invalid_field', ['field' => 'percentage']) }}
                                </div>
                            </div>
                        </div>




                        <div class="mb-3">
                            <x-forms.input label="Minimum Quantity" name="minimum_quantity" value="{{ old('minimum_quantity', $product->minimum_quantity) }}" message="Please provide a minimum quantity" required="false"/>
                        </div>
                        <div class="mb-3">
                            <x-forms.input label="Cable length" name="cable_length" value="{{ old('cable_length', $product->cable_length) }}" message="Please provide a cable length" required="false"/>
                        </div>
                        <div class="mb-3">
                            <x-forms.input label="KW rating" name="kw_rating" value="{{ old('kw_rating', $product->kw_rating) }}" message="Please provide a KW rating" required="false"/>
                        </div>
                        <div class="mb-3">
                            <x-forms.input label="Part number" name="part_number" value="{{ old('part_number', $product->part_number) }}" message="Please provide a part number" required="false"/>
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" name="notes" rows="5">{{ old('notes', $product->notes) }}</textarea>
                        </div>
                       
                        <button class="btn btn-primary" type="submit">@if ($product->id) {{ __('app.edit_title', ['field' => 'product']) }} @else {{ __('app.add_title', ['field' => 'product']) }} @endif</button>

                    </form>
                </div>
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