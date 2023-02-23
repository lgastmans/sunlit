<div class="table-responsive">
    <table class="table table-striped table-sm " cellspacing="0" width="100%">
        <thead >
            @if($period == "period_monthly")
                <tr style="text-align: right;">
                    <th style="text-align:left">{{ ucfirst($type) }}</th>
                    <th>January</th>
                    <th>February</th>
                    <th>March</th>
                    <th>April</th>
                    <th>May</th>
                    <th>June</th>
                    <th>July</th>   
                    <th>August</th>
                    <th>September</th>
                    <th>October</th>
                    <th>November</th>
                    <th>December</th>
                </tr>
            @else
                <tr style="text-align: right;">
                    <th style="text-align:left">Category</th>
                    <th>1st Quarter</th>
                    <th>2nd Quarter</th>
                    <th>3rd Quarter</th>
                    <th>4th Quarter</th>
                </tr>
            @endif
        </thead>
        <tbody>
            @foreach ($totals as $label=>$total)
                <tr>
                    <td>{{ $label }}</td>
                    @foreach ($total as $key=>$month)
                        <td style="text-align: right;">{{ $month['total_amount'] }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div> {{-- table-responsive --}}