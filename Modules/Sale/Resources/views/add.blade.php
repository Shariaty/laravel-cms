@extends('admin.include.layout')

@section('content')
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="panel-body form">
                <div class="panel panel-grey">
                    <div class="panel-heading">{{$title}}</div>
                    <div class="panel-body">

                        {!! Form::open( array('route' => ['admin.sale.create'] , 'method' => 'POST' , 'id' => 'FormInvoice')) !!}

                        <div class="col-md-4 col-xs-12">
                            <div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">
                                <label class="control-label">Sale Invoice Title</label>
                                {!! Form::text('title' , null , ['class' => 'form-control']) !!}
                                <span class="validation-message-block">{{ $errors->first('title', ':message') }}</span>
                            </div>
                        </div>

                        <div class="col-md-4 col-xs-12" style="float: right; text-align: right;">
                            <div class="form-group {{ $errors->has('user') ? 'has-error' : '' }}">
                                <label class="control-label">Customer</label>
                                {!! Form::select('user' , $usersList  , null , ['class' => 'form-control user_list' , 'style' => 'width: 100%']) !!}
                                <span class="validation-message-block">{{ $errors->first('user', ':message') }}</span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive"  style="margin-top: 20px;">
                                    <table class="table table-bordered">
                                        <thead>
                                        <tr class="item-row">
                                            <th style="width: 300px">Item</th>
                                            <th style="width: 150px">Price</th>
                                            <th>In Stock</th>
                                            <th>Unit</th>
                                            <th style="width: 200px">Quantity</th>
                                            <th style="width: 200px">Total</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        <tr id="hiderow">
                                            <td colspan="6">
                                                <a id="addRow" href="javascript:;" title="Add a row" class="btn btn-primary">Add a row</a>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td class="text-right"><strong>Sub Total</strong></td>
                                            <td><span id="subtotal">0.00</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total Quantity: </strong><span id="totalQty" style="color: red; font-weight: bold">0</span> Units</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td class="text-right" ><strong>Discount</strong></td>
                                            <td><input class="form-control" name="discount" id="discount" value="0" type="text"></td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td class="text-right"><strong>Shipping</strong></td>
                                            <td><input class="form-control" name="shipping" id="shipping" value="0" type="text"></td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td class="text-right"><strong>(Toman) Grand Total</strong>
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
                                    <a href="{{route('admin.sale.list')}}" class="btn btn-warning" >
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
.stock-number {
    text-align: center;
    font-size: 15px !important;
    font-weight: bold;
}
.conversion{
    font-size: 12px !important;
    font-weight: bold;
    margin-top: 10px;
    margin-left: 5px ;
}

</style>
@stop

@section('moduleFooter')
<script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ Module::asset('sale:plugins/invoice/jquery.invoice.js')}}" type="text/javascript"></script>
<script src="{{ Module::asset('sale:js/module.js') }}" type="application/javascript"></script>
<script>
var productListforJs = {!! json_encode($productListForJs) !!};

M6Module.createEditInvoice();

$(document).ready(function() {

    $().invoice({
        addRow: "#addRow",
        delete: ".delete",
        parentClass: ".item-row",

        price: ".price",
        qty: ".qty",
        total: ".total",
        totalQty: "#totalQty",

        subtotal: "#subtotal",
        discount: "#discount",
        shipping: "#shipping",
        grandTotal: "#grandTotal"
    });

    $(document).on('select2:select', '.product_selector', function (e) {
        var el = $(this).parents('tr:first').find('.price_value:first');
        var elStock = $(this).parents('tr:first').find('.stock:first');
        var elUnit = $(this).parents('tr:first').find('.unit_list:first');
        var elhelperCon = $(this).parents('tr:first').find('.helperCon:first');
        var elConversion = $(this).parents('tr:first').find('.conversion:first');

        $(elUnit).select2().val('').empty();
        $(elConversion).html('X 1');

        var selectedValue = $(this).val();
        var datasending = {id: selectedValue};
        $.ajax({
            type: 'post',
            headers: {'X-CSRF-TOKEN': jQuery('input[name="_token"]').attr('value')},
            url: Path + 'sale/ajaxGetPriceAndQuantity',
            data: JSON.stringify(datasending),
            contentType: "application/json; charset=utf-8",
            traditional: true,
            success: function (data) {
                if (data.status === 'success') {
                    $(el).val(data.response.price);
                    $(el).fadeOut(200).fadeIn(200).fadeOut(200).fadeIn(200);

                    if (data.response.stock > 0) {
                        $(elStock).html('<span class="text-success ">' + data.response.stock + '</span>');
                        $(elStock).fadeOut(200).fadeIn(200).fadeOut(200).fadeIn(200);
                    } else {
                        $(elStock).html('<span class="text-danger">' + data.response.stock + '</span>');
                        $(elStock).fadeOut(200).fadeIn(200).fadeOut(200).fadeIn(200);
                    }

                    console.log(data);
                    $(elUnit).select2({
                        placeholder: 'واحد فروش',
                        data: data.response.unitList,
                        minimumResultsForSearch: -1
                    }).prop('disabled', false);
                    $(elUnit).next().fadeOut(200).fadeIn(200).fadeOut(200).fadeIn(200);

                    $(elhelperCon).val(data.response.con);

                    var inv = new Invoice();
                    inv.init();

                } else {
                    swal({
                        title: "Product info is missing",
                        text: data.message,
                        type: "error",
                        showCancelButton: true,
                        confirmButtonColor: "#31c7b2",
                        cancelButtonColor: "#DD6B55",
                        cancelButtonText: "Ok"
                    })
                }
            }
        });
    });

    $(document).on('select2:select change', '.unit_list', function (e) {
        var selectedValue = $(this).val();
        var elCon = $(this).parents('tr:first').find('.con:first');
        var elhelperCon = $(this).parents('tr:first').find('.helperCon:first');
        var elConversion = $(this).parents('tr:first').find('.conversion:first');

        if(selectedValue === "1") {
            $(elCon).val(selectedValue);
            $(elConversion).html('X 1');
        } else {
            $(elCon).val($(elhelperCon).val());
            $(elConversion).html('X '+$(elhelperCon).val());
        }

        var inv = new Invoice();
        inv.init();
    });

    $('.user_list').select2({
        placeholder: 'انتخاب مشتری'
    });

});


</script>
@stop


