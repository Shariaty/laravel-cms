@extends('admin.include.layout')

@section('content')
<div class="row">
    <div class="col-md-12 col-xs-12">
        <div class="panel panel-grey">
            <div class="panel-heading">
                <span class="fa fa-newspaper-o"></span>
                <span class="bold">{{!empty($title) ? $title: ''}}</span>
                <span class="font-normal" id="counter-view"> ( Total : {{$count}} ) </span>
            </div>
            <div class="panel-body">

                <div class="uploader-container">
                    @include('portal::forms._fileUpload')
                </div>


                <table class="table table-bordered table-responsive table-hover" id="items-table" style="width: 100%; display: none; text-align: center !important;">
                    <thead>
                    <tr>
                        <th class="center-text">Created At</th>
                        <th width="200" class="center-text">State</th>
                        <th width="60" class="center-text">Records</th>
                        <th width="100" class="center-text"></th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div id="updater-modal" class="modal fade updater-modal" role="dialog" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <input type="hidden" value="" id="identifier" name="identifier"/>
                <iframe style="width: 100%;height: 350px;border: none;" src=""></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" id="close-btn" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
const PATHUPDATER = '{{route("admin.portal.taskPlayer")}}';
M6Module.portalItemList();
M6Module.portalAjaxFileUpload();
M6Module.initializeIframeUpdater(PATHUPDATER);

$(document).on('click' , '#finish-btn' , function () {
    $('#updater-modal').modal('hide');
    console.log('cliiiiiiiiicked');
});
</script>
@stop


