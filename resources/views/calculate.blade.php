@extends('admin.auth.include.layout')

@section('header')
<link href="{{asset('admin/auth/css/login-4.css')}}" rel="stylesheet" type="text/css" />
@stop

@section('content')
    <div class="login" style="background-color:transparent !important; margin-bottom: 20px; opacity: 0;">
        <div class="logo"></div>

        <div class="content">
            <!-- BEGIN LOGIN FORM -->
            {!! Form::open(array('route' => 'calculate' , 'method' => 'post')) !!}
            <div class="form-group">
                <label class="control-label visible-ie8 visible-ie9">Input value</label>
                <div class="input-icon">
                    <i class="fa fa-number"></i>
                    {!! Form::text('number', null, array('class' => 'form-control placeholder-no-fix' , 'placeholder' => 'enter value here ..')) !!}
                    <span class="validation-message-block">{{ $errors->first('number', ':message') }}</span>
                </div>
            </div>
            <div class="create-account">
                <div style="margin-bottom: 30px;">
                    <button type="submit" class="btn green btn-outline pull-right" href="{{ route('calculate') }}">Submit</button>
                </div>
            </div>
        {!! Form::close() !!}


            <div style="height: 50px; width: 100px; background-color: white;">
                @if(isset($result))
                    {{$result}}
                @endif
            </div>
        </div>

    </div>
@stop


