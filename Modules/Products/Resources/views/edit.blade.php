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
                                <i class="icon-basket-loaded icons"></i>&nbsp;Product
                            </a>
                        </li>
                        <li class="" data-event-tab data-tab-data="picture">
                            <a href="#picture" data-toggle="tab" aria-expanded="false">
                                <i class="icon icon-picture"></i>&nbsp;Images
                            </a>
                        </li>
                        <li class="" data-event-tab data-tab-data="attachment">
                            <a href="#attachment" data-toggle="tab" aria-expanded="false">
                                <i class="icon-paper-clip icons"></i>&nbsp;Attachment
                            </a>
                        </li>

                    </ul>
                    <div class="tab-content">
                            <div class="tab-pane fade active in" id="active">
                                {!! Form::model( $product , array('route' => ['admin.products.update' , $product] , 'method' => 'POST' , 'id' => 'FormStore')) !!}
                                <div class="col-md-6 col-xs-12">
                                    <div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">
                                        <label class="control-label">Product Title</label>
                                        {!! Form::text('title' , null , ['class' => 'form-control']) !!}
                                        <span class="validation-message-block">{{ $errors->first('title', ':message') }}</span>
                                    </div>
                                </div>

                                <div class="col-md-4 col-xs-12">
                                    <div class="form-group {{ $errors->has('category') ? 'has-error' : '' }}">
                                        {!! Form::label('category', '* Category') !!}
                                        {!! Form::select('category' , $categories , $product->category_id , ['class' => 'select2 form-control' , 'id' => 'cat_list' , 'placeholder' => '']) !!}
                                        <span class="validation-message-block">{{ $errors->first('category', ':message') }}</span>
                                    </div>
                                </div>

                                <div class="col-md-2 col-xs-12">
                                    <div class="md-checkbox" style="margin-top: 30px;">
                                        <input name="is_published" type="checkbox" id="checkbox2" class="md-check" {{$product->is_published == 'Y' ? 'checked' : ''}}>
                                        <label for="checkbox2">
                                            <span class="inc"></span>
                                            <span class="check"></span>
                                            <span class="box"></span> Publish Status</label>
                                    </div>
                                </div>

                                <div class="col-md-12 col-xs-12">
                                    <div class="form-group {{ $errors->has('o_cat') ? 'has-error' : '' }}">
                                        {!! Form::label('o_cat', 'Other Related Categories') !!}
                                        {!! Form::select('o_cat[]' , $categories  , $selectedOtherCategories , ['class' => 'select2 form-control' , 'size'=> 1 , 'multiple' , 'style' => 'width: 100%' , 'id' => 'o_cat_list' ]) !!}
                                        <span class="validation-message-block">{{ $errors->first('o_cat', ':message') }}</span>
                                    </div>
                                </div>

                                <div class="col-md-12 col-xs-12">
                                    <div class="form-group {{ $errors->has('options') ? 'has-error' : '' }}">
                                        {!! Form::label('options', '* Options') !!}
                                        {!! Form::select('options[]' , []  , null , ['class' => 'select2 form-control' , 'size'=> 1 , 'multiple' , 'style' => 'width: 100%' , 'id' => 'options_list' ]) !!}
                                        <span class="validation-message-block">{{ $errors->first('options', ':message') }}</span>
                                    </div>
                                </div>

                                <div class="col-md-2 col-xs-12">
                                    <div class="form-group {{ $errors->has('type') ? 'has-error' : '' }}">
                                        {!! Form::label('type', '* Type of product') !!}
                                        {!! Form::select('type' , [ 1 => 'محصول' , 2 => 'محصول ترکیبی' , 3 => 'مواد اولیه']  , null , ['class' => 'select2 form-control' , 'style' => 'width: 100%', 'id' => 'raw_material']) !!}
                                        <span class="validation-message-block">{{ $errors->first('type', ':message') }}</span>
                                    </div>
                                </div>

                                <div class="col-md-2 col-xs-12">
                                    <div class="form-group {{ $errors->has('mainUnit_id') ? 'has-error' : '' }}">
                                        {!! Form::label('mainUnit_id', '* Main Unit') !!}
                                        {!! Form::select('mainUnit_id' , $unitList , $product->mainUnit_id ? $product->mainUnit_id : 1 , ['class' => 'select2 form-control' , 'id' => 'unit_list' , 'placeholder' => '']) !!}
                                        <span class="validation-message-block">{{ $errors->first('mainUnit_id', ':message') }}</span>
                                    </div>
                                </div>

                                <div class="col-md-2 col-xs-12">
                                    <div class="form-group {{ $errors->has('subUnit') ? 'has-error' : '' }}">
                                        {!! Form::label('subUnit', '* Sub Unit') !!}
                                        {!! Form::select('subUnit' , $subUnitList , $product->subUnit_id ? $product->subUnit_id : 0 , ['class' => 'select2 form-control' , 'id' => 'subUnit_list' , 'placeholder' => '']) !!}
                                        <span class="validation-message-block">{{ $errors->first('subUnit', ':message') }}</span>
                                    </div>
                                </div>

                                <div class="col-md-3 col-xs-12">
                                    <div class="form-group {{ $errors->has('conversion_factor') ? 'has-error' : '' }}">
                                        <label class="control-label">Conversion</label>
                                        {!! Form::number('conversion_factor' , null , ['class' => 'form-control' , $product->subUnit_id == 0 ? 'disabled' : '' , 'min' => "1" , 'id' => 'conversion_factor']) !!}
                                        <span class="validation-message-block">{{ $errors->first('conversion_factor', ':message') }}</span>
                                    </div>
                                </div>

                                <div class="col-md-3 col-xs-12">
                                    <div class="form-group {{ $errors->has('manufacturer') ? 'has-error' : '' }}">
                                        {!! Form::label('manufacturer', '* Manufacturer') !!}
                                        {!! Form::select('manufacturer' , []  , null , ['class' => 'select2 form-control' , 'style' => 'width: 100%' , 'id' => 'manufacturer_list' ]) !!}
                                        <span class="validation-message-block">{{ $errors->first('manufacturer', ':message') }}</span>
                                    </div>
                                </div>

                                <div class="col-md-12 col-xs-12">
                                    <span style="display: inline-flex; margin-top: 15px; margin-bottom: 15px;">
                                        <span><strong>Limitation on product Sale :</strong></span>&nbsp;
                                        <span><input type="checkbox" id="on-off-switch" name="has_limit" {{$product->has_limit === 'Y' ? 'checked' : ''}}></span>

                                        <div id="limit-value-box" style="margin-left: 20px; margin-top: -6px; display: {{ $product->has_limit === 'Y' ? 'block' : 'none' }};">
                                            <div style="display: inline-flex;">

                                            <div class="form-group {{ $errors->has('limitValue') ? 'has-error' : '' }}">
                                                {!! Form::number('limitValue' , $product->limit_value ? $product->limit_value : null , ['class' => 'form-control' , 'id' => 'limitValue' , 'placeholder' => 'set the limit to']) !!}
                                                <span class="validation-message-block">{{ $errors->first('limitValue', ':message') }}</span>
                                            </div>

                                            <span style="margin: 5px"><strong>for each user per :</strong></span>

                                            <div class="form-group {{ $errors->has('limitTime') ? 'has-error' : '' }}">
                                                {!! Form::select('limitTime' , generateLimitTimingList()  , $product->limit_time ? $product->limit_time : null , ['class' => 'select2 form-control' , 'style' => 'width: 100%' , 'id' => 'limit_time' ]) !!}
                                                <span class="validation-message-block">{{ $errors->first('limitTime', ':message') }}</span>
                                            </div>

                                            </div>
                                        </div>

                                    </span>
                                </div>

                                <div class="col-md-12 col-xs-12">
                                   <div id="slider-handles" style="margin-top:50px ; margin-bottom: 70px"></div>
                                   <input type="hidden" name="ageRange" id="ageRange" value="">
                                </div>

                                <div class="col-md-12 col-xs-12">
                                    <div class="form-group {{ $errors->has('desc') ? 'has-error' : '' }}">
                                        <label class="control-label">* Product Description</label>
                                        {!! Form::textarea('desc', null, array('id' => 'tm_textArea' , 'class' => 'form-control input-sm mce', 'rows' => '5')) !!}
                                        <span class="validation-message-block">{{ $errors->first('desc', ':message') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="picture">
                                <div class="myDropzone" data-url="{{ route('admin.products.dropZoneUpload')}}">
                                </div>
                            </div>

                            <div class="tab-pane fade" id="attachment">
                                <div class="file-upload-area" style="margin: 25px 13px 25px 6px;">
                                    @include('products::forms._fileUpload' , ['magazine' => $product])
                                    <input type="hidden" id="identifier" data-id="{{$product->id}}">
                                </div>
                            </div>

                            <input type="hidden" id="store_id" data-id="{{$product->sku}}">

                            <div class="row">
                                <div class="col-md-12 col-xs-12">
                                    <div class="col-md-12 col-xs-12">
                                        <hr />
                                        <a href="{{route('admin.products.list')}}" class="btn btn-warning" >
                                            <i class="icon icon-action-undo icons"></i>&nbsp;Cancel
                                        </a>
                                        <button type="button" class="btn btn-success pull-right" id="btnCompanyCreate">
                                            <i class="fa fa-floppy-o" aria-hidden="true"></i>
                                            {{ (isset($product->title) AND $product->title) ? 'Update Product' : 'Create Product' }}
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
<link href="{{ Module::asset('products:plugins/dropzone/dropzone.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ Module::asset('products:plugins/noUiSlider/nouislider.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ Module::asset('products:plugins/jqueryfileupload/css/jquery.fileupload.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ Module::asset('products:plugins/Toggle-Switch-Plugin/css/on-off-switch.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ Module::asset('products:css/module.css') }}" rel="stylesheet" type="text/css" />
@stop

@section('moduleFooter')
<script src="{{asset('assets/plugins/select2/js/select2.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/plugins/jquery-mask/jquery.mask.min.js')}}" type="text/javascript"></script>
<script src="{{ Module::asset('products:plugins/dropzone/dropzone.js') }}" type="application/javascript"></script>
<script src="{{ Module::asset('products:plugins/noUiSlider/nouislider.min.js') }}" type="application/javascript"></script>
<script src="{{ Module::asset('products:plugins/tinymce/tinymce.min.js') }}" type="application/javascript"></script>
<script src="{{ Module::asset('products:plugins/jqueryfileupload/vendor/jquery.ui.widget.js')}}" type="text/javascript"></script>
<script src="{{ Module::asset('products:plugins/jqueryfileupload/jquery.fileupload.js')}}" type="text/javascript"></script>
<script src="{{ Module::asset('products:plugins/Toggle-Switch-Plugin/js/on-off-switch.js')}}" type="text/javascript"></script>
<script src="{{ Module::asset('products:plugins/Toggle-Switch-Plugin/js/on-off-switch-onload.js')}}" type="text/javascript"></script>
<script src="{{ Module::asset('products:js/module.js') }}" type="application/javascript"></script>
<script>

var imageGaleryRemovalLink = '{{route('admin.products.dropZone.image.delete')}}';
var images = {!! $images !!};
var list = {!! json_encode($optionsList) !!};
var manulist = {!! json_encode($manufactureList) !!};
var age = {!! json_encode($ageFinal) !!};
var ageEnable = {!! json_encode($ageEnable) !!};

M6Module.tabInitialize();
M6Module.product();
M6Module.initTinyMCE();
M6Module.ajaxFileUpload();

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
        var FullAddress = PublicPath+"uploads/admins/products/images/";

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


