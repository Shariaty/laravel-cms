@extends('admin.include.layout')

@section('content')
<div class="row">
    <div class="col-md-12 col-xs-12">
            <div class="panel panel-grey">
                <div class="panel-heading">
                    <span class="fa fa-newspaper-o"></span>
                    <span class="bold">{{!empty($title) ? $title: ''}}</span>
                    <span class="font-normal"> ( Total : {{$items->count()}} ) </span>
                    <span class="pull-right"><a href="{{route('admin.contacts.clearAll')}}" class="btn btn-xs btn-warning confirmation-mass-remove"> Clear All </a></span>
                </div>
                <div class="panel-body">
                    <table class="table table-bordered table-responsive table-hover" id="items-table" style="width: 100%; display: none; text-align: center !important;">
                        <thead>
                        <tr>
                            <th width="1" class="center-text"></th>
                            <th width="150" class="center-text">Subject</th>
                            <th width="150" class="center-text">sender</th>
                            <th class="center-text">Message</th>
                            <th width="80" class="center-text">Created At</th>
                            <th width="60" class="center-text"></th>
                        </tr>
                        </thead>
                    </table>

                </div>
            </div>
        </div>
</div>

@include('contacts::forms._MessageQuickView')

@stop

@section('moduleHeader')
<link href="{{asset('assets/plugins/datatables/datatables.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/plugins/datatables/datatables.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ Module::asset('contacts:css/module.css') }}" rel="stylesheet" type="text/css" />
@stop

@section('moduleFooter')
<script src="{{asset('assets/plugins/datatables/datatables.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/plugins/datatables/Responsive-2.2.1/js/responsive.bootstrap.min.js')}}" type="text/javascript"></script>
<script src="{{ Module::asset('contacts:js/module.js') }}" type="application/javascript"></script>
<script>
M6Module.itemList();
</script>
@stop


