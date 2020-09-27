@extends('admin.include.layout')

@section('content')
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="panel panel-grey">
                <div class="panel-heading">
                    <span class="fa fa-user"></span>
                    <span class="bold">{{!empty($title) ? $title: ''}}</span>
                    <span class="font-normal"> ( Total : {{$items->count()}} ) </span>
                    <span class="pull-right"><a href="{{route('admin.publicUsers.create')}}" class="btn btn-xs btn-success"> ADD + </a></span>
                </div>
                <div class="panel-body">
                    <table class="table table-bordered table-responsive table-hover" id="items-table" style="width: 100%; display: none; text-align: center !important;">
                        <thead>
                        <tr>
                            <th width="75" class="center-text">Status</th>
                            <th width="150" class="center-text">Full Name</th>
                            <th width="150" class="center-text">Email</th>
                            <th width="100" class="center-text">Cell Phone</th>
                            <th width="100" class="center-text">Last Login</th>
                            <th width="100" class="center-text">Registered At</th>
                            <th width="60" class="center-text"></th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

@stop

@section('moduleHeader')
<link href="{{asset('assets/plugins/datatables/datatables.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/plugins/datatables/datatables.min.css')}}" rel="stylesheet" type="text/css" />
@stop

@section('moduleFooter')
<script src="{{asset('assets/plugins/datatables/datatables.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/plugins/datatables/Responsive-2.2.1/js/responsive.bootstrap.min.js')}}" type="text/javascript"></script>
<script>

    var dataTableAjaxURL = Path+'publicUsers/dataTables';
    var dataTable = $('#items-table').DataTable({
        processing: true,
        serverSide: true,
        order: [[5, 'desc']],
        responsive: true,
        ajax: {
            type : 'post',
            url  : dataTableAjaxURL,
            data : {
                _token : _csrf_token
            }
        },
        initComplete : function () {
            $('#items-table').show();
        },
        columnDefs: [
            { name: 'status'},
            { name: 'fullName'},
            { name: 'email'},
            { name: 'cell'},
            { name: 'last_login'},
            { name: 'created_at'},
            { name: 'action' }
        ],
        columns: [
            { data: 'status'   , sortable: true  , searchable: false},
            { data: 'fullName'   , sortable: false , searchable: true},
            { data: 'email'   , sortable: false , searchable: true},
            { data: 'cell'   , sortable: false , searchable: true},
            { data: 'last_login', sortable: true , searchable: false},
            { data: 'created_at', sortable: true , searchable: false},
            { data: 'action'    , sortable: false , searchable: false}
        ]
    });

    $(document).on('click' , '.delete_btn' , function (e) {
        var identifier = $(this).data('id');
        statusChange(identifier);
    });

    function statusChange(identifier) {
        $.ajax({
            url: Path+'publicUsers/AjaxStatusUpdate',
            type: "POST",
            data: { _token: _csrf_token , identifier: identifier },
            success: function(response){
                if(response.status == 'success'){
                    toastr.success(response.message);
                    dataTable.ajax.reload();
                } else {
                    toastr.error(response.message);
                }
            }
        });
    }

</script>
@stop


