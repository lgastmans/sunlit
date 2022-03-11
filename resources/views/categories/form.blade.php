@extends('layouts.app')

@section('title')
    @parent() | Add a Category
@endsection

@section('page-title', 'Categories' )

@section('content')

<div class="row">
    <div class="col-5">
        <div class="card">
            <div class="card-body">
                <x-forms.errors class="mb-4" :errors="$errors" />
                    <form action="@if ($category->id) {{ route('categories.update', $category->id) }} @else {{ route('categories.store') }} @endif" method="POST" class="needs-validation" novalidate>
                        @csrf()
                        @if ($category->id)
                            @method('PUT')
                        @endif
                        <div class="row mb-6">
                            <div class="col-xl-5">
                                <x-forms.input label="Name" name="name" value="{{ old('name', $category->name) }}" required="true"/>
                            </div>
                            <div class="col-xl-5">
                                <x-forms.input label="HSN" name="hsn_code" value="{{ old('hsn_code', $category->hsn_code) }}" required="true"/>
                            </div>
                        </div>   
                        <div class="row mt-3 mb-6">
                            <div class="col-xl-4">
                                <label class="form-label" for="customs_duty">Customs Duty</label>
                                <div class="input-group">
                                    <input class="form-control" id="customs_duty" name="customs_duty" value="{{ old('customs_duty', $category->customs_duty) }}" required="true" />
                                    <span class="input-group-text" id="inputGroupAppend">%</span>

                                    <div class="invalid-feedback">
                                        {{ __('error.form_invalid_field', ['field' => 'percentage']) }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4">
                                <label class="form-label" for="igst">Social Welfare Surcharge</label>
                                <div class="input-group">
                                    <input class="form-control" id="social_welfare_surcharge" name="social_welfare_surcharge" value="{{ old('social_welfare_surcharge', $category->social_welfare_surcharge) }}" required="true" />
                                    <span class="input-group-text" id="inputGroupAppend">%</span>

                                    <div class="invalid-feedback">
                                        {{ __('error.form_invalid_field', ['field' => 'percentage']) }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4">
                                <label class="form-label" for="igst">IGST</label>
                                <div class="input-group">
                                    <input class="form-control" id="igst" name="igst" value="{{ old('igst', $category->igst) }}" required="true" />
                                    <span class="input-group-text" id="inputGroupAppend">%</span>

                                    <div class="invalid-feedback">
                                        {{ __('error.form_invalid_field', ['field' => 'percentage']) }}
                                    </div>
                                </div>
                            </div>
                        </div>                      
                        <button class="mt-4 btn btn-primary" type="submit">@if ($category->id) {{ __('app.edit_title', ['field' => 'category']) }} @else {{ __('app.add_title', ['field' => 'category']) }} @endif</button>
                    </form>
                </div>
            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col -->
</div>


@endsection
