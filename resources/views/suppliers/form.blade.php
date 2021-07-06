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
                            <label class="form-label" for="company_title">Company</label>
                            <input type="text" class="form-control" name="company_title" id="company_title" placeholder="" value="{{ old('company_title', $supplier->company_title) }}" required>
                            <div class="invalid-feedback">
                                Please provide the company name
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="contact_person">Contact person</label>
                            <input type="text" class="form-control" name="contact_person" id="contact_person" placeholder="" value="{{ old('contact_person', $supplier->contact_person) }}" required>
                            <div class="invalid-feedback">
                                Please provide the full name of the contact person
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="city">City</label>
                            <input type="text" class="form-control" name="city" id="city" placeholder="" value="{{ old('city', $supplier->city) }}" required>
                            <div class="invalid-feedback">
                                Please provide a valid city.
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="state">State</label>
                            <input type="text" class="form-control" name="state" id="state" placeholder="" value="{{ old('state', $supplier->state->state) }}" required>
                            <div class="invalid-feedback">
                                Please provide a valid state.
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="zip_code">Zip code</label>
                            <input type="text" class="form-control" name="zip_code" id="zip_code" placeholder="" value="{{ old('zip_code', $supplier->zip) }}" required>
                            <div class="invalid-feedback">
                                Please provide a valid zip code.
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="phone">Phone</label>
                            <input type="text" class="form-control" name="phone" id="phone" placeholder="" value="{{ old('phone', $supplier->phone) }}" required>
                            <div class="invalid-feedback">
                                Please provide a valid phone number.
                            </div>
                        </div>
                       <div class="mb-3">
                            <label class="form-label" for="email">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text" id="inputGroupPrepend">@</span>
                                <input type="text" class="form-control" name="email" id="email" placeholder="" value="{{ old('email', $supplier->email) }}"
                                    aria-describedby="inputGroupPrepend" required>
                                <div class="invalid-feedback">
                                    Please provide a valid email address.
                                </div>
                            </div>
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