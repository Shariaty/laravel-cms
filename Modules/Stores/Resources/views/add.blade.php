@extends('admin.include.layout')

@section('content')
<div class="row">
    <div class="col-md-12 col-xs-12">
        <div class="panel-body form">
            <div class="panel panel-grey">
                <div class="panel-heading">{{$title}}</div>
                <div class="panel-body">
                    {!! Form::open( array('route' => 'admin.stores.save', 'method' => 'POST' , 'id' => 'FormStore')) !!}

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
                            <div class="col-md-4 col-xs-12">
                                <div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">
                                    <label class="control-label">Store Name</label>
                                    {!! Form::text('title' , null , ['class' => 'form-control']) !!}
                                    <span class="validation-message-block">{{ $errors->first('title', ':message') }}</span>
                                </div>
                            </div>
                            {{--<div class="col-md-2 col-xs-12">--}}
                                {{--<div class="form-group {{ $errors->has('state_id') ? 'has-error' : '' }}">--}}
                                    {{--{!! Form::label('state_id', '*State') !!}--}}
                                    {{--{!! Form::select('state_id' , $states , null , ['class' => 'select2 form-control' , 'id' => 'country_list' , 'style' => 'width: 100%']) !!}--}}
                                    {{--<span class="validation-message-block">{{ $errors->first('state_id', ':message') }}</span>--}}
                                {{--</div>--}}
                            {{--</div>--}}
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
                                    {!! Form::select('type[]' , $types , null , ['class' => 'select2 form-control' ,'multiple', 'size' => 1 , 'id' => 'store_type']) !!}
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
                            <div class="text-center" style="padding: 50px;">
                                <img width="100px" src="{{asset('assets/admin/images/icons/info.png')}}">
                                <p>Gallery image upload will be available after creating store, you can add your pictures through edit functionality</p>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="vTour">
                            <div class="text-center" style="padding: 50px;">
                                <img width="100px" src="{{asset('assets/admin/images/icons/info.png')}}">
                                <p>Virtual Tour upload will be available after creating store, you can add your Virtual Tour through edit functionality</p>
                            </div>
                        </div>

                        <div class="col-md-12 col-xs-12 text-left">
                            <hr />
                            <a href="{{route('admin.stores.list')}}" class="btn btn-danger" >Cancel</a>
                            <button type="button" class="btn btn-success" id="btnCompanyCreate">{{ (isset($store->title) AND $store->title) ? 'Update Store' : 'Create Store' }}</button>
                        </div>
                    </div>

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@stop


@section('header')
<link href="{{asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/plugins/select2/css/select2-bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
@stop

@section('footer')
<script src="{{asset('assets/plugins/select2/js/select2.min.js')}}" type="text/javascript"></script>
<script src="{{ Module::asset('stores:js/module.js') }}" type="application/javascript"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyACitKM4wiGaWp7l9edxnXPLDpDvkudJKI&callback=initMap" async defer></script>
<script>
var flagsPatch = '{{ asset('global/plugins/flags/') }}'+'/';
M6Module.countryAndCity();
M6Module.tabInitialize();
M6Module.store();
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
</script>
@stop


