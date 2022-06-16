<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-3">Dealer Information</h4>
                <h5>{{ $order->dealer->company }}</h5>
                <h6>{{ $order->dealer->contact_person }}</h6>
                <address class="mb-0 font-14 address-lg">
                    <abbr title="Phone">P:</abbr> {{ $order->dealer->phone }} <br />
                    <abbr title="Mobile">M:</abbr> {{ $order->dealer->phone2 }} <br />
                    <abbr title="Mobile">@:</abbr> {{ $order->dealer->email }}
                </address>
            </div>
        </div>
    </div> <!-- end col -->

    @if ($order->status >= 2 && $order->status <= 3)
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Shipping Address</h4>

                    <div class="input-group input-group-sm mb-3">
                      <span class="input-group-text">Company & GSTIN</span>
                      <input type="text" aria-label="Company" class="form-control" value="{{$order->shipping_company}}" id="shipping_company">
                      <input type="text" aria-label="GSTIN" class="form-control" value="{{$order->shipping_gstin}}" id="shipping_gstin">
                    </div>

                    <div class="input-group input-group-sm mb-3">
                      <span class="input-group-text">Contact & Phone</span>
                      <input type="text" class="form-control" aria-label="Contact" value="{{$order->shipping_contact_person}}" id="shipping_contact">
                      <input type="text" class="form-control" aria-label="Contact" value="{{$order->shipping_phone}}" id="shipping_phone">
                    </div>

                    <div class="input-group input-group-sm mb-3">
                      <span class="input-group-text" id="inputGroup-address1">Address</span>
                      <input type="text" class="form-control" aria-label="Address" aria-describedby="inputGroup-address1" value="{{$order->shipping_address}}" id="shipping_address">
                    </div>

                    <div class="input-group input-group-sm mb-3">
                      <span class="input-group-text" id="inputGroup-address2">Address</span>
                      <input type="text" class="form-control" aria-label="Address" aria-describedby="inputGroup-address2" value="{{$order->shipping_address2}}" id="shipping_address2">
                    </div>

                    <div class="input-group input-group-sm mb-3">
                      <span class="input-group-text">City & ZIP</span>
                      <input type="text" aria-label="City" class="form-control" value="{{$order->shipping_city}}" id="shipping_city">
                      <input type="text" aria-label="ZIP" class="form-control" value="{{$order->shipping_zip_code}}" id="shipping_zip_code">
                    </div>

                    <div class="input-group input-group-sm mb-3">
                      {{-- <span class="input-group-text" id="inputGroup-state">State</span> --}}

                                <select class="shipping-state-select form-select form-select-sm" name="shipping_state_id">
                                    @if ($order->state)
                                        <option value="{{$order->state->id}}" selected="selected">{{$order->state->name}}</option>
                                    @endif
                                </select>

                      {{-- <input type="text" class="form-control" aria-label="State" aria-describedby="inputGroup-state" value="{{$order->shipping_state_id}}" id="shipping_state_id"> --}}
                    </div>

                </div>
            </div>
        </div> <!-- end col -->
    @endif

{{--     <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-3">Warehouse Information</h4>
                <h5>{{ $order->warehouse->name }}</h5>
                <h6>{{ $order->warehouse->contact_person }}</h6>
                <address class="mb-0 font-14 address-lg">
                    <abbr title="Phone">P:</abbr> {{ $order->warehouse->phone }} <br />
                    <abbr title="Mobile">M:</abbr> {{ $order->warehouse->phone2 }} <br />
                    <abbr title="Mobile">@:</abbr> {{ $order->warehouse->email }}
                </address>
            </div>
        </div>
    </div> <!-- end col -->
 --}}
    {{-- <div class="col-lg-4 d-none">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-3">Payment Information</h4>
                <h5>{{ $order->warehouse->name }}</h5>
                <h6>{{ $order->warehouse->contact_person }}</h6>
                <address class="mb-0 font-14 address-lg">
                    <abbr title="Phone">P:</abbr> {{ $order->warehouse->phone }} <br />
                    <abbr title="Mobile">M:</abbr> {{ $order->warehouse->phone2 }} <br />
                    <abbr title="Mobile">@:</abbr> {{ $order->warehouse->email }}
                </address>
            </div>
        </div>
    </div> <!-- end col --> --}}

    <div class="col-lg-4  @if (!$order->shipped_at) d-none @endif">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-3">Delivery Info</h4>

                <div class="text-center">
                    <i class="mdi mdi-truck-fast h2 text-muted"></i>
                    <h5><b>{{ $order->courier }}</b></h5>
                    <p class="mb-1"><b>Tracking # :</b> {{ $order->tracking_number }}</p>
                    <p class="mb-0"><b>ETA :</b> {{ $order->display_due_at }}</p>
                </div>
            </div>
        </div>
    </div> <!-- end col -->
</div>