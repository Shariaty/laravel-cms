@extends('admin.include.layout')

@section('content')
<div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="panel panel-grey">
                <div class="panel-heading">
                    <span class="fa fa-folder-open-o"></span>
                    <span class="bold">{{!empty($title) ? $title: ''}}</span>
                    {{--<span class="font-normal"> ( Total : {{$designers->total()}} ) </span>--}}
                    <span class="pull-right">
                    <a href="{{route('admin.portfolio.designers.add')}}" class="btn btn-xs btn-success" style="margin-top: -2px;">+ Add New</a>
                </span>
                </div>
                {{--{!! dump($designers) !!}--}}
                <div class="panel-body">
                    @if($designers)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="staticDataTable" style="width: 100%; display: none; text-align: center !important;">
                                <thead>
                                <tr>
                                    <th class="center-text" width="25px"></th>
                                    <th>Name</th>
                                    <th class="center-text" width="50px">Items</th>
                                    <th class="center-text" width="150px">Created at</th>
                                    <th class="center-text" width="60px"></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($designers as $designer)
                                    <tr>
                                        <td class="center-text">
                                            @if($designer->is_published == 'N')
                                                <button class="btn btn-xs btn-default status-change" data-status="{{$designer->is_published}}" data-new="{{$designer->id}}">
                                                    <i class="fa fa-ban fa-1x text-danger"></i>
                                                </button>
                                            @else
                                                <button class="btn btn-xs btn-default status-change" data-status="{{$designer->is_published}}" data-new="{{$designer->id}}">
                                                    <i class="fa fa-check fa-1x text-success"></i>
                                                </button>
                                            @endif
                                        </td>
                                        <td style="text-align: left">
                                            {{ $designer->title }}
                                        </td>
                                        <td width="50">
                                            <span class="badge badge-success">{{ $designer->portfolio_count }}</span>
                                        </td>

                                        <td width="150">
                                            {{\Carbon\Carbon::parse($designer->created_at)->format('Y-m-d H:m')}}
                                        </td>

                                        <td>
                                            <div class="btn-group btn-group-xs btn-group-solid">
                                                <a href="{{route('admin.portfolio.designers.edit' , $designer)}}" class="btn btn-warning"><i class="fa fa-edit"></i></a>
                                                <a href="{{route('admin.portfolio.designers.delete' , $designer->id)}}" class="btn red confirmation-remove"><i class="fa fa-trash"></i></a>
                                            </div>
                                        </td>
                                    </tr>
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
<script src="{{asset('assets/plugins/jquery-mask/jquery.mask.min.js')}}" type="text/javascript"></script>
<script src="{{ Module::asset('portfolio:js/module.js') }}" type="application/javascript"></script>
<script>
M6Module.ajaxStatusChange('portfolio/designers/AjaxStatusUpdate');
M6Module.designersList();
</script>
@stop
