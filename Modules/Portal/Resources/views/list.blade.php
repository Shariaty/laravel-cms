@extends('admin.include.layout')

@section('content')
<div class="row">
    <div class="col-md-12 col-xs-12">
        <div class="panel panel-grey">
            <div class="panel-heading">
                <span class="fa fa-newspaper-o"></span>
                <span class="bold">{{!empty($title) ? $title: ''}}</span>
                <span class="font-normal" id="counter-view"> ( Total : {{$count}} ) </span>
                <span class="pull-right">
                    <button type="button" class="btn btn-xs btn-success add-swal"> + ADD </button>
                    &nbsp;
                    <a href="{{route('admin.portalUpdater.clearAll')}}" class="btn btn-xs btn-danger confirmation-mass-remove-mine"> Clear All </a>
                </span>
            </div>
            <div class="panel-body">

                <div class="uploader-container">
                    @include('portal::forms._fileUpload')
                </div>


                <table class="table table-bordered table-responsive table-hover" id="items-table" style="width: 100%; display: none; text-align: center !important;">
                    <thead>
                    <tr>
                        <th width="1" class="center-text"></th>
                        <th style="text-align: left;">SKU</th>
                        <th class="center-text" width="100">Portal ID</th>
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
<link href="{{asset('assets/plugins/datatables/datatables.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/plugins/sweetalert3/dist/sweetalert2.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ Module::asset('portal:plugins/jqueryfileupload/css/jquery.fileupload.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ Module::asset('portal:css/module.css') }}" rel="stylesheet" type="text/css" />
@stop

@section('moduleFooter')
<script src="{{asset('assets/plugins/datatables/datatables.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/plugins/datatables/Responsive-2.2.1/js/responsive.bootstrap.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/plugins/sweetalert3/dist/sweetalert2.all.min.js')}}" type="text/javascript"></script>

<script src="{{ Module::asset('portal:plugins/jqueryfileupload/vendor/jquery.ui.widget.js')}}" type="text/javascript"></script>
<script src="{{ Module::asset('portal:plugins/jqueryfileupload/jquery.fileupload.js')}}" type="text/javascript"></script>
<script src="{{ Module::asset('portal:plugins/jquery.pulsate.min.js')}}" type="text/javascript"></script>
<script src="{{ Module::asset('portal:js/module.js') }}" type="application/javascript"></script>
<script>
M6Module.ajaxStatusChange('portal/AjaxStatusUpdate');
M6Module.itemList();
M6Module.ajaxFileUpload();
M6Module.swalAdd();
</script>
@stop


