@extends('layouts.app')

@section('title')
    @parent() | Create a Credit Note
@endsection

@section('page-title', 'Create a Credit Note')

@section('content')

<div class="row">
    <div class="col-6">
        <div class="card">
            <div class="card-body">
                <div class="row mb-2">
                    <p>{{ __('app.create_title', ['field' => 'credit note']) }} </p>
                </div>
                <x-forms.errors class="mb-4" :errors="$errors" />
                    <form action="{{ route('credit-notes.store') }}" method="POST" class="needs-validation" novalidate>
                        @csrf()

                        <div class="mb-3">
                            <label class="form-label" for="credit_note_number">Credit Note #</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="credit_note_number" id="credit_note_number" placeholder="" value="{{ $credit_note_number }}" required>

                                <div class="invalid-feedback">
                                    {{ __('error.form_invalid_field', ['field' => strtolower('credit note #') ]) }}
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="row mb-0">
                                <div class="col-1">
                                    <input type="hidden" name="is_against_invoice" id="is_against_invoice" value="1">
                                    <input type="checkbox" id="switch-against-invoice" data-switch="warning" checked/>
                                    <label for="switch-against-invoice" data-on-label="Yes" data-off-label="No"></label>
                                </div>
                                <div class="col-11">
                                    Is Credit Note against Invoice
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="invoice_number" id="invoice_number" value="">

                        <div class="mb-3" id="invoice-details">
                            <div class="row">
                                <div class="col-6 mb-0">
                                    <label class="form-label" for="invoice-select">Invoice Number</label>
                                    <select class="invoice-select form-control"></select>
                                    <div class="invalid-feedback">
                                        {{ __('error.form_invalid_field', ['field' => 'invoice' ]) }}
                                    </div>
                                </div>
                                <div class="col-6 mb-0">
                                    <label class="form-label" for="invoice_date">Invoice Date</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="invoice_date" id="credit_note_invoice_date" value="" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="dealer_id" id="dealer_id" value="">

                        <div class="mb-3" style="display:none" id="dealer-details">
                            <div class="row">
                                <div class="col-11">
                                    <label class="form-label" for="dealer-select">Dealer</label>
                                    <select class="dealer-select form-control"></select>
                                    <div class="invalid-feedback">
                                        {{ __('error.form_invalid_field', ['field' => 'dealer' ]) }}
                                    </div>
                                </div>
                                <div class="col-1">
                                    <label class="form-label" for="dealer-add"></label><br>
                                    <a href="#" id="btn-add-dealer" class="dealer-add">
                                        <i class="mdi mdi-36px mdi-account-multiple-plus"></i>
                                        <span></span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3" id="dealer-address">
                            <div class="row">
                                <div class="col-11">
                                    <label class="form-label" for="dealer-select">Dealer</label>
                                    <p class="mb-0" id="dealer-company"></p>
                                    <p class="mb-0" id="dealer-address1"></p>
                                    <p class="mb-0" id="dealer-address2"></p>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="warehouse_id" id="warehouse_id" value="">

                        <div class="mb-3" style="display:none" id="warehouse-details">
                            <label class="form-label" for="warehouse-select">Warehouse</label>
                            <select class="warehouse-select form-control"></select>
                            <div class="invalid-feedback">
                                {{ __('error.form_invalid_field', ['field' => 'warehouse' ]) }}
                            </div>
                        </div>
                    
                        <button class="btn btn-primary" type="submit">{{ __('app.create_title', ['field' => 'credit note']) }}</button>
                    </form>
                </div>
            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col -->
</div>



<!-- 

    Modal to add a Dealer
    
-->
<div class="modal fade" id="dealerModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Add dealer details</h5>
      </div>
      <div class="modal-body">
        
        <form>
            @csrf()
            <div class="mb-3 row">
                <div class="col-xl-6">
                    <x-forms.input label="company" name="company" id="company" value="" required="true"/>
                </div>
                <div class="col-xl-6">
                    <x-forms.input label="GSTIN" name="gstin" id="gstin" value="" required="true"/>
                </div>
            </div>
            <div class="mb-3 row">
                <div class="col-xl-6">
                    <x-forms.input label="contact person" name="contact_person" id="contact_person" value="" required="true"/>
                </div>
                <div class="col-xl-6">
                    <x-forms.input label="phone" name="phone" id="phone" value="" required="true"/>
                </div>
            </div>
            <div class="mb-3 row">
                <div class="col-xl-12">
                    <x-forms.input label="address" name="address" id="address" value="" required="true"/>
                </div>
            </div>
            <div class="mb-3 row">
                <div class="col-xl-12">
                    <label class="form-label" for="state-select">State</label>
                    <select class="state-select form-control" name="state_id" id="state_id">
                    </select>
                    <div class="invalid-feedback">
                        {{ __('error.form_invalid_field', ['field' => 'state' ]) }}
                    </div>
                </div>
            </div>
            <div class="mb-3 row">
                <div class="col-xl-8">
                    <x-forms.input label="city" name="city" id="city" value="" required="true"/>
                </div>
                <div class="col-xl-4">
                    <x-forms.input label="zip code" name="zip_code" id="zip_code" value="" required="true"/>
                </div>
            </div>
        </form>

      </div>
      <div class="modal-footer">
        <button type="button" id="btn-modal-close" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" id="btn-modal-save" class="btn btn-primary">Save</button>
      </div>
    </div>
  </div>
</div>



@endsection

@section('page-scripts')

    <script>

        var invoiceSelect = $(".invoice-select").select2();
        invoiceSelect.select2({
            ajax: {
                url: '{{route('ajax.invoices')}}',
                dataType: 'json'
            }
        });
        invoiceSelect.on('select2:select', function (e) {
            var data = e.params.data;
            console.log(data.id, data.company, data);
            $("#invoice_number").val(data.text);
            $("#credit_note_invoice_date").val(data.invoice_date);
            $("#dealer-company").text(data.company);
            $("#dealer-address1").text(data.address);
            $("#dealer-address2").text(data.address2);
            $("#dealer_id").val(data.dealer_id);
            $("#warehouse_id").val(data.warehouse_id);
        });        


        var dealerSelect = $(".dealer-select").select2();
        dealerSelect.select2({
            ajax: {
                url: '{{route('ajax.dealers')}}',
                dataType: 'json'
            }
        });
        dealerSelect.on('select2:select', function (e) {
            var data = e.params.data;
            $("#dealer_id").val(data.id);
        });


        var warehouseSelect = $(".warehouse-select").select2();
        warehouseSelect.select2({
            ajax: {
                url: '{{route('ajax.warehouses')}}',
                dataType: 'json'
            }
        });
        warehouseSelect.on('select2:select', function (e) {
            var data = e.params.data;
            $("#warehouse_id").val(data.id);
        });


        var stateSelect = $(".state-select").select2();
        stateSelect.select2({
            dropdownParent: $('#dealerModal'),
            ajax: {
                url: '{{route('ajax.states')}}',
                dataType: 'json'
            }
        });        


        $("#switch-against-invoice").on("click", function(event) {
            if($("#switch-against-invoice:checked").val()) {
                //console.log('yes')
                $("#is_against_invoice").val("1");

                $("#invoice-details").show();
                $("#dealer-details").hide();
                $("#warehouse-details").hide();
                $("#dealer-address").show();
            }
            else {
                //console.log('no')
                $("#is_against_invoice").val("0");

                $("#invoice-details").hide();
                $("#dealer-details").show();
                $("#warehouse-details").show();
                $("#dealer-address").hide();
            }
            $("#invoice_number").val('');
            $("#credit_note_invoice_date").val('');
            $("#dealer_id").val('');
            $("#warehouse_id").val('');
        })
        // $("#dealer-details").hide();

        $("#btn-modal-save").on("click", function(event) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                method: "POST",
                url: "{{ route('dealers.store') }}",
                data: {
                    company: $("#company").val(), 
                    gstin: $("#gstin").val(),
                    contact_person: $("#contact_person").val(), 
                    phone: $("#phone").val(), 
                    address: $("#address").val(), 
                    state_id: $("#state_id").val(),
                    city: $("#city").val(), 
                    zip_code: $("#zip_code").val()
                }
            }).done(function (msg) {
                
                console.log('msg',msg)

                $("#company").val('');
                $("#gstin").val('');
                $("#contact_person").val('');
                $("#phone").val('');
                $("#address").val('');
                $("#state_id").val('');
                $("#city").val('');
                $("#zip_code").val('');

                //$(".dealer-select").val($("#company").val()).attr("selected", true).trigger('change');
                $(".dealer-select").trigger('change');
                
                $('#dealerModal').modal("hide");

            });            
        })

        $("#btn-modal-close").on("click", function(event) {
            event.preventDefault();
            $('#dealerModal').modal("hide");
        });

        $("#btn-add-dealer").on("click", function(event) {
            event.preventDefault();
            $('#dealerModal').modal("show");
            $(".state-select").val("32").trigger("change");
        });

    </script>


@endsection