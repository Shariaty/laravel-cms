@extends('admin.include.layout')

@section('header')
    <link href="{{asset('assets/plugins/jquery-ui-1.12.1/jquery-ui.min.css')}}" rel="stylesheet" type="text/css" />
    <style>
        .progress{
            height: 19px;
            margin-top: 1px;
            margin-bottom: 0px !important;
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
                    <span class="font-normal"> ( Total : {{$skills->count()}} ) </span>
                    <span class="pull-right">
                    <a href="{{route('admin.skills.add')}}" class="btn btn-xs btn-success" style="margin-top: -2px;">+ Add New</a>
                </span>
                </div>
                <div class="panel-body">
                    @if(count($skills))
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Percentage</th>
                                    <th class="center-text">Created at</th>
                                    <th class="center-text">Status</th>
                                    <th width="60px"></th>
                                </tr>
                                </thead>
                                <tbody id="menu">
                                @foreach($skills as $post)
                                    <tr data-item="{{$post->id}}" style="cursor: pointer">
                                        <td>
                                            {{$post->title}}
                                        </td>
                                        <td>
                                            <div class="progress progress-striped active">
                                                <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="{{$post->percentage}}" aria-valuemin="0" aria-valuemax="100" style="width: {{$post->percentage}}%">
                                                    {{$post->percentage}}%
                                                </div>
                                            </div>
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
                                                <a href="{{route('admin.skills.edit' , $post)}}"  class="btn blue"><i class="fa fa-edit"></i></a>
                                                <a href="{{route('admin.skills.delete' , $post)}}" class="btn red confirmation-remove"><i class="fa fa-trash"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>

                            </table>
                        </div>

                        <div class="text-center">
                            {{ $skills->render() }}
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

@section('footer')
<script src="{{asset('assets/plugins/jquery-ui-1.12.1/jquery-ui.min.js')}}"></script>
<script>
M6.ajaxStatusChange('skills/AjaxStatusUpdate');
M6.ajaxOrderingSystem('skills/AjaxSort');
</script>
@stop