@php // print_r($totals); @endphp

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
                    <th>Total</th>
                </tr>
            @elseif($period == "period_quarterly")
                <tr style="text-align: right;">
                    <th style="text-align:left">{{ ucfirst($type) }}</th>
                    <th>1st Quarter<br>Jan - Mar</th>
                    <th>2nd Quarter<br>Apr - Jun</th>
                    <th>3rd Quarter<br>Jul - Sep</th>
                    <th>4th Quarter<br>Oct - Dec</th>
                    <th>Total</th>
                </tr>
            @elseif($period == "period_quarterly_ind")
                <tr style="text-align: right;">
                    <th style="text-align:left">{{ ucfirst($type) }}</th>
                    <th>1st Quarter<br>Apr - Jun</th>
                    <th>2nd Quarter<br>Jul - Sep</th>
                    <th>3rd Quarter<br>Oct - Dec</th>
                    <th>4th Quarter<br>Jan - Mar</th>
                    <th>Total</th>
                </tr>
            @endif
        </thead>
        <tbody>
            @foreach ($totals as $label=>$total)
                @if ($label == 'column_total')
                    <tr>
                        <td style="font-weight: bold;"">Totals</td>
                        @foreach ($total as $key=>$month)
                            <td style="text-align: right;font-weight: bold;">
                                @php 
                                    $fmt = new NumberFormatter($locale = 'en_IN', NumberFormatter::CURRENCY);
                                    $fmt->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 0);                                    
                                    echo $fmt->formatCurrency($month, "INR");
                                @endphp
                            </td>
                        @endforeach
                    </tr>
                @else
                    @if ($total['row_total'] > 0)
                        <tr>
                            <td>{{ $label }}</td>
                            @foreach ($total as $key=>$month)
                                @if ($key != 'row_total')
                                    <td style="text-align: right;">{{ $month['total_amount'] }}</td>
                                @else
                                    <td style="text-align: right;font-weight: bold;">
                                        @php 
                                            $fmt = new NumberFormatter($locale = 'en_IN', NumberFormatter::CURRENCY);
                                            $fmt->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 0);                                    
                                            echo $fmt->formatCurrency($total['row_total'], "INR");
                                        @endphp
                                    </td>
                                @endif
                            @endforeach
                        </tr>
                    @endif
                @endif
            @endforeach
        </tbody>
    </table>
</div> {{-- table-responsive --}}