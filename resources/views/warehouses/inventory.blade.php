<table class="table table-centered mb-0">
    <thead class="thead-dark">
        <tr>
            <th>Product</th>
            <th>Available</th>
            <th>Ordered</th>
            <th>Booked</th>
            <th>Average Cost</th>
        </tr>
    </thead>
    <tbody>
        @foreach($warehouse->inventories as $inventory)

            <tr>
                <td>{{ $inventory->product->code }}</td>
                <td>{{ $inventory->stock_available }}</td>
                <td>{{ $inventory->stock_ordered }}</td>
                <td>{{ $inventory->stock_booked }}</td>
                <td>{{ __('app.currency_symbol_inr')}} {{ $inventory->average_cost }}</td>
            </tr>
        @endforeach
    </tbody>
</table>