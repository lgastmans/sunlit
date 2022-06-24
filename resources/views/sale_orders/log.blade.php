<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-3">Order log</h4>

                <div class="table-responsive">
                    <table class="table table-sm table-centered mb-0">
                        <thead>
                            <th>Date</th>
                            <th>&nbsp;</th>
                        </thead>
                        <tbody>
                            @foreach($activities as $activity)
                                <tr>
                                    <td>
                                        {{ \Carbon\Carbon::parse($activity->updated_at)->toFormattedDateString() }}
                                    </td>
                                    <td>
                                        {!! $activity->description.' by <b>'.$activity->causer->name.'</b>' !!}
                                    </td>
                                </tr>
                            @endforeach


{{--                             @if ($order->status >= 7)
                            <tr>
                                <td>{{ $order->display_delivered_at }}</td><td>The order has been delivered</td>
                            </tr>
                            @endif
                          
                            @if ($order->status >= 4)
                            <tr>
                                <td>{{ $order->display_dispatched_at }}</td><td>The order has been dispatched via <b>{{ $order->courier }}</b></td>
                            </tr>
                            @endif
                            @if ($order->status >= 3)
                            <tr>
                                <td>{{ $order->display_booked_at }}</td><td>The order has been booked by <b>{{ $order->dealer->company }}</b></td>
                            </tr>
                            @endif
                            @if ($order->status >= 2)
                            <tr>
                                <td>{{ $order->display_blocked_at }}</td><td>The order has been blocked by <b>{{ $order->user->display_name }}</b></td>
                            </tr>
                            @endif
 --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>