/******/ (() => { // webpackBootstrap
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
  }];

  // Default Datatable
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
/******/ })()
;