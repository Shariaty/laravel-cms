@extends('admin.include.layout')
@section('content')
<div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="panel-body form">
                <div class="panel panel-grey">
                    <div class="panel-heading">{{$title}}</div>
                    <div class="panel-body">
                        {!! Form::open(array('route' => 'admin.user.create', 'method' => 'POST' , 'id' => 'AdminRegisterForm')) !!}
                        <div class="col-md-12" style="margin-bottom: 20px;">
                            <div class="row">
                                <div class="col-md-2 col-xs-12">
                                    <div style="width: 172px; height:auto; background-color: white;">
                                        @if(!empty($adminUser->img))
                                            <img id="profile-image" width="172px" style="padding: 10px; border: 1px solid lightgrey; border-bottom: 0px;" src="{{ asset('uploads/admins/profile-pictures/'.$adminUser->img) }}">
                                        @else
                                            <img id="profile-image" width="172px" style="padding: 10px; border: 1px solid lightgrey; border-bottom: 0px;" src="{{ asset('assets/admin/images/profile-placeholder.jpg') }}">
                                        @endif
                                        @include('admin.forms._fileUpload')
                                    </div>

                                </div>
                                <div class="col-md-10 col-xs-12">
                                    <div>
                                        <ul style="list-style-type: none; line-height: 2.1; font-size: 1em; margin-top: 15px;">
                                            <li><strong>Name: </strong>{{ generateUserFullName($adminUser) }}</li>
                                            <li><strong>Role:  </strong>@foreach($adminUser->roles as $rol)
                                                        <span class="badge badge-default" style="font-family: Tahoma, Helvetica, Arial"> {{$rol->label}} </span>
                                                       @endforeach
                                            </li>
                                            <li><strong>Email Address: </strong>{{$adminUser->email}}</li>
                                            <li><strong>Registration Date: </strong>{{$adminUser->created_at}}</li>
                                            <li><strong>Last login: </strong>{{\Carbon\Carbon::parse($adminUser->last_active)->diffForHumans()}}</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 col-xs-12">
                            <hr/>
                            <div class="">
                                <div class="pull-right">
                                    <a href="{{route('admin.profile.edit')}}" class="btn btn-success">Edit your profile</a>
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


@section('header')
<link href="{{asset('assets/plugins/jqueryfileupload/css/jquery.fileupload.css')}}" rel="stylesheet" type="text/css" />
    <style>
        #upload-box {
            height: 35px !important;
            margin: 0px !important;
        }
        .progress-custom {
            margin-top: -6px !important;
            margin-left: 1px !important;
            width: 99.0% !important;
        }
        .fileinput-button {
            margin-top: 5px !important;
        }
        #logo-error-reporting {
            margin-top: 15px !important;
        }
    </style>
@stop

@section('footer')
<script src="{{asset('assets/plugins/jqueryfileupload/vendor/jquery.ui.widget.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/plugins/jqueryfileupload/jquery.fileupload.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/plugins/jquery-pulsate/jquery.pulsate.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/admin/js/resumeUpload.js')}}" type="text/javascript"></script>
@stop



