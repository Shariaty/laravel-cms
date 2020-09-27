@extends('admin.include.layout')

@section('header')
<link href="{{asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/plugins/select2/css/select2-bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
@stop

@section('content')

    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="panel-body form">
                <div class="panel panel-grey">
                    <div class="panel-heading">Create Role</div>
                    <div class="panel-body">
                        {!! Form::model( $role  , array('route' => ['roles.update' , $role], 'method' => 'PATCH' )) !!}

                        <div class="col-md-12">
                            <div class="row">

                                <div class="col-md-3 col-xs-12">
                                    <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                                        <label class="control-label">
                                            <span>Name</span>
                                        </label>
                                        <div class="input-icon right">
                                            @if($errors->has('name'))
                                                <i class="fa fa-exclamation tooltips" data-container="body"></i>
                                            @endif
                                            {!! Form::text('name' , null , ['class' => 'form-control' , 'placeholder' => 'Name']) !!}
                                            <span class="validation-message-block">{{ $errors->first('name', ':message') }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3 col-xs-12">
                                    <div class="form-group {{ $errors->has('label') ? 'has-error' : '' }}">
                                        <label class="control-label">
                                            <span>Lable</span>
                                        </label>
                                        <div class="input-icon right">
                                            @if($errors->has('label'))
                                                <i class="fa fa-exclamation tooltips" data-container="body"></i>
                                            @endif
                                            {!! Form::text('label' , null , ['class' => 'form-control' , 'placeholder' => 'Lable']) !!}
                                            <span class="validation-message-block">{{ $errors->first('label', ':message') }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 col-xs-12">
                                    <div class="form-group {{ $errors->has('permission_id') ? 'has-error' : '' }}">
                                        <label class="control-label"><span>Permissions</span></label>
                                        <div class="input-icon right">
                                            @if($errors->has('permission_id'))
                                                <i class="fa fa-exclamation tooltips" data-container="body"></i>
                                            @endif
                                            {!! Form::select('permission_id[]' , $permissionList , $selectedCategory , ['class' => 'form-control input-sm select2', 'multiple' , 'size' => 1  , 'style' => 'width: 100%' , 'id' => 'cat_list' ]) !!}
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
                                    <a href="{{route('roles.index')}}" class="btn btn-danger">Cancel</a>
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
<script src="{{asset('assets/plugins/select2/js/select2.full.min.js')}}" type="text/javascript"></script>
<script>
    $("#cat_list").select2({
        placeholder: 'Select permissions'
    });
</script>
@stop

