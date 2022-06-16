/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be isolated against other entry modules.
(() => {
/*!***********************************************!*\
  !*** ./resources/js/pages/sale_order_cart.js ***!
  \***********************************************/
$(document).ready(function () {
  "use strict";

  var shippingStateSelect = $(".shipping-state-select").select2();
  var routeShippingState = globalSettings.sale_order_shipping_state;
  shippingStateSelect.select2({
    ajax: {
      url: routeShippingState,
      //'{{route('ajax.states')}}',
      dataType: 'json'
    }
  });

  function recalculateGrandTotal() {
    var grand_total = 0;
    $('.item-total').each(function (index) {
      grand_total = grand_total + parseFloat($(this).html());
    });
    $('#grand-total').html(grand_total.toFixed(2)); //var route = '{{ route("sale-orders.update", ":id") }}';

    var route = globalSettings.sale_order_update;
    route = route.replace(':id', $('#sale-order-id').val());
    $.ajax({
      type: 'POST',
      url: route,
      dataType: 'json',
      data: {
        'value': false,
        'field': 'amount',
        'item': false,
        '_method': 'PUT'
      },
      success: function success(result) {//
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

  function formatProduct(el) {
    var item = "<div><h5>" + el.text + "</h5>";
    var sa = 0;
    var sb = 0;
    var so = 0;
    if (el.stock_available != undefined) sa = el.stock_available;
    if (el.stock_booked != undefined) sb = el.stock_booked;
    if (el.stock_ordered != undefined) so = el.stock_ordered;
    item += "<small><strong>Available:</strong> " + sa + "</small>";
    item += "<small><strong> / Booked:</strong> " + sb + "</small>";
    item += "<small><strong> / Ordered:</strong> " + so + "</small>";
    item += "</div>";
    return $(item);
  }

  var productSelect = $(".product-select").select2();
  var product_route = globalSettings.product_route; //'{{ route("ajax.products.warehouse", [":warehouse_id"]) }}';

  product_route = product_route.replace(':warehouse_id', $('#warehouse-id').val());
  productSelect.select2({
    ajax: {
      url: product_route,
      dataType: 'json'
    },
    templateResult: formatProduct // templateSelection : formatProduct,
    //   escapeMarkup: function(m) {
    //       // Do not escape HTML in the select options text
    //       return m;
    //    },

  }); // productSelect.on("keyup", function (e) { 
  //     console.log(e.keyCode);
  //     if (( e.keyCode == 13 ) || ( e.keyCode == 9)) {
  //         e.preventDefault();
  //         $("#quantity_ordered").focus();
  //     }
  // });

  productSelect.on("change", function (e) {
    var product_id = $(".product-select").find(':selected').val();
    var warehouse_id = $(".product-select").find(':selected').val(); //var route = '{{ route("product.json", [":id", ":warehouse_id"]) }}';

    var route = globalSettings.product_json;
    route = route.replace(':id', product_id);
    route = route.replace(':warehouse_id', $('#warehouse-id').val());
    $.ajax({
      type: 'GET',
      url: route,
      dataType: 'json',
      success: function success(data) {
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
        } else {
          $('#selling_price').val('');
          $(" #suggested_selling_price ").show();
          $(" #suggested_selling_price ").html('BP: Rs ' + data.average_buying_price); //.toNumber().formatCurrency());
        }

        $('#tax').val(data.tax.amount);
      }
    });
  });

  function getTaxValue(tax_percentage) {
    if (tax_percentage == null) return 1;
    var tax = 1 + parseFloat(tax_percentage.replace('%', '') / 100);
    return tax;
  }

  $('body').on('blur', '.editable-field', function (e) {
    if ($(this).val() != $(this).attr('data-value')) {
      if ($(this).attr('data-field') == "price") {
        item_id = $(this).parent().parent().parent().attr('data-id');
      } else {
        var item_id = $(this).parent().parent().attr('data-id');
      }

      var total = $('#item-price-' + item_id).val() * getTaxValue($('#item-tax-' + item_id).html()) * $('#item-quantity-' + item_id).val();
      $('#item-total-' + item_id).html(total.toFixed(2));
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
        }
      }); //var route = '{{ route("sale-orders-items.update", ":id") }}';

      var route = globalSettings.sale_order_items_update;
      route = route.replace(':id', item_id);
      $.ajax({
        type: 'POST',
        url: route,
        dataType: 'json',
        data: {
          'value': $(this).val(),
          'field': $(this).attr('data-field'),
          'item': $(this).attr('data-item'),
          '_method': 'PUT'
        },
        success: function success(result) {
          $('#item-price-' + item_id).attr('data-value', $('#item-price-' + item_id).val());
          $('#item-quantity-' + item_id).attr('data-value', $('#item-quantity-' + item_id).val());
          recalculateGrandTotal();
        }
      });
    }
  });
  $('#delete-modal').on('show.bs.modal', function (e) {
    //var route = '{{ route("sale-orders-items.delete", ":id") }}';
    var route = globalSettings.sale_order_items_delete;
    var button = e.relatedTarget;

    if (button != null) {
      route = route.replace(':id', button.id);
      $('#delete-form').attr('action', route);
    }
  });
  $('#delete-modal-order').on('show.bs.modal', function (e) {
    //var route = '{{ route("sale-orders.delete", ":id") }}';
    var route = globalSettings.sale_order_delete;
    var button = e.relatedTarget;

    if (button != null) {
      route = route.replace(':id', button.id);
      $('#delete-order-form').attr('action', route);
    }
  });

  function is_existing_product(product_id) {
    var item_id = 0;
    $('.item').each(function (index) {
      if ($(this).attr('data-product-id') == product_id) {
        item_id = $(this).attr('data-id');
      }
    });
    return item_id;
  }

  $('.form-product').on('submit', function (e) {
    e.preventDefault();
    var price = $("#selling_price").val();
    var asked_quantity = $('#quantity_ordered').val();
    var product_id = $('#product_id').val();
    /**
     * validate product 
     */

    if (product_id === null) {
      $.NotificationApp.send("Error", "Product not defined", "top-right", "", "error");
      return false;
    }
    /**
     * validate quantity
     */


    if (isNaN(parseFloat(asked_quantity))) {
      $.NotificationApp.send("Error", "Invalid Quantity", "top-right", "", "error");
      return false;
    }

    if (parseFloat(asked_quantity) === 0) {
      $.NotificationApp.send("Error", "Quantity cannot be zero", "top-right", "", "error");
      return false;
    }
    /**
     * validate price
     */


    if (isNaN(parseFloat(price))) {
      $.NotificationApp.send("Error", "Invalid Selling Price", "top-right", "", "error");
      return false;
    }

    if (parseFloat(price) === 0) {
      $.NotificationApp.send("Error", "Selling Price cannot be zero", "top-right", "", "error");
      return false;
    }

    $(" #suggested_selling_price ").hide();
    $(" #suggested_selling_price ").html('');
    var existing_item_id = is_existing_product($('#product_id').val());

    if (existing_item_id > 0) {
      var new_quantity = parseInt(asked_quantity) + parseInt($('#item-quantity-' + existing_item_id).val());
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
      data: $(this).serialize(),
      success: function success(data) {
        if (existing_item_id > 0) {
          $('.item').each(function (index) {
            if ($(this).attr('data-product-id') == data.product.id) {
              var existing_item_id = $(this).attr('data-id');
              var existing_item_quantity = $('#item-quantity-' + existing_item_id).val();
              $('#item-quantity-' + existing_item_id).val(parseInt(data.item.quantity_ordered));
              var new_total = data.item.selling_price * getTaxValue(data.item.tax) * parseInt($('#item-quantity-' + existing_item_id).val()).toFixed(2);
              $('#item-total-' + existing_item_id).html(new_total.toFixed(2));
              new_product = false;
            }
          });
        } else {
          var item = '<tr class="item" data-id="' + data.item.id + '" data-product-id="' + data.product.id + '">';
          item += '<td>';
          item += '<p class="m-0 d-inline-block align-middle font-16">';
          item += '<a href="javascript:void(0); class="text-body product-name">' + data.product.part_number + '</a>';
          item += '<br>';
          item += '<small class="me-2"><b>Description:</b> <span class="product-note">' + data.product.notes + '</span> </small>';
          item += '</p>';
          item += '</td>';
          item += '<td>';
          item += '<div class="input-group flex-nowrap">';
          item += '<span class="input-group-text">' + globalSettings.inr_symbol + '</span>';
          item += '<input id="item-price-' + data.item.id + '" type="text" class="editable-field form-control" data-value="' + data.item.selling_price + '" data-field="price" data-item="' + data.item.id + '" placeholder="" value="' + data.item.selling_price + '">';
          item += '</div>';
          item += '</td>';
          item += '<td>';
          item += '<input id="item-quantity-' + data.item.id + '" type="number" min="1" value="' + data.item.quantity_ordered + '" class="editable-field form-control" data-value="' + data.item.quantity_ordered + '" data-field="quantity" data-item="' + data.item.id + '" placeholder="Qty" style="width: 90px;">';
          item += '</td>';
          item += '<td>';
          item += '<span id="item-tax-' + data.item.id + '" class="item-tax">' + data.item.tax + '%</span>';
          item += '</td>';
          item += '<td>';
          item += '<span>' + globalSettings.inr_symbol + '</span><span id="item-total-' + data.item.id + '" class="item-total">' + (data.item.selling_price * getTaxValue(data.item.tax) * parseInt(data.item.quantity_ordered)).toFixed(2) + '</span>';
          item += '</td>';
          item += '<td>';
          item += '<a href="javascript:void(0);" class="action-icon" id="' + data.item.id + '" data-bs-toggle="modal" data-bs-target="#delete-modal"> <i class="mdi mdi-delete"></i></a>';
          item += '</td>';
          item += '</tr> ';
          $('#sale-order-items-table > tbody:last-child').append(item);
          $('.no-items').remove();
        }

        $('.place-order-form-container').removeClass('d-none');
        recalculateGrandTotal(); // clear the form

        $('#quantity_ordered').val('');
        $('#selling_price').val('');
        $('#product_id').val(null).trigger('change');
      },
      error: function error(xhr, textStatus, thrownError, data) {
        console.log("Error: " + thrownError);
        console.log("Error: " + textStatus);
      }
    });
  });
  $('.edit-order-number').on('click', function (e) {
    $(this).hide();
    $('.edit-order-number-form').slideDown();
    $('#purchase-order-number').hide();
  });
  $('.edit-order-number-cancel').on('click', function (e) {
    $('.edit-order-number-form').slideUp();
    $('#purchase-order-number').show();
    $('.edit-order-number').show();
  });
  $('.edit-order-number-form').on('submit', function (e) {
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
      data: $(this).serialize(),
      success: function success(data) {
        var new_url = "{{ route('sale-orders.cart', ':order_number') }}";
        new_url = new_url.replace(':order_number', $('#order_number_slug').val());
        $('.invalid-feedback').hide();
        $('.edit-order-number-form').slideUp();
        $('#sale-order-number').show();
        $('.order-number').html('#' + $('#order_number').val());
        $('.edit-order-number').show();
        $('#order_number').val("");
        window.history.pushState("data", "Title", new_url);
      },
      error: function error(xhr, textStatus, thrownError, data) {
        $('.invalid-feedback').show();
      }
    });
  });
});
})();

// This entry need to be wrapped in an IIFE because it need to be isolated against other entry modules.
(() => {
/*!*****************************************!*\
  !*** ./resources/js/pages/suppliers.js ***!
  \*****************************************/
$(document).ready(function () {
  "use strict";

  var data = [{
    "id": 1,
    "company_title": "Gabtype",
    "address": "Wugong",
    "email": "mpobjoy0@princeton.edu",
    "contact_person": "Marris Pobjoy",
    "phone": "1117161045"
  }, {
    "id": 2,
    "company_title": "Skippad",
    "address": "Dongshandi",
    "email": "ppactat1@over-blog.com",
    "contact_person": "Perrine Pactat",
    "phone": "4662430883"
  }, {
    "id": 3,
    "company_title": "Zoonder",
    "address": "Bacalan",
    "email": "hfrangleton2@narod.ru",
    "contact_person": "Hedvig Frangleton",
    "phone": "2535049683"
  }, {
    "id": 4,
    "company_title": "Topicshots",
    "address": "Tha Yang",
    "email": "ssuggett3@newyorker.com",
    "contact_person": "Stirling Suggett",
    "phone": "3441245132"
  }, {
    "id": 5,
    "company_title": "Jazzy",
    "address": "Požarevac",
    "email": "hsprionghall4@qq.com",
    "contact_person": "Hertha Sprionghall",
    "phone": "9081931655"
  }, {
    "id": 6,
    "company_title": "Yacero",
    "address": "Besançon",
    "email": "wsalkeld5@smh.com.au",
    "contact_person": "Willem Salkeld",
    "phone": "8142750418"
  }, {
    "id": 7,
    "company_title": "Ainyx",
    "address": "São Francisco",
    "email": "dbrolechan6@people.com.cn",
    "contact_person": "Dewitt Brolechan",
    "phone": "5517714583"
  }, {
    "id": 8,
    "company_title": "Ainyx",
    "address": "Pita Kotte",
    "email": "lbuglass7@shutterfly.com",
    "contact_person": "Lorinda Buglass",
    "phone": "1854597700"
  }, {
    "id": 9,
    "company_title": "Wikizz",
    "address": "Qa‘ţabah",
    "email": "tvickars8@loc.gov",
    "contact_person": "Theobald Vickars",
    "phone": "3799705977"
  }, {
    "id": 10,
    "company_title": "Buzzbean",
    "address": "Haveluloto",
    "email": "llovewell9@pbs.org",
    "contact_person": "Lucais Lovewell",
    "phone": "1719286569"
  }, {
    "id": 11,
    "company_title": "Katz",
    "address": "Stavropol’",
    "email": "dgilhespya@dyndns.org",
    "contact_person": "Diane Gilhespy",
    "phone": "5553299066"
  }, {
    "id": 12,
    "company_title": "Devbug",
    "address": "Bunirasa",
    "email": "eandryushinb@php.net",
    "contact_person": "Eberto Andryushin",
    "phone": "7155271538"
  }, {
    "id": 13,
    "company_title": "Babblestorm",
    "address": "Santa Rosa",
    "email": "cbastidec@diigo.com",
    "contact_person": "Codi Bastide",
    "phone": "1583571794"
  }, {
    "id": 14,
    "company_title": "Demizz",
    "address": "Margherita",
    "email": "fastlingd@ca.gov",
    "contact_person": "Flinn Astling",
    "phone": "6562645176"
  }, {
    "id": 15,
    "company_title": "Ntags",
    "address": "Nong Bua Daeng",
    "email": "felbye@rediff.com",
    "contact_person": "Fonsie Elby",
    "phone": "8827000251"
  }]; // Default Datatable

  var url = "{{ route('suppliers.list') }}";
  console.log(url);
  var table = $('#suppliers-datatable').DataTable({
    //"data": data,
    processing: true,
    serverSide: true,
    //type : "POST",
    //contentType: "json",
    //processData: false,
    ajax: url,
    "language": {
      "paginate": {
        "previous": "<i class='mdi mdi-chevron-left'>",
        "next": "<i class='mdi mdi-chevron-right'>"
      },
      "info": "Showing suppliers _START_ to _END_ of _TOTAL_",
      "lengthMenu": "Display <select class='form-select form-select-sm ms-1 me-1'>" + '<option value="10">10</option>' + '<option value="20">20</option>' + '<option value="-1">All</option>' + '</select> suppliers'
    },
    "pageLength": 10,
    "columns": [{
      'data': 'id',
      'orderable': false,
      'render': function render(data, type, row, meta) {
        if (type === 'display') {
          data = "<div class=\"form-check\"><input type=\"checkbox\" class=\"form-check-input dt-checkboxes\"><label class=\"form-check-label\">&nbsp;</label></div>";
        }

        return data;
      },
      'checkboxes': {
        'selectRow': true,
        'selectAllRender': '<div class=\"form-check\"><input type=\"checkbox\" class=\"form-check-input dt-checkboxes\"><label class=\"form-check-label\">&nbsp;</label></div>'
      }
    }, {
      'data': 'contact_person',
      'render': function render(data) {
        var displayName = '<img width="48" src="/images/users/avatar-' + (Math.floor(Math.random() * 9) + 1) + '.jpg" alt="table-user" class="me-2 rounded-circle">';
        displayName += '<a href="javascript:void(0);" class="text-body fw-semibold">' + data + '</a>';
        return displayName;
      },
      'orderable': true
    }, {
      'data': 'company_title',
      'orderable': true
    }, {
      'data': 'address',
      'orderable': true
    }, {
      'data': 'email',
      'orderable': true
    }, {
      'data': 'phone',
      'orderable': true
    }],
    "select": {
      "style": "multi"
    },
    "order": [[4, "desc"]],
    "drawCallback": function drawCallback() {
      $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
      $('#suppliers-datatable_length label').addClass('form-label');
    }
  });
});
})();

/******/ })()
;