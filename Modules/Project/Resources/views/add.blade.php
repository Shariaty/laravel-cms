@extends('admin.include.layout')
@section('content')

<div class="row">
    <div class="col-md-12 col-xs-12">
        <div class="panel-body form">
            <div class="panel panel-grey">
                <div class="panel-heading">{{$title}}</div>
                <div class="panel-body">

                    {!! Form::open(array('route' => 'admin.projects.create', 'method' => 'POST' , 'id' => 'AddNewsForm' , 'files' => true )) !!}

                    <ul class="nav nav-tabs">
                        <li class="active" data-event-tab data-tab-data="active">
                            <a href="#active" data-toggle="tab" aria-expanded="true"> Main </a>
                        </li>
                        <li class="" data-event-tab data-tab-data="picture">
                            <a href="#picture" data-toggle="tab" aria-expanded="false"> Picture </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane fade active in" id="active">
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
                                        <div class="form-group {{ $errors->has('slug') ? 'has-error' : '' }}">
                                            <label class="control-label">
                                                <span>Slug</span>
                                            </label>
                                            <div class="input-icon right">
                                                @if($errors->has('slug'))
                                                    <i class="fa fa-exclamation tooltips" data-container="body"></i>
                                                @endif
                                                {!! Form::text('slug' , null , ['class' => 'form-control' , 'placeholder' => 'Slug (User friendly url)']) !!}
                                                <span class="validation-message-block">{{ $errors->first('slug', ':message') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-xs-12">
                                        <div class="form-group {{ $errors->has('categories') ? 'has-error' : '' }}">
                                            <label class="control-label"><span>Categories</span></label>
                                            <div class="input-icon right">
                                                @if($errors->has('categories'))
                                                    <i class="fa fa-exclamation tooltips" data-container="body"></i>
                                                @endif
                                                {!! Form::select('categories[]' , $categories , null , ['class' => 'form-control input-sm select2', 'multiple' , 'size' => '1' , 'style' => 'width: 100%' , 'id' => 'cat_list' ]) !!}
                                                <span class="validation-message-block">{{ $errors->first('categories', ':message') }}</span>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8 col-xs-12">
                                    <div class="form-group {{ $errors->has('url') ? 'has-error' : '' }}">
                                        <label class="control-label">
                                            <span>URL</span>
                                        </label>
                                        <div class="input-icon right">
                                            @if($errors->has('url'))
                                                <i class="fa fa-exclamation tooltips" data-container="body"></i>
                                            @endif
                                            {!! Form::text('url' , null , ['class' => 'form-control' , 'placeholder' => 'URL']) !!}
                                            <span class="validation-message-block">{{ $errors->first('url', ':message') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 col-xs-12">
                                    <div class="md-checkbox" style="margin-top: 30px;">
                                        <input name="is_expired" type="checkbox" id="checkbox1" class="md-check">
                                        <label for="checkbox1">
                                            <span class="inc"></span>
                                            <span class="check red-check"></span>
                                            <span class="box"></span> Is Expired</label>
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
                            <div class="row">
                                <div class="col-md-12 col-xs-12">
                                    <div class="form-group {{ $errors->has('body') ? 'has-error' : '' }}">
                                        <label class="control-label">
                                            <span>Body</span>
                                        </label>
                                        <div class="input-icon right">
                                            @if($errors->has('body'))
                                                <i class="fa fa-exclamation tooltips" data-container="body"></i>
                                            @endif
                                            {!! Form::textarea('body' , null , ['class' => 'form-control mce' , 'placeholder' => 'Body of the projects']) !!}
                                            <span class="validation-message-block">{{ $errors->first('body', ':message') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="picture">
                            <div class="col-md-5 col-xs-12">
                                {!! Form::file('image' , array('class' => 'form-control'))!!}
                                <span class="validation-message-block ">{{ $errors->first('image', ':message') }}</span>
                            </div>
                        </div>

                        <div class="col-md-12 col-xs-12">
                        <hr/>
                        <div class="">
                            <div class="pull-right">
                                <a href="{{route('admin.projects.list')}}" class="btn btn-danger">Cancel</a>
                                <button type="submit" class="btn btn-success" id="btn_post">Save</button>
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
    .red-check {
        border: 2px solid red !important;
        border-top: none !important;
        border-left: none !important;
    }
</style>
@stop

@section('footer')
<script src="{{asset('assets/plugins/select2/js/select2.full.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/plugins/tinymce/tinymce.min.js')}}" type="text/javascript"></script>
<script>
$("#cat_list").select2({
    placeholder: 'Select Categories' ,
    maximumSelectionLength: 2
});
M6.initTinyMCE();
M6.tabInitialize();
</script>
@stop


