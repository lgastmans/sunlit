@extends('layouts.app')

@section('page-title', 'Warehouses')

@section('content')

<div class="row">
    <div class="col-6">
        <div class="card">
            <div class="card-body">
                <x-forms.errors class="mb-4" :errors="$errors" />
                    <form action="@if ($warehouse->id) {{ route('warehouses.update', $warehouse->id) }} @else {{ route('warehouses.store') }} @endif" method="POST" class="needs-validation" novalidate>
                        @csrf()
                        @if ($warehouse->id)
                            @method('PUT')
                                <input type="hidden" name="id" value="{{ old('id', $warehouse->id) }}" />
                        @endif
                        <div class="mb-3">
                            <x-forms.input label="name" name="name" value="{{ old('name', $warehouse->name) }}" required="true"/>
                        </div>
                        <div class="mb-3">
                            <x-forms.input label="contact person" name="contact_person" value="{{ old('contact_person', $warehouse->contact_person) }}"  required="true"/>
                        </div>
                        <div class="mb-3">
                            <x-forms.input label="address" name="address" value="{{ old('address', $warehouse->address) }}" required="true"/>
                        </div>

                        <div class="mb-3">
                            <x-forms.input label="city" name="city" value="{{ old('city', $warehouse->city) }}" required="true"/>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="state-select">State</label>
                            <select class="state-select form-control" name="state_id">
                                @if ($warehouse->state)
                                    <option value="{{$warehouse->state->id}}" selected="selected">{{$warehouse->state->name}}</option>
                                @endif
                            </select>
                            <div class="invalid-feedback">
                                {{ __('error.form_invalid_field', ['field' => 'state' ]) }}
                            </div>
                        </div>
                        <div class="mb-3">
                            <x-forms.input label="zip code" name="zip_code" value="{{ old('zip_code', $warehouse->zip_code) }}"  required="true"/>
                        </div>
                        <div class="mb-3">
                            <x-forms.input label="phone" name="phone" value="{{ old('phone', $warehouse->phone) }}"  required="true"/>
                        </div>
                        <div class="mb-3">
                            <x-forms.input label="phone 2" name="phone2" value="{{ old('phone2', $warehouse->phone2) }}" required="true"/>
                        </div>
                       <div class="mb-3">
                            <x-forms.inputGroup label="email address" name="email" value="{{ old('email', $warehouse->email) }}"  required="true" position="before" symbol="@"/>
                        </div>
                       
                        <button class="btn btn-primary" type="submit">@if ($warehouse->id) {{ __('app.edit_title', ['field' => 'warehouse']) }} @else {{ __('app.add_title', ['field' => 'warehouse']) }} @endif</button>

                    </form>
                </div>
            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col -->
</div>
@endsection

@section('page-scripts')
    <script>
        var stateSelect = $(".state-select").select2();
        stateSelect.select2({
            ajax: {
                url: '{{route('ajax.states')}}',
                dataType: 'json'
            }
        });
    </script>
@endsection