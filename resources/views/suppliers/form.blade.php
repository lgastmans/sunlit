@extends('layouts.app')

@section('page-title', 'Suppliers')

@section('content')

<div class="row">
    <div class="col-6">
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <p>@if ($supplier->id) {{ __('app.edit_title', ['field' => 'supplier']) }}: <span class="text-primary">{{ $supplier->name }}</span> @else {{ __('app.add_title', ['field' => 'supplier']) }} @endif </p>
                </div>
                <x-forms.errors class="mb-4" :errors="$errors" />
                    <form action="@if ($supplier->id) {{ route('suppliers.update', $supplier->id) }} @else {{ route('suppliers.store') }} @endif" method="POST" class="needs-validation" novalidate>
                        @csrf()
                        @if ($supplier->id)
                            @method('PUT')
                                <input type="hidden" name="id" value="{{ old('id', $supplier->id) }}" />
                        @endif
                        <div class="mb-3">
                            <x-forms.input label="Company" name="company" value="{{ old('company', $supplier->company) }}" message="Please provide the company name" required="true"/>
                        </div>
                        <div class="mb-3">
                            <x-forms.input label="GSTIN" name="gstin" value="{{ old('gstin', $supplier->gstin) }}" message="Please provide the gstin" required="true"/>
                        </div>
                        <div class="mb-3">
                            <x-forms.input label="Contact person" name="contact_person" value="{{ old('contact_person', $supplier->contact_person) }}" message="Please provide the full name of the contact person" required="true"/>
                        </div>
                        <div class="mb-3">
                            <x-forms.input label="Address" name="address" value="{{ old('address', $supplier->address) }}" message="Please provide the full address" required="true"/>
                        </div>
                        <div class="mb-3">
                            <x-forms.input label="Address 2" name="address2" value="{{ old('address2', $supplier->address2) }}" message="Please provide the full address" required="false"/>
                        </div>
                        <div class="mb-3">
                            <x-forms.input label="City" name="city" value="{{ old('city', $supplier->city) }}" message="Please provide a valid city" required="true"/>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="state-select">State</label>
                            <select class="state-select form-control" name="state_id">
                                @if ($supplier->state)
                                    <option value="{{$supplier->state->id}}" selected="selected">{{$supplier->state->name}}</option>
                                @endif
                            </select>
                            <div class="invalid-feedback">
                                Please provide a valid state.
                            </div>
                        </div>
                        <div class="mb-3">
                            <x-forms.input label="Zip code" name="zip_code" value="{{ old('zip_code', $supplier->zip_code) }}" message="Please provide a valid zip code." required="true"/>
                        </div>
                        <div class="mb-3">
                            <x-forms.input label="Phone" name="phone" value="{{ old('phone', $supplier->phone) }}" message="Please provide a valid phone number." required="true"/>
                        </div>
                        <div class="mb-3">
                            <x-forms.input label="Phone 2" name="phone2" value="{{ old('phone2', $supplier->phone2) }}" message="Please provide a second valid phone number." required="true"/>
                        </div>
                       <div class="mb-3">
                            <x-forms.inputGroup label="Email address" name="email" value="{{ old('email', $supplier->email) }}" message="Please provide a valid email address." required="true" position="before" symbol="@"/>
                        </div>
                       
                        <button class="btn btn-primary" type="submit">@if ($supplier->id) {{ __('app.edit_title', ['field' => 'supplier']) }} @else {{ __('app.add_title', ['field' => 'supplier']) }} @endif</button>

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