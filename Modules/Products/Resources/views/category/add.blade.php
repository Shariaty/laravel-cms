@extends('admin.include.layout')
@section('content')
<div class="row">
    <div class="col-md-12 col-xs-12">
            <div class="panel-body form">
                <div class="panel panel-grey">
                    <div class="panel-heading">{{$title}}</div>
                    <div class="panel-body">
                        {!! Form::open(array('route' => ['admin.products.categories.create'], 'method' => 'POST' , 'id' => 'ProductCategoryForm')) !!}
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

                                <div class="col-md-3 col-xs-12">
                                    <div class="form-group {{ $errors->has('icon') ? 'has-error' : '' }}">
                                        <label class="control-label">
                                            <span>Icon code</span>
                                        </label>
                                        <div class="input-icon right">
                                            @if($errors->has('icon'))
                                                <i class="fa fa-exclamation tooltips" data-container="body"></i>
                                            @endif
                                            {!! Form::text('icon' , null , ['class' => 'form-control' , 'placeholder' => 'icon icon--bags']) !!}
                                            <span class="validation-message-block">{{ $errors->first('icon', ':message') }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3 col-xs-12">
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
                                        <input name="is_published" checked type="checkbox" id="checkbox2" class="md-check">
                                        <label for="checkbox2">
                                            <span class="inc"></span>
                                            <span class="check"></span>
                                            <span class="box"></span> Publish Status</label>
                                    </div>
                                </div>

                                <div class="col-md-12 col-xs-12">
                                    <div class="form-group {{ $errors->has('variants') ? 'has-error' : '' }}">
                                        <label class="control-label"><span>Variants</span></label>
                                        <div class="input-icon right">
                                            @if($errors->has('variants'))
                                                <i class="fa fa-exclamation tooltips" data-container="body"></i>
                                            @endif

                                            <a data-toggle="modal"  data-target="#AddVariants" class="btn btn-default btn-xs pull-right"
                                            >ADD +</a>

                                            <div class="clearfix"></div>
                                            <ul id="sortable">
                                                @if(!empty($selectedAttributes) && count($selectedAttributes))
                                                    @foreach($selectedAttributes as $att)
                                                        <li id="{{$att->id}}" class="remove-item farsi-text">
                                                            {{$att->title}}
                                                        </li>
                                                    @endforeach
                                                @endif
                                            </ul>

                                            <span class="validation-message-block">{{ $errors->first('variants', ':message') }}</span>
                                            <input id="variants-values" name="variants" type="hidden" value="">
                                        </div>
                                    </div>
                                </div>


                                {{--<div class="col-md-12 col-xs-12">--}}
                                    {{--<div class="form-group {{ $errors->has('variants') ? 'has-error' : '' }}">--}}
                                        {{--<label class="control-label"><span>Variants</span></label>--}}
                                        {{--<div class="input-icon right">--}}
                                            {{--@if($errors->has('variants'))--}}
                                                {{--<i class="fa fa-exclamation tooltips" data-container="body"></i>--}}
                                            {{--@endif--}}
                                            {{--{!! Form::select('variants[]' , $variants , null , ['class' => 'form-control input-sm select2', 'size' => 1 , 'multiple' , 'style' => 'width: 100%' , 'id' => 'variants_list' ]) !!}--}}
                                            {{--<span class="validation-message-block">{{ $errors->first('variants', ':message') }}</span>--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                {{--</div>--}}

                                <div class="col-md-12 col-xs-12">
                                    <div class="form-group {{ $errors->has('options') ? 'has-error' : '' }}">
                                        <label class="control-label"><span>Options</span></label>
                                        <div class="input-icon right">
                                            @if($errors->has('options'))
                                                <i class="fa fa-exclamation tooltips" data-container="body"></i>
                                            @endif
                                            {!! Form::select('options[]' , $options , null , ['class' => 'form-control input-sm select2', 'size' => 1 , 'multiple' , 'style' => 'width: 100%' , 'id' => 'options_list' ]) !!}
                                            <span class="validation-message-block">{{ $errors->first('options', ':message') }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12 col-xs-12">
                                    <div class="form-group {{ $errors->has('manufactures') ? 'has-error' : '' }}">
                                        <label class="control-label"><span>Manufactures</span></label>
                                        <div class="input-icon right">
                                            @if($errors->has('manufactures'))
                                                <i class="fa fa-exclamation tooltips" data-container="body"></i>
                                            @endif
                                            {!! Form::select('manufactures[]' , $manufactures , null , ['class' => 'form-control input-sm select2',  'size' => 1 , 'multiple' , 'style' => 'width: 100%' , 'id' => 'manufactures_list' ]) !!}
                                            <span class="validation-message-block">{{ $errors->first('manufactures', ':message') }}</span>
                                        </div>
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
                                    <a href="{{route('admin.products.categories')}}" class="btn btn-danger">Cancel</a>
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

@include('products::category.forms._addVariantsModal')

@stop


@section('header')
<link href="{{ Module::asset('products:plugins/icheck/skins/all.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ Module::asset('products:plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ Module::asset('products:plugins/select2/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
@stop

@section('moduleFooter')
<script src="{{ Module::asset('products:plugins/jquery-ui-1.12.1.custom/jquery-ui.min.js') }}" type="application/javascript"></script>
<script src="{{ Module::asset('products:plugins/icheck/icheck.min.js') }}" type="application/javascript"></script>
<script src="{{ Module::asset('products:plugins/select2/js/select2.min.js') }}" type="application/javascript"></script>
<script src="{{ Module::asset('products:plugins/tinymce/tinymce.min.js') }}" type="application/javascript"></script>
<script src="{{ Module::asset('products:js/module.js') }}" type="application/javascript"></script>
<script>
    M6Module.initTinyMCE();
    M6Module.categoryAddEdit();

    var limit = {!! CONFIG_LIMIT_COMBINATION_PRODUCTS_MODULE !!};
    var attributes = {!! $attributes !!}

</script>
@stop



{{--@section('header')--}}
{{--<link href="{{ Module::asset('products:plugins/icheck/skins/all.css') }}" rel="stylesheet" type="text/css" />--}}
{{--<link href="{{ Module::asset('products:plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />--}}
{{--<link href="{{ Module::asset('products:plugins/select2/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css" />--}}
{{--@stop--}}

{{--@section('moduleFooter')--}}
{{--<script src="{{ Module::asset('products:plugins/icheck/icheck.min.js') }}" type="application/javascript"></script>--}}
{{--<script src="{{ Module::asset('products:plugins/select2/js/select2.min.js') }}" type="application/javascript"></script>--}}
{{--<script src="{{ Module::asset('products:plugins/tinymce/tinymce.min.js') }}" type="application/javascript"></script>--}}
{{--<script src="{{ Module::asset('products:js/module.js') }}" type="application/javascript"></script>--}}
{{--<script>--}}
{{--M6Module.initTinyMCE();--}}

{{--var limit = {!! CONFIG_LIMIT_COMBINATION_PRODUCTS_MODULE !!};--}}

{{--$("#parent_list").select2({--}}
    {{--placeholder: 'Select parent category'--}}
{{--});--}}

{{--$("#variants_list").select2({--}}
    {{--placeholder: 'Select Variants types' ,--}}
    {{--maximumSelectionLength: limit--}}
{{--});--}}

{{--$("#option_list").select2({--}}
    {{--placeholder: 'Select Option types'--}}
{{--});--}}


{{--</script>--}}
{{--@stop--}}


