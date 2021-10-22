@extends('layouts.app')

@section('page-title', ucfirst(Request::segment(1)) )

@section('content')


<div class="row">
    <div class="col-lg-2">
        <div class="card widget-flat">
            <div class="card-body">
                <div class="float-end">
                    <i class="mdi mdi-cart-plus widget-icon"></i>
                </div>
                <h5 class="text-muted fw-normal mt-0" title="Number of Orders">Due Pending Orders</h5>
                <a href="{{ route('purchase-orders.filtered', 'due') }}">
                    <h3 class="mt-3 mb-3 @if (count($due_orders) < 5 ) text-success @elseif (count($due_orders) < 10) text-warning @else text-danger @endif">{{ count($due_orders) }}</h3>
                </a>
                <p class="mb-0 text-muted">
                    <span class="text-danger me-2"></span>
                    <span class="text-nowrap"></span>
                </p>
            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col-->
    <div class="col-lg-2">
        <div class="card widget-flat">
            <div class="card-body">
                <div class="float-end">
                    <i class="mdi mdi-cart-plus widget-icon"></i>
                </div>
                <h5 class="text-muted fw-normal mt-0" title="Number of Orders">Overdue Pending Orders</h5>
                <a href="{{ route('purchase-orders.filtered', 'overdue') }}">
                    <h3 class="mt-3 mb-3 @if ($overdue_orders < 5 ) text-success @elseif ($overdue_orders < 10) text-warning @else text-danger @endif">{{ $overdue_orders }}</h3>
                </a>
                <p class="mb-0 text-muted">
                    <span class="text-danger me-2"></span>
                    <span class="text-nowrap"></span>
                </p>
            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col-->
    <div class="col-lg-2 offset-lg-6">
        <div class="card widget-flat">
            <div class="card-body">
                <div class="float-end">
                    <i class="mdi mdi-currency-inr widget-icon"></i>
                </div>
                <h5 class="text-muted fw-normal mt-0" title="Growth">Current exchange rate</h5>
                <h3 class="mt-3 mb-3 text-warning">{{ __('app.currency_symbol_inr')}} {{ number_format(\Setting::get('purchase_order.exchange_rate'),2) }}</h3>
                <p class="mb-0 text-muted">
                    @if (str_contains($exchange_rate_update_ago, 'days'))
                        <a href="javascript:void(0);" class="update-echange-rate">
                            <span class="text-success me-2"><i class="mdi mdi-refresh"></i> last update</span>
                        </a>
                    @else
                        <span class="text-success me-2"><i class="mdi mdi-refresh"></i> last update</span>
                    @endif
                    <span id="currency-rate" class="text-nowrap"><a href="#" data-bs-container="#currency-rate" data-bs-toggle="tooltip" title="{{ \Carbon\Carbon::parse( \Setting::get('exchange_rate_updated_at') )->toDayDateTimeString() }}">{{ $exchange_rate_update_ago }}</a></span>
                </p>
            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col-->
</div>
<div class="row">
    <div class="col-12">
        <div class="card card-h-100">
            <div class="card-body">
                <div class="dropdown float-end">
                    <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="mdi mdi-dots-vertical"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <!-- item-->
                        <a href="javascript:void(0);" class="dropdown-item">Sales Report</a>
                        <!-- item-->
                        <a href="javascript:void(0);" class="dropdown-item">Export Report</a>
                        <!-- item-->
                        <a href="javascript:void(0);" class="dropdown-item">Profit</a>
                        <!-- item-->
                        <a href="javascript:void(0);" class="dropdown-item">Action</a>
                    </div>
                </div>
                <h4 class="header-title mb-3">Pending purchase orders</h4>

                <div class="table-responsive mt-4">
                    <table class="table table-bordered table-centered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Warehouse</th>
                                <th>Supplier</th>
                                <th>Order Number</th>
                                <th>Date ordered</th>
                                <th>Due date</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($due_orders as $order)
                            <tr class="due-purchase-order-item" data-order-number="{{ $order->order_number }}">
                                <td>{{ $order->warehouse->name }}</td>
                                <td>{{ $order->supplier->company }}</td>
                                <td>{{ $order->order_number }}</td>
                                <td>
                                    <div>{{ $order->display_ordered_at }}</div>
                                    <div>({{ $order->ordered_days_ago }})</div>
                                </td>
                                <td> 
                                    <div>
                                        {{ $order->display_due_at }}
                                    </div>
                                    
                                    @if ( $order->is_overdue() )
                                        <div>
                                            <span class="badge badge-danger-lighten">overdue</span>
                                        </div>
                                    @endif
                                </td>
                                <td>{{ __('app.currency_symbol_usd')}}{{ $order->amount_usd }}</td>
                                <td>{!! $order->display_status !!}</td>
                            </tr>
                            @break($loop->iteration >= 10)
                            @endforeach
                        </tbody>
                    </table>
                </div> <!-- end table-responsive-->
                    
            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div>
</div>
<!-- end row -->


@endsection

@section('page-scripts')
    <script>
        $('.due-purchase-order-item').on('dblclick', function(){
            var route = '{{ route("purchase-orders.show", ":order_number") }}';
            window.location.href = route.replace(':order_number', $(this).attr('data-order-number'));
        });
        
        $('.update-echange-rate').on('click', function(){
            console.log('updating exchange rate...');
            //This should make an ajax call to update the exchange rate.
        });

    </script>
@endsection