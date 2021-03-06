@extends('admin.include.layout')
@section('content')
<div class="row">
    <div class="col-md-12 col-xs-12">
        <div class="panel panel-grey">
            <div class="panel-heading">
                <span class="fa fa-shopping-cart"></span>
                <span class="bold">{{!empty($title) ? $title: ''}}</span>
                <span class="pull-right">
                <a href="{{route('admin.products.add')}}" class="btn btn-xs btn-success" style="margin-top: -2px;">+ Add Product</a>
                </span>
            </div>
            <div class="panel-body">
                <table class="table table-bordered table-responsive" id="products-table" style="width: 100%; display: none; text-align: center; font-family: Tahoma, Helvetica, Arial;">
                    <thead>
                    <tr>
                        <th class="center-text" width="1"></th>
                        <th class="center-text" width="100">SKU</th>
                        <th class="center-text">Title</th>
                        <th class="center-text">Category</th>
                        <th class="center-text" width="60" >Sub Products</th>
                        <th class="center-text" width="60" >Type</th>
                        <th class="center-text" width="100">Created At</th>
                        <th class="center-text" width="60" ></th>
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
<script src="{{ Module::asset('products:js/module.js') }}" type="application/javascript"></script>
<script>
M6Module.ajaxStatusChange('products/AjaxStatusUpdate');
M6Module.productList();
</script>
@stop