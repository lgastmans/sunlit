@extends('layouts.app')

@section('page-title', 'Products')

@section('content')

<div class="row">
    <div class="col-6">
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <h4>Add a product</h4>
                </div>
                <x-forms.errors class="mb-4" :errors="$errors" />
                    <form action="{{ route('products.store') }}" method="POST" class="needs-validation" novalidate>
                        @csrf()
                        <div class="mb-3">
                            <label class="form-label" for="category">Category</label>
                            <select class="form-control select2" data-toggle="select2" name="category" data-placeholder="Select a category" required>
                                <option value="10">Inverter</option>
                                <option value="20">Solar Panel</option>
                            </select>
                            <div class="invalid-feedback">
                                Please select a category
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="supplier">Supplier</label>
                            <select class="form-control select2" data-toggle="select2" name="supplier" data-placeholder="Select a supplier" required>
                                <option value="100">Wairee</option>
                                <option value="101">Studer</option>
                            </select>
                            <div class="invalid-feedback">
                                Please select a supplier
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="tax">Tax</label>
                            <select class="form-control select2" data-toggle="select2" name="tax" data-placeholder="Select a tax" required>
                                <option value="5">5.00 %</option>
                                <option value="10">10.00 %</option>
                            </select>
                            <div class="invalid-feedback">
                                Please select a tax
                            </div>
                        </div>
                        <div class="mb-3">
                            <x-forms.input label="Code" name="code" value="{{ old('code', $product->code) }}" message="Please provide a code" required="true"/>
                        </div>
                        <div class="mb-3">
                            <x-forms.input label="Name" name="name" value="{{ old('name', $product->name) }}" message="Please provide a name" required="true"/>
                        </div>
                        <div class="mb-3">
                            <x-forms.input label="Model" name="name" value="{{ old('name', $product->name) }}" message="Please provide a name" required="true"/>
                        </div>
                        <div class="mb-3">
                            <x-forms.input label="Cable length" name="cable_length" value="{{ old('cable_length', $product->cable_length) }}" message="Please provide a cable length" required="true"/>
                        </div>
                        <div class="mb-3">
                            <x-forms.input label="KW rating" name="kw_rating" value="{{ old('kw_rating', $product->kw_rating) }}" message="Please provide a KW rating" required="true"/>
                        </div>
                        <div class="mb-3">
                            <x-forms.input label="Part number" name="part_number" value="{{ old('part_number', $product->part_number) }}" message="Please provide a part number" required="true"/>
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" name="notes" rows="5" required></textarea>
                        </div>
                       
                        <button class="btn btn-primary" type="submit">Create tax</button>

                    </form>
                </div>
            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col -->
</div>


@endsection
