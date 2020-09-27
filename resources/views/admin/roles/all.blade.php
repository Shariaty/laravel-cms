@extends('admin.include.layout')

@section('content')

    <div class="row">
        <div class="col-md-12 col-xs-12">

            <div class="panel panel-grey">
                <div class="panel-heading">
                    <span class="fa fa-users"></span>
                    <span class="bold">{{!empty($title) ? $title: ''}}</span>
                    <span class="font-normal"> ( Total : {{$roles->count()}} ) </span>
                    <span class="pull-right">
                    <a href="{{route('roles.create')}}" class="btn btn-xs btn-success" style="margin-top: -2px;">+ Add New</a>
                </span>
                </div>
                <div class="panel-body">
                    @if(count($roles))
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th width="150px">Group Name</th>
                                    <th width="200px">Group Short Desc</th>
                                    <th>Has access to</th>
                                    <th width="30px"></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($roles as $role)
                                    <tr>
                                        <td>
                                            {{$role->name}}
                                        </td>
                                        <td>
                                            {{$role->label}}
                                        </td>
                                        <td>
                                            @foreach($role->permissions as $per)
                                                <span class="badge badge-success" style="font-family: Tahoma, Helvetica, Arial; margin-top: 5px;"> {{$per->label}} </span>
                                            @endforeach
                                        </td>
                                        <td style="width: 3px;">
                                            <a href="{{ route('roles.delete' , $role ) }}"  class="btn btn-xs btn-danger confirmation-remove"><i class="fa fa-trash-o"></i></a>
                                        </td>
                                        <td style="width: 3px;">
                                            <a href="{{ route('roles.edit' ,$role->id) }}"  class="btn btn-xs btn-info"><i class="fa fa-edit"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>

                            </table>
                        </div>

                        <div class="text-center">
                            {{ $roles->render() }}
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

@endsection