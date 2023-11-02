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
                    <th>1st Quarter<br><span id="Q1">Jan - Mar</span></th>
                    <th>2nd Quarter<br><span id="Q2">Apr - Jun</span></th>
                    <th>3rd Quarter<br><span id="Q3">Jul - Sep</span></th>
                    <th>4th Quarter<br><span id="Q4">Oct - Dec</span></th>
                    <th>Total</th>
                </tr>
            @elseif($period == "period_quarterly_ind")
                <tr style="text-align: right;">
                    <th style="text-align:left">{{ ucfirst($type) }}</th>
                    <th>1st Quarter<br><span id="Q1-ind">Apr - Jun</span></th>
                    <th>2nd Quarter<br><span id="Q2-ind">Jul - Sep</span></th>
                    <th>3rd Quarter<br><span id="Q3-ind">Oct - Dec</span></th>
                    <th>4th Quarter<br><span id="Q4-ind">Jan - Mar</span></th>
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
                    <!-- @if ($total['row_total'] > 0) -->
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
                    <!-- @endif -->
                @endif
            @endforeach
        </tbody>
    </table>
</div> {{-- table-responsive --}}