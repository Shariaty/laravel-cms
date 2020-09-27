@extends('admin.auth.include.layout')

@section('header')
<link href="{{asset('assets/admin/auth/css/login-4.css')}}" rel="stylesheet" type="text/css" />
<style>

    .BDC_ReloadLink {
        position: absolute !important;
        top: 20px  !important;
        right: 15px !important;
    }

    .BDC_SoundLink{
        position: absolute !important;
        top: 20px  !important;
        right: -10px  !important;
    }
    .BDC_CaptchaImageDiv{
        position: absolute;
    }
</style>
@stop

@section('content')

<div class="login" style="background-color:transparent !important; margin-bottom: 20px; opacity: 0;">
    <div class="content">
        <!-- BEGIN LOGIN FORM -->
        {!! Form::open(array('route' => 'password.email' , 'method' => 'post' , 'class' => 'login-form')) !!}
        <div class="logo">
            <h4 style="color: darkslategray">Password Recovery</h4>
        </div>
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif
        <div class="form-group">
            <label class="control-label visible-ie8 visible-ie9">{{trans('admin.EMAIL')}}</label>
            <div class="input-icon">
                <i class="fa fa-user"></i>
                {!! Form::text('email', null, array('class' => 'form-control placeholder-no-fix' , 'placeholder' => trans('admin.EMAIL') , 'autocomplete' => 'off')) !!}
                <span class="validation-message-block">{{ $errors->first('email', ':message') }}</span>
            </div>
        </div>
        <div class="form-group">
            <div class="row captcha-box" style="margin-bottom: 10px; height: 68px;">
                <div class="col-md-12 col-xs-12" style="margin-left: -15px;">
                    {!! captcha_image_html('ExampleCaptcha') !!}
                </div>
            </div>
            <label class="control-label visible-ie8 visible-ie9">{{trans('admin.CAPTCHA')}}</label>
            <div class="input-icon">
                <i class="fa fa-shield"></i>
                {!! Form::text('captcha', null , array('class' => 'form-control placeholder-no-fix',  'id' => 'CaptchaCode' , 'placeholder' =>  trans('admin.CAPTCHA') )) !!}
                <span class="validation-message-block">{{ $errors->first('captcha', ':message') }}</span>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn dark  pull-right">
                Send Recovery Password Link
            </button>
            <a class="btn btn-warning btn-outline pull-left" href="{{ (!empty($_GET['redirectUrl'])) ? $_GET['redirectUrl'] : route('admin.login')}}" id="register-button">@lang('admin.CANCEL_BTN')</a>
            <div class="clearfix"></div>
        </div>
    {!! Form::close() !!}
    <!-- END LOGIN FORM -->
    </div>
    <div class="copyright">@lang('admin.ADMINISTRATION_COPYRIGHT_TEXT')</div>

</div>

@stop

