@extends('admin.include.layout')
@section('content')

<div class="row">
    <div class="col-md-12 col-xs-12">
        <div class="panel-body form">
            <div class="panel panel-grey">
            <div class="panel-heading">{{$title}}</div>
            <div class="panel-body">
            {!! Form::open(array('route' => 'admin.publicUsers.save', 'method' => 'POST' , 'id' => 'publicUser')) !!}
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-3 col-xs-12">
                        <div class="form-group {{ $errors->has('fullName') ? 'has-error' : '' }}">
                            <label class="control-label">
                                <span>FullName</span>
                            </label>
                            <div class="input-icon right">
                                @if($errors->has('fullName'))
                                    <i class="fa fa-exclamation tooltips" data-container="body"></i>
                                @endif
                                {!! Form::text('fullName' , null , ['class' => 'form-control' , 'placeholder' => 'Full Name (Required)']) !!}
                                <span class="validation-message-block">{{ $errors->first('fullName', ':message') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 col-xs-12">
                        <div class="form-group {{ $errors->has('cell') ? 'has-error' : '' }}">
                            <label class="control-label">
                                <span>Cell</span>
                            </label>
                            <div class="input-icon right">
                                @if($errors->has('cell'))
                                    <i class="fa fa-exclamation tooltips" data-container="body"></i>
                                @endif
                                {!! Form::text('cell' , null , ['class' => 'form-control' , 'placeholder' => 'CellPhone (Required)']) !!}
                                <span class="validation-message-block">{{ $errors->first('cell', ':message') }}</span>
                            </div>
                        </div>
                    </div>


                    <div class="col-md-3 col-xs-12">
                        <div class="form-group {{ $errors->has('email-address') ? 'has-error' : '' }}">
                            <label class="control-label">
                                <span>Email</span>
                            </label>
                            <div class="input-icon right">
                                @if($errors->has('email-address'))
                                    <i class="fa fa-exclamation tooltips" data-container="body"></i>
                                @endif
                                {!! Form::text('email-address' , null , ['class' => 'form-control' , 'autocomplete' => 'off' , 'placeholder' => 'Email']) !!}
                                <span class="validation-message-block">{{ $errors->first('email-address', ':message') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 col-xs-12">
                        <div class="form-group {{ $errors->has('pass') ? 'has-error' : '' }}">
                            <label class="control-label">
                                <span>Password</span>
                            </label>
                            <div class="input-icon right">
                                @if($errors->has('pass'))
                                    <i class="fa fa-exclamation tooltips" data-container="body"></i>
                                @endif
                                {!! Form::input('password' , 'pass' , null , ['class' => 'form-control' , 'placeholder' => 'Password']) !!}
                                <span class="validation-message-block">{{ $errors->first('pass', ':message') }}</span>
                            </div>
                        </div>
                        <p class="text-right small-grey-text" style="margin-top: -10px">Password should be at least 6 characters</p>
                    </div>

                </div>
            </div>

            <div class="col-md-12 col-xs-12">
                <hr/>
                <div class="">
                    <div class="pull-right">
                        <a href="{{route('admin.publicUsers')}}" class="btn btn-danger">Cancel</a>
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

@section('footer')
<script>

</script>
@stop