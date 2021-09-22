@extends('layouts.app')

@section('title')
    @parent() | Create a tax
@endsection

@section('page-title')
    Create a tax 
@endsection

@section('content')

<div class="row">
    <div class="col-6">
        <div class="card">
            <div class="card-body">
                <x-forms.errors class="mb-4" :errors="$errors" />
                    <form action="@if ($tax->id) {{ route('taxes.update', $tax->id) }} @else {{ route('taxes.store') }} @endif" method="POST" class="needs-validation" novalidate>
                        @csrf()
                        @if ($tax->id)
                            @method('PUT')
                        @endif
                        <div class="row mb-3">  
                            <div class="col-xl-5">              
                                <x-forms.input label="name" name="name" value="{{ old('name', $tax->name) }}" required="true"/>
                            </div>        
                            <div class="col-xl-2">
                                <label class="form-label" for="amount">Amount</label>
                                <div class="input-group">
                                    <input class="form-control" id="amount" name="amount" value="{{ old('amount', $tax->amount) }}" required="true" data-toggle="input-mask"/>
                                    <span class="input-group-text" id="inputGroupPrepend">%</span>
                                    <div class="invalid-feedback">
                                        {{ __('error.form_invalid_field', ['field' => 'percentage']) }}
                                    </div>
                                </div>
                        </div>
                        </div>
                        <button class="btn btn-primary" type="submit">@if ($tax->id) {{ __('app.edit_title', ['field' => 'tax']) }} @else {{ __('app.add_title', ['field' => 'tax']) }} @endif</button>

                    </form>
                </div>
            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col -->
</div>


@endsection
