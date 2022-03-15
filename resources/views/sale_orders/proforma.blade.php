
{{--     <div id="container">
 --}}
      <section id="memo">

        <div class="logo">
          <span class="logo-lg bg-sunlit">
            <img src="/images/logo.png" alt="" height="48">
          </span>
        </div>

        <div class="company-info">
          <span>{{ $order->order_number }}</span>

          <div class="separator less"></div>

          <span>{company_address}</span>
          <span>{company_city_zip_state}</span>

          <br>

          <span>{company_email_web}</span>
          <span>{company_phone_fax}</span>
        </div>
      </section>

      <section id="invoice-title-number">
      
        <span id="title">{invoice_title}</span>
        <div class="separator"></div>
        <span id="number">{invoice_number}</span>
        
      </section>
      
      <div class="clearfix"></div>
      
      <section id="invoice-info">
        <div>
          <span>{issue_date_label}</span>
          <span>{due_date_label}</span>
          <span>{net_term_label}</span>
          <span>{currency_label}</span>
          <span>{po_number_label}</span>
        </div>
        
        <div>
          <span>{issue_date}</span>
          <span>{due_date}</span>
          <span>{net_term}</span>
          <span>{currency}</span>
          <span>{po_number}</span>
        </div>
      </section>
      
      <section id="client-info">
        <span>{bill_to_label}</span>
        <div>
          <span class="client-name">{client_name}</span>
        </div>
        
        <div>
          <span>{client_address}</span>
        </div>
        
        <div>
          <span>{client_city_zip_state}</span>
        </div>
        
        <div>
          <span>{client_phone_fax}</span>
        </div>
        
        <div>
          <span>{client_email}</span>
        </div>
        
        <div>
          <span>{client_other}</span>
        </div>
      </section>
      
      <div class="clearfix"></div>
      
      <section id="items">
        
        <table cellpadding="0" cellspacing="0">
        
          <tr>
            <th>{item_row_number_label}</th> <!-- Dummy cell for the row number and row commands -->
            <th>{item_description_label}</th>
            <th>{item_quantity_label}</th>
            <th>{item_price_label}</th>
            <th>{item_discount_label}</th>
            <th>{item_tax_label}</th>
            <th>{item_line_total_label}</th>
          </tr>
          
          <tr data-iterate="item">
            <td>{item_row_number}</td> <!-- Don't remove this column as it's needed for the row commands -->
            <td><span class="show-mobile">{item_description_label}</span> <span>{item_description}</span></td>
            <td><span class="show-mobile">{item_quantity_label}</span> <span>{item_quantity}</span></td>
            <td><span class="show-mobile">{item_price_label}</span> <span>{item_price}</span></td>
            <td><span class="show-mobile">{item_discount_label}</span> <span>{item_discount}</span></td>
            <td><span class="show-mobile">{item_tax_label}</span> <span>{item_tax}</span></td>
            <td><span class="show-mobile">{item_line_total_label}</span> <span>{item_line_total}</span></td>
          </tr>
          
        </table>
        
      </section>
      
      <section id="sums">
      
        <table cellpadding="0" cellspacing="0">
          <tr>
            <th>{amount_subtotal_label}</th>
            <td>{amount_subtotal}</td>
          </tr>
          
          <tr data-iterate="tax">
            <th>{tax_name}</th>
            <td>{tax_value}</td>
          </tr>
          
          <tr class="amount-total">
            <th>{amount_total_label}</th>
            <td>{amount_total}</td>
          </tr>
          
          <!-- You can use attribute data-hide-on-quote="true" to hide specific information on quotes.
               For example Invoicebus doesn't need amount paid and amount due on quotes  -->
          <tr data-hide-on-quote="true">
            <th>{amount_paid_label}</th>
            <td>{amount_paid}</td>
          </tr>
          
          <tr data-hide-on-quote="true">
            <th>{amount_due_label}</th>
            <td>{amount_due}</td>
          </tr>
          
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

      <div class="bottom-circles">
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
      </div>
    {{-- </div> --}}


