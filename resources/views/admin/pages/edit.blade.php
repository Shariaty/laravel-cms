@extends('admin.include.layout')
@section('content')

<div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="panel-body form">
                <div class="panel panel-grey">
                    <div class="panel-heading">{{$title}}</div>
                    <div class="panel-body">
                        <ul class="nav nav-tabs">
                            @php $i = 0; @endphp
                            @foreach($locales as $key => $value)
                                @php $i++; @endphp
                                <li class="{{$i == 1 ? 'active' : ''}}" data-event-tab data-tab-data="{{$key}}">
                                    <a href="#{{$key}}" data-toggle="tab" aria-expanded="false">
                                        <i class="fa fa-language"></i>&nbsp;{{ $value }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade active in" id="active">
                                {!! Form::model($page , array('route' => ['admin.pages.update' , $page ], 'method' => 'POST' , 'files' => true , 'id' => 'PostEditForm')) !!}
                            </div>

                            @php $e = 0; @endphp
                            @foreach($locales as $key => $value)
                                @php $e++; @endphp
                                <div class="tab-pane fade {{$e == 1 ? 'active in' : ''}}" id="{{$key}}">
                                    <div class="col-md-4 col-xs-12">
                                        <div class="form-group {{ $errors->has($key.'.title') ? 'has-error' : '' }}">
                                            <label class="control-label">* {{$value}} Page Title</label>
                                            {!! Form::text( $key.'[title]' , $page->hasTranslation($key) ? $page->translate($key)->title : null , ['class' => 'form-control']) !!}
                                            <span class="validation-message-block">{{ $errors->first($key.'.title', ':message') }}</span>
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-xs-12">
                                        <div class="form-group {{ $errors->has($key.'.slug') ? 'has-error' : '' }}">
                                            <label class="control-label">* {{$value}} friendly url</label>
                                            {!! Form::text( $key.'[slug]' , $page->hasTranslation($key) ? $page->translate($key)->slug : null , ['class' => 'form-control' , 'disabled']) !!}
                                            <span class="validation-message-block">{{ $errors->first($key.'.slug', ':message') }}</span>
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-xs-12">
                                        <div class="form-group {{ $errors->has($key.'.meta') ? 'has-error' : '' }}">
                                            <label class="control-label">* {{$value}} Meta Description</label>
                                            {!! Form::text( $key.'[meta]' , $page->hasTranslation($key) ? $page->translate($key)->meta : null , ['class' => 'form-control']) !!}
                                            <span class="validation-message-block">{{ $errors->first($key.'.meta', ':message') }}</span>
                                        </div>
                                    </div>

                                    <div class="col-md-12 col-xs-12">
                                        <div class="form-group {{ $errors->has($key.'.desc') ? 'has-error' : '' }}">
                                            <label class="control-label">{{$value}} Description</label>
                                            {!! Form::textarea($key.'[desc]', $page->hasTranslation($key) ? $page->translate($key)->desc : null , array('class' => 'form-control input-sm mce tm_textArea', 'rows' => '5')) !!}
                                            <span class="validation-message-block">{{ $errors->first($key.'.desc', ':message') }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach


                            <div class="col-md-12 col-xs-12">
                            <div class="form-group">
                                        <label class="edit-label">
                                            <i class="fa fa-globe" aria-hidden="true"></i>
                                            <span>Keywords</span>
                                        </label>

                                        {!! Form::select('keywords[]' , $tags , $selectedTags , ['class' => '', 'multiple' => '"multiple' , 'size' => 1  , 'style' => 'width: 100%' , 'id' => 'keywords' ]) !!}
                                    </div>
                            <hr/>

                            <div class="">
                                <div class="pull-right">
                                    <a href="{{route('admin.pages.list')}}" class="btn btn-danger">Cancel</a>
                                    <button type="submit" class="btn btn-success" id="btn_page">Update</button>
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
@stop

@section('footer')
<script src="{{asset('assets/plugins/select2/js/select2.full.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/plugins/tinymce/tinymce.min.js')}}" type="text/javascript"></script>
<script>
$("#cat_list").select2({
    placeholder: 'Select Categories' ,
    maximumSelectionLength: 2
});

$('#keywords').select2({
    placeholder: 'Select keywords',
    tags: true
});

M6.initTinyMCE();
M6.tabInitialize();
</script>
@stop


