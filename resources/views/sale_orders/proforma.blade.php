<!DOCTYPE html>
<html lang="en">

  <head>

    <meta charset="utf-8">
    <title>Sunlit Future - Proforma Invoice</title>
    
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <meta name="description" content="Sunlit Invoice">
    <meta name="author" content="Sunlit">

    <style type="text/css">

/*! Invoice Templates @author: Invoicebus @email: info@invoicebus.com @web: https://invoicebus.com @version: 1.0.0 @updated: 2017-09-07 12:09:32 @license: Invoicebus */
/* Reset styles */
@import url("https://fonts.googleapis.com/css?family=PT+Sans:400,700&subset=latin,latin-ext,cyrillic,cyrillic-ext");

/*html, body, div, span, applet, object, iframe,
h1, h2, h3, h4, h5, h6, p, blockquote, pre,
a, abbr, acronym, address, big, cite, code,
del, dfn, em, img, ins, kbd, q, s, samp,
small, strike, strong, sub, sup, tt, var,
b, u, i, center,
dl, dt, dd, ol, ul, li,
fieldset, form, label, legend,
table, caption, tbody, tfoot, thead, tr, th, td,
article, aside, canvas, details, embed,
figure, figcaption, footer, header, hgroup,
menu, nav, output, ruby, section, summary,
time, mark, audio, video {
	margin: 0;
	padding: 0;
	border: 1;
	font: inherit;
	font-size: 100%;
	vertical-align: baseline;
}*/

html, body {
  margin: 0;
  padding: 0;
  border: 1;
  font: inherit;
  font-size: 100%;
  vertical-align: baseline;
}

html {
	line-height: 1;
}

ol, ul {
	list-style: none;
}

table {
	border-collapse: collapse;
	border-spacing: 0;
}

caption, th, td {
	text-align: left;
	font-weight: normal;
	vertical-align: middle;
}

q, blockquote {
	quotes: none;
}
q:before, q:after, blockquote:before, blockquote:after {
	content: "";
	content: none;
}

a img {
	border: none;
}

article, aside, details, figcaption, figure, footer, header, hgroup, main, menu, nav, section, summary {
	display: block;
}

/* Invoice styles */
/**
* DON'T override any styles for the <html> and <body> tags, as this may break the layout.
	* Instead wrap everything in one main <div id="container"> element where you may change
	* something like the font or the background of the invoice
	*/
html, body {
	/* MOVE ALONG, NOTHING TO CHANGE HERE! */
}

/** 
	* IMPORTANT NOTICE: DON'T USE '!important' otherwise this may lead to broken print layout.
* Some browsers may require '!important' in oder to work properly but be careful with it.
*/
.clearfix {
	display: block;
	clear: both;
}

.hidden {
	display: none;
}

.separator {
	height: 15px;
}
.separator.less {
	height: 3px !important;
}

#container {
  font: normal 14px/1.4em 'PT Sans', Sans-serif;
  margin: 0 auto;
  padding: 10mm 10mm;
  position: relative;
  border-style: none;
  border-color: grey;
  width: 210mm;
  /*min-height: 297mm;*/
  /*min-height: 1058px;*/
}

#container table {
  width: 190mm;
  margin-bottom: 5mm;
}

#memo {
  min-height: 100px;
}
#memo .logo {
  float: left;
  margin-right: 20px;
  position: relative !important;
}
#memo .logo img {
  width: 150px;
  height: 100px;
}
#memo .company-info {
  float: left;
  margin-top: 8px;
  max-width: 515px;
}
#memo .company-info span {
  color: grey;
  display: inline-block;
  min-width: 15px;
}
#memo .company-info > span:first-child {
  color: black;
  font-weight: bold;
  font-size: 28px;
  line-height: 1em;
}
#memo:after {
  content: '';
  display: block;
  clear: both;
}


#invoice-info {
float: left;
margin-top: 50px;
}
#invoice-info > div {
float: left;
}
#invoice-info > div > span {
display: block;
min-width: 100px;
min-height: 18px;
margin-bottom: 3px;
}
#invoice-info > div:last-child {
margin-left: 10px;
}
#invoice-info:after {
content: '';
display: block;
clear: both;
}

#client-info {
  /*float: right;*/
  margin-top: 10px;
  margin-right: 10px;
  /*min-width: 220px;*/
}
#client-info > div {
  margin-bottom: 3px;
}
#client-info span {
  display: block;
}
#client-info .title {
  font-weight: bold;
  font-size: 14px;
  margin-bottom: 7px;
}
#client-info .client-name {
  font-weight: bold;
}
#client-info .label {
  font-style: italic;
}

#invoice-title-number {
  float: left;
  margin: 0px;
}
#invoice-title-number span {
  display: inline-block;
  min-width: 15px;
  line-height: 1em;
  font-size: 12px;
}
#invoice-title-number #title {
  font-size: 50px;
  color: #396E00;
}
#invoice-title-number #number {
  font-size: 16px;
}

table {
  font-weight: normal;
  font-style: normal;
  font-size: 11px;

}
table th, table td {
	vertical-align: top;
	word-break: keep-all;
	word-wrap: break-word;
}


#signatory {
  margin-top: 10px;
  /*margin-right: 70px;*/
  
}
#signatory table {
  border-collapse: separate;
  border-style: solid;
  /*width: 100%;*/
}
#signatory table td {
  /*border-bottom: 1px solid #C4C4C4;*/
  padding: 12px;
  text-align: left;
}


#items {
  margin-top: 10px;
}
#items .first-cell, #items table th:first-child, #items table td:first-child {
  width: 10px;
  text-align: right;
}
#items table {
  border-collapse: separate;
  /*width: 100%;*/
}
#items table th span:empty, #items table td span:empty {
  display: inline-block;
}
#items table th {
  font-weight: bold;
  padding: 5px;
  text-align: right;
  border-bottom: 2px solid #898989;
}
/*#items table th:nth-child(2) {
  width: 20%;
  text-align: left;
}
*/#items table th:last-child {
  text-align: right;
}
#items table td {
  border-bottom: 1px solid #C4C4C4;
  padding: 4px;
  text-align: right;
}
#items table td:first-child {
  text-align: left;
}
/*#items table td:nth-child(2) {
  text-align: left;
}
*/
#sums {
float: right;
margin-top: 10px;
page-break-inside: avoid;
}
#sums table tr th, #sums table tr td {
min-width: 100px;
padding: 10px;
text-align: right;
}
#sums table tr th {
text-align: left;
padding-right: 25px;
}
#sums table tr.amount-total th {
text-transform: uppercase;
color: #396E00;
}
#sums table tr.amount-total th, #sums table tr.amount-total td {
font-weight: bold;
font-size: 16px;
border-top: 2px solid #898989;
}
#sums table tr:last-child th {
text-transform: uppercase;
color: #396E00;
}
#sums table tr:last-child th, #sums table tr:last-child td {
font-size: 16px;
font-weight: bold;
}

#terms {
  font-family: Verdana, sans-serif;
  font-style: normal;
  font-size: 12px;
  margin-top: 20px;
  page-break-inside: avoid;
}
#terms > span {
  font-weight: bold;
}
#terms > div {
  color: #396E00;
  font-size: 12px;
  min-height: 25px;
  width: 100%;
/*max-width: 500px;*/
}

.payment-info {
	color: black; /*#888;*/
  font-weight: bold;
	font-size: 12px;
	margin-top: 20px;
	width: 100%;
  text-align: left;
	/*max-width: 440px;*/
}
.payment-info div {
	display: inline-block;
	min-width: 15px;
}


      
/*      table {
        font-family: Verdana, sans-serif;;
        font-weight: normal;
        font-style: normal;
        font-size: 11px;

        max-width: 2480px;
        width:100%;
      }
      table td {
        overflow: hidden;
        word-wrap: normal;
      }
*/
      .company-info {
        font-size: 14px;
      }

    </style>
  </head>

<body>

  <div id="container">

    <section id="memo">
      {{-- HEADER --}}
      <table>
        <tr>
          <td>

             <img src="{{ public_path('images/logo.png') }}" alt="" height="48">
            
          </td>
          <td style="text-align: right;">
            <p>Original for Recipient<br>
            Duplicate for Transporter<br>
            Triplicate for Supplier</p>
          </td>
        </tr>
      </table> {{-- header --}}

      {{-- COMPANY INFO --}}
      <table>
        <tr>
          <td class="company-info">
            <span>{{ $settings['company']['name'] }}</span>
            <div class="separator less"></div>
            <span>{{ $settings['company']['city'] }} {{ $settings['company']['zipcode'] }}</span><br>
            <span>{{ $settings['company']['state'] }} {{ $settings['company']['country'] }}</span><br>
            <span>GSTIN: {{ $settings['company']['gstin'] }}</span><br>
            <span>Email: {{ Auth::user()->email ?? "" }}</span><br>
            <span>Phone: {{ $settings['company']['phone'] }}</span>
          </td>
        </tr>
      </table> {{-- company info --}}
    </section> {{-- memo --}}

    <div class="clearfix"></div>

    <section id="invoice-title-number">
      {{-- INVOICE DETAILS --}}
      <table>
        <tr>
          <td colspan="3">
            <span id="number">Proforma Invoice #{{ $order->order_number }}</span>
          </td>
        </tr>
        <tr>
          <td style="text-align: right; width:15%;"><span>Booked On:</span></td>
          <td><span>&nbsp;{{ $order->display_booked_at}}</span></td>
          <td></td>
        </tr>
        <tr>
          <td style="text-align: right;"><span>Dispatch On: </span></td>
          <td><span>&nbsp;{{ $order->display_dispatched_at}} </span><br></td>
          <td></td>
        </tr>
      </table>
    </section> {{-- invoice-title-number --}}

    <div class="clearfix"></div>

    <section id="client-info">
      {{-- DEALER DETAILS --}}
      <table>
        <tr>
          <td colspan="2" width="50%"><span class="title">Details of Receiver (Billed to)</span></td>
          <td colspan="2" width="50%"><span class="title">Details of Consignee (Shipped to)</span></td>
        </tr>
        <tr>
          <td width="15%" class="label">Name</td>
          <td class="client-name">{{ $order->dealer->company}}</td>
          <td width="15%" class="label">Name</td>
          <td class="client-name">{{ $order->shipping_company}}</td>
        </tr>
        <tr>
          <td class="label">Address</td>
          <td class="text-break">{{ $order->dealer->address }}</td>
          <td class="label">Address</td>
          <td class="text-break">{{ $order->shipping_address }}</td>
        </tr>
        <tr>
          <td></td>
          <td>{{ $order->dealer->address2 }}</td>
          <td></td>
          <td>{{ $order->shipping_address2 }}</td>
        </tr>
        <tr>
          <td class="label">City</td>
          <td>{{ $order->dealer->city }} {{ $order->dealer->zip_code }}</td>
          <td class="label">City</td>
          <td>{{ $order->shipping_city }} {{ $order->shipping_zip_code }}</td>
        </tr>
        <tr>
          <td class="label">State</td>
          <td>{{ $order->dealer->state->name }}</td>
          <td class="label">State</td>
          <td>{{ $order->state->name ?? '' }}</td>
        </tr>
        <tr>
          <td class="label">State Code</td>
          <td>{{ $order->dealer->state->code }}</td>
          <td class="label">State Code</td>
          <td>{{ $order->state->code ?? '' }}</td>
        </tr>
        <tr>
          <td class="label">GSTIN</td>
          <td>{{ $order->dealer->gstin }}</td>
          <td class="label">GSTIN</td>
          <td>{{ $order->shipping_gstin }}</td>
        </tr>
        <tr>
          <td class="label">Contact</td>
          <td>{{ $order->dealer->contact_person}}</td>
          <td class="label">Contact</td>
          <td>{{ $order->shipping_contact_person}}</td>
        </tr>
        <tr>
          <td class="label">Phone</td>
          <td>{{ $order->dealer->phone }}</td>
          <td class="label">Phone</td>
          <td>{{ $order->shipping_phone }}</td>
        </tr>
        <tr>
          <td class="label">Email</td>
          <td>{{ $order->dealer->email }}</td>
          <td class="label">&nbsp;</td>
          <td></td>
        </tr>
        <tr>
          <td class="label">Transport details</td>
          <td colspan="3"></td>
        </tr>
      </table>

    </section> {{-- client info --}}


    <section id="items">

      {{-- ITEM DETAILS --}}
      <table>
        <thead>
          <tr>
            <th rowspan="2" style="vertical-align: bottom;">Sr.<br>No.</th> {{-- Dummy cell for the row number and row commands --}}
            <th rowspan="2" style="vertical-align: bottom;text-align: left;width:35%;">Part Number</th>
            <th rowspan="2" style="vertical-align: bottom;">Qty</th>
            <th rowspan="2" style="vertical-align: bottom;">Unit</th>
            <th rowspan="2" style="vertical-align: bottom;">Rate</th>
            {{-- <th rowspan="2">Total</th> --}}
            {{-- <th rowspan="2">Discount</th> --}}
            <th rowspan="2" style="vertical-align: bottom;">Taxable<br>Value</th>
            @if ($order->dealer->state->code==33)
              <th colspan="2" style="text-align: center;">SGST</th>
              <th colspan="2" style="text-align: center;">CGST</th>
            @else
              <th colspan="2" style="text-align: center;">IGST</th>
            @endif
          </tr>

          <tr>
            {{-- <th>Sr.<br>No.</th> Dummy cell for the row number and row commands --}}
            {{-- <th>Part Number</th> --}}
            {{-- <th>Qty</th> --}}
            {{-- <th>Unit</th> --}}
            {{-- <th>Rate</th> --}}
            {{-- <th>Total</th> --}}
            {{-- <th>Discount</th> --}}
            {{-- <th>Value</th> --}}
            @if ($order->dealer->state->code==33)
              <th>Rate</th>
              <th class="text-end">Amt</th>
              <th>Rate</th>
              <th>Amt</th>
            @else
              <th>Rate</th>
              <th>Amt</th>
            @endif
          </tr>
        </thead>
        
        <tbody>
          {{-- 
            The list of items of the sales order
          --}}
          @php $tax = 0 @endphp
          @foreach ($order->items as $item)

            <tr data-iterate="item">

              <td>{{ $loop->iteration }}</td> <!-- Don't remove this column as it's needed for the row commands -->

              <td style="text-align: left;"><span>{{ $item->product->part_number }} <br> {{ $item->product->notes }} </span></td>
              <td><span>{{ $item->quantity_ordered }}</span></td>
              <td><span>Nos</span></td>
              <td><span>{{ $item->selling_price }}</span></td>
              {{-- <td><span>{{ $item->taxable_value }}</span></td> --}}
              {{-- <td><span>&mdash;</span></td> --}}
              <td><span>@php echo number_format($item->taxable_value,2) @endphp</span></td>
              @if ($order->dealer->state->code==33)
                <td>{{ $item->tax/2 }}%</td>
                <td>@php echo number_format($item->tax_amount/2, 2); @endphp</td>
                <td>{{ $item->tax/2 }}%</td>
                <td>@php echo number_format($item->tax_amount/2, 2); @endphp</td>
              @else
                <td>{{ $item->tax }}%</td>
                <td>@php echo number_format($item->tax_amount,2) @endphp</td>
              @endif

            </tr>
            
            @php
              if ($item->tax > $tax)
                $tax = $item->tax;
              $last_item = $loop->count + 1;
            @endphp

          @endforeach

          {{-- 
            The Transport Charges as a row 
          --}}
          <tr>
              <td>{{ $last_item }}</td> <!-- Don't remove this column as it's needed for the row commands -->
              <td style="text-align: left;"><span> Transport charges </span></td>
              <td><span> 1 </span></td>
              <td><span> Nos </span></td>
  {{--             <td><span>{{ $order->transport_charges }}</span></td> --}}
              <td><span>{{ $order->transport_charges }}</span></td>
              {{-- <td><span>&mdash;</span></td> --}}
              <td><span>{{ $order->transport_charges }}</span></td>
              @if ($order->dealer->state->code==33)
                <td>{{ $tax/2 }}%</td>
                <td>@php echo number_format($order->transport_tax_amount/2, 2); @endphp</td>
                <td>{{ $tax/2 }}%</td>
                <td>@php echo number_format($order->transport_tax_amount/2, 2); @endphp</td>
              @else
                <td>{{ $tax }}%</td>
                <td>{{ $order->transport_tax_amount }}</td>
              @endif
          </tr>

        </tbody>

        <tfoot>
          {{-- 
            The column totals 
          --}}
          <tr>
            {{-- <th rowspan="2">Sr.<br>No.</th> Dummy cell for the row number and row commands --}}
            {{-- <th rowspan="2">Part Number</th> --}}
            {{-- <th rowspan="2">Qty</th> --}}
            {{-- <th rowspan="2">Unit</th> --}}
            <th colspan="5">Subtotals</th>
            {{-- <th >{{ $order->sub_total }}</th> --}}
            {{-- <th>&mdash;</th> --}}
            <th style="text-align: right;">{{ $order->sub_total }}</th>
            @if ($order->dealer->state->code==33)
              <th></th>
              <th style="text-align: right;">@php echo $order->tax_total_half; @endphp</th>
              <th></th>
              <th style="text-align: right;">@php echo $order->tax_total_half; @endphp</th>
            @else
              <th></th>
              <th style="text-align: right;">{{ $order->tax_total }}</th>
            @endif
          </tr>

          <tr class="subtotal">
            <td colspan="{{ $order->dealer->state->code==33 ? 7 : 5}}"></td>
            <th style="text-align: right;">Subtotal</th>
            <th colspan="2">{{ $order->sub_total }}</th>
          </tr>
          
          <tr data-iterate="tax">
            <td colspan="{{ $order->dealer->state->code==33 ? 7 : 5}}"></td>
            <th style="text-align: right;">Tax</th>
            <th colspan="2">{{ $order->tax_total }}</th>
          </tr>
          
          <tr class="amount-total">
            <th colspan="{{ $order->dealer->state->code==33 ? 7 : 5}}"></th>
            <th style="text-align: right;">Total (rounded)</th>
            <th colspan="2">{{ $order->total }}</th>
          </tr>

          @if ($order->total_advance > 0)
            <tr class="amount-total">
              <th colspan="{{ $order->dealer->state->code==33 ? 7 : 5}}"></th>
              <th style="text-align: right;">Advance</th>
              <th colspan="2">{{ $order->display_total_advance }}</th>
            </tr>
            <tr class="amount-total">
              <th colspan="{{ $order->dealer->state->code==33 ? 7 : 5}}"></th>
              <th style="text-align: right;">Balance Due</th>
              <th colspan="2">{{ $order->display_balance_due }}</th>
            </tr>
          @endif

        </tfoot> 
      </table>

    </section> {{-- items --}}
        
      
    <section id="terms">
      <p>

        {!! $order->payment_terms !!}

{{--         <span class="text-danger">Note:</span><span> Material is readily available for dispatch</span><br>
        <span class="text-danger">Note:</span><span> On receiving the goods and before signing LR copy, please open the box to check for any damage.</span><br>
        <span class="text-danger">PI Validity</span><span> 7 Days</span><br>
        <span class="text-danger">Payment Terms</span><span> 100% payment before Dispatch</span><br>
        <span>Amount of tax subject to Reverse Charge (Yes or No):</span>
 --}}        
      </p>
      <span class="hidden">{terms_label}</span>
      <div>Total invoice value (in words): {{ $order->total_spellout }}</div>
    </section> {{-- terms --}}


    <section id="signatory">
      <table>
        <tr>
          <td>Prepared By:</td>
          <td></td>
          <td style="text-align: right;">{{ $order->user->name }} - for Sunlit Future</td>
        </tr>
        <tr>
          <td>Designation / Status:</td>
          <td></td>
          <td></td>
        </tr>
        <tr>
          <td>Date:</td>
          <td></td>
          <td style="text-align: right;vertical-align: bottom;">Authorized Signatory</td>
        </tr>
      </table>
    </section> {{-- signatory --}}

    <div class="payment-info">
      <div>Bank Details
          Name of the Bank : HDFC Bank Ltd
          Bank Account No. : 04071450000340
          IFSC Code        : HDFC0000407
      </div>
    </div>


  </div> {{-- container --}}

</body>
</html>
