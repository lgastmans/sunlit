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

    </style>
  </head>

<body>

  <div id="container">

    <section id="memo">
      {{-- HEADER --}}
      <table>
        <tr>
          <td>
            <img src="/images/logo.png" alt="" height="48">
            {{-- <img src="{{ asset('/images/logo.png') }}" alt="" height="48"> --}}
            
          </td>
{{--           <td style="text-align: right;">
            <p>Original for Recipient</p>
            <p>Duplicate for Transporter</p>
            <p>Triplicate for Supplier</p>
          </td>
 --}}        
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
            <span>Email: {{ $settings['company']['email'] }}</span><br>
            <span>Phone: {{ $settings['company']['phone'] }}</span>
          </td>
        </tr>
      </table> {{-- company info --}}
    </section> {{-- memo --}}

    <div class="clearfix"></div>


  </div> {{-- container --}}

</body>
</html>
