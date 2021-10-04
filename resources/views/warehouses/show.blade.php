@extends('layouts.app')

@section('title')
    @parent() | {{ $warehouse->name }}
@endsection

@section('page-title',)
   Product details {{ $warehouse->name }}
@endsection

@section('content')



<div class="row">

    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-3">Supplier Information</h4>
                <h5>{{ $warehouse->company }}</h5>
                <h6>{{ $warehouse->contact_person }}</h6>
                <address class="mb-0 font-14 address-lg">
                    {{ $warehouse->address }}<br>
                    @if ($warehouse->address2)
                       {{ $warehouse->address2 }}<br>
                    @else
                      &nbsp;<br>
                    @endif
                    {{ $warehouse->city }}, {{ $warehouse->zip_code }}<br/>
                    <abbr title="Phone">P:</abbr> {{ $warehouse->phone }} <br/>
                    <abbr title="Mobile">M:</abbr> {{ $warehouse->phone2 }} <br/>
                    <abbr title="Mobile">@:</abbr> {{ $warehouse->email }}
                </address>
            </div>
        </div>
    </div> <!-- end col -->
    <div class="col-lg-4 end">
      {{-- we can add some stats here --}}
    </div>
</div><!-- end row -->

<div class="row">
    <div class="col-lg-8">
        
    </div>
</div>

<div class="card">
    <div class="card-body">
        <ul class="nav nav-pills bg-nav-pills nav-justified mb-3">
            <li class="nav-item">
                <a href="#inventory" data-bs-toggle="tab" aria-expanded="false" class="nav-link active">
                    <i class="mdi mdi-home-variant d-md-none d-block"></i>
                    <span class="d-none d-md-block">Inventory</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#movement" data-bs-toggle="tab" aria-expanded="true" class="nav-link ">
                    <i class="mdi mdi-account-circle d-md-none d-block"></i>
                    <span class="d-none d-md-block">Movement</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#purchase-orders" data-bs-toggle="tab" aria-expanded="false" class="nav-link">
                    <i class="mdi mdi-settings-outline d-md-none d-block"></i>
                    <span class="d-none d-md-block">Purchase</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#sales-orders" data-bs-toggle="tab" aria-expanded="false" class="nav-link">
                    <i class="mdi mdi-settings-outline d-md-none d-block"></i>
                    <span class="d-none d-md-block">Sales</span>
                </a>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane show active" id="inventory">
                @include('warehouses.inventory')
            </div>
            <div class="tab-pane" id="movement">
                @include('warehouses.movement')
            </div>
            <div class="tab-pane" id="purchase-orders">
                @include('warehouses.purchase_orders')
            </div>
            <div class="tab-pane" id="sales-orders">
                @include('warehouses.sales_orders')
            </div>
        </div>
    </div>
</div>
@endsection

@push('page-scripts')
    <script>

        @if(Session::has('success'))
            $.NotificationApp.send("Success","{{ session('success') }}","top-right","","success")
        @endif
        @if(Session::has('error'))
            $.NotificationApp.send("Error","{{ session('error') }}","top-right","","error")
        @endif

    </script>

@endpush
