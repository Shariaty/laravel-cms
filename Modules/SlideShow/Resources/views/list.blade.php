@extends('admin.include.layout')

@section('header')
    <link href="{{asset('assets/plugins/jquery-ui-1.12.1/jquery-ui.min.css')}}" rel="stylesheet" type="text/css" />
    <style>
        tr:not(thead) {
            background-color: white;
            margin: 10px;
            border-bottom: 1px solid #e8e8e8;
        }
    </style>
@stop

@section('content')
<div class="row">
    <div class="col-md-12 col-xs-12">
        <div class="panel panel-grey">
                <div class="panel-heading">
                    <span class="fa fa-users"></span>
                    <span class="bold">{{!empty($title) ? $title: ''}}</span>
                    <span class="font-normal"> ( Total : {{$slides->count()}} ) </span>
                    <span class="pull-right">
                    <a href="{{route('admin.slide.categories')}}" class="btn btn-xs btn-danger" style="margin-top: -2px;">Back</a>
                    <a href="{{route('admin.slide.add' , $cat)}}" class="btn btn-xs btn-success" style="margin-top: -2px;">+ Add New</a>
                </span>
                </div>
                <div class="panel-body">
                    @if(count($slides))
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                <tr style="background-color: whitesmoke!important;">
                                    <th>Title</th>
                                    <th width="150px" class="center-text">Created at</th>
                                    <th width="50px" class="center-text">Status</th>
                                    <th width="100px"></th>
                                </tr>
                                </thead>
                                <tbody id="menu">
                                @foreach($slides as $post)
                                    <tr data-item="{{$post->id}}" style="cursor: pointer">
                                        <td>
                                            {{$post->title}}
                                        </td>

                                        <td class="center-text">
                                            {{\Carbon\Carbon::parse($post->created_at)->diffForHumans()}}
                                        </td>
                                        <td class="center-text">
                                            @if($post->is_published == 'N')
                                                <button class="btn btn-xs btn-default status-change" data-status="{{$post->is_published}}" data-new="{{$post->id}}">
                                                    <i class="fa fa-ban fa-1x text-danger"></i>
                                                </button>
                                            @else
                                                <button class="btn btn-xs btn-default status-change" data-status="{{$post->is_published}}" data-new="{{$post->id}}">
                                                    <i class="fa fa-check fa-1x text-success"></i>
                                                </button>
                                            @endif
                                        </td>
                                        <td style="width: 3px;">
                                            <div class="btn-group btn-group-xs btn-group-solid">
                                                <a href="{{route('admin.slide.edit' , [$cat , $post])}}"  class="btn blue"><i class="fa fa-edit"></i></a>
                                                <a href="{{route('admin.slide.delete' , $post)}}" class="btn red confirmation-remove"><i class="fa fa-trash"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>

                            </table>
                        </div>

                        <div class="text-center">
                            {{ $slides->render() }}
                        </div>
                    @else
                        <div class="text-center" style="padding: 50px;">
                            <img width="100px" src="{{asset('assets/admin/images/icons/sad.png')}}">
                            <p>No data found to view</p>
                        </div>
                    @endif
                </div>
            </div>
    </div>
</div>
@stop




@section('moduleHeader')
<style>
    .ui-sortable-placeholder{
        border-top: 2px dotted red;
    }
</style>
@stop


@section('moduleFooter')
    <script src="{{asset('assets/plugins/jquery-ui-1.12.1/jquery-ui.min.js')}}"></script>
    <script src="{{ Module::asset('slideshow:js/module.js') }}" type="application/javascript"></script>
    <script>
        M6Module.ajaxStatusChange('slide-show/AjaxStatusUpdate');
        M6Module.ajaxOrderingSystem('slide-show/AjaxSort');
    </script>
@stop
