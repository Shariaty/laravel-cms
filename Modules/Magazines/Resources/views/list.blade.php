@extends('admin.include.layout')

@section('content')
<div class="row">
    <div class="col-md-12 col-xs-12">
            <div class="panel panel-grey">
                <div class="panel-heading">
                    <span class="fa fa-newspaper-o"></span>
                    <span class="bold">{{!empty($title) ? $title: ''}}</span>
                    <span class="font-normal"> ( Total : {{$items->count()}} ) </span>
                    <span class="pull-right">
                    <a href="{{route('admin.magazines.add')}}" class="btn btn-xs btn-success" style="margin-top: -2px;">+ Add New</a>
                </span>
                </div>
                <div class="panel-body">
                    <table class="table table-bordered table-responsive table-hover" id="items-table" style="width: 100%; display: none; text-align: center !important;">
                        <thead>
                        <tr>
                            <th width="1" class="center-text"></th>
                            <th class="center-text">Title</th>
                            <th width="150" class="center-text">Categories</th>
                            <th width="50" class="center-text">Downloads</th>
                            <th width="80" class="center-text">Created At</th>
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
<link href="{{asset('assets/plugins/datatables/Responsive-2.2.1/css/responsive.bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
@stop

@section('moduleFooter')
<script src="{{asset('assets/plugins/datatables/datatables.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/plugins/datatables/Responsive-2.2.1/js/responsive.bootstrap.min.js')}}" type="text/javascript"></script>
<script src="{{ Module::asset('magazines:js/module.js') }}" type="application/javascript"></script>
<script>
M6Module.ajaxStatusChange('magazines/AjaxStatusUpdate');
M6Module.itemList();
</script>
@stop


