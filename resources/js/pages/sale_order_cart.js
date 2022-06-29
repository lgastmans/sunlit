$(document).ready(function () {
    "use strict";

    var shippingStateSelect = $(".shipping-state-select").select2();
    var routeShippingState = globalSettings.sale_order_shipping_state;
    shippingStateSelect.select2({
        ajax: {
            url: routeShippingState,
            dataType: 'json'
            // processResults: function (data) {
            //     return {
            //         //results: data
            //     };
            // }
        }
    });

    shippingStateSelect.on("change", function(e) {

        var lastValue = e.currentTarget.value;
        var lastText = e.currentTarget.textContent;

        //console.log(lastValue +' :: ' + lastText);

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        }); 

        var route = globalSettings.sale_order_update;
        route = route.replace(':id', $('#sale-order-id').val());

        var field_name = 'shipping_state_id'; //$(this).attr('id');
        var field_value = lastValue;

        $.ajax({
            type: 'POST',
            url: route,
            dataType: 'json',
            data: { 
                'value': field_value,
                'field': field_name, 
                'item': false,
                '_method': 'PUT'
            },
            success : function(result){
                console.log(result);

                // $(" #transport-charges ").html(result.transport_charges);
                // $(" #total-cost ").html(result.total);

                // $.NotificationApp.send("Success","Transport Charges saved","top-right","","success")

            }
        });

     });


    function recalculateGrandTotal(){
        var grand_total = 0;
        $('.item-total').each(function( index ){
            grand_total = grand_total + parseFloat($(this).html());
        });
        $('#grand-total').html(grand_total.toFixed(2));

        //var route = '{{ route("sale-orders.update", ":id") }}';
        var route = globalSettings.sale_order_update;
        route = route.replace(':id', $('#sale-order-id').val());
        $.ajax({
                type: 'POST',
                url: route,
                dataType: 'json',
                data: { 
                    'value' : false , 
                    'field': 'amount', 
                    'item': false,
                    '_method': 'PUT'
                },
                success : function(result){
                    $("#freight").val(result.freight_charges);
                }
            });
    }

 
    var dealerSelect = $(".dealer-select").select2();
    dealerSelect.select2({
        ajax: {
            //url: '{{route("ajax.dealers")}}',
            url: globalSettings.ajax_dealers,
            dataType: 'json'
        }
    });

    function formatProduct(el){
            var item = "<div><h5>" + el.text + "</h5>";
            var sa = 0;
            var sb = 0;
            var so = 0;
                if (el.stock_available != undefined)
                    sa = el.stock_available;
                if (el.stock_booked != undefined)
                    sb = el.stock_booked;
                if (el.stock_ordered != undefined)
                    so = el.stock_ordered;
                item += "<small><strong>Available:</strong> "+ sa + "</small>"
                item += "<small><strong> / Booked:</strong> "+ sb + "</small>"
                item += "<small><strong> / Ordered:</strong> "+ so + "</small>"
            item += "</div>";
        return $(item);
    }

    var productSelect = $(".product-select").select2();
    var product_route = globalSettings.product_route; //'{{ route("ajax.products.warehouse", [":warehouse_id"]) }}';
    product_route = product_route.replace(':warehouse_id', $('#warehouse-id').val());
    productSelect.select2({
        ajax: {
            url: product_route,
            dataType: 'json',
        },
        templateResult : formatProduct,
        // templateSelection : formatProduct,
        //   escapeMarkup: function(m) {
        //       // Do not escape HTML in the select options text
        //       return m;
        //    },
    });

    // productSelect.on("keyup", function (e) { 
    //     console.log(e.keyCode);
    //     if (( e.keyCode == 13 ) || ( e.keyCode == 9)) {
    //         e.preventDefault();
    //         $("#quantity_ordered").focus();
    //     }
    // });


    productSelect.on("change", function (e) { 
        var product_id = $(".product-select").find(':selected').val();
        var warehouse_id = $(".product-select").find(':selected').val();
        //var route = '{{ route("product.json", [":id", ":warehouse_id"]) }}';
        var route = globalSettings.product_json;

        route = route.replace(':id', product_id);
        route = route.replace(':warehouse_id', $('#warehouse-id').val());
        $.ajax({
            type: 'GET',
            url: route,
            dataType: 'json',
            success : function(data){

                // console.log(JSON.stringify(data)); 

                /**
                 * default quantity to 1
                 */
                $("#quantity_ordered").val("1");

                /**
                 * suggest price if average selling price not set
                 */
                if (parseInt(data.average_selling_price) > 0) {
                    $('#selling_price').val(data.average_selling_price);
                    $(" #suggested_selling_price ").hide();
                    $(" #suggested_selling_price ").html('');
                }
                else {
                    $('#selling_price').val('');
                    $(" #suggested_selling_price ").show();
                    $(" #suggested_selling_price ").html('BP: Rs ' + data.average_buying_price); //.toNumber().formatCurrency());
                }

                $('#tax').val(data.tax.amount);

            }
        })
    });

    function getTaxValue(tax_percentage){
        if (tax_percentage == null)
            return 1;
        
        var tax = 1 + (parseFloat(tax_percentage.replace('%','') / 100)); 

        return tax;
    }

    $('body').on('blur', '.editable-field', function(e){

        if ($(this).val() != $(this).attr('data-value')){

            if ($(this).attr('data-field') == "price"){
                item_id = $(this).parent().parent().parent().attr('data-id');
            }
            else{
                var item_id = $(this).parent().parent().attr('data-id');
            }
            var total = $('#item-price-' + item_id).val() * getTaxValue($('#item-tax-' + item_id).html()) * $('#item-quantity-' + item_id).val();
            
            $('#item-total-' + item_id).html(total.toFixed(2));
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            });
            //var route = '{{ route("sale-orders-items.update", ":id") }}';
            var route = globalSettings.sale_order_items_update;
            route = route.replace(':id', item_id);
            $.ajax({
                type: 'POST',
                url: route,
                dataType: 'json',
                data: { 
                    'value' : $(this).val(), 
                    'field': $(this).attr('data-field'), 
                    'item': $(this).attr('data-item'),
                    '_method': 'PUT'
                },
                success : function(result){
                    $('#item-price-' + item_id).attr('data-value', $('#item-price-' + item_id).val())
                    $('#item-quantity-' + item_id).attr('data-value', $('#item-quantity-' + item_id).val())
                    recalculateGrandTotal()
                }
            });
        }
    });


    $('#delete-modal').on('show.bs.modal', function (e) {
        //var route = '{{ route("sale-orders-items.delete", ":id") }}';
        var route = globalSettings.sale_order_items_delete;
        var button = e.relatedTarget;
        if (button != null){
            route = route.replace(':id', button.id);
            $('#delete-form').attr('action', route);
        }
    });

    $('#delete-modal-order').on('show.bs.modal', function (e) {
        //var route = '{{ route("sale-orders.delete", ":id") }}';
        var route = globalSettings.sale_order_delete
        var button = e.relatedTarget;
        if (button != null){
            route = route.replace(':id', button.id);
            $('#delete-order-form').attr('action', route);
        }
    });

    function is_existing_product(product_id)
    {
        var item_id = 0;
        $('.item').each(function(index){
            if ($(this).attr('data-product-id') == product_id){
                item_id =  $(this).attr('data-id');
            } 
        }); 
        return item_id;
    }
    

    $('.form-product').on('submit', function(e){
        e.preventDefault();

        var price = $("#selling_price").val();
        var asked_quantity = $('#quantity_ordered').val();        
        var product_id = $('#product_id').val();
        

        /**
         * validate product 
         */
         if (product_id === null)
         {
            $.NotificationApp.send("Error","Product not defined","top-right","","error")
            return false;
        }

        /**
         * validate quantity
         */
        if (isNaN(parseFloat(asked_quantity)))
        {
            $.NotificationApp.send("Error","Invalid Quantity","top-right","","error")
            return false;
        }

        if (parseFloat(asked_quantity) === 0)
        {
            $.NotificationApp.send("Error","Quantity cannot be zero","top-right","","error")
            return false;
        }

        /**
         * validate price
         */
        if (isNaN(parseFloat(price)))
        {
            $.NotificationApp.send("Error","Invalid Selling Price","top-right","","error")
            return false;
        }

        if (parseFloat(price) === 0)
        {
            $.NotificationApp.send("Error","Selling Price cannot be zero","top-right","","error")
            return false;
        }

        $(" #suggested_selling_price ").hide();
        $(" #suggested_selling_price ").html('');

        var existing_item_id = is_existing_product($('#product_id').val());
        if (existing_item_id > 0){
            var new_quantity = parseInt(asked_quantity) + parseInt($('#item-quantity-'+existing_item_id).val())
            $('#quantity_ordered').val(new_quantity);
        }

        $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                }
            });   
        $.ajax({
            type: 'POST',
            url: $(this).attr("action"),
            dataType: 'json',
            data: $( this ).serialize(),
            success: function (data) {
                if (existing_item_id > 0){
                    $('.item').each(function(index){
                        if ($(this).attr('data-product-id') == data.product.id){
                            var existing_item_id = $(this).attr('data-id');
                            var existing_item_quantity = $('#item-quantity-'+existing_item_id).val();
                            $('#item-quantity-'+existing_item_id).val(parseInt(data.item.quantity_ordered));
                            var new_total = (data.item.selling_price * getTaxValue(data.item.tax)) * parseInt($('#item-quantity-'+existing_item_id).val()).toFixed(2);
                            $('#item-total-'+existing_item_id).html(new_total.toFixed(2))
                            var new_product = false;
                        } 
                    });
                }
                else{
                    var item = '<tr class="item" data-id="'+ data.item.id +'" data-product-id="'+data.product.id+'">';
                    item += '<td>';
                        item += '<p class="m-0 d-inline-block align-middle font-16">';
                            item += '<a href="javascript:void(0); class="text-body product-name">'+ data.product.part_number +'</a>';
                                item += '<br>';
                                item += '<small class="me-2"><b>Description:</b> <span class="product-note">'+ data.product.notes +'</span> </small>';
                                item += '</p>';
                                item += '</td>';
                                item += '<td>';
                                    item += '<div class="input-group flex-nowrap">';
                                        item += '<span class="input-group-text">'+globalSettings.inr_symbol+'</span>';
                                        item += '<input id="item-price-'+ data.item.id +'" type="text" class="editable-field form-control" data-value="'+ data.item.selling_price +'" data-field="price" data-item="'+ data.item.id +'" placeholder="" value="'+ data.item.selling_price  +'">';
                                        item += '</div>';
                                        item += '</td>';
                                        item += '<td>';
                                            item += '<input id="item-quantity-'+ data.item.id +'" type="number" min="1" value="'+ data.item.quantity_ordered +'" class="editable-field form-control" data-value="'+ data.item.quantity_ordered +'" data-field="quantity" data-item="'+ data.item.id +'" placeholder="Qty" style="width: 90px;">';
                            item += '</td>';
                            item += '<td>';
                            item += '<span id="item-tax-'+ data.item.id +'" class="item-tax">'+ data.item.tax +'%</span>';
                            item += '</td>';
                            item += '<td>';
                            item += '<span>'+globalSettings.inr_symbol+'</span><span id="item-total-'+ data.item.id +'" class="item-total">'+((data.item.selling_price * getTaxValue(data.item.tax)) * parseInt(data.item.quantity_ordered)).toFixed(2) +'</span>';
                            item += '</td>';
                            item += '<td>';
                            item += '<a href="javascript:void(0);" class="action-icon" id="'+data.item.id+'" data-bs-toggle="modal" data-bs-target="#delete-modal"> <i class="mdi mdi-delete"></i></a>';
                            item += '</td>';
                            item += '</tr> ';
                    $('#sale-order-items-table > tbody:last-child').append(item);
                    $('.no-items').remove();
                }
                $('.place-order-form-container').removeClass('d-none');
                recalculateGrandTotal()
                // clear the form
                $('#quantity_ordered').val('');
                $('#selling_price').val('');
                $('#product_id').val(null).trigger('change');
            },
            error:function(xhr, textStatus, thrownError, data)
            {
                console.log("Error: " + thrownError);
                console.log("Error: " + textStatus);


            }
        });     
    });


    $('.edit-order-number').on('click', function(e){
        $(this).hide();
        $('.edit-order-number-form').slideDown();
        $('#purchase-order-number').hide();
    });

    $('.edit-order-number-cancel').on('click', function(e){
        $('.edit-order-number-form').slideUp();
        $('#purchase-order-number').show();
        $('.edit-order-number').show();
    });


    $('.edit-order-number-form').on('submit', function(e){
        e.preventDefault();       
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            }
        });   
        $.ajax({
            type: 'POST',
            url: $(this).attr("action"),
            dataType: 'json',
            data: $( this ).serialize(),
            success: function (data) {
                var new_url = "{{ route('sale-orders.cart', ':order_number') }}";
                new_url = new_url.replace(':order_number', $('#order_number_slug').val());
                $('.invalid-feedback').hide();
                $('.edit-order-number-form').slideUp();
                $('#sale-order-number').show();
                $('.order-number').html('#'+$('#order_number').val());
                $('.edit-order-number').show();
                $('#order_number').val("");
                window.history.pushState("data","Title",new_url);
            },
            error:function(xhr, textStatus, thrownError, data)
            {
                $('.invalid-feedback').show();
            }
        });     
    });
});