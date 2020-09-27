@extends('admin.include.layout')
@section('content')
<div class="row">
    <div class="col-md-12 col-xs-12">
        <div class="panel panel-grey">
            <div class="panel-heading">
                <span class="fa fa-shopping-cart"></span>
                <span class="bold">{{!empty($title) ? $title: ''}}</span>
                <span class="pull-right">
                <a href="{{route('admin.services.add')}}" class="btn btn-xs btn-success" style="margin-top: -2px;">+ Add Item</a>
                </span>
            </div>
            <div class="panel-body">
                @if($services)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="staticDataTable" style="width: 100%; display: none; text-align: center !important;">
                            <thead>
                            <tr>
                                <th class="center-text" width="25px" ></th>
                                <th>Title</th>
                                <th class="center-text" width="125px">Created at</th>
                                <th class="center-text" width="60px"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($services as $service)
                                <tr>
                                    <td class="center-text">
                                        @if($service->is_published == 'N')
                                            <button class="btn btn-xs btn-default status-change" data-status="{{$service->is_published}}" data-new="{{$service->id}}">
                                                <i class="fa fa-ban fa-1x text-danger"></i>
                                            </button>
                                        @else
                                            <button class="btn btn-xs btn-default status-change" data-status="{{$service->is_published}}" data-new="{{$service->id}}">
                                                <i class="fa fa-check fa-1x text-success"></i>
                                            </button>
                                        @endif
                                    </td>
                                    <td class="text-left">
                                        <a href="{{route('admin.services.edit' , $service)}}">
                                            {{$service->title}}
                                        </a>
                                    </td>
                                    <td>
                                        {{\Carbon\Carbon::parse($service->created_at)->format('Y-m-d H:m')}}
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-xs btn-group-solid">
                                            <a href="{{route('admin.services.edit' , $service)}}" class="btn btn-warning"><i class="fa fa-edit"></i></a>
                                            <a href="{{route('admin.services.delete' , $service)}}" class="btn red confirmation-remove"><i class="fa fa-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                @if($service->children)
                                    @foreach($service->children as $childService)
                                        <tr>
                                            <td class="center-text">
                                                @if($childService->is_published == 'N')
                                                    <button class="btn btn-xs btn-default status-change" data-status="{{$childService->is_published}}" data-new="{{$childService->id}}">
                                                        <i class="fa fa-ban fa-1x text-danger"></i>
                                                    </button>
                                                @else
                                                    <button class="btn btn-xs btn-default status-change" data-status="{{$childService->is_published}}" data-new="{{$childService->id}}">
                                                        <i class="fa fa-check fa-1x text-success"></i>
                                                    </button>
                                                @endif
                                            </td>
                                            <td class="text-left">
                                                <a href="{{route('admin.services.edit' , $childService)}}">
                                                    - {{$childService->title}}
                                                </a>
                                            </td>
                                            <td>
                                                {{\Carbon\Carbon::parse($childService->created_at)->format('Y-m-d H:m')}}
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-xs btn-group-solid">
                                                    <a href="{{route('admin.services.edit' , $childService)}}" class="btn btn-warning"><i class="fa fa-edit"></i></a>
                                                    <a href="{{route('admin.services.delete' , $childService)}}" class="btn red confirmation-remove" data-items="{{$childService->portfolio_count}}"><i class="fa fa-trash"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                        @if($childService->children)
                                            @foreach($childService->children as $SecondChildService)
                                                <tr>
                                                    <td class="center-text">
                                                        @if($SecondChildService->is_published == 'N')
                                                            <button class="btn btn-xs btn-default status-change" data-status="{{$SecondChildService->is_published}}" data-new="{{$SecondChildService->id}}">
                                                                <i class="fa fa-ban fa-1x text-danger"></i>
                                                            </button>
                                                        @else
                                                            <button class="btn btn-xs btn-default status-change" data-status="{{$SecondChildService->is_published}}" data-new="{{$SecondChildService->id}}">
                                                                <i class="fa fa-check fa-1x text-success"></i>
                                                            </button>
                                                        @endif
                                                    </td>
                                                    <td class="text-left">
                                                        <a href="{{route('admin.services.edit' , $SecondChildService)}}">
                                                            - - {{$SecondChildService->title}}
                                                        </a>
                                                    </td>
                                                    <td>
                                                        {{\Carbon\Carbon::parse($SecondChildService->created_at)->format('Y-m-d H:m')}}
                                                    </td>
                                                    <td>
                                                        <div class="btn-group btn-group-xs btn-group-solid">
                                                            <a href="{{route('admin.services.edit' , $SecondChildService)}}" class="btn btn-warning"><i class="fa fa-edit"></i></a>
                                                            <a href="{{route('admin.services.delete' , $SecondChildService)}}" class="btn red confirmation-remove"><i class="fa fa-trash"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @if($SecondChildService->children)
                                                @foreach($SecondChildService->children as $ThirdChildService)
                                                    <tr>
                                                        <td class="center-text">
                                                            @if($ThirdChildService->is_published == 'N')
                                                                <button class="btn btn-xs btn-default status-change" data-status="{{$ThirdChildService->is_published}}" data-new="{{$ThirdChildService->id}}">
                                                                    <i class="fa fa-ban fa-1x text-danger"></i>
                                                                </button>
                                                            @else
                                                                <button class="btn btn-xs btn-default status-change" data-status="{{$ThirdChildService->is_published}}" data-new="{{$ThirdChildService->id}}">
                                                                    <i class="fa fa-check fa-1x text-success"></i>
                                                                </button>
                                                            @endif
                                                        </td>
                                                        <td class="text-left">
                                                            <a href="{{route('admin.services.edit' , $ThirdChildService)}}">
                                                                - - -{{$ThirdChildService->title}}
                                                            </a>
                                                        </td>
                                                        <td>
                                                            {{\Carbon\Carbon::parse($ThirdChildService->created_at)->format('Y-m-d H:m')}}
                                                        </td>
                                                        <td>
                                                            <div class="btn-group btn-group-xs btn-group-solid">
                                                                <a href="{{route('admin.services.edit' , $ThirdChildService)}}" class="btn btn-warning"><i class="fa fa-edit"></i></a>
                                                                <a href="{{route('admin.services.delete' , $ThirdChildService)}}" class="btn red confirmation-remove"><i class="fa fa-trash"></i></a>
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
<script src="{{ Module::asset('khadamat:js/module.js') }}" type="application/javascript"></script>
<script>
M6Module.ajaxStatusChange('services/AjaxStatusUpdate');
M6Module.servicesList();
</script>
@stop