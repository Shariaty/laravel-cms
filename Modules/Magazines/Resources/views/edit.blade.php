@extends('admin.include.layout')


@section('content')

<div class="row">
    <div class="col-md-12 col-xs-12">
            <div class="panel-body form">
                <div class="panel panel-grey">
                    <div class="panel-heading">{{$title}}</div>
                    <div class="panel-body">
                        {!! Form::model($news , array('route' => ['admin.magazines.update' , $news ], 'method' => 'POST' , 'files' => true , 'id' => 'MagazineForm')) !!}

                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-9 col-xs-12" style="padding: 0px !important;">
                                        <div class="col-md-5 col-xs-12">
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
                                            <div class="form-group {{ $errors->has('news_categories') ? 'has-error' : '' }}">
                                                <label class="control-label"><span>Categories</span></label>
                                                <div class="input-icon right">
                                                    @if($errors->has('news_categories'))
                                                        <i class="fa fa-exclamation tooltips" data-container="body"></i>
                                                    @endif
                                                    {!! Form::select('news_categories[]' , $categories , isset($selectedCategory) ? $selectedCategory : null , ['class' => 'form-control input-sm select2', 'multiple' , 'size' => '1' , 'style' => 'width: 100%' , 'id' => 'cat_list' ]) !!}
                                                    <span class="validation-message-block">{{ $errors->first('news_categories', ':message') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-xs-12">
                                            <div class="md-checkbox" style="margin-top: 30px;">
                                                <input name="is_published" type="checkbox" id="checkbox2" class="md-check" {{$news->is_published == 'Y' ? 'checked' : ''}}>
                                                <label for="checkbox2">
                                                    <span class="inc"></span>
                                                    <span class="check"></span>
                                                    <span class="box"></span> Publish Status</label>
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-xs-12">
                                            <div class="form-group {{ $errors->has('body') ? 'has-error' : '' }}">
                                                <label class="control-label">
                                                    <span>Body</span>
                                                </label>
                                                <div class="input-icon right">
                                                    @if($errors->has('body'))
                                                        <i class="fa fa-exclamation tooltips" data-container="body"></i>
                                                    @endif
                                                    {!! Form::textarea('body' , null , ['class' => 'form-control mce' , 'placeholder' => 'Body of the news']) !!}
                                                    <span class="validation-message-block">{{ $errors->first('body', ':message') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-xs-12" style="padding: 0px !important;">
                                        <div class="crop-box-container"  data-id="{{$news->id}}"  data-title="{{$news->title}}"  data-image="{{ $news->img ? $news->img : null}}">
                                            <div class="imageBox">
                                                <div class="thumbBox"></div>
                                                <div class="spinner" style="display: none">Loading...</div>
                                            </div>
                                            <div class="action">
                                                <div class="row">
                                                        <div class="col-md-12 col-xs-12">
                                                            <div class="form-group">
                                                                <input type="file" name="image" accept="image/*" id="file" class="inputfile inputfile-1 pull-left" style="width: 210px">
                                                                <input type="hidden" id="finalFile" name="finalFile" value="qqqq">
                                                                {{--<label for="file"><i class="fa fa-user"></i> &nbsp;<span>انتخاب تصویر</span></label>--}}
                                                                <span class="validation-message-block"></span>
                                                            </div>
                                                        </div>
                                                    <div class="col-md-12 col-xs-12">
                                                        <div class="btn-group btn-group-justified" role="group" >
                                                            <div class="btn-group btn-group-justified" role="group" >
                                                                <div class="btn-group" role="group">
                                                                    <label for="file" class="btn  btn-xs btn-success"><i class="fa fa-user"></i> &nbsp;<span>انتخاب تصویر</span></label>
                                                                    {{--<button type="button" class="btn btn-xs green-jungle" data-loading-text="{{Module::config('loadingText')}}" id="btnCrop">بارگزاری</button>--}}
                                                                </div>
                                                                <div class="btn-group" role="group">
                                                                    <button type="button" class="btn  btn-xs btn-danger" data-loading-text="{{Module::config('loadingText')}}" id="remove-image"><i class="fa fa-trash"></i></button>
                                                                </div>
                                                                <div class="btn-group" role="group">
                                                                    <button type="button" class="btn  btn-xs btn-default"  id="btnZoomIn">+</button>
                                                                </div>
                                                                <div class="btn-group" role="group">
                                                                    <button type="button" class="btn  btn-xs btn-default" id="btnZoomOut">-</button>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <div class="clearfix"></div>
                                                </div>
                                            </div>
                                            <div class="cropped">
                                            </div>

                                        </div>
                                        <div class="file-upload-area" style="margin: 25px 13px 25px 6px;">
                                            @include('magazines::forms._fileUpload' , ['magazine' => $news])
                                            <input type="hidden" id="identifier" data-id="{{$news->id}}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                            <hr/>
                            <div class="pull-right">
                                <a href="{{route('admin.magazines.list')}}" class="btn btn-danger">Cancel</a>
                                <button type="submit" class="btn btn-success" id="save-update-btn">Update</button>
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

@section('moduleHeader')
<link href="{{ Module::asset('magazines:plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ Module::asset('magazines:plugins/select2/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ Module::asset('magazines:plugins/cropbox/cropbox.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ Module::asset('magazines:plugins/jqueryfileupload/css/jquery.fileupload.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ Module::asset('magazines:css/module.css') }}" rel="stylesheet" type="text/css" />

<style>
    .crop-box-container{
        padding: 7px;
        border: 1px solid lightgray;
        background-color: #dfdfdf;
        border-radius: 5px !important;
        margin: 0px auto;
        height: 400px;
        width: 240px;
        text-align: center;
    }
</style>
@stop

@section('moduleFooter')
<script src="{{ Module::asset('magazines:plugins/select2/js/select2.min.js') }}" type="application/javascript"></script>
<script src="{{ Module::asset('magazines:plugins/tinymce/tinymce.min.js') }}" type="application/javascript"></script>
<script src="{{ Module::asset('magazines:plugins/cropbox/cropbox.js') }}" type="application/javascript"></script>
{{--<script src="{{ Module::asset('magazines:plugins/cropbox/require.js') }}" type="application/javascript"></script>--}}
<script src="{{ Module::asset('magazines:plugins/jqueryfileupload/vendor/jquery.ui.widget.js')}}" type="text/javascript"></script>
<script src="{{ Module::asset('magazines:plugins/jqueryfileupload/jquery.fileupload.js')}}" type="text/javascript"></script>
<script src="{{ Module::asset('magazines:plugins/jquery.pulsate.min.js')}}" type="text/javascript"></script>
<script src="{{ Module::asset('magazines:js/module.js') }}" type="application/javascript"></script>

<script>
M6Module.initTinyMCE();
M6Module.tabInitialize();
M6Module.magazine();
M6Module.ajaxFileUpload();
</script>
@stop



