<!DOCTYPE html>
<html lang="en">

  <head>

    <meta charset="utf-8">
    <title>Sunlit Future - Sale Order</title>
    
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <meta name="description" content="Sunlit Invoice">
    <meta name="author" content="Sunlit">

    <style type="text/css">

/*! Invoice Templates @author: Invoicebus @email: info@invoicebus.com @web: https://invoicebus.com @version: 1.0.0 @updated: 2017-09-07 12:09:32 @license: Invoicebus */
/* Reset styles */
@import url("https://fonts.googleapis.com/css?family=PT+Sans:400,700&subset=latin,latin-ext,cyrillic,cyrillic-ext");

html, body, div, span, applet, object, iframe,
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
	height: 10px !important;
}

#container {
font: normal 14px/1.4em 'PT Sans', Sans-serif;
margin: 0 auto;
padding: 50px 65px;
min-height: 1058px;
position: relative;
border-style: solid;
border-color: grey;
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
color: #888;
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
float: right;
margin-top: 9px;
margin-right: 30px;
min-width: 220px;
}
#client-info > div {
margin-bottom: 3px;
}
#client-info span {
display: block;
}
#client-info .client-name {
font-weight: bold;
}

#invoice-title-number {
float: left;
margin: 60px 0 10px 0;
}
#invoice-title-number span {
display: inline-block;
min-width: 15px;
line-height: 1em;
}
#invoice-title-number #title {
font-size: 50px;
color: #396E00;
}
#invoice-title-number #number {
font-size: 22px;
}

table {
	/* table-layout: fixed; */
}
table th, table td {
	vertical-align: top;
	word-break: keep-all;
	word-wrap: break-word;
}


#signatory {
margin-top: 10px;
border-style: solid;
}
#signatory table {
border-collapse: separate;
width: 100%;
}
#signatory table td {
/*border-bottom: 1px solid #C4C4C4;*/
padding: 10px;
text-align: left;
}

#items {
margin-top: 40px;
}
#items .first-cell, #items table th:first-child, #items table td:first-child {
width: 42px;
text-align: right;
}
#items table {
border-collapse: separate;
width: 100%;
}
#items table th span:empty, #items table td span:empty {
display: inline-block;
}
#items table th {
font-weight: bold;
padding: 10px;
text-align: right;
border-bottom: 2px solid #898989;
}
#items table th:nth-child(2) {
width: 30%;
text-align: left;
}
#items table th:last-child {
text-align: right;
}
#items table td {
border-bottom: 1px solid #C4C4C4;
padding: 10px;
text-align: right;
}
#items table td:first-child {
text-align: left;
}
#items table td:nth-child(2) {
text-align: left;
}

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
margin-top: 50px;
page-break-inside: avoid;
}
#terms > span {
font-weight: bold;
}
#terms > div {
color: #396E00;
font-size: 16px;
min-height: 45px;
width: 100%;
/*max-width: 500px;*/
}

.payment-info {
	color: #888;
	font-size: 12px;
	margin-top: 20px;
	width: 100%;
	/*max-width: 440px;*/
}
.payment-info div {
	display: inline-block;
	min-width: 15px;
}


      
      table {
        font-family: Verdana, sans-serif;;
        font-weight: normal;
        font-style: normal;
        font-size: 11px;

        max-width: 2480px;
        width:100%;
      }

      table td {
        /*width: auto;*/
        overflow: hidden;
        word-wrap: normal;
      }

      .company-info {
        font-size: 14px;
      }

    </style>
  </head>

<body>

  <div id="container">

    {{-- HEADER --}}
    <table style="width:100%">
      <tr>
        <td>
          <img src="/images/logo.png" alt="" height="48">
          {{-- <img src="{{ asset('/images/logo.png') }}" alt="" height="48"> --}}
          
        </td>
        <td class="text-end">
          Original for Recipient<br>
          Duplicate for Transporter<br>
          Triplicate for Supplier
        </td>
      </tr>
    </table>


    {{-- COMPANY INFO --}}
    <table>
      <tr class="company-info">
        <td><strong>{{ $settings['company']['name'] }}</strong></td>
      </tr>
      <tr class="company-info">
        <td><span>{{ $settings['company']['city'] }} {{ $settings['company']['zipcode'] }}</span></td>
      </tr>
      <tr class="company-info">
        <td><span>{{ $settings['company']['state'] }} {{ $settings['company']['country'] }}</span></td>
      </tr>
      <tr class="company-info">
        <td><span>GSTIN: {{ $settings['company']['gstin'] }}</span></td>
      </tr>
      <tr class="company-info">
        <td><span>Email: {{ $settings['company']['email'] }}</span></td>
      </tr>
      <tr class="company-info">
        <td><span>Phone: {{ $settings['company']['phone'] }}</span></td>
      </tr>
    </table>


    {{-- INVOICE DETAILS --}}
    <table>
      <tr class="invoice-title-number">
        <td><span id="number">Proforma Invoice #{{ $order->order_number }}</span></td>
      </tr>
      <tr>
        <td><span>Booked On: {{ $order->display_booked_at}}</span></td>
      </tr>
      <tr>
        <td><span>Due On: {{ $order->display_due_at}} </span></td>
      </tr>
    </table>


    {{-- DEALER DETAILS --}}
    <table>
      <tr>
        <td colspan="2" width="50%"><span><strong>Details of Receiver (Billed to)</strong></span></td>
        <td colspan="2" width="50%"><span><strong>Details of Consignee (Shipped to)</strong></span></td>
      </tr>
      <tr>
        <td>Name</td>
        <td>{{ $order->dealer->company}}</td>
        <td>Name</td>
        <td>{{ $order->dealer->shipping_company}}</td>
      </tr>
      <tr>
        <td>Address</td>
        <td class="text-break">{{ $order->dealer->address }}</td>
        <td>Address</td>
        <td class="text-break">{{ $order->dealer->shipping_address }}</td>
      </tr>
      <tr>
        <td></td>
        <td>{{ $order->dealer->address2 }}</td>
        <td></td>
        <td>{{ $order->dealer->shipping_address2 }}</td>
      </tr>
      <tr>
        <td>City</td>
        <td>{{ $order->dealer->city }} {{ $order->dealer->zip_code }}</td>
        <td>City</td>
        <td>{{ $order->dealer->shipping_city }} {{ $order->dealer->shipping_zip_code }}</td>
      </tr>
      <tr>
        <td>State</td>
        <td>{{ $order->dealer->state->name }}</td>
        <td>State</td>
        <td>{{ $order->dealer->shipping_state->name ?? '' }}</td>
      </tr>
      <tr>
        <td>State Code</td>
        <td>{{ $order->dealer->state->code }}</td>
        <td>State Code</td>
        <td>{{ $order->dealer->shipping_state->code ?? '' }}</td>
      </tr>
      <tr>
        <td>GSTIN</td>
        <td>{{ $order->dealer->gstin }}</td>
        <td>GSTIN</td>
        <td>{{ $order->dealer->shipping_gstin }}</td>
      </tr>
      <tr>
        <td>Contact</td>
        <td>{{ $order->dealer->contact_person}}</td>
        <td>Contact</td>
        <td>{{ $order->dealer->shipping_contact_person}}</td>
      </tr>
      <tr>
        <td>Phone</td>
        <td>{{ $order->dealer->phone }}</td>
        <td>Phone</td>
        <td>{{ $order->dealer->shipping_phone }}</td>
      </tr>
      <tr>
        <td>Email</td>
        <td>{{ $order->dealer->email }}</td>
        <td>Email</td>
        <td>{{ $order->dealer->shipping_email }}</td>
      </tr>
      <tr>
        <td>Transport details</td>
        <td colspan="3"></td>
      </tr>
    </table>


    {{-- ITEM DETAILS --}}
      <table cellpadding="0" cellspacing="0">
      
        <tr>
          <th rowspan="2">Sr.<br>No.</th> {{-- Dummy cell for the row number and row commands --}}
          <th rowspan="2">Part Number</th>
          <th rowspan="2">Qty</th>
          <th rowspan="2">Unit</th>
          <th rowspan="2">Rate</th>
          {{-- <th rowspan="2">Total</th> --}}
          {{-- <th rowspan="2">Discount</th> --}}
          <th rowspan="2">Taxable<br>Value</th>
          @if ($order->dealer->state->code==33)
            <th colspan="2" class="text-center">SGST</th>
            <th colspan="2" class="text-center">CGST</th>
          @else
            <th colspan="2" class="text-center">IGST</th>
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
        

        {{-- 
          The list of items of the sales order
        --}}
        @foreach ($order->items as $item)

          <tr data-iterate="item">

            <td>{{ $loop->iteration }}</td> <!-- Don't remove this column as it's needed for the row commands -->

            <td><span class="text-end">{{ $item->product->part_number }} <br> {{ $item->product->notes }} </span></td>
            <td><span>{{ $item->quantity_ordered }}</span></td>
            <td><span>Nos</span></td>
            <td><span>{{ $item->selling_price }}</span></td>
            {{-- <td><span>{{ $item->taxable_value }}</span></td> --}}
            {{-- <td><span>&mdash;</span></td> --}}
            <td><span>{{ $item->taxable_value }}</span></td>
            @if ($order->dealer->state->code==33)
              <td>{{ $item->tax/2 }}%</td>
              <td>@php echo number_format($item->tax_amount/2, 2); @endphp</td>
              <td>{{ $item->tax/2 }}%</td>
              <td>@php echo number_format($item->tax_amount/2, 2); @endphp</td>
            @else
              <td>{{ $item->tax }}%</td>
              <td>{{ $item->tax_amount }}</td>
            @endif

          </tr>
          
          @php
            $last_item = $loop->count + 1;
          @endphp

        @endforeach

        {{-- 
          The Transport Charges as a row 
        --}}
        <tr>
            <td>{{ $last_item }}</td> <!-- Don't remove this column as it's needed for the row commands -->
            <td><span> Transport charges </span></td>
            <td><span> 1 </span></td>
            <td><span> Nos </span></td>
{{--             <td><span>{{ $order->transport_charges }}</span></td> --}}
            <td><span>{{ $order->transport_charges }}</span></td>
            {{-- <td><span>&mdash;</span></td> --}}
            <td><span>{{ $order->transport_charges }}</span></td>
            @if ($order->dealer->state->code==33)
              <td>{{ $item->tax/2 }}%</td>
              <td>@php echo number_format(($order->transport_charges/2) * ($item->tax)/100, 2); @endphp</td>
              <td>{{ $item->tax/2 }}%</td>
              <td>@php echo number_format(($order->transport_charges/2) * ($item->tax)/100, 2); @endphp</td>
            @else
              <td>{{ $item->tax }}%</td>
              <td>{{ $order->transport_charges }}</td>
            @endif
        </tr>

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
          <th class="text-end">{{ $order->sub_total }}</th>
          @if ($order->dealer->state->code==33)
            <th></th>
            <th>@php echo $order->tax_total_half; @endphp</th>
            <th></th>
            <th>@php echo $order->tax_total_half; @endphp</th>
          @else
            <th></th>
            <th>{{ $order->tax_total }}</th>
          @endif
        </tr>
        
      </table>
        
      {{-- TOTALS       --}}
      <table cellpadding="0" cellspacing="0">
        <tr>
          <th>Subtotal</th>
          <td>{{ $order->sub_total }}</td>
        </tr>
        
        <tr data-iterate="tax">
          <th>Tax</th>
          <td>{{ $order->tax_total }}</td>
        </tr>
        
        <tr class="amount-total">
          <th>Total</th>
          <td>{{ $order->total }}</td>
        </tr>
        
      </table>
        
      
      <section id="terms">

        <p>
          <span class="text-danger">Note:</span><span> Material is readily available for dispatch</span><br>
          <span class="text-danger">Note:</span><span> On receiving the goods and before signing LR copy, please open the box to check for any damage.</span><br>
          <span class="text-danger">PI Validity</span><span> 7 Days</span><br>
          <span class="text-danger">Payment Terms</span><span> 100% payment before Dispatch</span>
        </p>
                
        <span class="hidden">{terms_label}</span>

        <div>Total invoice value (in words): {{ $order->total_spellout }}</div>

        Amount of tax subject to Reverse Charge (Yes or No):
        
      </section>


      <section id="signatory">
        
        <table cellpadding="0" cellspacing="0">
          <tr>
            <td>Name of the Signatory:</td>
            <td></td>
            <td>For Sunlit Future</td>
          </tr>
          <tr>
            <td>Designation / Status:</td>
            <td></td>
            <td></td>
          </tr>
          <tr>
            <td>Date:</td>
            <td></td>
            <td>Authorized Signatory</td>
          </tr>
        </table>

      </section>

      <div class="payment-info">
        <div>Bank Details
            Name of the Bank : HDFC Bank Ltd
            Bank Account No. : 04071450000340
            IFSC Code        : HDFC0000407
        </div>
      </div>

    </div>

</body>
</html>
