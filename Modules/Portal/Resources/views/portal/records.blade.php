@extends('admin.include.layout')

@section('content')
<div class="row">
    <div class="col-md-12 col-xs-12">
        <div class="panel panel-grey">
            <div class="panel-heading">
                <span class="fa fa-newspaper-o"></span>
                <span class="bold">{{!empty($title) ? $title: ''}}</span>
                <span class="font-normal" id="counter-view"> ( Total : {{$count}} ) </span>
                <span class="pull-right">&nbsp;
                    <a href="{{route('admin.portal.list')}}" class="btn btn-xs btn-success"> Back To tasks </a>
                </span>
            </div>
            <div class="panel-body">
                <table class="table table-bordered table-responsive table-hover" id="items-table" style="width: 100%; display: none; text-align: center !important;">
                    <thead>
                    <tr>
                        <th class="center-text">SKU</th>
                        <th width="60" class="center-text">STOCK</th>
                        <th width="200" class="center-text">STATUS</th>
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
<link href="{{ Module::asset('portal:css/module.css') }}" rel="stylesheet" type="text/css" />
@stop

@section('moduleFooter')
<script src="{{asset('assets/plugins/datatables/datatables.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/plugins/datatables/Responsive-2.2.1/js/responsive.bootstrap.min.js')}}" type="text/javascript"></script>
<script src="{{ Module::asset('portal:js/module.js') }}" type="application/javascript"></script>
<script>
M6Module.portalRecordsItemList({{$portal_id}});
</script>
@stop


