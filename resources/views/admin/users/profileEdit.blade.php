@extends('admin.include.layout')
@section('content')
<div class="row">
    <div class="col-md-12 col-xs-12">
        <div class="panel-body form">
            <div class="panel panel-grey">
                <div class="panel-heading">{{$title}}</div>
                <div class="panel-body">
                    {!! Form::open(array('route' => 'admin.profile.update', 'method' => 'POST')) !!}
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-2 col-xs-12">
                                <div class="form-group {{ $errors->has('firstname') ? 'has-error' : '' }}">
                                    <label class="control-label">
                                        <span>First Name</span>
                                    </label>
                                    <div class="input-icon right">
                                        @if($errors->has('firstname'))
                                            <i class="fa fa-exclamation tooltips" data-container="body"></i>
                                        @endif
                                        {!! Form::text('firstname' , $user->firstname , ['class' => 'form-control' , 'placeholder' => 'First Name']) !!}
                                        <span class="validation-message-block">{{ $errors->first('firstname', ':message') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2 col-xs-12">
                                <div class="form-group {{ $errors->has('lastname') ? 'has-error' : '' }}">
                                    <label class="control-label">
                                        <span>Last Name</span>
                                    </label>
                                    <div class="input-icon right">
                                        @if($errors->has('lastname'))
                                            <i class="fa fa-exclamation tooltips" data-container="body"></i>
                                        @endif
                                        {!! Form::text('lastname' , $user->lastname , ['class' => 'form-control' , 'placeholder' => 'Last Name']) !!}
                                        <span class="validation-message-block">{{ $errors->first('lastname', ':message') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 col-xs-12">
                                <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                                    <label class="control-label">
                                        <span>Email</span>
                                    </label>
                                    <div class="input-icon right">
                                        @if($errors->has('email'))
                                            <i class="fa fa-exclamation tooltips" data-container="body"></i>
                                        @endif
                                        {!! Form::text('email' , $user->email , ['class' => 'form-control', 'disabled' , 'placeholder' => 'email']) !!}
                                        <span class="validation-message-block">{{ $errors->first('email', ':message') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-xs-12">
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
                            </div>
                            <div class="col-md-2 col-xs-12">
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
                            <p class="text-right custom-small-label">Leave the password fields empty if you don't want to change</p>
                        </div>
                    </div>
                    <div class="col-md-12 col-xs-12">
                        <hr/>
                        <div class="">
                            <div class="pull-right">
                                <a href="{{route('admin.profile')}}" class="btn btn-danger">Cancel</a>

                                <button type="submit" class="btn btn-success">Update</button>
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



