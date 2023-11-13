@extends('layouts.app')

@section('title')
    @parent() | DSTR
@endsection

@section('page-title', 'DSTR')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    
                    <div class="mb-3 row">

                        <div class="col-xl-1">
                        </div>

                        
                        {{-- Monthly - month --}}

                        <div class="col-xl-2" id="display_monthly_month">
                            <label class="form-label">&nbsp;</label>
                            <select class="month-select form-control" id="month_id">
                                @foreach(range(1,12) as $month)
                                        <option value="{{$month}}" {{ $month == date('m') ? "selected" : "" }} >{{ date('F', mktime(0, 0, 0, $month, 1)) }}</option>
                                @endforeach
                            </select>
                        </div>


                        {{-- Monthly - year --}}

                        <div class="col-xl-1 position-relative " id="display_monthly_year">
                            <label class="form-label">&nbsp;</label>
                            <input type="text" class="form-control " id="year_id" value="{{ date('Y') }}" required data-provide="datepicker" 
                            data-date-container="#display_monthly_year">
                        </div>

                        <div class="col-xl-1">
                            <label class="form-label">&nbsp;</label>
                            <button class="btn btn-primary form-control" type="button" id="btn-load">
                                Load
                            </button>
                        </div>

                        <div class="col-xl-4">
                        </div>

                        <div class="col-xl-3">
                        </div>

                    </div>   
                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col -->
    </div>

    <div class="row">
        <div class="col-12">
            <div class="table-responsive">

                <table id="table-dstr" class="table table-striped table-condensed" cellspacing="0" width="100%">
                </table>

            </div> {{-- table-responsive --}}
        </div>
    </div>


@endsection <!-- content -->

@section('page-scripts')

<script>

    $(document).ready(function () {
        "use strict";    

        var month_id,
            year_id,
            footerData;

        var table,
            data,
            tableName = '#table-dstr';


        $('#year_id').datepicker({
            format: 'yyyy',
            minViewMode: 2,
            maxViewMode: 2,
            autoclose: true,
        })


        $(" #btn-load ").on("click", function() {

            $(this).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true" id="spinner"></span>Loading...');

            month_id = $(" #month_id ").val();
            year_id = $(" #year_id ").val();

            $.ajax({
                method  : "GET",
                url     : "{{ route('ajax.dstr') }}",
                data    : { 
                    month_id : month_id,
                    year_id : year_id,
                }                
            })
            .done ( function( msg ) {

                //$(" #spinner ").hide();
                $("#btn-load").html('Load');

                data = JSON.parse(msg);
                console.log(data);
        
                if (table) {
                    table.clear();
                    table.destroy();
                    // clear the column headers
                    $(tableName).html(''); 
                }

                table = $(tableName).DataTable({
                    dom: 't',
                    // processing: true,
                    serverSide: true,
                    orderCellsTop: true,
                    retrieve: true,
                    fixedHeader: true,
                    deferLoading: 0,
                    searching: true,
                    paging: false,
                    ordering: false,
                    data          : data.data,
                    columns       : data.columns,
                    footer        : data.footer,            
                });

            });
        });


    }); // document ready

</script>    

@endsection <!-- page-scripts -->
