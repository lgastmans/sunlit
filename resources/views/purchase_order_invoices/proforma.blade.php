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
          <td>{{ $order->invoice_number}}</td>
        </tr>
        <tr>
          <td class="label">Address</td>
          <td class="text">{{ $settings['company']['city'] }} {{ $settings['company']['zipcode'] }}</td>
          <td>{{ $order->display_created_at }}</td>
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
          <td class="text">{{ $order->purchase_order->supplier->address }} {{ $order->purchase_order->supplier->address2 }}</td>
        </tr>
        <tr>
          <td class="label">City</td>
          <td class="text">{{ $order->purchase_order->supplier->city }} {{ $order->purchase_order->supplier->zip_code }}</td>
        </tr>
        <tr>
          <td class="label">Phone</td>
          <td class="text">{{ $order->purchase_order->supplier->phone }} {{ $order->purchase_order->supplier->phone2 }}</td>
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


  </div> {{-- container --}}

</body>
</html>
