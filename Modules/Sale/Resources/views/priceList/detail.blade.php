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
                                <strong>شماره سفارش :</strong>&nbsp;
                                 {{$invoice->invoice_number}}
                            </span>
                            <br/>
                            <span>
                                <strong>نوغ سفارش :</strong>&nbsp;
                                {!! generateOrderTypeBadge($invoice->type) !!}
                            </span>
                            <br/>
                            <span>
                                <strong>زمان ثبت سفارش سفارش :</strong>&nbsp;
                                {{ orderTimeFormat($invoice->created_at) }}
                            </span>
                            <br/>
                            <span>
                                <strong>وضعیت پرداخت :</strong>&nbsp;
                                {!!  generatePaymentStatusBadge($invoice->is_paid) !!}
                            </span>
                            <br/>
                            @if($invoice->transaction_id)
                                <span>
                                <strong>کد تراکنش :</strong>&nbsp;
                                    {{ $invoice->transaction_id }}
                            </span>
                                <br/>
                            @endif
                            <span>
                                <strong>وضعیت سفارش :</strong>&nbsp;
                                {!!  generateOrderStatusBadge($invoice->status) !!}
                            </span>
                            <br/>
                        </div>


                        <table class="table table-bordered table-responsive-lg card-table" style="margin-top: 20px">
                            <tbody>
                            <tr style="background-color: lightgray">
                                <th class="text-center">کد محصول</th>
                                <th class="text-center">نام محصول</th>
                                <th class="text-center">واحد فروش</th>
                                <th class="text-center">تعداد</th>
                                <th class="text-center">مقدار کل</th>
                                <th class="text-center">قیمت واحد</th>
                                <th class="text-center">قیمت کل</th>
                            </tr>


                            @foreach($invoiceGoods as $good)
                                <tr>
                                    <td class="text-center"><span>{{ $good->product->visible_sku }}</span></td>
                                    <td class="text-center"><span>{{ generateGoodItemTitle($good->product) }}</span></td>
                                    <td class="text-center"><span>{{ generateUnitView($good->unit_id) }}</span></td>
                                    <td class="text-center"><span>{{ $good->quantity }}</span></td>
                                    <td class="text-center"><span>{{ $good->final_quantity }}</span></td>
                                    <td class="text-center price"><span>{{ $good->sale_price }}</span></td>
                                    <td class="text-center price"><span>{{ $good->sale_price *  $good->final_quantity }}</span></td>
                                </tr>
                            @endforeach

                            <tr>
                                <th colSpan="6"><span class="pull-right"><strong>جمع کل مبلغ قابل پرداخت</strong></span></th>
                                <th class="text-center" style="background-color: #d3ffd3">
                                    <span class="price">{{$invoice->total_price}}</span>
                                </th>
                            </tr>

                            </tbody>
                        </table>

                        @if($invoice->user)
                        <div>
                            <br/>
                            <span>
                            <strong>نام سفارش دهنده :</strong>&nbsp;
                                {{$invoice->user->fullName}}
                        </span>
                            <br/>
                            <span>
                            <strong>تلفن :</strong>&nbsp;
                                {{$invoice->user->cell}}
                        </span>
                            <br/>
                            @if($invoice->user->email)
                                <span>
                            <strong>پست الکترونیک :</strong>&nbsp;
                                {{$invoice->user->email}}
                            </span>
                            <br/>
                            @endif

                            @if($invoice->address)
                            <span>
                            <strong>آدرس تحویل سفارش :</strong>&nbsp;
                            {{$invoice->address->address}}
                            </span>
                            <br/>
                            @endif
                        </div>
                        @endif

                        </div>
                        <div class="printable-visible" style="{{ $invoice->status > 2 ? 'display: none' : ''}}">
                            <hr/>
                            <div class="row">
                                <div class="col-md-6 col-xs-12">
                                    <a href="{{route('admin.order.cancelOrder' , $invoice)}}" class="btn btn-block btn-danger order-status-change farsi-text">لغو سفارش</a>
                                </div>
                                <div class="col-md-6 col-xs-12">
                                    {!! generateOrderBtn($invoice->status , $invoice->id) !!}
                                </div>
                            </div>
                        </div>
                    </div>
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


