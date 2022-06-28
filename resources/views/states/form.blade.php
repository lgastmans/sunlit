@extends('layouts.app')

@section('title')

    @parent() | States

@endsection

@section('page-title', 'States')

@section('content')

<div class="row">
    <div class="col-9">
        <div class="card">
            <div class="card-body">

                <x-forms.errors class="mb-4" :errors="$errors" />

                <form action="@if ($state->id) {{ route('states.update', $state->id) }} @else {{ route('states.store') }} @endif" method="POST" class="needs-validation" novalidate>

                    @csrf()
                    @if ($state->id)
                        @method('PUT')
                            <input type="hidden" name="id" value="{{ old('id', $state->id) }}" />
                    @endif

                    <div class="mb-3 row">
                        <div class="col-xl-5">
                            <x-forms.input label="name" name="name" value="{{ old('name', $state->name) }}" required="true"/>
                        </div>
                        <div class="col-xl-3">
                            <x-forms.input label="code" name="code" value="{{ old('code', $state->code) }}" required="true"/>
                        </div>
                        <div class="col-xl-3">
                            <x-forms.input label="abbreviation" name="abbreviation" value="{{ old('abbreviation', $state->abbreviation) }}" required="true"/>
                        </div>
                    </div>
                
                    <div class="mb-3 row">
                        <div class="col-xl-5">
                            <label class="form-label" for="freightzone-select">Freight Zone</label>
                            <select class="freightzone-select form-control" name="freight_zone_id"></select>
                            <div class="invalid-feedback">
                                {{ __('error.form_invalid_field', ['field' => 'warehouse' ]) }}
                            </div>
                        </div>
                    </div>

                    <button class="btn btn-primary" type="submit">@if ($state->id) {{ __('app.edit_title', ['field' => 'state']) }} @else {{ __('app.add_title', ['field' => 'state']) }} @endif</button>

                </form>
                
            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col -->
</div>

@endsection

@section('page-scripts')

    <script>
        var freightzoneSelect = $(".freightzone-select").select2();
        freightzoneSelect.select2({
            ajax: {
                url: '{{route('ajax.freightzones')}}',
                dataType: 'json'
            }
        });

    </script>
@endsection