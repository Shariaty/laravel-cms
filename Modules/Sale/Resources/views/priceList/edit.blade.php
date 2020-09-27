@extends('admin.include.layout')

@section('content')
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="panel-body form">
                <div class="panel panel-grey">
                    <div class="panel-heading">{{$title}}</div>
                    <div class="panel-body" style="text-align: center; font-family: Tahoma, Helvetica, Arial;">
                        {!! Form::model( $priceList , array('route' => ['admin.priceList.update' , $priceList] , 'method' => 'POST' , 'id' => 'FormPriceList')) !!}

                        <div class="row">
                            <table class="table table-bordered" style="background-color: white">
                        <thead>
                            <tr>
                                <td width="250">SKU</td>
                                <td>Title</td>
                                {{--<td>Id</td>--}}
                                <td width="250">Price (Dirham)</td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($priceList->items as $item)
                            <tr>
                                <input type="hidden" name="item[]" value="{{$item->product_id}}"/>
                                <td><span>{{ $productListCustom[$item->product_id]->sku }}</span></td>
                                <td><span>{{ $productListCustom[$item->product_id]->text }}</span></td>
                                <td>
                                    <div class="form-group {{ $errors->has('price') ? 'has-error' : '' }}">
                                        {!! Form::text('price[]['.random_int(11111,99999).']' , $item->price ? $item->price : null , ['class' => 'form-control price_value' , 'placeholder' => 'price']) !!}
                                        <span class="validation-message-block">{{ $errors->first('price', ':message') }}</span>
                                    </div>
                                </td>
                                <input type="hidden" name="titles[]" value="{{$productListCustom[$item->product_id]->text}}"/>
                                <input type="hidden" name="sku[]" value="{{$productListCustom[$item->product_id]->sku}}"/>
                            </tr>
                            @endforeach
                        </tbody>
                        </table>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-xs-12">
                                <hr />
                                <a href="{{route('admin.priceList.list')}}" class="btn btn-warning pull-left" >
                                    <i class="icon icon-action-undo icons"></i>&nbsp;Cancel
                                </a>
                                <button type="button" class="btn btn-success pull-right" id="btnCreate">
                                    <i class="fa fa-floppy-o" aria-hidden="true"></i>
                                    Update List
                                </button>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop


@section('moduleHeader')
<link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/select2/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
<style>
    .form-group{
        margin-bottom: 0px;
    }
    .panel-body{
        padding-top: 0px !important;
    }
    .table{
        margin-bottom: 0px !important;
    }
</style>
@stop

@section('moduleFooter')
<script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ Module::asset('sale:plugins/invoice/jquery.invoice.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/plugins/jquery-mask/jquery.mask.min.js')}}" type="text/javascript"></script>
<script src="{{ Module::asset('sale:js/module.js') }}" type="application/javascript"></script>
<script>
M6Module.createEditPriceList();
</script>
@stop


