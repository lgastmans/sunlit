<div class="row">
    <div class="col-lg-3">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-3">Supplier Information</h4>
                <h5>{{ $purchase_order->supplier->company }}</h5>
                <h6>{{ $purchase_order->supplier->contact_person }}</h6>
                <address class="mb-0 font-14 address-lg">
                    <abbr title="Phone">P:</abbr> {{ $purchase_order->supplier->phone }} <br />
                    <abbr title="Mobile">M:</abbr> {{ $purchase_order->supplier->phone2 }} <br />
                    <abbr title="Mobile">@:</abbr> {{ $purchase_order->supplier->email }}
                </address>
            </div>
        </div>
    </div> <!-- end col -->

    <div class="col-lg-3">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-3">Warehouse Information</h4>
                <h5>{{ $purchase_order->warehouse->name }}</h5>
                <h6>{{ $purchase_order->warehouse->contact_person }}</h6>
                <address class="mb-0 font-14 address-lg">
                    <abbr title="Phone">P:</abbr> {{ $purchase_order->warehouse->phone }} <br />
                    <abbr title="Mobile">M:</abbr> {{ $purchase_order->warehouse->phone2 }} <br />
                    <abbr title="Mobile">@:</abbr> {{ $purchase_order->warehouse->email }}
                </address>
            </div>
        </div>
    </div> <!-- end col -->

    <div class="col-lg-3 e">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-3">Payment Information</h4>
                <h5>{{ $purchase_order->warehouse->name }}</h5>
                <h6>{{ $purchase_order->warehouse->contact_person }}</h6>
                <address class="mb-0 font-14 address-lg">
                    <abbr title="Phone">P:</abbr> {{ $purchase_order->warehouse->phone }} <br />
                    <abbr title="Mobile">M:</abbr> {{ $purchase_order->warehouse->phone2 }} <br />
                    <abbr title="Mobile">@:</abbr> {{ $purchase_order->warehouse->email }}
                </address>
            </div>
        </div>
    </div> <!-- end col -->

    <div class="col-lg-3 ">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-3">Delivery Info</h4>

                <div class="text-center">
                    <i class="mdi mdi-truck-fast h2 text-muted"></i>
                    <h5><b>UPS Delivery</b></h5>
                    <p class="mb-1"><b>Order ID :</b> xxxx235</p>
                    <p class="mb-0"><b>Payment Mode :</b> COD</p>
                </div>
            </div>
        </div>
    </div> <!-- end col -->
</div>