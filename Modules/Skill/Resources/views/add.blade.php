@extends('admin.include.layout')
@section('content')

<div class="row">
    <div class="col-md-12 col-xs-12">
        <div class="panel-body form">
            <div class="panel panel-grey">
                <div class="panel-heading">{{$title}}</div>
                <div class="panel-body">
                    <ul class="nav nav-tabs">
                        <li class="active" data-event-tab data-tab-data="active">
                            <a href="#active" data-toggle="tab" aria-expanded="true">
                                <i class="fa fa-info-circle"></i>&nbsp;Information
                            </a>
                        </li>
                        <li class="" data-event-tab data-tab-data="attachment">
                            <a href="#attachment" data-toggle="tab" aria-expanded="false">
                                <i class="icon-paper-clip icons"></i>&nbsp;Attachments
                            </a>
                        </li>

                    </ul>

                    <div class="tab-content">
                        {!! Form::open(array('route' => 'admin.skills.create', 'method' => 'POST' , 'id' => 'AddNewsForm' , 'files' => true )) !!}

                        <div class="tab-pane fade active in" id="active">

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
                                                {!! Form::text('title' , null , ['class' => 'form-control' , 'placeholder' => 'Title']) !!}
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
                                                {!! Form::select('category' , $categories , null , ['class' => 'form-control input-sm select2' , 'style' => 'width: 100%' , 'id' => 'cat_list' ]) !!}
                                                <span class="validation-message-block">{{ $errors->first('category', ':message') }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-2 col-xs-12">
                                        <div class="center-text">
                                            <input name="percentage" type="text" class="dial" value="15">
                                            <span class="validation-message-block">{{ $errors->first('percentage', ':message') }}</span>
                                        </div>
                                    </div>

                                    <div class="col-md-2 col-xs-12">
                                        <div class="md-checkbox" style="margin-top: 30px;">
                                            <input name="is_published" type="checkbox" id="checkbox2" class="md-check" checked>
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
                                        <button type="submit" class="btn btn-success" id="btn_post">Save</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade active in" id="attachment">
                            <div id="catalog" class="file-upload-area" style="margin: -2px 13px 25px 6px;">
{{--                                @include('slideshow::forms._fileUpload' , ['magazine' => $slide , 'type' => "catalog"])--}}
{{--                                <input type="hidden" id="identifier" data-id="{{$slide->id}}">--}}
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


