@extends('admin.include.layout')

@section('content')

<style>
    a[title="JavaScript charts"]
    {
        display: none !important;
    }
</style>

<div class="row">
    <div class="col-lg-10 col-xs-12 col-sm-12">
        <div class="row">
            <div class="col-lg-6 col-xs-12 col-sm-12">
                <div class="panel panel-grey">
                    <div class="panel-heading">
                        <span class="fa fa-area-chart"></span>
                        <span class="bold">Site visitors</span>
                        <span class="font-normal">(in last 15 months)</span>
                    </div>
                    <div class="panel-body">
                        <div id="site_statistics_loading">
                            <img src="{{asset('assets/admin/images/loading.gif')}}" width="25px" alt="loading"/> </div>
                        <div id="site_statistics_content" class="display-none">
                            <div id="site_statistics" class="chart" style="height: 200px"> </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-xs-12 col-sm-12">

                <div class="panel panel-grey">
                    <div class="panel-heading">
                        <span class="fa fa-bar-chart"></span>
                        <span class="bold">Site visitors</span>
                        <span class="font-normal">(Based on their country)</span>
                    </div>
                    <div class="panel-body">
                        <div id="site_statistics_loading-bars">
                            <img src="{{asset('assets/admin/images/loading.gif')}}" width="25px" alt="loading"/>
                            <div id="curtain"></div>
                        </div>
                        <div id="site_statistics_content-bars" class="display-none">
                            <div id="chartdiv" class="chart" style="height: 200px; overflow: hidden; text-align: left;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{--<div class="col-lg-6 col-xs-12 col-sm-12">--}}
                {{--<div class="panel panel-grey">--}}
                    {{--<div class="panel-heading">--}}
                        {{--<span class="fa fa-area-chart"></span>--}}
                        {{--<span class="bold">User Registration</span>--}}
                        {{--<span class="font-normal">(in last 15 months)</span>--}}
                    {{--</div>--}}
                    {{--<div class="panel-body">--}}
                        {{--<div id="user_statistics_loading">--}}
                            {{--<img src="{{asset('assets/admin/images/loading.gif')}}" width="25px" alt="loading"/> </div>--}}
                        {{--<div id="user_statistics_content" class="display-none">--}}
                            {{--<div id="site_statistics" class="chart" style="height: 200px"> </div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
            {{--<div class="col-lg-6 col-xs-12 col-sm-12">--}}
                {{--<div class="panel panel-grey">--}}
                    {{--<div class="panel-heading">--}}
                        {{--<span class="fa fa-bar-chart"></span>--}}
                        {{--<span class="bold">Orders</span>--}}
                        {{--<span class="font-normal">(Based on site orders)</span>--}}
                    {{--</div>--}}
                    {{--<div class="panel-body">--}}
                        {{--<div id="order_statistics_loading">--}}
                            {{--<img src="{{asset('assets/admin/images/loading.gif')}}" width="25px" alt="loading"/>--}}
                            {{--<div id="curtain"></div>--}}
                        {{--</div>--}}
                        {{--<div id="order_statistics_content" class="display-none">--}}
                            {{--<div id="orderChartDiv" class="chart" style="height: 200px; overflow: hidden; text-align: left;">--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
        </div>
    </div>
    <div class="col-lg-2 col-xs-12 col-sm-12">

        @if( hasModule('Stores') )
            <a href="{{route('admin.stores.list')}}" class="dashboard-stat dashboard-stat-v2 red">
                <div class="visual">
                    <i class="fa fa-building-o"></i>
                </div>
                <div class="details">
                    <div class="number">
                        <span data-counter="counterup" id="counterup" data-value="{{$totalStores}}">{{ $totalStores }}</span>
                    </div>
                    <div class="desc"> Stores </div>
                </div>
            </a>
        @endif

        @if( hasModule('Contact') )
            @if(isset($messageContactCounter))
                <a class="dashboard-stat dashboard-stat-v2 green-jungle {{ $messageContactCounter> 0 ? 'pulsate' : '' }}" href="{{route('admin.contacts.list')}}">
                    <div class="visual">
                        <i class="fa fa fa-envelope"></i>
                    </div>
                    <div class="details">
                        <div class="number">
                            <span data-counter="counterup" data-value="{{$messageContactCounter}}">{{$messageContactCounter}}</span>
                        </div>
                        <div class="desc"> New Messages </div>
                    </div>
                </a>
            @endif
        @endif

        @if( hasModule('Sale') )
            @if(isset($ordersCounter))
                <a class="dashboard-stat dashboard-stat-v2 blue {{ $ordersCounter> 0 ? 'pulsate' : '' }}" href="{{route('admin.order.list')}}">
                    <div class="visual">
                        <i class="fa fa-shopping-bag"></i>
                    </div>
                    <div class="details">
                        <div class="number">
                            <span data-counter="counterup" data-value="{{$ordersCounter}}">{{$ordersCounter}}</span>
                        </div>
                        <div class="desc"> New Orders </div>
                    </div>
                </a>
            @endif
        @endif

        @if( hasModule('Comments') )
            @if(isset($commentsCounter))
                <a class="dashboard-stat dashboard-stat-v2 purple {{ $commentsCounter > 0 ? 'pulsate' : '' }}" href="{{route('admin.comments.list')}}">
                    <div class="visual">
                        <i class="fa fa-comments"></i>
                    </div>
                    <div class="details">
                        <div class="number">
                            <span data-counter="counterup" data-value="{{$commentsCounter}}">{{$commentsCounter}}</span>
                        </div>
                        <div class="desc"> New Comments </div>
                    </div>
                </a>
            @endif
        @endif
    </div>
</div>

<div class="row">
        <div class="col-lg-12 col-xs-12 col-sm-12">
            <div class="panel panel-grey">
                <div class="panel-heading">
                    <span class="fa fa-bell"></span>
                    <span class="bold">Last Activities</span>
                    <span class="font-normal">(Based on their login to administration area)</span>
                </div>
                <div class="panel-body">
                    <ul class="feeds">
                        @foreach($lastActivities as $activitie)
                            <li>
                                <a href="javascript:;">
                                    <div class="col1">
                                        <div class="cont">
                                            <div class="cont-col1" style="width: 40px">
                                                @if($activitie->img)
                                                    <img class="img-thumbnail sidebar-logo" style="width: 40px;border-radius: 10px !important;box-shadow: 0px 0px 35px #eee;opacity: 0.8;" src="{{asset('uploads/admins/profile-pictures/'.$activitie->img)}}" />
                                                @else
                                                    <img class="img-thumbnail sidebar-logo" style="width: 40px;border-radius: 10px !important;box-shadow: 0px 0px 35px #eee;opacity: 0.5;" id="header-company-logo" src="{{ asset('assets/admin/images/profile-placeholder.jpg') }}" />
                                                @endif
                                            </div>
                                            <div class="cont-col2">
                                                <div class="desc" style="margin-left: 45px !important; margin-top: 7px;"> {{$activitie->name ? $activitie->name : $activitie->email}}&nbsp; logged in to admin area </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col2" style="width: 150px; margin-left: -150px; margin-top: 7px;">
                                        <div class="date"> {{\Carbon\Carbon::parse($activitie->last_active)->diffForHumans()}} </div>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

@stop

@section('footer')
<script src="{{asset('assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/plugins/amcharts/amcharts/amcharts.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/plugins/amcharts/amcharts/serial.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/plugins/flot/jquery.flot.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/plugins/flot/jquery.flot.resize.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/plugins/flot/jquery.flot.categories.min.js')}}" type="text/javascript"></script>
<script>
var fakeArray = {!! json_encode($candidateArray) !!};
var orderArray = {!! json_encode($orderArray) !!};

var visitArray = {!! json_encode($array) !!};
var finalCountryReport = {!! json_encode($countryReport) !!};

function showChartTooltip(x, y, xValue, yValue) {
    $('<div id="tooltip" class="chart-tooltip">' + yValue + '<\/div>').css({
        position: 'absolute',
        display: 'none',
        top: y - 40,
        left: x - 40,
        border: '0px solid #ccc',
        padding: '2px 6px',
        'background-color': '#fff'
    }).appendTo("body").fadeIn(200);
}

// Site statistics
if ($('#site_statistics').size() != 0) {

    $('#site_statistics_loading').hide();
    $('#site_statistics_content').show();

    var plot_statistics = $.plot($("#site_statistics") , [{
            data: visitArray,
            lines: {
                fill: 0.6,
                lineWidth: 0
            },
            color: ['#00a9f8']
        }, {
            data: visitArray,
            points: {
                show: true,
                fill: true,
                radius: 5,
                fillColor: "#0092db",
                lineWidth: 3
            },
            color: '#fff',
            shadowSize: 0
        }],

        {
            xaxis: {
                tickLength: 0,
                tickDecimals: 0,
                mode: "categories",
                min: 0,
                font: {
                    lineHeight: 14,
                    style: "normal",
                    variant: "small-caps",
                    color: "#6F7B8A"
                }
            },
            yaxis: {
                ticks: 5,
                tickDecimals: 0,
                tickColor: "#eee",
                font: {
                    lineHeight: 14,
                    style: "normal",
                    variant: "small-caps",
                    color: "#6F7B8A"
                }
            },
            grid: {
                hoverable: true,
                clickable: true,
                tickColor: "#eee",
                borderColor: "#eee",
                borderWidth: 1
            }
        });

    var previousPoint = null;
    $("#site_statistics").bind("plothover", function(event, pos, item) {
        $("#x").text(pos.x.toFixed(2));
        $("#y").text(pos.y.toFixed(2));
        if (item) {
            if (previousPoint != item.dataIndex) {
                previousPoint = item.dataIndex;

                $("#tooltip").remove();
                var x = item.datapoint[0].toFixed(2),
                    y = item.datapoint[1].toFixed(2);

                showChartTooltip(item.pageX, item.pageY, item.datapoint[0], item.datapoint[1] + ' visits');
            }
        } else {
            $("#tooltip").remove();
            previousPoint = null;
        }
    });
}


// User registration statistics
if ($('#user_statistics').size() != 0) {

    $('#user_statistics_loading').hide();
    $('#user_statistics_content').show();

    var plot_statistics = $.plot($("#user_statistics") , [{
            data: fakeArray,
            lines: {
                fill: 0.6,
                lineWidth: 0
            },
            color: ['#00a9f8']
        }, {
            data: fakeArray,
            points: {
                show: true,
                fill: true,
                radius: 5,
                fillColor: "#0092db",
                lineWidth: 3
            },
            color: '#fff',
            shadowSize: 0
        }],

        {
            xaxis: {
                tickLength: 0,
                tickDecimals: 0,
                mode: "categories",
                min: 0,
                font: {
                    lineHeight: 14,
                    style: "normal",
                    variant: "small-caps",
                    color: "#6F7B8A"
                }
            },
            yaxis: {
                ticks: 5,
                tickDecimals: 0,
                tickColor: "#eee",
                font: {
                    lineHeight: 14,
                    style: "normal",
                    variant: "small-caps",
                    color: "#6F7B8A"
                }
            },
            grid: {
                hoverable: true,
                clickable: true,
                tickColor: "#eee",
                borderColor: "#eee",
                borderWidth: 1
            }
        });

    var previousPoint = null;
    $("#user_statistics").bind("plothover", function(event, pos, item) {
        $("#x").text(pos.x.toFixed(2));
        $("#y").text(pos.y.toFixed(2));
        if (item) {
            if (previousPoint != item.dataIndex) {
                previousPoint = item.dataIndex;

                $("#tooltip").remove();
                var x = item.datapoint[0].toFixed(2),
                    y = item.datapoint[1].toFixed(2);

                showChartTooltip(item.pageX, item.pageY, item.datapoint[0], item.datapoint[1] + ' Registration');
            }
        } else {
            $("#tooltip").remove();
            previousPoint = null;
        }
    });
}

// Countries
var chart = AmCharts.makeChart("chartdiv", {
    "theme": "light",
    "type": "serial",
    "startDuration": 2,
    "dataProvider": finalCountryReport ,
    "valueAxes": [{
        "position": "left",
        "title": "Visitors"
    }],
    "graphs": [{
        "balloonText": "[[category]]: <b>[[value]]</b>",
        "fillColorsField": "color",
        "fillAlphas": 1,
        "lineAlpha": 0.1,
        "type": "column",
        "valueField": "visits"
    }],
    "depth3D": 20,
    "angle": 30,
    "chartCursor": {
        "categoryBalloonEnabled": false,
        "cursorAlpha": 0,
        "zoomable": false
    },
    "categoryField": "country",
    "categoryAxis": {
        "gridPosition": "start",
        "labelRotation": 90
    },
    "export": {
        "enabled": true
    } ,
    "listeners": [{
        "event": "rendered",
        "method": function(e) {
            $('#site_statistics_loading-bars').hide();
            $('#site_statistics_content-bars').show();
        }
    }]
}, 2000);

// Orders statistics
var orderChart = AmCharts.makeChart("orderChartDiv", {
    "theme": "light",
    "type": "serial",
    "startDuration": 2,
    "dataProvider": orderArray ,
    "valueAxes": [{
        "position": "left",
        "title": "سفارشات سایت"
    }],
    "graphs": [{
        "balloonText": "[[category]]: <b>[[value]]</b>",
        "fillColorsField": "color",
        "fillAlphas": 1,
        "lineAlpha": 0.1,
        "type": "column",
        "valueField": "total"
    }],
    "depth3D": 20,
    "angle": 30,
    "chartCursor": {
        "categoryBalloonEnabled": false,
        "cursorAlpha": 0,
        "zoomable": false
    },
    "categoryField": "monthYear",
    "categoryAxis": {
        "gridPosition": "start",
        "labelRotation": 90
    },
    "export": {
        "enabled": true
    } ,
    "listeners": [{
        "event": "rendered",
        "method": function(e) {
            $('#order_statistics_loading').hide();
            $('#order_statistics_content').show();
        }
    }]
}, 2000);

</script>
@stop
