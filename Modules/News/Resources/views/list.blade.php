@extends('admin.include.layout')

@section('content')
<div class="row">
    <div class="col-md-12 col-xs-12">
            <div class="panel panel-grey">
                <div class="panel-heading">
                    <span class="fa fa-newspaper-o"></span>
                    <span class="bold">{{!empty($title) ? $title: ''}}</span>
                    <span class="font-normal"> ( Total : {{$news->count()}} ) </span>
                    <span class="pull-right">
                    <a href="{{route('admin.news.add')}}" class="btn btn-xs btn-success" style="margin-top: -2px;">+ Add New</a>
                </span>
                </div>
                <div class="panel-body">
                    <table class="table table-bordered table-responsive table-hover" id="news-table" style="width: 100%; display: none; text-align: center !important;">
                        <thead>
                        <tr>
                            <th width="1" class="center-text"></th>
                            <th class="center-text">title</th>
                            <th width="250" class="center-text">categories</th>
                            <th width="50" class="center-text">Views</th>
                            <th width="80" class="center-text">Created At</th>
                            <th width="60" class="center-text"></th>
                        </tr>
                        </thead>
                    </table>

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
M6NEWS.ajaxStatusChange('news/AjaxStatusUpdate');
M6NEWS.newsList();
</script>
@stop


