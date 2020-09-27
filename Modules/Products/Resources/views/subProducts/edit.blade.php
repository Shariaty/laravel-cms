@extends('admin.include.layout')

@section('content')
<div class="row">
    <div class="col-md-12 col-xs-12">
        <div class="panel-body form">
            <div class="panel panel-grey">
                <div class="panel-heading">{{$title}}</div>
                <div class="panel-body">
                    <ul class="nav nav-tabs">
                        <li class="active" data-event-tab data-tab-data="active">
                            <a href="#active" data-toggle="tab" aria-expanded="true">
                                <i class="icon-basket-loaded icons"></i>&nbsp;Product
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade active in" id="active">
                            {!! Form::model( $product , array('route' => ['admin.subProduct.update' , $product] , 'method' => 'POST' , 'id' => 'FormStore')) !!}

                            <div class="col-md-2 col-xs-12">
                                <div class="md-checkbox" style="margin-top: 30px;">
                                    <input name="is_published" type="checkbox" id="checkbox2" class="md-check" {{$product->is_published == 'Y' ? 'checked' : ''}}>
                                    <label for="checkbox2">
                                        <span class="inc"></span>
                                        <span class="check"></span>
                                        <span class="box"></span> Publish Status</label>
                                </div>
                            </div>

                            @if($filteringData && count((array)$filteringData))
                                <div class="row" style="padding-bottom: 20px">
                                    <div class="col-md-3 col-xs-12">
                                        @foreach($filteringData as $key => $value)
                                            <div class="col-md-12 col-xs-12">
                                                <div class="form-group {{ $errors->has('categories') ? 'has-error' : '' }}">
                                                    {!! Form::label( $value->title , $value->title) !!}
                                                    {!! Form::select( 'option_'.intval($key+1), $value->select2Values , null , ['class' => 'form-control select2']) !!}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if($motherType === PRODUCT_TYPE_COMPLEX)
                            <hr/>
                            <div style="padding: 20px">
                                <a href="javascript:;" class="btn btn-xs btn-success add-bom">Add build of materials (BOM)</a>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-responsive"  style="margin-top: 20px;">
                                            <table class="table table-bordered">
                                                <thead>
                                                <tr class="item-row">
                                                    <th style="width: 31%">Raw Material (Build From)</th>
                                                    <th style="width: 31%">Conversion Unit</th>
                                                    <th style="width: 31%">To</th>
                                                </tr>
                                                </thead>
                                                <tbody>

                                                @if(count($selectedRaw))
                                                    @foreach($selectedRaw as $key => $value)

                                                        <tr class="item-row">
                                                            <td class="item-name">
                                                                <div class="form-group">
                                                                    <div class="delete-btn">
                                                                        {!! Form::select('rawProduct[]' , $rawProductsList  , $value , ['class' => 'select2 form-control item product_selector' , 'style' => 'width: 100%']) !!}

                                                                        {{--<select class="form-control item product_selector" name="rawProduct[]" style="width: 100%;"></select>--}}
                                                                        <span class="validation-message-block"></span>
                                                                        <a class="remove-item-row delete" href="javascript:;" title="Remove row">X</a>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            <td>
                                                                <div class="form-group">
                                                                    <input class="form-control convert" value="{{$selectedValue[$key]}}" name="convert[][{{ random_int(11111,99999) }}]" placeholder="conversion" type="number">
                                                                    <span class="validation-message-block"></span>
                                                                    </div>
                                                                </td>
                                                            <td>
                                                                <div>
                                                                    <span class="rawUnit"><span class="text-success farsi-text">{{$units[$key]}}</span></span>
                                                                </div>
                                                            </td>
                                                        </tr>

                                                    @endforeach
                                                @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif


                        </div>
                        <input type="hidden" id="store_id" data-id="{{$product->sku}}">

                        <div class="row">
                            <div class="col-md-12 col-xs-12">
                                <div class="col-md-12 col-xs-12">
                                    <hr />
                                    <a href="{{route('admin.subProducts.list' , $product->parent)}}" class="btn btn-warning" >
                                        <i class="icon icon-action-undo icons"></i>&nbsp;Cancel
                                    </a>
                                    <button type="button" class="btn btn-success pull-right" id="btnCreate">
                                        <i class="fa fa-floppy-o" aria-hidden="true"></i> &nbsp; Submit
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
</div>
@stop


@section('moduleHeader')
<style>
    .nav-pills, .nav-tabs{
        margin-bottom: 0px !important;
    }

    .tab-content{
        background-color: white;
        padding: 20px 10px 10px 10px;
        border: 1px solid #ddd;
        border-top: 0;
    }

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
<link href="{{asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('assets/plugins/select2/css/select2-bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ Module::asset('products:css/module.css') }}" rel="stylesheet" type="text/css" />
@stop

@section('moduleFooter')
<script src="{{asset('assets/plugins/select2/js/select2.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/plugins/jquery-mask/jquery.mask.min.js')}}" type="text/javascript"></script>
<script src="{{ Module::asset('products:plugins/tinymce/tinymce.min.js') }}" type="application/javascript"></script>
<script src="{{ Module::asset('products:js/module.js') }}" type="application/javascript"></script>
<script>
var rawProductsList = {!! json_encode($rawProductsList) !!};
var rawProductsListForJs = {!! json_encode($rawProductsListForJs) !!};
// M6Module.tabInitialize();
M6Module.subProduct();
// M6Module.initTinyMCE();
$('#price').mask("000,000,000,000,000", {reverse: true});

</script>
@stop


