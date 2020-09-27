@extends('admin.include.layout')
@section('content')
<div class="row">
    <div class="col-md-12 col-xs-12">
        <div class="panel panel-grey">
                <div class="panel-heading">
                    <span class="fa fa-users"></span>
                    <span class="bold">{{!empty($title) ? $title: ''}}</span>
                    <span class="font-normal"> ( Total : {{$pages->count()}} ) </span>
                    <span class="pull-right">
                    <a href="{{route('admin.pages.add')}}" class="btn btn-xs btn-success" style="margin-top: -2px;">+ Add New</a>
                </span>
                </div>
                <div class="panel-body">
                    @if(count($pages))
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Slug</th>
                                    <th>Created at</th>
                                    <th>Last Update</th>
                                    <th width="60px"></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($pages as $post)
                                    <tr>
                                        <td>
                                            {{$post->title}}
                                        </td>
                                        <td>
                                            {{$post->slug}}
                                        </td>
                                        <td>
                                            {{\Carbon\Carbon::parse($post->created_at)->format('Y-m-d H:m')}}
                                        </td>
                                        <td>
                                            {{\Carbon\Carbon::parse($post->updated_at)->diffForHumans()}}
                                        </td>
                                        <td style="width: 3px;">
                                            <div class="btn-group btn-group-xs btn-group-solid">
                                                <a href="{{route('admin.pages.edit' , $post)}}"  class="btn blue"><i class="fa fa-edit"></i></a>
                                                <a href="{{route('admin.pages.delete' , $post)}}" class="btn red confirmation-remove"><i class="fa fa-trash"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>

                            </table>
                        </div>

                        <div class="text-center">
                            {{ $pages->render() }}
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
