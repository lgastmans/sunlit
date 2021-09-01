@extends('layouts.app')

@section('page-title', 'Purchase Order Details')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="row justify-content-center">
            <div class="col-lg-7 col-md-10 col-sm-11">

                <div class="horizontal-steps mt-4 mb-4 pb-5" id="tooltip-container">
                    <div class="horizontal-steps-content">
                        <div class="step-item current">
                            <span data-bs-container="#tooltip-container" data-bs-toggle="tooltip" data-bs-placement="bottom" title="{{ $purchase_order->ordered_at }}">Ordered</span>
                        </div>
                        <div class="step-item">
                            <span>Confirmed</span>
                        </div>
                        <div class="step-item">
                            <span>Shipped</span>
                        </div>
                        <div class="step-item">
                            <span>Customs</span>
                        </div>
                        <div class="step-item">
                            <span>Cleared</span>
                        </div>
                        <div class="step-item">
                            <span>Received</span>
                        </div>
                    </div>
                    <div class="process-line" style="width: 10%;"></div>
                </div>
            </div>
        </div>
        <!-- end row -->    
        
        
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title mb-3">Items from Order #{{ $purchase_order->order_number }}</h4>

                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach($purchase_order->items as $item)
                                <tr>
                                    <td>{{ $item->product->name }}</td>
                                    <td>{{ $item->quantity_ordered }}</td>
                                    <td>{{ $item->selling_price }}</td>
                                    <td>{{ $item->quantity_ordered * $item->selling_price }}</td>
                                </tr>
                                @endforeach
                                
                                </tbody>
                            </table>
                        </div>
                        <!-- end table-responsive -->

                    </div>
                </div>
            </div> <!-- end col -->

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title mb-3">Order Summary #{{ $purchase_order->order_number }}</h4>

                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead class="table-light">
                                <tr>
                                    <th>Description</th>
                                    <th>Price</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>Grand Total :</td>
                                    <td>${{ $purchase_order->amount_usd }}</td>
                                </tr>
                                <tr>
                                    <td>Shipping Charge :</td>
                                    <td>$23</td>
                                </tr>
                                <tr>
                                    <td>Estimated Tax : </td>
                                    <td>$19.22</td>
                                </tr>
                                <tr>
                                    <th>Total :</th>
                                    <th>$1683.22</th>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- end table-responsive -->

                    </div>
                </div>
            </div> <!-- end col -->
        </div>
        <!-- end row -->


        <div class="row">
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title mb-3">Supplier Information</h4>
                        <h5>{{ $purchase_order->supplier->company }}</h5>
                        <h6>{{ $purchase_order->supplier->contact_person }}</h6>
                        <address class="mb-0 font-14 address-lg">
                            <abbr title="Phone">P:</abbr> {{ $purchase_order->supplier->phone }} <br/>
                            <abbr title="Mobile">M:</abbr> {{ $purchase_order->supplier->phone2 }} <br/>
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
                            <abbr title="Phone">P:</abbr> {{ $purchase_order->warehouse->phone }} <br/>
                            <abbr title="Mobile">M:</abbr> {{ $purchase_order->warehouse->phone2 }} <br/>
                            <abbr title="Mobile">@:</abbr> {{ $purchase_order->warehouse->email }}
                        </address>
                    </div>
                </div>
            </div> <!-- end col -->

            <div class="col-lg-3 d-none">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title mb-3">Warehouse Information</h4>
                        <h5>{{ $purchase_order->warehouse->name }}</h5>
                        <h6>{{ $purchase_order->warehouse->contact_person }}</h6>
                        <address class="mb-0 font-14 address-lg">
                            <abbr title="Phone">P:</abbr> {{ $purchase_order->warehouse->phone }} <br/>
                            <abbr title="Mobile">M:</abbr> {{ $purchase_order->warehouse->phone2 }} <br/>
                            <abbr title="Mobile">@:</abbr> {{ $purchase_order->warehouse->email }}
                        </address>
                    </div>
                </div>
            </div> <!-- end col -->
        
            <div class="col-lg-3 d-none">
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
        <!-- end row -->
    </div> <!-- end col-->
</div>
@endsection


