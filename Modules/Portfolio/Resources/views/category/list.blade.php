@extends('admin.include.layout')

@section('content')
<div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="panel panel-grey">
                <div class="panel-heading">
                    <span class="fa fa-folder-open-o"></span>
                    <span class="bold">{{!empty($title) ? $title: ''}}</span>
                    {{--<span class="font-normal"> ( Total : {{$categories->total()}} ) </span>--}}
                    <span class="pull-right">
                    <a href="{{route('admin.portfolio.categories.add')}}" class="btn btn-xs btn-success" style="margin-top: -2px;">+ Add New</a>
                </span>
                </div>
                <div class="panel-body">
                    @if($categories)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="staticDataTable" style="width: 100%; display: none; text-align: center !important;">
                                <thead>
                                <tr>
                                    <th class="center-text" width="25px" ></th>
                                    <th>Title</th>
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
                                        <td class="text-left">
                                            <a href="{{route('admin.portfolio.categories.edit' , $cat)}}">
                                                {{$cat->title}}
                                            </a>
                                        </td>
                                        <td>
                                            {{\Carbon\Carbon::parse($cat->created_at)->format('Y-m-d H:m')}}
                                        </td>
                                        <td class="center-text">
                                            <span class="badge badge-success"> {{ $cat->portfolio_count }} </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-xs btn-group-solid">
                                                <a href="{{route('admin.portfolio.categories.edit' , $cat)}}" class="btn btn-warning"><i class="fa fa-edit"></i></a>
                                                <a href="{{route('admin.portfolio.categories.delete' , $cat)}}" class="btn red confirmation-remove" data-items="{{$cat->products_count}}"><i class="fa fa-trash"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                    @if($cat->children)
                                        @foreach($cat->children as $childCategory)
                                        <tr>
                                            <td class="center-text">
                                                @if($childCategory->is_published == 'N')
                                                    <button class="btn btn-xs btn-default status-change" data-status="{{$childCategory->is_published}}" data-new="{{$childCategory->id}}">
                                                        <i class="fa fa-ban fa-1x text-danger"></i>
                                                    </button>
                                                @else
                                                    <button class="btn btn-xs btn-default status-change" data-status="{{$childCategory->is_published}}" data-new="{{$childCategory->id}}">
                                                        <i class="fa fa-check fa-1x text-success"></i>
                                                    </button>
                                                @endif
                                            </td>
                                            <td class="text-left">
                                                <a href="{{route('admin.portfolio.categories.edit' , $childCategory)}}">
                                                    - {{$childCategory->title}}
                                                </a>
                                            </td>
                                            <td>
                                                {{\Carbon\Carbon::parse($childCategory->created_at)->format('Y-m-d H:m')}}
                                            </td>
                                            <td class="center-text">
                                                <span class="badge badge-success"> {{ $childCategory->portfolio_count }} </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-xs btn-group-solid">
                                                    <a href="{{route('admin.portfolio.categories.edit' , $childCategory)}}" class="btn btn-warning"><i class="fa fa-edit"></i></a>
                                                    <a href="{{route('admin.portfolio.categories.delete' , $childCategory)}}" class="btn red confirmation-remove" data-items="{{$childCategory->portfolio_count}}"><i class="fa fa-trash"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                            @if($childCategory->children)
                                                @foreach($childCategory->children as $SecondChildCategory)
                                                    <tr>
                                                        <td class="center-text">
                                                            @if($SecondChildCategory->is_published == 'N')
                                                                <button class="btn btn-xs btn-default status-change" data-status="{{$SecondChildCategory->is_published}}" data-new="{{$SecondChildCategory->id}}">
                                                                    <i class="fa fa-ban fa-1x text-danger"></i>
                                                                </button>
                                                            @else
                                                                <button class="btn btn-xs btn-default status-change" data-status="{{$SecondChildCategory->is_published}}" data-new="{{$SecondChildCategory->id}}">
                                                                    <i class="fa fa-check fa-1x text-success"></i>
                                                                </button>
                                                            @endif
                                                        </td>
                                                        <td class="text-left">
                                                            <a href="{{route('admin.portfolio.categories.edit' , $SecondChildCategory)}}">
                                                                - - {{$SecondChildCategory->title}}
                                                            </a>
                                                        </td>
                                                        <td>
                                                            {{\Carbon\Carbon::parse($SecondChildCategory->created_at)->format('Y-m-d H:m')}}
                                                        </td>
                                                        <td class="center-text">
                                                            <span class="badge badge-success"> {{ $SecondChildCategory->portfolio_count }} </span>
                                                        </td>
                                                        <td>
                                                            <div class="btn-group btn-group-xs btn-group-solid">
                                                                <a href="{{route('admin.portfolio.categories.edit' , $SecondChildCategory)}}" class="btn btn-warning"><i class="fa fa-edit"></i></a>
                                                                <a href="{{route('admin.portfolio.categories.delete' , $SecondChildCategory)}}" class="btn red confirmation-remove" data-items="{{$SecondChildCategory->portfolio_count}}"><i class="fa fa-trash"></i></a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @if($SecondChildCategory->children))
                                                        @foreach($SecondChildCategory->children as $ThirdChildCategory)
                                                            <tr>
                                                                <td class="center-text">
                                                                    @if($ThirdChildCategory->is_published == 'N')
                                                                        <button class="btn btn-xs btn-default status-change" data-status="{{$ThirdChildCategory->is_published}}" data-new="{{$ThirdChildCategory->id}}">
                                                                            <i class="fa fa-ban fa-1x text-danger"></i>
                                                                        </button>
                                                                    @else
                                                                        <button class="btn btn-xs btn-default status-change" data-status="{{$ThirdChildCategory->is_published}}" data-new="{{$ThirdChildCategory->id}}">
                                                                            <i class="fa fa-check fa-1x text-success"></i>
                                                                        </button>
                                                                    @endif
                                                                </td>
                                                                <td class="text-left">
                                                                    <a href="{{route('admin.portfolio.categories.edit' , $ThirdChildCategory)}}">
                                                                        - - -{{$ThirdChildCategory->title}}
                                                                    </a>
                                                                </td>
                                                                <td>
                                                                    @if($ThirdChildCategory->attributes)
                                                                        @foreach($ThirdChildCategory->attributes as $att)
                                                                            <span class="badge custom-badge">{{ $att->title }}</span>
                                                                        @endforeach
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if($ThirdChildCategory->options)
                                                                        @foreach($ThirdChildCategory->options as $att)
                                                                            <span class="badge custom-badge">{{ $att->title }}</span>
                                                                        @endforeach
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    {{\Carbon\Carbon::parse($ThirdChildCategory->created_at)->format('Y-m-d H:m')}}
                                                                </td>
                                                                <td class="center-text">
                                                                    <span class="badge badge-success"> {{ $ThirdChildCategory->portfolio_count }} </span>
                                                                </td>
                                                                <td>
                                                                    <div class="btn-group btn-group-xs btn-group-solid">
                                                                        <a href="{{route('admin.portfolio.categories.edit' , $ThirdChildCategory)}}" class="btn btn-warning"><i class="fa fa-edit"></i></a>
                                                                        <a href="{{route('admin.portfolio.categories.delete' , $ThirdChildCategory)}}" class="btn red confirmation-remove" data-items="{{$ThirdChildCategory->portfolio_count}}"><i class="fa fa-trash"></i></a>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @endif
                                                @endforeach
                                            @endif
                                        @endforeach
                                    @endif
                                @endforeach
                                </tbody>
                            </table>
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
<script src="{{ Module::asset('portfolio:js/module.js') }}" type="application/javascript"></script>
<script>
M6Module.ajaxStatusChange('portfolio/categories/AjaxStatusUpdate');
M6Module.productsCatList();
</script>
@stop
