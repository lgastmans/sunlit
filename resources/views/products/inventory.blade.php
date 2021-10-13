<table class="table table-centered mb-0">
    <thead class="thead-dark">
        <tr>
            <th>Warehouse</th>
            <th>Available</th>
            <th>Ordered</th>
            <th>Booked</th>
            <th>Average Cost</th>
        </tr>
    </thead>
    <tbody>
        @foreach($product->inventory as $inventory)

            <tr>
                <td>{{ $inventory->warehouse->name }}</td>
                <td>{{ $inventory->stock_available }}</td>
                <td>{{ $inventory->stock_ordered }}</td>
                <td>{{ $inventory->stock_booked }}</td>
                <td>{{ __('app.currency_symbol_inr')}} {{ $inventory->average_buying_price }}</td>
            </tr>
        @endforeach
    </tbody>
</table>