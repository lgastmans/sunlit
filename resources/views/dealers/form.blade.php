@extends('layouts.app')

@section('page-title', 'Dealers')

@section('content')

<div class="row">
    <div class="col-9">
        <div class="card">
            <div class="card-body">
                <x-forms.errors class="mb-4" :errors="$errors" />
                    <form action="@if ($dealer->id) {{ route('dealers.update', $dealer->id) }} @else {{ route('dealers.store') }} @endif" method="POST" class="needs-validation" novalidate>
                        @csrf()
                        @if ($dealer->id)
                            @method('PUT')
                                <input type="hidden" name="id" value="{{ old('id', $dealer->id) }}" />
                        @endif

                        <div class="mb-3 row">
                            <div class="col-xl-5">
                                <x-forms.input label="company" name="company" value="{{ old('company', $dealer->company) }}" required="true"/>
                            </div>
                            <div class="col-xl-3">
                                <x-forms.input label="GSTIN" name="gstin" value="{{ old('gstin', $dealer->gstin) }}" required="true"/>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <div class="col-xl-5">
                                <x-forms.input label="contact person" name="contact_person" value="{{ old('contact_person', $dealer->contact_person) }}" required="true"/>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <div class="col-xl-12">
                                <x-forms.input label="address" name="address" value="{{ old('address', $dealer->address) }}" required="true"/>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <div class="col-xl-12">
                                <x-forms.input label="address 2" name="address2" value="{{ old('address2', $dealer->address2) }}" required="false"/>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <div class="col-xl-3">
                                <label class="form-label" for="state-select">State</label>
                                <select class="state-select form-control" name="state_id">
                                    @if ($dealer->state)
                                        <option value="{{$dealer->state->id}}" selected="selected">{{$dealer->state->name}}</option>
                                    @endif
                                </select>
                                <div class="invalid-feedback">
                                    {{ __('error.form_invalid_field', ['field' => 'state' ]) }}
                                </div>
                            </div>
                            <div class="col-xl-3">
                                <x-forms.input label="city" name="city" value="{{ old('city', $dealer->city) }}" required="true"/>
                            </div>
                            <div class="col-xl-2">
                                <x-forms.input label="zip code" name="zip_code" value="{{ old('zip_code', $dealer->zip_code) }}" required="true"/>
                            </div>
                            
                        </div>
                        <div class="mb-3 row">
                            <div class="col-xl-2">
                                <x-forms.input label="phone" name="phone" value="{{ old('phone', $dealer->phone) }}" required="true"/>
                            </div>
                            <div class="col-xl-2">
                                <x-forms.input label="phone 2" name="phone2" value="{{ old('phone2', $dealer->phone2) }}"  required="true"/>
                            </div>
                           <div class="col-xl-4">
                                <x-forms.inputGroup label="email address" name="email" value="{{ old('email', $dealer->email) }}"  required="true" position="before" symbol="@"/>
                            </div>
                        </div>
                        
                        
                       
                        
                       
                        <button class="btn btn-primary" type="submit">@if ($dealer->id) {{ __('app.edit_title', ['field' => 'dealer']) }} @else {{ __('app.add_title', ['field' => 'dealer']) }} @endif</button>

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