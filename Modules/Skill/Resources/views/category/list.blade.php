@extends('admin.include.layout')
@section('content')
<div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="panel panel-grey">
                <div class="panel-heading">
                    <span class="fa fa-users"></span>
                    <span class="bold">{{!empty($title) ? $title: ''}}</span>
                    <span class="font-normal"> ( Total : {{$categories->count()}} ) </span>
                    <span class="pull-right">
                    <a href="{{route('admin.skills.categories.add')}}" class="btn btn-xs btn-success" style="margin-top: -2px;">+ Add New</a>
                </span>
                </div>
                <div class="panel-body">
                    @if(count($categories))
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Slug</th>
                                    <th>Created at</th>
                                    <th>Last Update</th>
                                    <th class="center-text">Status</th>
                                    <th class="center-text">Items</th>
                                    <th width="60px"></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($categories as $cat)
                                    <tr>
                                        <td>
                                            <a href="{{route('admin.skills.categories.edit' , $cat)}}">
                                                {{$cat->title}}
                                            </a>
                                        </td>
                                        <td>
                                            {{$cat->slug}}
                                        </td>
                                        <td>
                                            {{\Carbon\Carbon::parse($cat->created_at)->format('Y-m-d H:m')}}
                                        </td>
                                        <td>
                                            {{\Carbon\Carbon::parse($cat->updated_at)->diffForHumans()}}
                                        </td>
                                        <td class="center-text">
                                            @if($cat->is_published == 'N')
                                                <button class="btn btn-xs btn-default status-change" data-status="{{$cat->is_published}}" data-new="{{$cat->id}}">
                                                    <i class="fa fa-ban fa-1x text-danger"></i>
                                                </button>
                                            @else
                                                <button class="btn btn-xs btn-default status-change" data-status="{{$cat->is_published}}" data-new="{{$cat->id}}">
                                                    <i class="fa fa-check fa-1x text-success"></i>
                                                </button>
                                            @endif
                                        </td>
                                        <td class="center-text">
                                            <span class="badge badge-success"> {{  $cat->skills->count() }} </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-xs btn-group-solid">
                                                <a href="{{route('admin.skills.categories.edit' , $cat)}}" class="btn blue"><i class="fa fa-edit"></i></a>
                                                <a href="{{route('admin.skills.categories.delete' , $cat)}}" class="btn red confirmation-remove" data-items="{{$cat->skills->count()}}"><i class="fa fa-trash"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>

                            </table>
                        </div>

                        <div class="text-center">
                            {{ $categories->render() }}
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
<script>
M6.ajaxStatusChange('skills/categories/AjaxStatusUpdate');
</script>
@stop