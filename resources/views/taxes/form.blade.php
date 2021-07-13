@extends('layouts.app')

@section('page-title', 'Taxes')

@section('content')

<div class="row">
    <div class="col-6">
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <p>@if ($tax->id) {{ __('app.edit_title', ['field' => 'tax']) }}: <span class="text-primary">{{ $tax->name }}</span> @else {{ __('app.add_title', ['field' => 'tax']) }} @endif </p>
                </div>
                <x-forms.errors class="mb-4" :errors="$errors" />
                    <form action="@if ($tax->id) {{ route('taxes.update', $tax->id) }} @else {{ route('taxes.store') }} @endif" method="POST" class="needs-validation" novalidate>
                        @csrf()
                        @if ($tax->id)
                            @method('PUT')
                        @endif
                        @if ($tax->id)
                            <div class="mb-3">
                                <input type="hidden" name="id" value="{{ old('id', $tax->id) }}" />
                            </div>
                        @endif
                        <x-forms.input label="Name" name="name" value="{{ old('name', $tax->name) }}" message="Please provide a name" required="true"/>
                        <div class="mb-3">
                            <label class="form-label" for="display_amount">Amount</label>
                            <div class="input-group">
                                <input class="form-control" id="display_amount" name="display_amount" value="{{ old('display_amount', $tax->display_amount) }}" message="Please provide the percentage" required="true" data-toggle="input-mask" data-mask-format="00.00"/>
                                <span class="input-group-text" id="inputGroupPrepend">%</span>
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
