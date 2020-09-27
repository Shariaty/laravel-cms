@extends('admin.include.layout')

@section('content')
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="panel-body form">
                <div class="panel panel-grey">
                    <div class="panel-heading">{{$title}}</div>
                    <div class="panel-body">

                        {!! Form::open( array('route' => ['admin.warehouse.create'] , 'method' => 'POST' , 'id' => 'FormInvoice')) !!}
                        <div class="col-md-4 col-xs-12">
                                <div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">
                                    <label class="control-label">Purchase Invoice Title</label>
                                    {!! Form::text('title' , null , ['class' => 'form-control']) !!}
                                    <span class="validation-message-block">{{ $errors->first('title', ':message') }}</span>
                                </div>
                            </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive"  style="margin-top: 20px;">
                                    <table class="table table-bordered">
                                        <thead>
                                        <tr class="item-row">
                                            <th style="width: 500px">Item</th>
                                            <th>Price</th>
                                            <th>Quantity</th>
                                            <th>Total</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        <tr id="hiderow">
                                            <td colspan="4">
                                                <a id="addRow" href="javascript:;" title="Add a row" class="btn btn-primary">Add a row</a>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td class="text-right"><strong>Sub Total</strong></td>
                                            <td><span id="subtotal">0.00</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total Quantity: </strong><span id="totalQty" style="color: red; font-weight: bold">0</span> Units</td>
                                            <td></td>
                                            <td style="display: none;" class="text-right" ><strong>Discount</strong></td>
                                            <td style="display: none;"><input class="form-control" id="discount" value="0" type="text"></td>
                                        </tr>
                                        <tr style="display: none;">
                                            <td></td>
                                            <td></td>
                                            <td class="text-right"><strong>Shipping</strong></td>
                                            <td><input class="form-control" id="shipping" value="0" type="text"></td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td class="text-right"><strong>Grand Total</strong>
                                                <input type="hidden" id="total_price" value="0" name="total_price"/>
                                                <input type="hidden" id="total_qty" value="0" name="total_qty"/>
                                            </td>
                                            <td><span id="grandTotal">0</span></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-xs-12">
                                <div class="col-md-12 col-xs-12">
                                    <hr />
                                    <a href="{{route('admin.warehouse.list')}}" class="btn btn-warning" >
                                        <i class="icon icon-action-undo icons"></i>&nbsp;Cancel
                                    </a>
                                    <button type="button" class="btn btn-success pull-right" id="btnCreate">
                                        <i class="fa fa-floppy-o" aria-hidden="true"></i>
                                        Save Invoice
                                    </button>
                                </div>
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
.delete-btn {
    position: relative;
}
.delete {
    display: block;
    color: #fff;
    text-decoration: none;
    position: absolute;
    background: #ff0000;
    font-weight: bold;
    padding: 0px 4px;
    border-radius: 25px !important;
    top: -6px;
    left: -6px;
    font-family: Verdana;
    font-size: 12px;
}
.select2-results__option , .select2-selection__rendered{
    direction: rtl;
    text-align: right;
    font-family: Tahoma, Helvetica, Arial;
}
.form-group {
    margin-bottom: -5px !important;
}
</style>
@stop

@section('moduleFooter')
<script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ Module::asset('warehouse:plugins/invoice/jquery.invoice.js')}}" type="text/javascript"></script>
<script src="{{ Module::asset('warehouse:js/module.js') }}" type="application/javascript"></script>
<script>
var productList = {!! json_encode($productList) !!};

M6Module.createEditInvoice();

$(document).ready(function(){
$().invoice({
    addRow : "#addRow",
    delete : ".delete",
    parentClass : ".item-row",

    price : ".price",
    qty : ".qty",
    total : ".total",
    totalQty: "#totalQty",

    subtotal : "#subtotal",
    discount: "#discount",
    shipping : "#shipping",
    grandTotal : "#grandTotal"
});

});
</script>
@stop


