@extends('layouts.app')

@section('page-title', 'warehouses')

@section('content')

<div class="row">
    <div class="col-6">
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <p>@if ($warehouse->id) {{ __('app.edit_title', ['field' => 'warehouse']) }}: <span class="text-primary">{{ $warehouse->name }}</span> @else {{ __('app.add_title', ['field' => 'warehouse']) }} @endif </p>
                </div>
                <x-forms.errors class="mb-4" :errors="$errors" />
                    <form action="@if ($warehouse->id) {{ route('warehouses.update', $warehouse->id) }} @else {{ route('warehouses.store') }} @endif" method="POST" class="needs-validation" novalidate>
                        @csrf()
                        @if ($warehouse->id)
                            @method('PUT')
                                <input type="hidden" name="id" value="{{ old('id', $warehouse->id) }}" />
                        @endif
                        <div class="mb-3">
                            <x-forms.input label="Name" name="name" value="{{ old('name', $warehouse->name) }}" message="Please provide the name name" required="true"/>
                        </div>
                        <div class="mb-3">
                            <x-forms.input label="Contact person" name="contact_person" value="{{ old('contact_person', $warehouse->contact_person) }}" message="Please provide the full name of the contact person" required="true"/>
                        </div>
                        <div class="mb-3">
                            <x-forms.input label="Address" name="address" value="{{ old('address', $warehouse->address) }}" message="Please provide the full address" required="true"/>
                        </div>

                        <div class="mb-3">
                            <x-forms.input label="City" name="city" value="{{ old('city', $warehouse->city) }}" message="Please provide a valid city" required="true"/>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="state-select">State</label>
                            <select class="state-select form-control" name="state_id">
                                @if ($warehouse->state)
                                    <option value="{{$warehouse->state->id}}" selected="selected">{{$warehouse->state->name}}</option>
                                @endif
                            </select>
                            <div class="invalid-feedback">
                                Please provide a valid state.
                            </div>
                        </div>
                        <div class="mb-3">
                            <x-forms.input label="Zip code" name="zip_code" value="{{ old('zip_code', $warehouse->zip_code) }}" message="Please provide a valid zip code." required="true"/>
                        </div>
                        <div class="mb-3">
                            <x-forms.input label="Phone" name="phone" value="{{ old('phone', $warehouse->phone) }}" message="Please provide a valid phone number." required="true"/>
                        </div>
                        <div class="mb-3">
                            <x-forms.input label="Phone 2" name="phone2" value="{{ old('phone2', $warehouse->phone2) }}" message="Please provide a second valid phone number." required="true"/>
                        </div>
                       <div class="mb-3">
                            <x-forms.inputgroup label="Email address" name="email" value="{{ old('email', $warehouse->email) }}" message="Please provide a valid email address." required="true" position="before" symbol="@"/>
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