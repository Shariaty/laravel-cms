@extends('admin.include.layout')
@section('content')

<div class="row">
     <div class="col-md-12 col-xs-12">
        <div class="panel-body form">
            <div class="panel panel-grey">
                <div class="panel-heading">{{$title}}</div>
                <div class="panel-body">
                    {!! Form::model($cat , array('route' => ['admin.projects.categories.update' , $cat], 'method' => 'POST' , 'id' => 'AddNewsCategoryForm')) !!}
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-5 col-xs-12">
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
                            <div class="col-md-5 col-xs-12">
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
                            <div class="col-md-2 col-xs-12">
                                <div class="md-checkbox" style="margin-top: 30px;">
                                    <input name="is_published" type="checkbox" id="checkbox2" class="md-check" {{$cat->is_published == 'Y' ? 'checked' : ''}}>
                                    <label for="checkbox2">
                                        <span class="inc"></span>
                                        <span class="check"></span>
                                        <span class="box"></span> Publish Status</label>
                                </div>
                            </div>
                            <div class="col-md-12 col-xs-12">
                                <div class="form-group {{ $errors->has('desc') ? 'has-error' : '' }}">
                                    <label class="control-label">
                                        <span>Description</span>
                                    </label>
                                    <div class="input-icon right">
                                        @if($errors->has('desc'))
                                            <i class="fa fa-exclamation tooltips" data-container="desc"></i>
                                        @endif
                                        {!! Form::textarea('desc' , null , ['class' => 'form-control mce' , 'placeholder' => 'Description']) !!}
                                        <span class="validation-message-block">{{ $errors->first('desc', ':message') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 col-xs-12">
                        <hr/>
                        <div class="">
                            <div class="pull-right">
                                <a href="{{route('admin.projects.categories')}}" class="btn btn-danger">Cancel</a>
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
<link href="{{asset('assets/plugins/icheck/skins/all.css ')}}" rel="stylesheet" type="text/css" />
@stop

@section('footer')
<script src="{{asset('assets/plugins/icheck/icheck.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/plugins/tinymce/tinymce.min.js')}}" type="text/javascript"></script>
<script>
        tinymce.init({
            selector: '.mce',
            theme: 'modern',
            height : 100 ,
            plugins: [
                'advlist autolink lists link image charmap print preview hr anchor pagebreak',
                'searchreplace wordcount visualblocks visualchars fullscreen',
                'insertdatetime media nonbreaking save table contextmenu directionality',
                'emoticons template paste textcolor colorpicker textpattern imagetools responsivefilemanager code'
            ],
            toolbar1: "undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | styleselect",
            toolbar2: "| responsivefilemanager | link unlink anchor | image media | forecolor backcolor  | print preview code ",
            image_advtab: true,

            external_filemanager_path: Path+"filemanager/",
            filemanager_title:"Responsive Filemanager" ,
            external_plugins: { "filemanager" : PublicPath+"assets/plugins/tinymce/plugins/responsivefilemanager/plugin.min.js"}

        });
    </script>
@stop


