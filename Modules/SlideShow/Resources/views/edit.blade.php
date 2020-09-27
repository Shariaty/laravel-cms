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
                                    <i class="icon-paper-clip icons"></i>&nbsp;Attachments
                                </a>
                            </li>

                            @foreach($locales as $key => $value)
                                <li class="" data-event-tab data-tab-data="{{$key}}">
                                    <a href="#{{$key}}" data-toggle="tab" aria-expanded="false">
                                        <i class="fa fa-language"></i>&nbsp;{{ $value }}
                                    </a>
                                </li>
                            @endforeach

                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade active in" id="active">
                                {!! Form::model( $slide , array('route' => ['admin.slide.update' , $slide] , 'files' => true , 'method' => 'POST' , 'id' => 'FormStore')) !!}

                                <div class="col-md-6 col-xs-12">
                                    <div id="catalog" class="file-upload-area" style="margin: -2px 13px 25px 6px;">
                                        @include('slideshow::forms._fileUpload' , ['magazine' => $slide , 'type' => "catalog"])
                                        <input type="hidden" id="identifier" data-id="{{$slide->id}}">
                                    </div>
                                </div>

                                <div class="col-md-4 col-xs-12">
                                    <div class="form-group {{ $errors->has('link') ? 'has-error' : '' }}">
                                        <label class="control-label">Link</label>
                                        {!! Form::text( 'link' , $slide->link ? $slide->link : null , ['class' => 'form-control']) !!}
                                        <span class="validation-message-block">{{ $errors->first('link', ':message') }}</span>
                                    </div>
                                </div>

                                <div class="col-md-2 col-xs-12">
                                    <div class="md-checkbox" style="margin-top: 30px;">
                                        <input name="is_published" type="checkbox" id="checkbox2" class="md-check" {{$slide->is_published == 'Y' ? 'checked' : ''}}>
                                        <label for="checkbox2">
                                            <span class="inc"></span>
                                            <span class="check"></span>
                                            <span class="box"></span> Publish Status</label>
                                    </div>
                                </div>
                            </div>

                            @foreach($locales as $key => $value)
                                <div class="tab-pane fade" id="{{$key}}">
                                    <div class="col-md-12 col-xs-12">
                                        <div class="form-group {{ $errors->has($key.'.title') ? 'has-error' : '' }}">
                                            <label class="control-label">* {{$value}} Service Title</label>
                                            {!! Form::text( $key.'[title]' , $slide->hasTranslation($key) ? $slide->translate($key)->title : null , ['class' => 'form-control']) !!}
                                            <span class="validation-message-block">{{ $errors->first($key.'.title', ':message') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-xs-12">
                                        <div class="form-group {{ $errors->has($key.'.desc') ? 'has-error' : '' }}">
                                            <label class="control-label">{{$value}} Description</label>
                                            {!! Form::textarea($key.'[desc]', $slide->hasTranslation($key) ? $slide->translate($key)->desc : null , array('class' => 'form-control input-sm mce tm_textArea', 'rows' => '5')) !!}
                                            <span class="validation-message-block">{{ $errors->first($key.'.desc', ':message') }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach


                            <input type="hidden" id="store_id" data-id="{{$slide->id}}">

                            {{--<div class="tab-pane fade" id="attachment">--}}
                               {{----}}
                            {{--</div>--}}

                            <div class="row">
                                <div class="col-md-12 col-xs-12">
                                    <div class="col-md-12 col-xs-12">
                                        <hr />
                                        <a href="{{route('admin.slide.list')}}" class="btn btn-warning" >
                                            <i class="icon icon-action-undo icons"></i>&nbsp;Cancel
                                        </a>
                                        <button type="submit" class="btn btn-success pull-right" id="btnCompanyCreate">
                                            <i class="fa fa-floppy-o" aria-hidden="true"></i>
                                            {{ ( isset($slide->title) AND $slide->title ) ? 'Update' : 'Create' }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop


@section('moduleHeader')
    <style>
        .nav-pills, .nav-tabs{
            margin-bottom: 0px !important;
        }
        .tab-content{
            background-color: white;
            padding: 20px 10px 10px 10px;
            border: 1px solid #ddd;
            border-top: 0;
        }
        .dz-remove{
            background-color: #5b0000;
            color: white;
            padding: 1px;
            font-size: 9px !important;
        }
        .dz-remove:hover{
            text-decoration: none;
            background-color: red;
            color: whitesmoke;
        }
        .noUi-value-large{
            margin-top: 10px !important;
            font-size: 11px !important;
        }
        .select2-results__group{
            color: white !important;
            background-color: #364150 !important;
            font-family: Tahoma, Helvetica, Arial !important;
        }
        .noUi-handle .noUi-tooltip{
            font-size: 11px !important;
            background: #f7f7f7 !important;
            top: -33px !important;
            left: 15px !important;
        }
        .noUi-connect {
            background: #d8ff00 !important;
        }
    </style>
    <link href="{{ Module::asset('slideshow:plugins/jqueryfileupload/css/jquery.fileupload.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{ Module::asset('slideshow:css/module.css') }}" rel="stylesheet" type="text/css" />
@stop

@section('moduleFooter')
    <script src="{{asset('assets/plugins/jquery-mask/jquery.mask.min.js')}}" type="text/javascript"></script>
    <script src="{{ Module::asset('slideshow:plugins/tinymce/tinymce.min.js') }}" type="application/javascript"></script>
    <script src="{{ Module::asset('slideshow:plugins/jqueryfileupload/vendor/jquery.ui.widget.js')}}" type="text/javascript"></script>
    <script src="{{ Module::asset('slideshow:plugins/jqueryfileupload/jquery.fileupload.js')}}" type="text/javascript"></script>
    <script src="{{ Module::asset('slideshow:js/module.js') }}" type="application/javascript"></script>
    <script>
        M6Module.tabInitialize();
        // M6Module.product();
        M6Module.initTinyMCE();
        M6Module.ajaxFileUpload();
    </script>
@stop


