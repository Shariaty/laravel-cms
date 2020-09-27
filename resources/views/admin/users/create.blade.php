@extends('admin.include.layout')
@section('content')

<div class="row">
    <div class="col-md-12 col-xs-12">
        <div class="panel-body form">
            <div class="panel panel-grey">
            <div class="panel-heading">{{$title}}</div>
            <div class="panel-body">
            {!! Form::open(array('route' => 'admin.user.create', 'method' => 'POST' , 'id' => 'AdminRegisterForm')) !!}
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-4 col-xs-12">
                        <div class="form-group {{ $errors->has('firstName') ? 'has-error' : '' }}">
                            <label class="control-label">
                                <span>First Name</span>
                            </label>
                            <div class="input-icon right">
                                @if($errors->has('firstname'))
                                    <i class="fa fa-exclamation tooltips" data-container="body"></i>
                                @endif
                                {!! Form::text('firstname' , null , ['class' => 'form-control' , 'placeholder' => 'First Name']) !!}
                                <span class="validation-message-block">{{ $errors->first('firstname', ':message') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 col-xs-12">
                        <div class="form-group {{ $errors->has('lastName') ? 'has-error' : '' }}">
                            <label class="control-label">
                                <span>Last Name</span>
                            </label>
                            <div class="input-icon right">
                                @if($errors->has('lastname'))
                                    <i class="fa fa-exclamation tooltips" data-container="body"></i>
                                @endif
                                {!! Form::text('lastname' , null , ['class' => 'form-control' , 'placeholder' => 'Last Name']) !!}
                                <span class="validation-message-block">{{ $errors->first('lastname', ':message') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 col-xs-12">
                        <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                            <label class="control-label">
                                <span>Email</span>
                            </label>
                            <div class="input-icon right">
                                @if($errors->has('email'))
                                    <i class="fa fa-exclamation tooltips" data-container="body"></i>
                                @endif
                                {!! Form::text('email' , null , ['class' => 'form-control' , 'placeholder' => 'email']) !!}
                                <span class="validation-message-block">{{ $errors->first('email', ':message') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-xs-12">
                        <div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}">
                            <label class="control-label">
                                <span>Password</span>
                            </label>
                            <div class="input-icon right">
                                @if($errors->has('password'))
                                    <i class="fa fa-exclamation tooltips" data-container="body"></i>
                                @endif
                                {!! Form::input('password' , 'password' , null , ['class' => 'form-control' , 'placeholder' => 'password']) !!}
                                <span class="validation-message-block">{{ $errors->first('password', ':message') }}</span>
                            </div>
                        </div>
                        <p class="text-right small-grey-text" style="margin-top: -10px">Password should be at least 6 characters</p>
                    </div>
                    <div class="col-md-4 col-xs-12">
                        <div class="form-group {{ $errors->has('password_confirmation') ? 'has-error' : '' }}">
                            <label class="control-label">
                                <span>Password Confirmation</span>
                            </label>
                            <div class="input-icon right">
                                @if($errors->has('password_confirmation'))
                                    <i class="fa fa-exclamation tooltips" data-container="body"></i>
                                @endif
                                {!! Form::input('password' , 'password_confirmation' , null , ['class' => 'form-control' , 'placeholder' => 'Password Confirmation']) !!}
                                <span class="validation-message-block">{{ $errors->first('password_confirmation', ':message') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 col-xs-12">
                        <div class="form-group {{ $errors->has('permission_id') ? 'has-error' : '' }}">
                            <label class="control-label"><span>Permissions</span></label>
                            <div class="input-icon right">
                                @if($errors->has('permission_id'))
                                    <i class="fa fa-exclamation tooltips" data-container="body"></i>
                                @endif
                                {!! Form::select('permission_id[]' , getAdminRolsList() , null , ['class' => 'form-control input-sm select2', 'multiple' , 'size' => 1  , 'style' => 'width: 100%' , 'id' => 'cat_list' ]) !!}
                                <span class="validation-message-block">{{ $errors->first('permission_id', ':message') }}</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="col-md-12 col-xs-12">
                <hr/>
                <div class="">
                    <div class="pull-right">
                        <a href="{{route('admin.users')}}" class="btn btn-danger">Cancel</a>
                        <button type="submit" class="btn btn-success" id="btn_post">Save</button>
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

@section('header')
<link href="{{asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/plugins/select2/css/select2-bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
@stop
@section('footer')
<script src="{{asset('assets/plugins/select2/js/select2.full.min.js')}}" type="text/javascript"></script>
<script>
$("#cat_list").select2({
    placeholder: 'Select permissions' ,
    maximumSelectionLength: 1
});
</script>
@stop