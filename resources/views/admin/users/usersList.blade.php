@extends('admin.include.layout')

@section('content')
<div class="row">
    <div class="col-md-12 col-xs-12">

        <div class="panel panel-grey">
            <div class="panel-heading">
                <span class="fa fa-users"></span>
                <span class="bold">{{!empty($title) ? $title: ''}}</span>
                <span class="font-normal"> ( Total : {{$users->count()}} ) </span>
                <span class="pull-right">
                    <a href="{{route('admin.user.create')}}" class="btn btn-xs btn-success" style="margin-top: -2px;">+ Add New</a>
                </span>
            </div>
            <div class="panel-body">
                @if(count($users))
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>Active</th>
                            <th></th>
                            <th>Group</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Created at</th>
                            <th>Last Login</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>
                                @if($user->id != 1)
                                    @if($user->status == 'N')
                                        <button class="btn btn-xs btn-default status-change" data-status="{{$user->status}}" data-new="{{$user->id}}">
                                            <i class="fa fa-ban fa-1x text-danger"></i>
                                        </button>
                                    @else
                                        <button class="btn btn-xs btn-default status-change" data-status="{{$user->status}}" data-new="{{$user->id}}">
                                            <i class="fa fa-check fa-1x text-success"></i>
                                        </button>
                                    @endif
                                @endif
                            </td>
                            <td >
                                @if($user->img)
                                    <img class="img-thumbnail sidebar-logo" style="width: 40px;border-radius: 10px !important;box-shadow: 0px 0px 35px #eee;opacity: 0.8;" src="{{asset('uploads/admins/profile-pictures/'.$user->img)}}" />
                                @else
                                    <img class="img-thumbnail sidebar-logo" style="width: 40px;border-radius: 10px !important;box-shadow: 0px 0px 35px #eee;opacity: 0.5;" id="header-company-logo" src="{{ asset('assets/admin/images/profile-placeholder.jpg') }}" />
                                @endif
                            </td>
                            <td>
                                @foreach($user->roles as $rol)
                                    <span class="badge badge-default" style="font-family: Tahoma, Helvetica, Arial"> {{$rol->label}} </span>
                                @endforeach
                            </td>
                            <td>
                                {{$user->firstname.' '.$user->lastname}}
                            </td>
                            <td>
                                {{$user->email}}
                            </td>
                            <td>
                                {{$user->created_at}}
                            </td>
                            <td>
                                {{\Carbon\Carbon::parse($user->last_active)->diffForHumans()}}
                            </td>
                            <td style="width: 3px;">
                            <a href="{{route('admin.user.update' , $user)}}" class="btn btn-xs btn-info"><i class="fa fa-edit"></i></a>
                            </td>
                            <td style="width: 3px;">
                            <a href="{{route('admin.user.remove' , $user)}}" class="btn btn-xs btn-danger confirmation-remove"><i class="fa fa-trash"></i></a>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>

                    </table>
                </div>

                <div class="text-center">
                    {{ $users->render() }}
                </div>
                @else
                <div class="text-center" style="padding: 50px;">
                    <img width="100px" src="{{asset('assets/admin/images/icons/sad.png')}}">
                    <p>No data found to view</p>
                </div>
                @endif
            </div>
        </div>

    </div>
</div>
@stop

@section('footer')
<script>
    M6.ajaxStatusChange('users/AjaxStatusUpdate');
</script>
@stop