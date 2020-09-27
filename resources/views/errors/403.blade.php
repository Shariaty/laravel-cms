@extends('admin.include.layout')
@section('content')

    <div class="row">

        <div class="col-md-12 col-xs-12">
            <div class="margin-top-40">
                <img class="img-responsive center-block" style="width: 200px;" src="{{asset('/assets/admin/images/icons/access-denied.png')}}">
            </div>
        </div>
        <div class="col-md-12 col-xs-12">
            <div class="ot-info-display-big">
                <h1 class="h-pad-sm center-text">Access Denied</h1>
                <div class="clearfix margin-top-10"></div>
                <h4 class="h-pad-sm center-text">
                    You are not authorized to access this resource on this web site
                </h4>
                <div class="clearfix margin-top-20"></div>
                <div class="center-text">
                    <a href="{{route('admin.dashboard')}}" class="btn btn btn-success">
                        <span>Back to Dashboard</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

@stop

