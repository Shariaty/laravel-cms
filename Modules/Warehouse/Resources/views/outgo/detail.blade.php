@extends('admin.include.layout')

@section('content')
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="panel-body form">
                <div class="portlet">
                    <div class="panel-heading">
                        <div>{{$title}}</div>
                        <div class="pull-right print printable-visible" style="margin-top: -15px">
                            <i class="fa fa-2x fa-print"></i>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div style="direction: rtl" class="farsi-text">

                        <div class="printable-invisible">
                            <div class="img-container" style="text-align: center">
                                <img src="{{asset('assets/admin/images/logo-transparent.png')}}" alt="logo">
                                <hr/>
                            </div>
                        </div>

                        <div>
                            <span>
                                <strong>شماره حواله خروج از انبار :</strong>&nbsp;
                                 {{$invoice->code}}
                            </span>
                            <br/>
                        </div>


                        <table class="table table-bordered table-responsive-lg card-table" style="margin-top: 20px">
                            <tbody>
                            <tr style="background-color: lightgray">
                                <th class="text-center">کد محصول</th>
                                <th class="text-center">نام محصول</th>
                                <th class="text-center">میزان</th>
                                <th class="text-center">واحد</th>
                            </tr>


                            @foreach($invoiceGoods as $good)
                                <tr>
                                    <td class="text-center"><span>{{ $good->product->visible_sku }}</span></td>
                                    <td class="text-center"><span>{{ generateGoodItemTitle($good->product) }}</span></td>
                                    <td class="text-center"><span>{{ $good->quantity }}</span></td>
                                    <td class="text-center"><span>{{ generateUnitView($good->unit_id) }}</span></td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>

                        @if($invoice->saleInvoice)
                        <a href="{{route('admin.order.detail' , $invoice->saleInvoice)}}" class="btn btn-success printable-visible">
                            نمایش فاکتور فروش
                        </a>
                        @endif

                </div>
            </div>
        </div>
    </div>
@stop


@section('moduleHeader')

@stop

@section('moduleFooter')
<script src="{{asset('assets/plugins/jquery-mask/jquery.mask.min.js')}}" type="text/javascript"></script>
<script>
$('.print').on('click' , function () { window.print(); });
$('.price').mask("000,000,000,000,000  تومان", {reverse: true});

$('.order-status-change').click(function (e) {
    var href = $(this).attr('href');
    swal({
        title: "لغو سفارش",
        text: "آیا اطمینان دارید از لغو کردن این سفارش",
        showCancelButton: true,
        confirmButtonColor: "#31c7b2",
        cancelButtonColor: "#DD6B55",
        confirmButtonText: "بله ، سفارش را لغو کن",
        cancelButtonText: "انصراف"
    }).then(function(result) {
        if (result.value) {
            window.location.href = href;
        }
    });
    return false;
});
</script>
@stop


