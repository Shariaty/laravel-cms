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
                                <i class="icon-basket-loaded icons"></i>&nbsp;Category
                            </a>
                        </li>
                        <li class="" data-event-tab data-tab-data="picture">
                            <a href="#picture" data-toggle="tab" aria-expanded="false">
                                <i class="icon icon-picture"></i>&nbsp;Image
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade active in" id="active">
                            {!! Form::model($cat , array('route' => ['admin.portfolio.categories.update' , $cat], 'method' => 'POST' , 'files' => true, 'id' => 'ProductCategoryForm')) !!}
                            <div class="col-md-12">
                                <div class="row">

                                    <div class="col-md-6 col-xs-12">
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
                                        <div class="form-group {{ $errors->has('parent') ? 'has-error' : '' }}">
                                            <label class="control-label"><span>Parent</span></label>
                                            <div class="input-icon right">
                                                @if($errors->has('parent'))
                                                    <i class="fa fa-exclamation tooltips" data-container="body"></i>
                                                @endif
                                                {!! Form::select('parent' , $parents , null , ['class' => 'form-control input-sm select2', 'style' => 'width: 100%' , 'id' => 'parent_list' ]) !!}
                                                <span class="validation-message-block">{{ $errors->first('parent', ':message') }}</span>
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

                        </div>

                        <div class="tab-pane fade" id="picture">
                            <div class="custom-image-container">
                                <img style="width: 500px; height: 230px;" src="{{$cat->full_url_image}}"/>
                            </div>
                            @if($cat->img)
                            <a href="{{route('admin.portfolio.categories.imageRemove' , $cat)}}" class="btn btn-sm btn-danger">Remove image</a>
                            @else
                            <input type="file" name="image" class="form-control" accept="image/*" id="file" style="width: 210px">
                            @endif
                        </div>
                        <div class="col-md-12 col-xs-12">
                            <hr/>
                            <div class="">
                                <div class="pull-right">
                                    <a href="{{route('admin.portfolio.categories')}}" class="btn btn-danger">Cancel</a>
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
</div>

@include('portfolio::category.forms._addVariantsModal')

@stop


@section('header')
<link href="{{ Module::asset('portfolio:plugins/icheck/skins/all.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ Module::asset('portfolio:plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ Module::asset('portfolio:plugins/select2/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ Module::asset('portfolio:css/module.css') }}" rel="stylesheet" type="text/css" />
@stop

@section('moduleFooter')
<script src="{{ Module::asset('portfolio:plugins/jquery-ui-1.12.1.custom/jquery-ui.min.js') }}" type="application/javascript"></script>
<script src="{{ Module::asset('portfolio:plugins/icheck/icheck.min.js') }}" type="application/javascript"></script>
<script src="{{ Module::asset('portfolio:plugins/select2/js/select2.min.js') }}" type="application/javascript"></script>
<script src="{{ Module::asset('portfolio:plugins/tinymce/tinymce.min.js') }}" type="application/javascript"></script>
<script src="{{ Module::asset('portfolio:js/module.js') }}" type="application/javascript"></script>
<script>
M6Module.tabInitialize();
M6Module.initTinyMCE();
M6Module.categoryAddEdit();
</script>
@stop


