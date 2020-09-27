@extends('admin.include.layout')
@section('content')
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="panel panel-grey">
                <div class="panel-heading">
                    <span class="fa fa-money"></span>
                    <span class="bold">{{!empty($title) ? $title: ''}}</span>
                    <span class="pull-right">
                </span>
                </div>

                <div class="panel-body">
                    <table class="table table-bordered table-responsive" id="list-table" style="width: 100%; display: none; text-align: center; font-family: Tahoma, Helvetica, Arial;">
                        <thead>
                        <tr>
                            <th class="center-text" width="100">Status</th>
                            <th class="center-text" width="120">Port</th>
                            <th class="center-text" width="120">User</th>
                            <th class="center-text" width="120">Cell</th>
                            <th class="center-text" width="120">Invoice number</th>
                            <th class="center-text" width="100">Amount</th>
                            <th class="center-text" width="100" >Payment Date</th>
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
    <link href="{{asset('assets/plugins/datatables/Responsive-2.2.1/css/responsive.bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
@stop

@section('moduleFooter')
<script src="{{asset('assets/plugins/datatables/datatables.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/plugins/datatables/Responsive-2.2.1/js/responsive.bootstrap.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/plugins/jquery-mask/jquery.mask.min.js')}}" type="text/javascript"></script>
<script src="{{ Module::asset('sale:js/module.js') }}" type="application/javascript"></script>
<script>
M6Module.paymentList();
</script>
@stop