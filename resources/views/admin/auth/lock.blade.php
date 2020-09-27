@extends('admin.auth.include.layout')

@section('header')
<link href="{{asset('assets/admin/auth/css/lock-2.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/admin/css/custom.css')}}" rel="stylesheet" type="text/css" />
@stop

@section('content')

<body class="">
<div class="page-lock">
    <div class="page-body">
        @if(empty($user->img))
        <img class="page-lock-img img-circle" src="{{ asset('assets/admin/images/profile-placeholder.jpg')}}">
        @else
        <img class="page-lock-img" src="{{ asset('uploads/admins/profile-pictures/'.$user->img)}}">
        @endif
        <div class="page-lock-info">
            @if(!empty($user->name))
                <span class="email" style="font-size: 1.2em"> {{$user->name}} </span>
            @endif
            <span class="email"> {{$user->email}} </span>
            <span class="locked"> Locked </span>
            {!! Form::open(array(route('admin.lock'), 'method' => 'post' , 'class' => 'form-inline')) !!}

            <div class="input-group input-medium {{  $errors->has('password') ? 'has-error' : '' }}">
                 {!! Form::password('password', array('class' => 'form-control' , 'placeholder' => 'password')) !!}
                    <span class="input-group-btn">
                        <button type="submit" class="btn red icn-only">
                            <i class="fa fa-chevron-right" aria-hidden="true"></i>
                        </button>
                    </span>
            </div>
            <div>
                <span class="validation-message-block">{{ $errors->first('password', ':message') }}</span>
            </div>
            <!-- /input-group -->
                <div class="relogin">
                    <a href="{{route('admin.lock.cancel')}}"> Not {{$user->email}} ? </a>
                </div>
            {!! Form::close() !!}

        </div>
    </div>
    <div class="page-footer-custom"> 2016 - {{\Carbon\Carbon::now()->format('Y')}} &copy; {{trans('admin.WEB_SITE_NAME')}} - Management Area | Lock Screen </div>
</div>
</body>

@stop
