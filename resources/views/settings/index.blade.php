@extends('layouts.app')

@section('title')
    @parent() | Settings
@endsection

@section('page-title', 'Global settings')

@section('content')

<div class="row col-12">
    <form action="{{ route('settings.update') }}" method="POST" class="row">
        @csrf()
        @method('PUT')
        <div class="col-lg-6 ">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <h4 class="mb-4">Company</h4>
                                <div class="mb-3 row">
                                    <div class="mt-sm-3 mt-xl-0 col-xl-8">
                                        <label class="form-label" for="name">Name</label>
                                        <input type="text" class="form-control" name="company__name" id="name" placeholder="" value="{{ $settings['company']['name'] }}" required @if (Auth::user()->cannot('edit settings')) disabled @endif>
                                    </div>
                                    <div class="mt-sm-3 mt-xl-0 col-xl-4">
                                        <label class="form-label" for="gstin">GSTIN</label>
                                        <input type="text" class="form-control" name="company__gstin" id="gstin" placeholder="" value="{{ $settings['company']['gstin'] }}" required @if (Auth::user()->cannot('edit settings')) disabled @endif>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <div class="mt-sm-3 mt-xl-0 col-xl-6">
                                        <label class="form-label" for="city">City</label>
                                        <input type="text" class="form-control" name="company__city" id="city" placeholder="" value="{{ $settings['company']['city'] }}" required @if (Auth::user()->cannot('edit settings')) disabled @endif>
                                    </div>
                                    <div class="mt-sm-3 mt-xl-0 col-xl-3">
                                        <label class="form-label" for="zipcode">Zip Code</label>
                                        <input type="text" class="form-control" name="company__zipcode" id="zipcode" placeholder="" value="{{ $settings['company']['zipcode'] }}" required @if (Auth::user()->cannot('edit settings')) disabled @endif>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <div class="mt-sm-3 mt-xl-0 col-xl-6">
                                        <label class="form-label" for="state">State</label>
                                        <input type="text" class="form-control" name="company__state" id="state" placeholder="" value="{{ $settings['company']['state'] }}" required @if (Auth::user()->cannot('edit settings')) disabled @endif>
                                    </div>
                                    <div class="mt-sm-3 mt-xl-0 col-xl-6">
                                        <label class="form-label" for="country">Country</label>
                                        <input type="text" class="form-control" name="company__country" id="country" placeholder="" value="{{ $settings['company']['country'] }}" required @if (Auth::user()->cannot('edit settings')) disabled @endif>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <div class="mt-sm-3 mt-xl-0 col-xl-6">
                                        <label class="form-label" for="phone">Phone</label>
                                        <input type="text" class="form-control" name="company__phone" id="phone" placeholder="" value="{{ $settings['company']['phone'] }}" required @if (Auth::user()->cannot('edit settings')) disabled @endif>
                                    </div>
                                    <div class="mt-sm-3 mt-xl-0 col-xl-6">
                                        <label class="form-label" for="email">Email</label>
                                        <input type="text" class="form-control" name="company__email" id="email" placeholder="" value="{{ $settings['company']['email'] }}" required @if (Auth::user()->cannot('edit settings')) disabled @endif>
                                    </div>
                                </div>
                        </div><!-- end col-->
                    </div>
                </div> <!-- end card-body-->
            </div> <!-- end card-->


            <!-- Purchase Order -->

            <div class="card  d-none">
                <div class="card-body">
                    <div class="row ">
                        <div class="col-sm-12">
                            <h4 class="mb-4">Purchase Order</h4>
                                <div class="mb-3 row">
                                    <div class="mt-sm-3 mt-xl-0 col-xl-4">
                                        <label class="form-label" for="igst">IGST</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="purchase_order__igst" id="igst" placeholder="" value="{{ $settings['purchase_order']['igst'] }}" required @if (Auth::user()->cannot('edit settings')) disabled @endif>
                                            <span class="input-group-text" id="inputGroupPrepend">%</span>
                                        </div>

                                    </div>
                                    <div class="mt-sm-3 mt-xl-0 col-xl-4">
                                        <label class="form-label" for="transport">Transport</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="purchase_order__transport" id="transport" placeholder="" value="{{ $settings['purchase_order']['transport'] }}" required @if (Auth::user()->cannot('edit settings')) disabled @endif>
                                            <span class="input-group-text" id="inputGroupPrepend">%</span>
                                        </div>
                                    </div>
                                    <div class="mt-sm-3 mt-xl-0 col-xl-4">
                                        <label class="form-label" for="customs_duty">Customs Duty</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="purchase_order__customs_duty" id="customs_duty" placeholder="" value="{{ $settings['purchase_order']['customs_duty'] }}" required @if (Auth::user()->cannot('edit settings')) disabled @endif>
                                            <span class="input-group-text" id="inputGroupPrepend">%</span>
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="mb-3 row">
                                    <div class="mt-sm-3 mt-xl-0 col-xl-6">
                                        <label class="form-label" for="social_welfare_surcharge">Social Welfare Surcharge</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="purchase_order__social_welfare_surcharge" id="social_welfare_surcharge" placeholder="" value="{{ $settings['purchase_order']['social_welfare_surcharge'] }}" required @if (Auth::user()->cannot('edit settings')) disabled @endif>
                                            <span class="input-group-text" id="inputGroupPrepend">%</span>
                                        </div>
                                    </div>
                                    <div class="mt-sm-3 mt-xl-0 col-xl-6">
                                        <label class="form-label" for="exchange_rate">Exchange Rate</label>
                                        <div class="input-group">
                                            <span class="input-group-text" id="inputGroupPrepend">{{ __('app.currency_symbol_inr')}}</span>
                                        <input type="text" class="form-control" name="purchase_order__exchange_rate" id="exchange_rate" placeholder="" value="{{ $settings['purchase_order']['exchange_rate'] }}" required @if (Auth::user()->cannot('edit settings')) disabled @endif>
                                    </div>
                                    </div>
                                </div>
                        </div><!-- end col-->
                    </div>
                </div> <!-- end card-body-->
            </div> <!-- end card-->

            <div class="card">
                <div class="card-body">
                    <div class="row ">
                        <div class="col-sm-12">
                            <h4 class="mb-4">Quotations</h4>
                                <div class="mb-3 row">
                                    <div class="mt-sm-3 mt-xl-0 col-xl-4">
                                        <label class="form-label" for="qt_prefix">Quotation Prefix</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="quotation__prefix" id="qt_prefix" placeholder="" value="{{ $settings['quotation']['prefix'] }}" required @if (Auth::user()->cannot('edit settings')) disabled @endif>
                                        </div>
                                    </div>
                                    <div class="mt-sm-3 mt-xl-0 col-xl-4">
                                        <label class="form-label" for="qt_suffix">Quotation Suffix</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="quotation__suffix" id="qt_suffix" placeholder="" value="{{ $settings['quotation']['suffix'] }}" required @if (Auth::user()->cannot('edit settings')) disabled @endif>
                                        </div>
                                    </div>
                                    <div class="mt-sm-3 mt-xl-0 col-xl-4">
                                        <label class="form-label" for="qt_quotation_number">Quotation Number</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="quotation__quotation_number" id="qt_quotation_number" placeholder="" value="{{ $settings['quotation']['quotation_number'] }}" required @if (Auth::user()->cannot('edit settings')) disabled @endif>
                                        </div>
                                    </div>
                                   <br>&nbsp;
                                    <div class="mb-3">
                                        <label for="qt_terms" class="form-label">Payment & Delivery Terms</label>
                                        <textarea class="form-control" name="quotation__terms" id="qt_terms">{!! $settings['quotation']['terms'] !!}</textarea>
                                    </div>                                    
                                </div>
                        </div><!-- end col-->
                    </div>
                </div> <!-- end card-body-->
            </div> <!-- end card-->

            <div class="card">
                <div class="card-body">
                    <div class="row ">
                        <div class="col-sm-12">
                            <h4 class="mb-4">Credit Notes</h4>
                                <div class="mb-3 row">
                                    <div class="mt-sm-3 mt-xl-0 col-xl-4">
                                        <label class="form-label" for="cn_prefix">Credit Note Prefix</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="credit_note__prefix" id="cn_prefix" placeholder="" value="{{ $settings['credit_note']['prefix'] }}" required @if (Auth::user()->cannot('edit settings')) disabled @endif>
                                        </div>
                                    </div>
                                    <div class="mt-sm-3 mt-xl-0 col-xl-4">
                                        <label class="form-label" for="cn_suffix">Credit Note Suffix</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="credit_note__suffix" id="cn_suffix" placeholder="" value="{{ $settings['credit_note']['suffix'] }}" required @if (Auth::user()->cannot('edit settings')) disabled @endif>
                                        </div>
                                    </div>
                                    <div class="mt-sm-3 mt-xl-0 col-xl-4">
                                        <label class="form-label" for="cn_credit_note_number">Credit Note Number</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="credit_note__credit_note_number" id="cn_credit_note_number" placeholder="" value="{{ $settings['credit_note']['credit_note_number'] }}" required @if (Auth::user()->cannot('edit settings')) disabled @endif>
                                        </div>
                                    </div>
                                   <br>&nbsp;
                                    <div class="mb-3">
                                        <label for="cn_remarks" class="form-label">Remarks</label>
                                        <textarea class="form-control" name="credit_note__remarks" id="cn_remarks">{!! $settings['credit_note']['remarks'] !!}</textarea>
                                    </div>                                    
                                </div>
                        </div><!-- end col-->
                    </div>
                </div> <!-- end card-body-->
            </div> <!-- end card-->

            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <h4 class="mb-4">UI Settings</h4>
                                <div class="mb-3 row">
                                    <div class="mt-sm-3 mt-xl-0 col-xl-3">
                                        <label class="form-label" for="grid_rows">Grid rows</label>
                                        <input type="text" class="form-control" name="general__grid_rows" id="grid_rows" placeholder="" value="{{ $settings['general']['grid_rows'] }}" required @if (Auth::user()->cannot('edit settings')) disabled @endif>
                                    </div>
                                </div>
                        </div><!-- end col-->
                    </div>
                </div> <!-- end card-body-->
            </div> <!-- end card-->

        </div>
        <div class="col-lg-6">

            <!-- Purchase Order -->

            <div class="card d-none">
                <div class="card-body">
                    <div class="row ">
                        <div class="col-sm-12">
                            <h4 class="mb-4">Purchase Order</h4>
                                <div class="mb-3 row">
                                    <div class="mt-sm-3 mt-xl-0 col-xl-4">
                                        <label class="form-label" for="igst">IGST</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="purchase_order__igst" id="igst" placeholder="" value="{{ $settings['purchase_order']['igst'] }}" required @if (Auth::user()->cannot('edit settings')) disabled @endif>
                                            <span class="input-group-text" id="inputGroupPrepend">%</span>
                                        </div>

                                    </div>
                                    <div class="mt-sm-3 mt-xl-0 col-xl-4">
                                        <label class="form-label" for="transport">Transport</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="purchase_order__transport" id="transport" placeholder="" value="{{ $settings['purchase_order']['transport'] }}" required @if (Auth::user()->cannot('edit settings')) disabled @endif>
                                            <span class="input-group-text" id="inputGroupPrepend">%</span>
                                        </div>
                                    </div>
                                    <div class="mt-sm-3 mt-xl-0 col-xl-4">
                                        <label class="form-label" for="customs_duty">Customs Duty</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="purchase_order__customs_duty" id="customs_duty" placeholder="" value="{{ $settings['purchase_order']['customs_duty'] }}" required @if (Auth::user()->cannot('edit settings')) disabled @endif>
                                            <span class="input-group-text" id="inputGroupPrepend">%</span>
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="mb-3 row">
                                    <div class="mt-sm-3 mt-xl-0 col-xl-6">
                                        <label class="form-label" for="social_welfare_surcharge">Social Welfare Surcharge</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="purchase_order__social_welfare_surcharge" id="social_welfare_surcharge" placeholder="" value="{{ $settings['purchase_order']['social_welfare_surcharge'] }}" required @if (Auth::user()->cannot('edit settings')) disabled @endif>
                                            <span class="input-group-text" id="inputGroupPrepend">%</span>
                                        </div>
                                    </div>
                                    <div class="mt-sm-3 mt-xl-0 col-xl-6">
                                        <label class="form-label" for="exchange_rate">Exchange Rate</label>
                                        <div class="input-group">
                                            <span class="input-group-text" id="inputGroupPrepend">{{ __('app.currency_symbol_inr')}}</span>
                                        <input type="text" class="form-control" name="purchase_order__exchange_rate" id="exchange_rate" placeholder="" value="{{ $settings['purchase_order']['exchange_rate'] }}" required @if (Auth::user()->cannot('edit settings')) disabled @endif>
                                    </div>
                                    </div>
                                </div>
                        </div><!-- end col-->
                    </div>
                </div> <!-- end card-body-->
            </div> <!-- end card-->

            <div class="card">
                <div class="card-body">
                    <div class="row ">
                        <div class="col-sm-12">
                            <h4 class="mb-4">Purchase Orders</h4>
                                <div class="mb-3 row">
                                    <div class="mt-sm-3 mt-xl-0 col-xl-4">
                                        <label class="form-label" for="po_prefix">PO Prefix</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="purchase_order__prefix" id="po_prefix" placeholder="" value="{{ $settings['purchase_order']['prefix'] }}" required @if (Auth::user()->cannot('edit settings')) disabled @endif>
                                        </div>
                                    </div>
                                    <div class="mt-sm-3 mt-xl-0 col-xl-4">
                                        <label class="form-label" for="po_suffix">PO Suffix</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="purchase_order__suffix" id="po_suffix" placeholder="" value="{{ $settings['purchase_order']['suffix'] }}" required @if (Auth::user()->cannot('edit settings')) disabled @endif>
                                        </div>
                                    </div>
                                    <div class="mt-sm-3 mt-xl-0 col-xl-4">
                                        <label class="form-label" for="po_order_number">PO Number</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="purchase_order__order_number" id="po_order_number" placeholder="" value="{{ $settings['purchase_order']['order_number'] }}" required @if (Auth::user()->cannot('edit settings')) disabled @endif>
                                        </div>
                                    </div>
                                <div class="mb-3 row">
                                </div>
                                    <div class="mb-3">
                                        <label for="poi_terms" class="form-label">Payment & Delivery Terms</label>
                                        <textarea class="form-control" name="purchase_order__terms" id="poi_terms">{!! $settings['purchase_order']['terms'] !!}</textarea>
                                    </div>                                    
                                </div>
                        </div><!-- end col-->
                    </div>
                </div> <!-- end card-body-->
            </div> <!-- end card-->


            <div class="card">
                <div class="card-body">
                    <div class="row ">
                        <div class="col-sm-12">
                            <h4 class="mb-4">Proforma Invoices</h4>
                                <div class="mb-3 row">
                                    <div class="mt-sm-3 mt-xl-0 col-xl-4">
                                        <label class="form-label" for="so_prefix">PI Prefix</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="sale_order__prefix" id="so_prefix" placeholder="" value="{{ $settings['sale_order']['prefix'] }}" required @if (Auth::user()->cannot('edit settings')) disabled @endif>
                                        </div>
                                    </div>
                                    <div class="mt-sm-3 mt-xl-0 col-xl-4">
                                        <label class="form-label" for="so_suffix">PI Suffix</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="sale_order__suffix" id="so_suffix" placeholder="" value="{{ $settings['sale_order']['suffix'] }}" required @if (Auth::user()->cannot('edit settings')) disabled @endif>
                                        </div>
                                    </div>
                                    <div class="mt-sm-3 mt-xl-0 col-xl-4">
                                        <label class="form-label" for="so_order_number">PI Number</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="sale_order__order_number" id="so_order_number" placeholder="" value="{{ $settings['sale_order']['order_number'] }}" required @if (Auth::user()->cannot('edit settings')) disabled @endif>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <div class="mt-sm-3 mt-xl-0 col-xl-4">
                                        <label class="form-label" for="so_order_tcs">TCS %</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="sale_order__tcs" id="so_order_tcs" placeholder="" value="{{ $settings['sale_order']['tcs'] }}" required @if (Auth::user()->cannot('edit settings')) disabled @endif>
                                        </div>
                                    </div>
                                    <div class="mt-sm-3 mt-xl-0 col-xl-4">
                                        <label class="form-label" for="so_order_tcs_text">TCS Caption</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="sale_order__tcs_text" id="so_order_tcs_text" placeholder="" value="{{ $settings['sale_order']['tcs_text'] }}" required @if (Auth::user()->cannot('edit settings')) disabled @endif>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <div class="mb-3">
                                        <label for="so_terms" class="form-label">Payment & Delivery Terms</label>
                                        <textarea class="form-control" name="sale_order__terms" id="so_terms">{!! $settings['sale_order']['terms'] !!}</textarea>
                                    </div>
                                </div>
                        </div><!-- end col-->
                    </div>
                </div> <!-- end card-body-->
            </div> <!-- end card-->



        </div>
        {{-- @foreach ($settings as $group=>$gsettings)
            <div class="col-xl-6">
                
            </div> <!-- end col -->
        @endforeach --}}
        <div class="row">
            <div class="col-lg-4 col-xl-3">
                <button class="btn btn-primary" type="submit">Save settings</button>
            </div>
        </div>
    </form>
</div>



@endsection

@section('page-scripts')
    <script>
        
 $(document).ready(function () {
    "use strict";

    ClassicEditor
        .create( document.querySelector( '#poi_terms' ) )
        .catch( error => {
            console.error( error );
        });

    ClassicEditor
        .create( document.querySelector( '#so_terms' ) )
        .catch( error => {
            console.error( error );
        });

    ClassicEditor
        .create( document.querySelector( '#qt_terms' ) )
        .catch( error => {
            console.error( error );
        });

    ClassicEditor
        .create( document.querySelector( '#cn_remarks' ) )
        .catch( error => {
            console.error( error );
        });

    @if(Session::has('success'))
        $.NotificationApp.send("Success","{{ session('success') }}","top-right","","success")
    @endif
    @if(Session::has('error'))
        $.NotificationApp.send("Error","{{ session('error') }}","top-right","","error")
    @endif

});

    </script>    
@endsection
