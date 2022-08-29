<!DOCTYPE html>
<html lang="en">

  <head>

    <meta charset="utf-8">
    <title>Sunlit Future - Purchase Order Invoice</title>
    
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <meta name="description" content="Sunlit Invoice">
    <meta name="author" content="Sunlit">

    <style type="text/css">

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
      #memo .title {
        font-weight: bold;
        font-size: 12px;
        margin-bottom: 7px;
      }
      #memo .label {
        font-style: italic;
        font-size: 12px;
      }
      #memo .text {
        font-style: normal;
        font-size: 12px;
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
        font-size: 12px;
        margin-bottom: 7px;
      }
      #client-info .client-name {
        font-weight: bold;
      }
      #client-info .label {
        font-style: italic;
        font-size: 12px;
      }
      #client-info .text {
        font-style: normal;
        font-size: 12px;
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
      #terms table td {
        vertical-align: top;
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
{{--           <td style="text-align: right;">
            <p>Original for Recipient</p>
            <p>Duplicate for Transporter</p>
            <p>Triplicate for Supplier</p>
          </td>
 --}}        
        </tr>
        <tr>
          <td style="text-align: center;font-weight: bold;">
            PURCHASE ORDER
          </td>
        </tr>
      </table> {{-- header --}}

      {{-- COMPANY INFO --}}
      <table>
        <tr>
          <td style="width: 120px;" class="title">INVOICE TO</td>
          <td style="width: 60%;">{{ $settings['company']['name'] }}</td>
          <td class="title"># {{ $order->invoice_number}}</td>
        </tr>
        <tr>
          <td class="label">Address</td>
          <td class="text">{{ $settings['company']['city'] }} {{ $settings['company']['zipcode'] }}</td>
          <td class="title">{{ $order->display_created_at }}</td>
        </tr>
        <tr>
          <td class="label">State</td>
          <td class="text">{{ $settings['company']['state'] }}</td>
          <td></td>
        </tr>
        <tr>
          <td class="label">Phone</td>
          <td class="text">{{ $settings['company']['phone'] }}</td>
          <td></td>
        </tr>
        <tr>
          <td class="label">Email</td>
          <td class="text">{{ $settings['company']['email'] }}</td>
        </tr>
        <tr>
          <td class="label">GSTIN</td>
          <td class="text">{{ $settings['company']['gstin'] }}</td>
          <td></td>
        </tr>
        <tr>
          <td class="label">Website</td>
          <td class="text">http://sunlitfuture.in</td>
          <td></td>
        </tr>
      </table> {{-- company info --}}

    </section> {{-- memo --}}


    <div class="clearfix"></div>


    <section id="client-info">

      {{-- SUPPLIER INFO --}}
      <table>
        <tr>
          <td style="width: 120px;" class="title">SUPPLIER</td>
          <td></td>
        </tr>
        <tr>
          <td class="label">Name</td>
          <td class="text">{{ $order->purchase_order->supplier->company }}</td>
        </tr>
        <tr>
          <td class="label">Address</td>
          <td class="text">{{ $order->purchase_order->supplier->address }}@if (!empty($order->purchase_order->supplier->address2)), @endif {{ $order->purchase_order->supplier->address2 }}</td>
        </tr>
        <tr>
          <td class="label">City</td>
          <td class="text">{{ $order->purchase_order->supplier->city }} {{ $order->purchase_order->supplier->zip_code }}</td>
        </tr>
        <tr>
          <td class="label">Phone</td>
          <td class="text">{{ $order->purchase_order->supplier->phone }} @if (!empty($order->purchase_order->supplier->phone2)) / @endif {{ $order->purchase_order->supplier->phone2 }}</td>
        </tr>
        <tr>
          <td class="label">Email</td>
          <td class="text">{{ $order->purchase_order->supplier->email }}</td>
        </tr>
        <tr>
          <td class="label">Contact Person</td>
          <td class="text">{{ $order->purchase_order->supplier->contact_person }}</td>
        </tr>
      </table>

    </section>  {{-- client-info --}}


    <section id="items">

      {{-- ITEM DETAILS --}}
      <table>
        <thead>
          <tr>
            <th style="vertical-align: bottom;">Sr.<br>No.</th> {{-- Dummy cell for the row number and row commands --}}
            <th style="vertical-align: bottom;text-align: left;width:35%;">Part Number</th>
            <th style="vertical-align: bottom;text-align: left;width:35%;">Description</th>
            <th style="vertical-align: bottom;">Qty</th>
            <th style="vertical-align: bottom;">Unit Price USD</th>
            <th style="vertical-align: bottom;">Total Price USD</th>
          </tr>
        </thead>
        
        <tbody>
          {{-- 
            The list of items of the sales order
          --}}
          @foreach ($order->items as $item)

            <tr data-iterate="item">

              <td>{{ $loop->iteration }}</td> <!-- Don't remove this column as it's needed for the row commands -->

              <td style="text-align: left;"><span>{{ $item->product->part_number }} </span></td>

              <td style="text-align: left;"><span>{{ $item->product->notes }}</span></td>

              <td><span>{{ $item->quantity_shipped }}</span></td>

              <td><span>{{ $item->buying_price }}</span></td>

              <td><span>@php echo number_format($item->quantity_shipped * $item->buying_price,2,'.',','); @endphp</span></td>

            </tr>
            
            @php
              $last_item = $loop->count + 1;
            @endphp

          @endforeach

        </tbody>

        <tfoot>
          {{-- 
            The column totals 
          --}}
          <tr>
            <th colspan="5">Grand Total</th>
            <th style="text-align: right;">{{ $order->amount_usd }}</th>
          </tr>

        </tfoot> 
      </table>

    </section> {{-- items --}}

    <section id="terms">
      <table>
        <tr>
          <th colspan="3">Terms of Delivery and Payment</th>
        </tr>
        <tr>
          <td width="5%">1</td>
          <td width="25%">Delivery :</td>
          <td>Item 1 in Q4 / Item 2 in Q3 / Item 3 in Q4 / Item 4 in Q4 / Item 5 in Q4 / Item 6 in Q4 / Item 7 & 8 in Q4</td>
        </tr>
        <tr>
          <td>2</td>
          <td>Requested date of Delivery</td>
          <td></td>
        </tr>
        <tr>
          <td>3</td>
          <td>Shipment Terms (Inco Term 2010)</td>
          <td>CIF-FTWZ-Chennai,</td>
        </tr>
        <tr>
          <td>4</td>
          <td>Payment Terms:</td>
          <td>60 Days from SolarEdge Invoice date.</td>
        </tr>
        <tr>
          <td>5</td>
          <td>Condition:</td>
          <td>This goods are only used for Solar Power Generating Systems and have no other Commercial aspect<br>
            Please mention heading "Solar Inverters" under Part number description. (Auroville Foundation)<br>
            Please mention IE Code 0488003440 & Branch Code: 31 in all the documents.<br>
            Mention terms CIF- FTWZ-Chennai<br>
            Please send warranty certficates and test reports.
          </td>
        </tr>
          
      </table>

      <p>
        <span>Items sold under the purchase order (PO) shall be subject to SolarEdge warranty terms that may be located at https://www.solaredge.com/sites/default/files/solaredge-warranty-May-2021.pdf.</span><br>
        <span>This order is approved in accordance with and subject to the terms & Conditions of SolarEdge, which can be found at https://www.solaredge.com/sites/default/files/commercial-terms-and-conditions-</span><br>
      </p>

    </section> {{-- terms --}}


    <section id="signatory">
      <table>
        <tr>
          <td>Name of the Signatory:</td>
          <td></td>
          <td style="text-align: right;">For Sunlit Future</td>
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


  </div> {{-- container --}}

</body>
</html>
