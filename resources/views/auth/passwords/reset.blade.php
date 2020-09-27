@extends('admin.auth.include.layout')

@section('header')
<link href="{{asset('assets/admin/auth/css/login-4.css')}}" rel="stylesheet" type="text/css" />
@stop

@section('content')


<div class="login" style="background-color:transparent !important; margin-bottom: 20px; opacity: 0;">
    <div class="content">
        <!-- BEGIN LOGIN FORM -->
        {!! Form::open(array('route' => 'password.reset.post' , 'method' => 'post' , 'class' => 'login-form')) !!}
        <input type="hidden" name="token" value="{{ $token }}">
        <div class="logo">
            <h4 style="color: darkslategray">Reset Password</h4>
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
            <label class="control-label visible-ie8 visible-ie9">{{trans('admin.PASSWORD')}}</label>
            <div class="input-icon">
                <i class="fa fa-user"></i>
                {!! Form::password('password',  array('class' => 'form-control placeholder-no-fix' , 'placeholder' => trans('admin.PASSWORD') , 'autocomplete' => 'off')) !!}
                <span class="validation-message-block">{{ $errors->first('password', ':message') }}</span>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label visible-ie8 visible-ie9">{{trans('admin.CONFIRM_PASSWORD')}}</label>
            <div class="input-icon">
                <i class="fa fa-user"></i>
                {!! Form::password('password_confirmation',  array('class' => 'form-control placeholder-no-fix' , 'placeholder' =>trans('admin.CONFIRM_PASSWORD') , 'autocomplete' => 'off')) !!}
                <span class="validation-message-block">{{ $errors->first('password_confirmation', ':message') }}</span>
            </div>
        </div>

        <div class="form-group">
            <div class="row captcha-box" style="margin-bottom: 10px">
                <div class="col-md-10 col-xs-10" style="margin-left: -15px;">
                    {!! Html::image(Captcha::url() , null , ['id' => 'captcha']) !!}
                </div>
                <div class="col-md-2 col-xs-1">
                    <button type='button' id="reload" tabindex="-1" style="margin: 20px 0 0 5px ;" class="btn btn-xs btn-default waves-effect waves-light bolder">
                        <span class="fa fa-refresh"></span>
                    </button>
                </div>
            </div>
            <label class="control-label visible-ie8 visible-ie9">{{trans('admin.CAPTCHA')}}</label>
            <div class="input-icon">
                <i class="fa fa-shield"></i>
                {!! Form::text('captcha', null , array('class' => 'form-control placeholder-no-fix' , 'placeholder' =>  trans('admin.CAPTCHA') )) !!}
                <span class="validation-message-block">{{ $errors->first('captcha', ':message') }}</span>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-block dark ">
                Reset Password
            </button>
        </div>

    {!! Form::close() !!}
    <!-- END LOGIN FORM -->
    </div>
    <div class="copyright">@lang('admin.ADMINISTRATION_COPYRIGHT_TEXT')</div>
</div>
@stop