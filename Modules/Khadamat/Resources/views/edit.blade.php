@extends('admin.include.layout')

@section('content')
<div class="row">
    <div class="col-md-12 col-xs-12">
        <div class="panel-body form">
            <div class="panel panel-grey">
                <div class="panel-heading">{{$title}}</div>
                <div class="panel-body">
                    <ul class="nav nav-tabs">
                        <li class="active" data-event-tab data-tab-data="active">
                            <a href="#active" data-toggle="tab" aria-expanded="true">
                                <i class="icon-basket-loaded icons"></i>&nbsp;Service
                            </a>
                        </li>

                        @foreach($locales as $key => $value)
                            <li class="" data-event-tab data-tab-data="{{$key}}">
                                <a href="#{{$key}}" data-toggle="tab" aria-expanded="false">
                                    <i class="fa fa-language"></i>&nbsp;{{ $value }}
                                </a>
                            </li>
                        @endforeach

                        <li class="" data-event-tab data-tab-data="image">
                            <a href="#image" data-toggle="tab" aria-expanded="false">
                                <i class="icon icon-picture"></i>&nbsp;Cover Image
                            </a>
                        </li>

                        <li class="" data-event-tab data-tab-data="picture">
                            <a href="#picture" data-toggle="tab" aria-expanded="false">
                                <i class="icon icon-picture"></i>&nbsp;Images
                            </a>
                        </li>
                        <li class="" data-event-tab data-tab-data="attachment">
                            <a href="#attachment" data-toggle="tab" aria-expanded="false">
                                <i class="icon-paper-clip icons"></i>&nbsp;Attachments
                            </a>
                        </li>

                    </ul>
                    <div class="tab-content">
                            <div class="tab-pane fade active in" id="active">
                                {!! Form::model( $service , array('route' => ['admin.services.update' , $service] , 'files' => true , 'method' => 'POST' , 'id' => 'FormStore')) !!}
                                <div class="col-md-10 col-xs-12">
                                    <div class="form-group {{ $errors->has('parent') ? 'has-error' : '' }}">
                                        <label class="control-label"><span>Parent</span></label>
                                        <div class="input-icon right">
                                            @if($errors->has('parent'))
                                                <i class="fa fa-exclamation tooltips" data-container="body"></i>
                                            @endif
                                            {!! Form::select('parent' , $parents , null , ['class' => 'form-control input-sm select2', 'style' => 'width: 100%' , 'id' => 'parent_list' ]) !!}
                                            <span class="validation-message-block">{{ $errors->first('parent', ':message') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 col-xs-12">
                                    <div class="md-checkbox" style="margin-top: 30px;">
                                        <input name="is_published" type="checkbox" id="checkbox2" class="md-check" {{$service->is_published == 'Y' ? 'checked' : ''}}>
                                        <label for="checkbox2">
                                            <span class="inc"></span>
                                            <span class="check"></span>
                                            <span class="box"></span> Publish Status</label>
                                    </div>
                                </div>
                                <div class="col-md-12 col-xs-12" style="margin-bottom: 20px">
                                    <div class="form-group">
                                        <label class="edit-label">
                                            <i class="fa fa-globe" aria-hidden="true"></i>
                                            <span>Keywords</span>
                                        </label>

                                        {!! Form::select('keywords[]' , $tags , $selectedTags , ['class' => '', 'multiple' => '"multiple' , 'size' => 1  , 'style' => 'width: 100%' , 'id' => 'keywords' ]) !!}
                                    </div>
                                </div>
                            </div>

                            @foreach($locales as $key => $value)
                                <div class="tab-pane fade" id="{{$key}}">
                                    <div class="col-md-6 col-xs-12">
                                        <div class="form-group {{ $errors->has($key.'.title') ? 'has-error' : '' }}">
                                            <label class="control-label">* {{$value}} Service Title</label>
                                            {!! Form::text( $key.'[title]' , $service->hasTranslation($key) ? $service->translate($key)->title : null , ['class' => 'form-control']) !!}
                                            <span class="validation-message-block">{{ $errors->first($key.'.title', ':message') }}</span>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-xs-12">
                                        <div class="form-group {{ $errors->has($key.'.meta') ? 'has-error' : '' }}">
                                            <label class="control-label">* {{$value}} Meta Description</label>
                                            {!! Form::text( $key.'[meta]' , $service->hasTranslation($key) ? $service->translate($key)->meta : null , ['class' => 'form-control']) !!}
                                            <span class="validation-message-block">{{ $errors->first($key.'.meta', ':message') }}</span>
                                        </div>
                                    </div>

                                    <div class="col-md-12 col-xs-12">
                                        <div class="form-group {{ $errors->has($key.'.desc') ? 'has-error' : '' }}">
                                            <label class="control-label">{{$value}} Description</label>
                                            {!! Form::textarea($key.'[desc]', $service->hasTranslation($key) ? $service->translate($key)->desc : null , array('class' => 'form-control input-sm mce tm_textArea', 'rows' => '5')) !!}
                                            <span class="validation-message-block">{{ $errors->first($key.'.desc', ':message') }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <div class="tab-pane fade" id="image">
                                <div class="col-md-12 col-xs-12">
                                    <div class="custom-image-container">
                                        <img style="width: 500px; height: 400px;" src="{{$service->full_url_image}}"/>
                                    </div>
                                    @if($service->img)
                                        <a href="{{route('admin.services.imageRemove' , $service)}}" class="btn btn-sm btn-danger">Remove image</a>
                                    @else
                                        <input type="file" name="image" class="form-control" accept="image/*" id="file" style="width: 210px">
                                    @endif
                                </div>
                            </div>

                            <input type="hidden" id="store_id" data-id="{{$service->id}}">

                            <div class="tab-pane fade" id="picture">
                                <div class="myDropzone" data-url="{{ route('admin.services.dropZoneUpload')}}">
                                </div>
                            </div>

                            <div class="tab-pane fade" id="attachment">
                                <div id="catalog" class="file-upload-area" style="margin: 25px 13px 25px 6px;">
                                    @include('khadamat::forms._fileUpload' , ['magazine' => $service , 'type' => "catalog"])
                                    <input type="hidden" id="identifier" data-id="{{$service->id}}">
                                </div>

                                <div id="sheet" class="file-upload-area" style="margin: 25px 13px 25px 6px;">
                                    @include('khadamat::forms._fileUpload' , ['magazine' => $service , 'type' => "sheet"])
                                    <input type="hidden" id="identifier-second" data-id="{{$service->id}}">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 col-xs-12">
                                    <div class="col-md-12 col-xs-12">
                                        <hr />
                                        <a href="{{route('admin.services.list')}}" class="btn btn-warning" >
                                            <i class="icon icon-action-undo icons"></i>&nbsp;Cancel
                                        </a>
                                        <button type="button" class="btn btn-success pull-right" id="btnCompanyCreate">
                                            <i class="fa fa-floppy-o" aria-hidden="true"></i>
                                            {{ (isset($service->title) AND $service->title) ? 'Update' : 'Create' }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop


@section('moduleHeader')
<style>
.nav-pills, .nav-tabs{
    margin-bottom: 0px !important;
}
.tab-content{
    background-color: white;
    padding: 20px 10px 10px 10px;
    border: 1px solid #ddd;
    border-top: 0;
}
.dz-remove{
    background-color: #5b0000;
    color: white;
    padding: 1px;
    font-size: 9px !important;
}
.dz-remove:hover{
    text-decoration: none;
    background-color: red;
    color: whitesmoke;
}
.noUi-value-large{
    margin-top: 10px !important;
    font-size: 11px !important;
}
.select2-results__group{
    color: white !important;
    background-color: #364150 !important;
    font-family: Tahoma, Helvetica, Arial !important;
}
.noUi-handle .noUi-tooltip{
    font-size: 11px !important;
    background: #f7f7f7 !important;
    top: -33px !important;
    left: 15px !important;
}
.noUi-connect {
    background: #d8ff00 !important;
}
</style>
<link href="{{asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/plugins/select2/css/select2-bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ Module::asset('khadamat:plugins/dropzone/dropzone.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ Module::asset('khadamat:plugins/noUiSlider/nouislider.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ Module::asset('khadamat:plugins/jqueryfileupload/css/jquery.fileupload.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ Module::asset('khadamat:plugins/Toggle-Switch-Plugin/css/on-off-switch.css')}}" rel="stylesheet" type="text/css" />
{{--<link href="{{ Module::asset('khadamat:plugins/select2/css/select2.min.css')}}" rel="stylesheet" type="text/css" />--}}
<link href="{{ Module::asset('khadamat:css/module.css') }}" rel="stylesheet" type="text/css" />
@stop

@section('moduleFooter')
<script src="{{asset('assets/plugins/jquery-mask/jquery.mask.min.js')}}" type="text/javascript"></script>
<script src="{{ Module::asset('khadamat:plugins/dropzone/dropzone.js') }}" type="application/javascript"></script>
<script src="{{ Module::asset('khadamat:plugins/noUiSlider/nouislider.min.js') }}" type="application/javascript"></script>
<script src="{{ Module::asset('khadamat:plugins/tinymce/tinymce.min.js') }}" type="application/javascript"></script>
<script src="{{ Module::asset('khadamat:plugins/jqueryfileupload/vendor/jquery.ui.widget.js')}}" type="text/javascript"></script>
<script src="{{ Module::asset('khadamat:plugins/jqueryfileupload/jquery.fileupload.js')}}" type="text/javascript"></script>
<script src="{{ Module::asset('khadamat:plugins/Toggle-Switch-Plugin/js/on-off-switch.js')}}" type="text/javascript"></script>
<script src="{{ Module::asset('khadamat:plugins/Toggle-Switch-Plugin/js/on-off-switch-onload.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/plugins/select2/js/select2.min.js')}}" type="text/javascript"></script>
{{--<script src="{{ Module::asset('khadamat:plugins/select2/js/select2.min.js')}}" type="text/javascript"></script>--}}
<script src="{{ Module::asset('khadamat:js/module.js') }}" type="application/javascript"></script>
<script>

var imageGaleryRemovalLink = '{{route('admin.services.dropZone.image.delete')}}';
var images = {!! $images !!};

M6Module.tabInitialize();
M6Module.product();
M6Module.initTinyMCE();
M6Module.ajaxFileUpload();

$('#keywords').select2({
    placeholder: 'Select keywords',
    tags: true
});


$('#price').mask("000,000,000,000,000", {reverse: true});

Dropzone.autoDiscover = false;
var dropZoneUrl = $(".myDropzone").data('url');
$(".myDropzone").dropzone({
    url: dropZoneUrl,
    acceptedFiles: 'image/*',
    autoProcessQueue: true,
    parallelUploads: 1,
    addRemoveLinks: true,
    dictRemoveFileConfirmation: 'Are you sure to remove this picture',
    headers: {
        'X-CSRF-TOKEN': _csrf_token
    },
    init: function () {
        var store_id = $('#store_id').data('id');

        this.on('sending', function(file, xhr, formData){
            formData.append('store_id', store_id);
        });

        this.on("success", function(file, response) {
            var uploadedFile = file.previewElement.querySelector("[data-dz-name]");
            uploadedFile.innerHTML = response.name;
        });

        this.on('removedfile', function (e) {
            toastr.clear();
            var imageId = $.trim($(e.previewElement).find(".dz-filename > span").text());
            console.log(imageId);
            $.ajax({
                url: imageGaleryRemovalLink,
                data: { id: imageId , _token : _csrf_token},
                type: 'POST',
                success: function (data) {
                    if (data.status === "error") {
                        toastr.error(data.message);
                    } else {
                        toastr.success(data.message);
                    }},
                error: function (data) {
                    toastr.error(data.message);
                }
            });
        });

        var _droper = this;
        var FullAddress = PublicPath+"uploads/admins/general-images/";

        $.each(images, function (index, item) {

            var mockFile = { name : item.img , size: item.size};

            _droper.options.addedfile.call(_droper, mockFile);
            _droper.options.thumbnail.call(_droper, mockFile, FullAddress+item.img);

            mockFile.previewElement.classList.add('dz-success');
            mockFile.previewElement.classList.add('dz-complete');
        });
    }
});

new DG.OnOffSwitch({
    el: '#on-off-switch',
    textOn: 'Limit',
    textOff: 'No Limit',
    height:22,
    listener:function(name, checked){
        if (checked) {
            $("#limit-value-box").show();
        } else {
            $("#limit-value-box").hide();
        }
    }
});

</script>
@stop


