<table class="table table-centered  table-striped table-bordered mb-0">
    <thead class="thead-dark">
        <tr>
            <th>Warehouse</th>
            <th style="text-align: right;">Available</th>
            <th style="text-align: right;">Ordered</th>
            <!-- <th>Blocked</th> -->
            <th style="text-align: right;">Booked</th>
            <th style="text-align: right;">Average Cost</th>
            <th style="text-align: right;">Average Price</th>
        </tr>
    </thead>
    <tbody>
        @foreach($product->inventory as $inventory)

            <tr>
                <td>{{ $inventory->warehouse->name }}</td>
                <td style="text-align: right;">{{ $inventory->stock_available }}</td>
                <td style="text-align: right;">{{ $inventory->stock_ordered }}</td>
                <!-- <td>{{ $inventory->stock_blocked }}</td> -->
                <td style="text-align: right;">{{ $inventory->stock_booked }}</td>
                <td style="text-align: right;">{{ __('app.currency_symbol_inr')}} {{ $inventory->average_buying_price }}</td>
                <td style="text-align: right;">{{ __('app.currency_symbol_inr')}} {{ $inventory->average_selling_price }}</td>
            </tr>
        @endforeach
    </tbody>
</table>