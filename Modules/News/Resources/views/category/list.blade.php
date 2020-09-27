@extends('admin.include.layout')
@section('content')
<div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="panel panel-grey">
                <div class="panel-heading">
                    <span class="fa fa-folder-open-o"></span>
                    <span class="bold">{{!empty($title) ? $title: ''}}</span>
                    <span class="font-normal"> ( Total : {{$categories->count()}} ) </span>
                    <span class="pull-right">
                    <a href="{{route('admin.news.categories.add')}}" class="btn btn-xs btn-success" style="margin-top: -2px;">+ Add New</a>
                </span>
                </div>
                <div class="panel-body">
                    @if(count($categories))
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="staticDataTable" style="width: 100%; display: none; text-align: center !important;">
                                <thead>
                                <tr>
                                    <th class="center-text" width="25px" ></th>
                                    <th class="center-text">Title</th>
                                    <th class="center-text">Slug</th>
                                    <th class="center-text" width="125px">Created at</th>
                                    <th class="center-text" width="50px">Items</th>
                                    <th class="center-text" width="60px"></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($categories as $cat)
                                    <tr>
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
                                        <td>
                                            <a href="{{route('admin.news.categories.edit' , $cat)}}">
                                                {{$cat->title}}
                                            </a>
                                        </td>
                                        <td>
                                            {{$cat->slug}}
                                        </td>
                                        <td>
                                            {{\Carbon\Carbon::parse($cat->created_at)->format('Y-m-d H:m')}}
                                        </td>
                                        <td class="center-text">
                                            <span class="badge badge-success"> {{ $cat->news->count() }} </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-xs btn-group-solid">
                                                <a href="{{route('admin.news.categories.edit' , $cat)}}" class="btn btn-warning"><i class="fa fa-edit"></i></a>
                                                <a href="{{route('admin.news.categories.delete' , $cat)}}" class="btn red confirmation-remove" data-items="{{$cat->news->count()}}"><i class="fa fa-trash"></i></a>
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

@section('moduleHeader')
<link href="{{asset('assets/plugins/datatables/datatables.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/plugins/datatables/Responsive-2.2.1/css/responsive.bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
@stop

@section('moduleFooter')
<script src="{{asset('assets/plugins/datatables/datatables.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/plugins/datatables/Responsive-2.2.1/js/responsive.bootstrap.min.js')}}" type="text/javascript"></script>
<script src="{{ Module::asset('news:js/news.js') }}" type="application/javascript"></script>
<script>
M6NEWS.ajaxStatusChange('news/categories/AjaxStatusUpdate');
M6NEWS.newsCatList();
</script>
@stop
