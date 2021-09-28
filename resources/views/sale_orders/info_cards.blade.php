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

    {{-- <div class="col-lg-4 d-none">
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
    </div> <!-- end col --> --}}

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