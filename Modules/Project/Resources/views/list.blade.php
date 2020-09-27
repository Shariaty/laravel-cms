@extends('admin.include.layout')
@section('content')
<div class="row">
    <div class="col-md-12 col-xs-12">
        <div class="panel panel-grey">
                <div class="panel-heading">
                    <span class="fa fa-users"></span>
                    <span class="bold">{{!empty($title) ? $title: ''}}</span>
                    <span class="font-normal"> ( Total : {{$projects->count()}} ) </span>
                    <span class="pull-right">
                    <a href="{{route('admin.projects.add')}}" class="btn btn-xs btn-success" style="margin-top: -2px;">+ Add New</a>
                </span>
                </div>
                <div class="panel-body">
                    @if(count($projects))
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Url</th>
                                    <th>Created at</th>
                                    <th>Last Update</th>
                                    <th width="40px" class="center-text"></th>
                                    <th class="center-text">Status</th>
                                    <th class="center-text">Views</th>
                                    <th width="60px"></th>
                                </tr>
                                </thead>
                                <tbody id="menu">
                                @foreach($projects as $post)
                                    <tr data-item="{{$post->id}}" style="cursor: pointer">
                                        <td>
                                            {{$post->title}}
                                        </td>
                                        <td>
                                            {{$post->url}}
                                        </td>
                                        <td>
                                            {{\Carbon\Carbon::parse($post->created_at)->format('Y-m-d H:m')}}
                                        </td>
                                        <td>
                                            {{\Carbon\Carbon::parse($post->updated_at)->diffForHumans()}}
                                        </td>
                                        <td>
                                            @if($post->is_expired == 'Y')
                                                <span class="text-danger">Expired</span>
                                            @endif
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
                                        <td class="center-text">
                                            <span class="badge badge-success"> {{$post->views}} </span>
                                        </td>
                                        <td style="width: 3px;">
                                            <div class="btn-group btn-group-xs btn-group-solid">
                                                <a href="{{route('admin.projects.edit' , $post)}}"  class="btn blue"><i class="fa fa-edit"></i></a>
                                                <a href="{{route('admin.projects.delete' , $post)}}" class="btn red confirmation-remove"><i class="fa fa-trash"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>

                            </table>
                        </div>

                        <div class="text-center">
                            {{ $projects->render() }}
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
    M6.ajaxStatusChange('projects/AjaxStatusUpdate');
    M6.ajaxOrderingSystem('projects/AjaxSort');
</script>
@stop