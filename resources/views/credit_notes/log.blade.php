<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-3">Credit Note log</h4>

                <div class="table-responsive">
                    <table class="table table-sm table-centered mb-0" id="table-activity-log">
                        <thead>
                            <th>Date</th>
                            <th>&nbsp;</th>
                        </thead>
                        <tbody>
                            @foreach($activities as $activity)
                                <tr>
                                    <td>
                                        {{ \Carbon\Carbon::parse($activity->updated_at)->toFormattedDateString() }}
                                    </td>
                                    <td>
                                        {!! $activity->description.' by <b>'.$activity->causer->name.'</b>' !!}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-3">Remarks</h4>
                <textarea class="form-control" name="credit_note_remarks" id="cn_remarks">{!! $order->remarks !!}</textarea>
            </div>
        </div>
    </div>

</div>


@push('page-scripts')

    <script>

        var text_changed = false;

        ClassicEditor
            .create( document.querySelector( '#cn_remarks' ) )
            .then( editor => {
                window.editor = editor;

                detectTextChanges(editor);
                detectFocusOut(editor);
                    // editor.on("blur", function(){
                    //     console.log("hello world");
                    // });
            })
            .catch( error => {
                console.error( error );
            });

        function detectFocusOut(editor) {
            editor.ui.focusTracker.on('change:isFocused', (evt, name, isFocused) => {
                if (!isFocused && text_changed) {
                    text_changed = false;

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    var route = globalSettings.credit_note_update;
                    route = route.replace(':id', $('#credit-note-id').val());

                    var field_name = 'remarks';
                    var field_value = editor.getData();

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
                            //console.log(result);
                            
                            $.NotificationApp.send("Success","Remarks saved","top-right","","success")
                        }
                    });
                }
            });
        }

        function detectTextChanges(editor) {
            editor.model.document.on('change:data', () => {
                text_changed = true;
            });
        }

        
    </script>

@endpush