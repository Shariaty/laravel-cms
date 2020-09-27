@extends('admin.include.layout')
@section('content')

<div class="row">
    <div class="col-md-12 col-xs-12">
        <div class="panel-body form">
            <div class="panel panel-grey">
                <div class="panel-heading">{{$title}}</div>
                <div class="panel-body">
                    {!! Form::open(array('route' => 'admin.site.settings', 'method' => 'POST')) !!}
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-3 col-xs-12">
                                <div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">
                                    <label class="control-label">
                                        <span>Site Name</span>
                                    </label>
                                    <div class="input-icon right">
                                        @if($errors->has('title'))
                                            <i class="fa fa-exclamation tooltips" data-container="body"></i>
                                        @endif
                                        {!! Form::text('title' , $siteSettings->title->value ? $siteSettings->title->value : null , ['class' => 'form-control' , 'placeholder' => 'Site name']) !!}
                                        <span class="validation-message-block">{{ $errors->first('title', ':message') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-xs-12">
                                <div class="form-group {{ $errors->has('description') ? 'has-error' : '' }}">
                                    <label class="control-label">
                                        <span>Site Description</span>
                                    </label>
                                    <div class="input-icon right">
                                        @if($errors->has('description'))
                                            <i class="fa fa-exclamation tooltips" data-container="body"></i>
                                        @endif
                                        {!! Form::text('description' , $siteSettings->description->value ? $siteSettings->description->value : null , ['class' => 'form-control', 'placeholder' => 'Description']) !!}
                                        <span class="validation-message-block">{{ $errors->first('description', ':message') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-xs-12">
                                <div class="form-group {{ $errors->has('dirhamRate') ? 'has-error' : '' }}">
                                    <label class="control-label">
                                        <span>AED Rate ( convert to iranian toman)</span>
                                    </label>
                                    <div class="input-icon right">
                                        @if($errors->has('dirhamRate'))
                                            <i class="fa fa-exclamation tooltips" data-container="body"></i>
                                        @endif
                                        {!! Form::text('dirhamRate' , $siteSettings->dirhamRate->value ? $siteSettings->dirhamRate->value : null , ['class' => 'form-control' , 'placeholder' => 'Dirham Rate']) !!}
                                        <span class="validation-message-block">{{ $errors->first('dirhamRate', ':message') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 col-xs-12">
                                <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                                    <label class="control-label">
                                        <span>Email</span>
                                    </label>
                                    <div class="input-icon right">
                                        @if($errors->has('email'))
                                            <i class="fa fa-exclamation tooltips" data-container="body"></i>
                                        @endif
                                        {!! Form::text('email' , $siteSettings->email->value ? $siteSettings->email->value : null , ['class' => 'form-control', 'placeholder' => 'Email Address']) !!}
                                        <span class="validation-message-block">{{ $errors->first('email', ':message') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-xs-12">
                                <div class="form-group {{ $errors->has('phone') ? 'has-error' : '' }}">
                                    <label class="control-label">
                                        <span>Phone Number</span>
                                    </label>
                                    <div class="input-icon right">
                                        @if($errors->has('phone'))
                                            <i class="fa fa-exclamation tooltips" data-container="body"></i>
                                        @endif
                                        {!! Form::text('phone' , $siteSettings->phone->value ? $siteSettings->phone->value : null, ['class' => 'form-control', 'placeholder' => 'Phone Number']) !!}
                                        <span class="validation-message-block">{{ $errors->first('phone', ':message') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-xs-12">
                                <div class="form-group {{ $errors->has('mobile') ? 'has-error' : '' }}">
                                    <label class="control-label">
                                        <span>Cell phone</span>
                                    </label>
                                    <div class="input-icon right">
                                        @if($errors->has('mobile'))
                                            <i class="fa fa-exclamation tooltips" data-container="body"></i>
                                        @endif
                                        {!! Form::text('mobile' , $siteSettings->mobile->value ? $siteSettings->mobile->value : null, ['class' => 'form-control', 'placeholder' => 'Cellphone Number']) !!}
                                        <span class="validation-message-block">{{ $errors->first('mobile', ':message') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-xs-12">
                                <div class="form-group {{ $errors->has('fax') ? 'has-error' : '' }}">
                                    <label class="control-label">
                                        <span>Fax Number</span>
                                    </label>
                                    <div class="input-icon right">
                                        @if($errors->has('fax'))
                                            <i class="fa fa-exclamation tooltips" data-container="body"></i>
                                        @endif
                                        {!! Form::text('fax' , $siteSettings->fax->value ? $siteSettings->fax->value : null, ['class' => 'form-control', 'placeholder' => 'Fax Number']) !!}
                                        <span class="validation-message-block">{{ $errors->first('fax', ':message') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 col-xs-12">
                                <div class="form-group {{ $errors->has('address') ? 'has-error' : '' }}">
                                    <label class="control-label">
                                        <span>Postal address</span>
                                    </label>
                                    <div class="input-icon right">
                                        @if($errors->has('address'))
                                            <i class="fa fa-exclamation tooltips" data-container="body"></i>
                                        @endif
                                        {!! Form::textarea('address' , $siteSettings->address->value ? $siteSettings->address->value : null, ['class' => 'form-control', 'placeholder' => 'Postal Address' , 'rows' => '8']) !!}
                                        <span class="validation-message-block">{{ $errors->first('address', ':message') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-xs-12">
                                <label class="control-label">
                                    <span>Site Location <span style="font-size: 0.8em; color: grey;">(drag the pointer and save the form)</span></span>
                                </label>
                                <div id="map" style="height: 166px; position: relative; overflow: hidden;">
                                </div>
                            </div>
                            <input id="inputlat" name="lat" type="hidden" value="{{ $siteSettings->lat->value ? $siteSettings->lat->value : 35.762794 }}" data-lat="{{$siteSettings->lat->value ? $siteSettings->lat->value : 35.762794}}">
                            <input id="inputlng" name="lng" type="hidden" value="{{ $siteSettings->lng->value ? $siteSettings->lng->value : 51.457748 }}" data-lng="{{ $siteSettings->lng->value ? $siteSettings->lng->value : 51.457748 }}">

                        </div>
                        <div class="row">
                            <div class="col-md-4 col-xs-12">
                                <div class="form-group {{ $errors->has('profile_telegram') ? 'has-error' : '' }}">
                                    <label class="control-label">
                                        <span>Telegram Url</span>
                                    </label>
                                    <div class="input-icon right">
                                        @if($errors->has('profile_telegram'))
                                            <i class="fa fa-exclamation tooltips" data-container="body"></i>
                                        @endif
                                        {!! Form::text('profile_telegram' , $siteSettings->profile_telegram->value ? $siteSettings->profile_telegram->value : null , ['class' => 'form-control', 'placeholder' => 'Telegram Url']) !!}
                                        <span class="validation-message-block">{{ $errors->first('profile_telegram', ':message') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 col-xs-12">
                                <div class="form-group {{ $errors->has('profile_facebook') ? 'has-error' : '' }}">
                                    <label class="control-label">
                                        <span>Facebook Url</span>
                                    </label>
                                    <div class="input-icon right">
                                        @if($errors->has('profile_facebook'))
                                            <i class="fa fa-exclamation tooltips" data-container="body"></i>
                                        @endif
                                        {!! Form::text('profile_facebook' , $siteSettings->profile_facebook->value ? $siteSettings->profile_facebook->value : null , ['class' => 'form-control', 'placeholder' => 'Facebook Url']) !!}
                                        <span class="validation-message-block">{{ $errors->first('profile_facebook', ':message') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 col-xs-12">
                                <div class="form-group {{ $errors->has('profile_twitter') ? 'has-error' : '' }}">
                                    <label class="control-label">
                                        <span>Twitter Url</span>
                                    </label>
                                    <div class="input-icon right">
                                        @if($errors->has('profile_twitter'))
                                            <i class="fa fa-exclamation tooltips" data-container="body"></i>
                                        @endif
                                        {!! Form::text('profile_twitter' , $siteSettings->profile_twitter->value ? $siteSettings->profile_twitter->value : null , ['class' => 'form-control', 'placeholder' => 'Twitter Url']) !!}
                                        <span class="validation-message-block">{{ $errors->first('profile_twitter', ':message') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 col-xs-12">
                                <div class="form-group {{ $errors->has('profile_linkedin') ? 'has-error' : '' }}">
                                    <label class="control-label">
                                        <span>Linkedin Url</span>
                                    </label>
                                    <div class="input-icon right">
                                        @if($errors->has('profile_linkedin'))
                                            <i class="fa fa-exclamation tooltips" data-container="body"></i>
                                        @endif
                                        {!! Form::text('profile_linkedin' , $siteSettings->profile_linkedin->value ? $siteSettings->profile_linkedin->value : null , ['class' => 'form-control', 'placeholder' => 'Linkedin Url']) !!}
                                        <span class="validation-message-block">{{ $errors->first('profile_linkedin', ':message') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 col-xs-12">
                                <div class="form-group {{ $errors->has('profile_instagram') ? 'has-error' : '' }}">
                                    <label class="control-label">
                                        <span>Instagram Url</span>
                                    </label>
                                    <div class="input-icon right">
                                        @if($errors->has('profile_instagram'))
                                            <i class="fa fa-exclamation tooltips" data-container="body"></i>
                                        @endif
                                        {!! Form::text('profile_instagram' , $siteSettings->profile_instagram->value ? $siteSettings->profile_instagram->value : null , ['class' => 'form-control', 'placeholder' => 'Instagram Url']) !!}
                                        <span class="validation-message-block">{{ $errors->first('profile_instagram', ':message') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 col-xs-12">
                                <div class="form-group {{ $errors->has('profile_google_plus') ? 'has-error' : '' }}">
                                    <label class="control-label">
                                        <span>Google+ Url</span>
                                    </label>
                                    <div class="input-icon right">
                                        @if($errors->has('profile_google_plus'))
                                            <i class="fa fa-exclamation tooltips" data-container="body"></i>
                                        @endif
                                        {!! Form::text('profile_google_plus' , $siteSettings->profile_google_plus->value ? $siteSettings->profile_google_plus->value : null , ['class' => 'form-control', 'placeholder' => 'Google+ Url']) !!}
                                        <span class="validation-message-block">{{ $errors->first('profile_google_plus', ':message') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 col-xs-12">
                        <hr/>
                        <div class="">
                            <div class="pull-right">
                                <a href="{{route('admin.dashboard')}}" class="btn btn-danger">Cancel</a>
                                <button type="submit" class="btn btn-success">Save</button>
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>

@stop


@section('footer')
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyACitKM4wiGaWp7l9edxnXPLDpDvkudJKI&callback=initMap" async defer></script>

    <script>
        var oldLat = parseFloat($('#inputlat').data('lat'));
        var oldLng = parseFloat($('#inputlng').data('lng'));

        function initMap() {
        var myLatLng = { lat:oldLat , lng:oldLng };
        var pinImage = new google.maps.MarkerImage("https://maps.google.com/mapfiles/ms/icons/green-dot.png");

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



