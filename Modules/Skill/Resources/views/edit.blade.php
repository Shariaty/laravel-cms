@extends('admin.include.layout')
@section('content')

<div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="panel-body form">
                <div class="panel panel-grey">
                    <div class="panel-heading">{{$title}}</div>
                    <div class="panel-body">
                        {!! Form::model($skill , array('route' => ['admin.skills.update' , $skill ], 'method' => 'POST' , 'files' => true , 'id' => 'PostEditForm')) !!}

                            <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-4 col-xs-12">
                                            <div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">
                                                <label class="control-label">
                                                    <span>Title</span>
                                                </label>
                                                <div class="input-icon right">
                                                    @if($errors->has('title'))
                                                        <i class="fa fa-exclamation tooltips" data-container="body"></i>
                                                    @endif
                                                    {!! Form::text('title' , null , ['class' => 'form-control' , 'placeholder' => 'Title'] ) !!}
                                                    <span class="validation-message-block">{{ $errors->first('title', ':message') }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 col-xs-12">
                                            <div class="form-group {{ $errors->has('category') ? 'has-error' : '' }}">
                                                <label class="control-label"><span>Categories</span></label>
                                                <div class="input-icon right">
                                                    @if($errors->has('category'))
                                                        <i class="fa fa-exclamation tooltips" data-container="body"></i>
                                                    @endif
                                                    {!! Form::select('category' , $categories , $skill->cat_id , ['class' => 'form-control input-sm select2' , 'style' => 'width: 100%' , 'id' => 'cat_list' ]) !!}
                                                    <span class="validation-message-block">{{ $errors->first('category', ':message') }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-2 col-xs-12">
                                            <div class="center-text">
                                            <input name="percentage" type="text" class="dial" value="{{$skill->percentage}}">
                                            <span class="validation-message-block">{{ $errors->first('slug', ':message') }}</span>
                                            </div>
                                        </div>

                                        <div class="col-md-2 col-xs-12">
                                            <div class="md-checkbox" style="margin-top: 30px;">
                                                <input name="is_published" type="checkbox" id="checkbox2" class="md-check" {{$skill->is_published == 'Y' ? 'checked' : ''}}>
                                                <label for="checkbox2">
                                                    <span class="inc"></span>
                                                    <span class="check"></span>
                                                    <span class="box"></span> Publish Status</label>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            <div class="col-md-12 col-xs-12">

                            <hr/>
                            <div class="">
                                <div class="pull-right">
                                    <a href="{{route('admin.skills.list')}}" class="btn btn-danger">Cancel</a>
                                    <button type="submit" class="btn btn-success" id="btn_skill">Update</button>
                                </div>
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
<style>
    canvas {
        margin-bottom: -115px;
    }
</style>
@stop

@section('footer')
<script src="{{asset('assets/plugins/select2/js/select2.full.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/plugins/knob/jquery.knob.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/plugins/tinymce/tinymce.min.js')}}" type="text/javascript"></script>
<script>
$(function() {
    $(".dial").knob({
        step : "5",
        thickness : ".2",
        width : "75" ,
        cursor : false ,
        fgColor : "#36c6d3",
        displayPrevious : false
    })
});
$("#cat_list").select2({
    placeholder: 'Select Categories' ,
    maximumSelectionLength: 2
});
M6.initTinyMCE();
M6.tabInitialize();
</script>
@stop


