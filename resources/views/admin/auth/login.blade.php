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
<div class="logo"></div>

<div class="content">
    <!-- BEGIN LOGIN FORM -->
    {!! Form::open(array('route' => 'admin.login' , 'method' => 'post' , 'class' => 'login-form')) !!}
        <div class="logo" style="margin-top: -10px;">
            <a href="{{ url('/') }}">
                <img src="{{asset('assets/admin/images/logo-transparent.png')}}" alt="">
            </a>
        </div>
    <p>Hasssan</p>
        <div class="form-group">
            <label class="control-label visible-ie8 visible-ie9">{{trans('admin.EMAIL')}}</label>
            <div class="input-icon">
                <i class="fa fa-user"></i>
                {!! Form::text('email', null, array('class' => 'form-control placeholder-no-fix' , 'placeholder' => trans('admin.EMAIL') , 'autocomplete' => 'on')) !!}
                <span class="validation-message-block">{{ $errors->first('email', ':message') }}</span>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label visible-ie8 visible-ie9">{{trans('admin.PASSWORD')}}</label>
            <div class="input-icon">
                <i class="fa fa-lock"></i>
                {!! Form::password('password', array('class' => 'form-control placeholder-no-fix' , 'placeholder' => trans('admin.PASSWORD') , 'autocomplete' => 'off')) !!}
                <span class="validation-message-block">{{ $errors->first('password', ':message') }}</span>
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
                {!! Form::text('captcha', null , array('class' => 'form-control placeholder-no-fix', 'id' => 'CaptchaCode' , 'placeholder' =>  trans('admin.CAPTCHA') )) !!}
                <span class="validation-message-block">{{ $errors->first('captcha', ':message') }}</span>
            </div>
        </div>
        <div class="form-actions">
            <label class="rememberme mt-checkbox mt-checkbox-outline">
                <input type="checkbox" name="remember" value="1" />@lang('admin.REMEMBER_ME_BTN')
                <span></span>
            </label>
            <button type="submit" class="btn dark  pull-right">@lang('admin.LOGIN')</button>
        </div>
        <div class="create-account">
            <div style="margin-bottom: 30px;">
            <a class="btn btn-xs dark  btn-outline btn-no-border pull-right" href="{{ route('password.reset' , array( '' , 'redirectUrl' => url()->current()) ) }}" style="text-align: right;" >@lang('admin.FORGOT_YOUR_PASSWORD_BTN')</a>
            </div>
        </div>
    {!! Form::close() !!}
    <!-- END LOGIN FORM -->
</div>
<div class="copyright">@lang('admin.ADMINISTRATION_COPYRIGHT_TEXT')</div>

</div>
@stop


