<!doctype html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CallBack</title>
    <style>
        body{
            font-family: Tahoma;
            direction: rtl;
            background-color: #efefef;
        }

        .btn{
            background-color: #868686;
            color: white;
            text-decoration: none;
            padding: 7px 10px;
            font-size: 12px;
        }

        .text-center{
            text-align: center;
        }

        .callback-danger{
            background-color: rgba(253, 253, 253, 0.9);
            min-height: 200px;
            width: 340px;
            margin: 0 auto !important;
            margin-top: 52px !important;
            padding: 20px;
            border-radius: 5px !important;
        }

        .callback-success{
            background-color: rgba(253, 253, 253, 0.9);
            min-height: 200px;
            width: 340px;
            margin: 0 auto !important;
            margin-top: 52px !important;
            padding: 20px;
            border-radius: 5px !important;
        }

        .call-back-info-box{
            color: slategray;
            margin-top: 20px;
            text-align: center;
        }

        .call-back-info-box h5{
            font-size: 15px;
            font-weight: bolder;
        }
    </style>
</head>

<body>
    <div class="flex-center position-ref full-height">
        <div class="content">
            <div class="callback-{{$type}}">

                <div class="text-center">
                    @if($type == 'success')
                        <img width="125px;" class="con" src="{{asset('assets/admin/images/icons/orderConfirm.jpg')}}">
                    @else
                        <img width="125px;" class="con" src="{{asset('assets/admin/images/icons/access-denied.png')}}">
                    @endif
                </div>

                <div class="call-back-info-box">
                    @php
                        if(strpos($error, '#') !== false){
                            $pos = trim(strstr($error, '#', true));
                        } else {
                            $pos = $error;
                        }
                    @endphp
                    <h6>{{ $pos }}</h6>
                    <h6>{{ !empty($trackingCode) ? 'کد رهگیری  بانک : '.$trackingCode : ''}}</h6>
                    @if($type == 'success')
                        <h6>پرداخت شما با موفقیت انجام شد ، شما به صورت اتوماتیک تا لحظاتی دیگر به سایت منتقل خواهید شد</h6>
                    @endif

                    @if($invoiceID)
                        <a class="btn" href="http://localhost:3000/userProfile?code={{$invoiceID}}"> بازگشت به سایت </a>
                    @else
                        <a class="btn" href="http://localhost:3000/userProfile"> بازگشت به سایت </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>
</html>
