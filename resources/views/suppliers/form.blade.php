@extends('layout.app')

@section('page-title', 'Suppliers')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <h4>Add a supplier</h4>
                </div>
                    <form action="{{ route('suppliers.store') }}" method="POST" class="needs-validation" novalidate>
                        @csrf()
                        <div class="mb-3">
                            <x-forms.input label="Company" name="company_title" value="{{ old('company_title', $supplier->company_title) }}" message="Please provide the company name" required="true"/>
                        </div>
                        <div class="mb-3">
                            <x-forms.input label="Contact person" name="contact_person" value="{{ old('contact_person', $supplier->contact_person) }}" message="Please provide the full name of the contact person" required="true"/>
                        </div>
                        <div class="mb-3">
                            <x-forms.input label="Address" name="address" value="{{ old('address', $supplier->address) }}" message="Please provide the full address" required="true"/>
                        </div>
                        <div class="mb-3">
                            <x-forms.input label="City" name="city" value="{{ old('city', $supplier->city) }}" message="Please provide a valid city" required="true"/>
                        </div>
                        <div class="mb-3">
                            <x-forms.input label="State" name="state" value="" message="Please provide a valid state." required="true"/>
                        </div>
                        <div class="mb-3">
                            <x-forms.input label="Zip code" name="zip_code" value="{{ old('zip_code', $supplier->zip_code) }}" message="Please provide a valid zip code." required="true"/>
                        </div>
                        <div class="mb-3">
                            <x-forms.input label="District" name="district" value="{{ old('district', $supplier->district) }}" message="Please provide a valid district." required="true"/>
                        </div>
                        <div class="mb-3">
                            <x-forms.input label="Phone" name="phone" value="{{ old('phone', $supplier->phone) }}" message="Please provide a valid phone number." required="true"/>
                        </div>
                        <div class="mb-3">
                            <x-forms.input label="Phone 2" name="phone2" value="{{ old('phone2', $supplier->phone2) }}" message="Please provide a second valid phone number." required="true"/>
                        </div>
                       <div class="mb-3">
                            <x-forms.inputgroup label="Email address" name="email" value="{{ old('email', $supplier->email) }}" message="Please provide a valid email address." required="true" position="before" symbol="@"/>
                        </div>
                       
                        <button class="btn btn-primary" type="submit">Create supplier</button>

                    </form>
                </div>
            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col -->
</div>


@endsection

@section('page-scripts')
    <script src="{{ asset("assets/js/pages/suppliers.js") }}"></script>    
@endsection