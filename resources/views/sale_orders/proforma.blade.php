
    <div id="container">

      <section id="memo">

        <div class="logo">
          <span class="logo-lg bg-sunlit">
            <img src="/images/logo.png" alt="" height="48">
          </span>
        </div>

        <div class="company-info">
          <span>{{ $settings['company']['name'] }}</span>

          <div class="separator less"></div>

          <span>{{ $settings['company']['city'] }} {{ $settings['company']['zipcode'] }}</span><br>
          <span>{{ $settings['company']['state'] }} {{ $settings['company']['country'] }}</span><br>
          <span>GSTIN: {{ $settings['company']['gstin'] }}</span><br>

          <span>Email: {{ $settings['company']['email'] }}</span>
          <span>Phone: {{ $settings['company']['phone'] }}</span>
        </div>

        <div class="client-info">

          <p class="p-3 text-end">Original for Recipient<br>
            Duplicate for Transporter<br>
            Triplicate for Supplier
          </p>

        </div>

      </section>


      <section id="invoice-title-number">
      
{{--         <span id="title">{invoice_title}</span>
        <div class="separator"></div> --}}
        <span id="number">Proforma Invoice #{{ $order->order_number }}</span>
        
      </section>

      
      <div class="clearfix"></div>

      
      <section id="invoice-info">

        <div>
          <span><strong>Booked On</strong></span>
          <span><strong>Due On</strong></span>
          <span></span>
          <span></span>
          <span></span>
        </div>
        
        <div>
          <span>{{ $order->display_booked_at}}</span>
          <span>{{ $order->display_due_at}}</span>
          <span></span>
          <span></span>
          <span></span>
        </div>

      </section>
      


      <section id="client-info">
        <div>
          <span><h5>Details of Receiver (Billed to)</h5></span>
          <span class="client-name">{{ $order->dealer->company}}</span>
        </div>

        <div>
          <span class="client-name">{{ $order->dealer->contact_person}}</span>
        </div>
        
        <div>
          <span>{{ $order->dealer->address }}</span>
        </div>
        
        <div>
          <span>{{ $order->dealer->city }} {{ $order->dealer->zip_code }} {{ $order->dealer->state->name }}</span>
        </div>
        
        <div>
          <span><em>Phone:</em> {{ $order->dealer->phone }}</span>
        </div>
        
        <div>
          <span><em>Email:</em> {{ $order->dealer->email }}</span>
        </div>
        
        <div>
          <span><em>GSTIN:</em> {{ $order->dealer->gstin }}</span>
        </div>
      </section>
      
      <div class="clearfix"></div>
      
      <section id="items">
        
        <table cellpadding="0" cellspacing="0">
        
          <tr>
            <th rowspan="2">Sr.<br>No.</th> {{-- Dummy cell for the row number and row commands --}}
            <th rowspan="2">Part Number</th>
            <th rowspan="2">Qty</th>
            <th rowspan="2">Unit</th>
            <th rowspan="2">Rate</th>
            <th rowspan="2">Total</th>
            <th rowspan="2">Discount</th>
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
              <th>Amt</th>
              <th>Rate</th>
              <th>Amt</th>
            @else
              <th>Rate</th>
              <th>Amt</th>
            @endif
          </tr>
          

          @foreach ($order->items as $item)

            <tr data-iterate="item">

              <td>{{ $loop->iteration }}</td> <!-- Don't remove this column as it's needed for the row commands -->
              <td><span class="show-mobile">Part Number</span> <span>{{ $item->product->part_number }} <br> {{ $item->product->notes }} </span></td>
              <td><span class="show-mobile">Quantity</span> <span>{{ $item->quantity_ordered }}</span></td>
              <td><span class="show-mobile">Unit</span> <span>Nos</span></td>
              <td><span class="show-mobile">Rate</span> <span>{{ $item->selling_price }}</span></td>
              <td><span class="show-mobile">Total</span> <span>{{ $item->taxable_value }}</span></td>
              <td><span class="show-mobile">Discount</span> <span>&mdash;</span></td>
              <td><span class="show-mobile">Tax</span> <span>{{ $item->taxable_value }}</span></td>
              @if ($order->dealer->state->code==33)
                <td>{{ $item->tax/2 }}%</td>
                <td>{{ $item->tax_amount_cgst }}</td>
                <td>{{ $item->tax/2 }}%</td>
                <td>{{ $item->tax_amount_cgst }}</td>
              @else
                <td>{{ $item->tax }}%</td>
                <td>{{ $item->tax_amount_igst }}</td>
              @endif
              
            </tr>

            @php
              $i = $loop->count + 1;
            @endphp

          @endforeach

          {{-- The Transport Charges as a row --}}
          <tr>
              <td>{{ $i }}</td> <!-- Don't remove this column as it's needed for the row commands -->
              <td><span class="show-mobile">Part Number</span> <span> Transport charges </span></td>
              <td><span class="show-mobile">Quantity</span> <span> 1 </span></td>
              <td><span class="show-mobile">Unit</span> <span> Nos </span></td>
              <td><span class="show-mobile">Rate</span> <span>{{ $order->transport_charges }}</span></td>
              <td><span class="show-mobile">Total</span> <span>{{ $order->transport_charges }}</span></td>
              <td><span class="show-mobile">Discount</span> <span>&mdash;</span></td>
              <td><span class="show-mobile">Tax</span> <span>{{ $order->transport_charges }}</span></td>
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
          
        </table>
        
      </section>
      
      <section id="sums">
      
        <table cellpadding="0" cellspacing="0">
          <tr>
            <th>Subtotal</th>
            <td>{amount_subtotal}</td>
          </tr>
          
          <tr data-iterate="tax">
            <th>Tax</th>
            <td>{tax_value}</td>
          </tr>
          
          <tr class="amount-total">
            <th>Total</th>
            <td>{amount_total}</td>
          </tr>
          
          <!-- You can use attribute data-hide-on-quote="true" to hide specific information on quotes.
               For example Invoicebus doesn't need amount paid and amount due on quotes  -->
{{--           
          <tr data-hide-on-quote="true">
            <th>{amount_paid_label}</th>
            <td>{amount_paid}</td>
          </tr>
          
          <tr data-hide-on-quote="true">
            <th>{amount_due_label}</th>
            <td>{amount_due}</td>
          </tr>
 --}}          
        </table>
        
      </section>
      
      <div class="clearfix"></div>
      
      <section id="terms">
      
        <span class="hidden">{terms_label}</span>
        <div>{terms}</div>
        
      </section>

      <div class="payment-info">
        <div>{payment_info1}</div>
        <div>{payment_info2}</div>
        <div>{payment_info3}</div>
        <div>{payment_info4}</div>
        <div>{payment_info5}</div>
      </div>

{{--       <div class="bottom-circles">
        <section>
          <div>
            <div></div>
          </div>
          <div>
            <div>
              <div>
                <div></div>
              </div>
            </div>
          </div>
        </section>
      </div> --}}

    </div>


