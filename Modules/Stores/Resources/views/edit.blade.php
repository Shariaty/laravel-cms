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
                                    <i class="icon-basket-loaded icons"></i>&nbsp;Store
                                </a>
                            </li>
                            <li class="" data-event-tab data-tab-data="picture">
                                <a href="#picture" data-toggle="tab" aria-expanded="false">
                                    <i class="icon icon-picture"></i>&nbsp;Gallery
                                </a>
                            </li>
                            <li class="" data-event-tab data-tab-data="vTour">
                                <a href="#vTour" data-toggle="tab" aria-expanded="false">
                                    <i class="fa fa-globe"></i>&nbsp;Virtual Tour
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane fade active in" id="active">
                                {!! Form::model( $store , array('route' => ['admin.stores.update' , $store] , 'method' => 'POST' , 'id' => 'FormStore')) !!}
                                <div class="col-md-4 col-xs-12">
                                    <div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">
                                        <label class="control-label">Store Name</label>
                                        {!! Form::text('title' , null , ['class' => 'form-control']) !!}
                                        <span class="validation-message-block">{{ $errors->first('title', ':message') }}</span>
                                    </div>
                                </div>

                                <div class="col-md-2 col-xs-12">
                                    <div class="form-group {{ $errors->has('city_id') ? 'has-error' : '' }}">
                                        {!! Form::label('city_id', '*City') !!}
                                        {!! Form::select('city_id' , $cities , null , ['class' => 'select2 form-control city_list' , 'id' => 'city_list' , 'style' => 'width: 100%']) !!}
                                        <span class="validation-message-block">{{ $errors->first('city_id', ':message') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-2 col-xs-12">
                                    <div class="form-group {{ $errors->has('city_id') ? 'has-error' : '' }}">
                                        {!! Form::label('district', '*District') !!}
                                        {!! Form::select('district' , $districts , null , ['class' => 'select2 form-control district_list' , 'id' => 'district_list' , 'style' => 'width: 100%']) !!}
                                        <span class="validation-message-block">{{ $errors->first('district', ':message') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-3 col-xs-12">
                                    <div class="form-group {{ $errors->has('phone') ? 'has-error' : '' }}">
                                        <label class="control-label">*Telephone</label>
                                        <div class="input-icon right">
                                            {!! Form::text('phone' , null , ['class' => 'form-control', 'data-number']) !!}
                                            <span class="validation-message-block">{{ $errors->first('phone', ':message') }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-1 col-xs-12">
                                    <div class="form-group {{ $errors->has('rate') ? 'has-error' : '' }}">
                                        {!! Form::label('rate', '*Rate') !!}
                                        {!! Form::select('rate' , [1 => 1 , 2 => 2 , 3 => 3 , 4 => 4 , 5 => 5] , null , ['class' => 'select2 form-control rate_list' , 'style' => 'width: 100%']) !!}
                                        <span class="validation-message-block">{{ $errors->first('rate', ':message') }}</span>
                                    </div>
                                </div>

                                <div class="col-md-12 col-xs-12">
                                    <div class="form-group {{ $errors->has('type') ? 'has-error' : '' }}">
                                        {!! Form::label('type', '*Type') !!}
                                        {!! Form::select('type[]' , $types , $selectedCategory , ['class' => 'select2 form-control' ,'multiple', 'size' => 1 , 'id' => 'store_type']) !!}
                                        <span class="validation-message-block">{{ $errors->first('type', ':message') }}</span>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 col-xs-12">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="form-group {{ $errors->has('desc') ? 'has-error' : '' }}">
                                                <label class="control-label">*About the Store</label>
                                                {!! Form::textarea('desc', null, array('id' => 'tm_textArea' , 'class' => 'form-control input-sm', 'rows' => '5')) !!}
                                                <span class="validation-message-block">{{ $errors->first('desc', ':message') }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-xs-12">
                                            <div class="form-group {{ $errors->has('address') ? 'has-error' : '' }}">
                                                <label class="control-label">*Address</label>
                                                {!! Form::textarea('address', null, array('id' => 'address' , 'class' => 'form-control input-sm', 'rows' => '5', 'maxlength' => '500')) !!}
                                                <span class="validation-message-block">{{ $errors->first('address', ':message') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xs-12">
                                        <div>
                                            <label class="control-label">
                                                <i class="icon-map icons"></i>&nbsp;
                                                <span>Location <span style="font-size: 0.8em; color: grey;">(drag the pointer and save the form)</span></span>
                                            </label>
                                            <div id="map" style="width: 97% ;height: 245px; position: relative; overflow: hidden;">
                                            </div>
                                            <input id="inputlat" name="lat" type="hidden" value="{{ isset($store->lat) ? $store->lat : 35.762794 }}" data-lat="{{ isset($store->lat) ? $store->lat : 35.762794}}">
                                            <input id="inputlng" name="lng" type="hidden" value="{{ isset($store->lng) ? $store->lng : 51.457748 }}" data-lng="{{ isset($store->lng) ? $store->lng : 51.457748 }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="picture">

                                <div class="myDropzone" data-url="{{ route('admin.stores.dropZoneUpload')}}">

                                </div>

                            </div>

                            <div class="tab-pane fade" id="vTour">
                                <div class="file-upload-area" style="margin: 25px 13px 25px 6px;">
                                    @include('stores::forms._fileUpload' , ['magazine' => $store])
                                    <input type="hidden" id="identifier" data-id="{{$store->st_number}}">
                                </div>
                            </div>

                            <input type="hidden" id="store_id" data-id="{{$store->st_number}}">

                            <div class="row">
                                <div class="col-md-12 col-xs-12">
                                    <div class="col-md-12 col-xs-12">
                                        <hr />
                                        <a href="{{route('admin.stores.list')}}" class="btn btn-warning" >
                                            <i class="icon icon-action-undo icons"></i>&nbsp;Cancel
                                        </a>
                                        <button type="button" class="btn btn-success pull-right" id="btnCompanyCreate">
                                            <i class="fa fa-floppy-o" aria-hidden="true"></i>&nbsp;
                                            {{ (isset($store->title) AND $store->title) ? 'Update Store' : 'Create Store' }}
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
</style>
    <link href="{{asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/plugins/select2/css/select2-bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{ Module::asset('stores:plugins/jqueryfileupload/css/jquery.fileupload.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{ Module::asset('stores:plugins/dropzone/dropzone.css')}}" rel="stylesheet" type="text/css" />
    {{--<link href="{{ Module::asset('stores:plugins/dropzone/basic.css')}}" rel="stylesheet" type="text/css" />--}}
@stop

@section('moduleFooter')
    <script src="{{asset('assets/plugins/select2/js/select2.min.js')}}" type="text/javascript"></script>
    <script src="{{ Module::asset('stores:plugins/dropzone/dropzone.js') }}" type="application/javascript"></script>
    <script src="{{ Module::asset('stores:plugins/jqueryfileupload/vendor/jquery.ui.widget.js')}}" type="text/javascript"></script>
    <script src="{{ Module::asset('stores:plugins/jqueryfileupload/jquery.fileupload.js')}}" type="text/javascript"></script>
    <script src="{{ Module::asset('stores:plugins/jqueryfileupload/jquery.fileupload-process.js')}}" type="text/javascript"></script>
    <script src="{{ Module::asset('stores:plugins/jqueryfileupload/jquery.fileupload-validate.js')}}" type="text/javascript"></script>
    <script src="{{ Module::asset('stores:js/module.js') }}" type="application/javascript"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyACitKM4wiGaWp7l9edxnXPLDpDvkudJKI&callback=initMap" async defer></script>
    <script>

    var flagsPatch = '{{ asset('global/plugins/flags/') }}'+'/';
    var imageGaleryRemovalLink = '{{route('admin.stores.dropZone.image.delete')}}';
    var images = {!! $images !!};

    M6Module.tabInitialize();
    M6Module.countryAndCity();
    M6Module.ajaxFileUpload();
    M6Module.store();

    //Map
    var oldLat = parseFloat($('#inputlat').data('lat'));
    var oldLng = parseFloat($('#inputlng').data('lng'));
    function initMap() {
        var myLatLng = { lat:oldLat , lng:oldLng };
        var pinImage = new google.maps.MarkerImage("https://maps.google.com/mapfiles/ms/icons/red-dot.png");

        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 12,
            center: myLatLng
        });
        var marker = new google.maps.Marker({
            position: myLatLng,
            icon: pinImage,
            map: map,
            draggable: true,
            title: 'Drag Me'
        });

        google.maps.event.addListener(marker, 'dragend', function (event) {
            $("#inputlat").val(this.getPosition().lat().toFixed(6));
            $("#inputlng").val(this.getPosition().lng().toFixed(6));
        });
    }
    //Map


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
            var FullAddress = PublicPath+"uploads/admins/stores/images/";

            $.each(images, function (index, item) {

                var mockFile = { name : item.img , size: item.size};

                _droper.options.addedfile.call(_droper, mockFile);
                _droper.options.thumbnail.call(_droper, mockFile, FullAddress+item.img);

                mockFile.previewElement.classList.add('dz-success');
                mockFile.previewElement.classList.add('dz-complete');
            });
        }
    });


    </script>
@stop


