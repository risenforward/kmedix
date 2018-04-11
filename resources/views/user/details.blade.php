@extends('layouts.app')

@section('title', '| User details')

@section('content_header')
    @include('layouts.html.content-header', [
        'title' => 'User details',
        'menu' => [
            '/users' => ['icon' => 'fa-users', 'name' => 'Users'],
            '' => ['last' => true, 'name' => 'User details']
        ]
    ])
@endsection

@section('content')
<div class="box box-primary">
    <div class="box-body">
        <table class="table table-bordered" style="width: 60%;">
            <tr>
                <td colspan="2" align="center">
                    <strong style="font-size: 20px;">{{ $user->full_name }}</strong><br>
                    @if($user->isEngineer())<div class="rateit bigstars" data-rateit-value="{{ $user->rating }}" data-rateit-ispreset="true" data-rateit-readonly="true"></div>@endif
                </td>
            </tr>
            <tr>
                <td width="30%">User type</td>
                <td>{{ $user->role }}</td>
            </tr>
            <tr>
                <td>Phone</td>
                <td>{{ phone_format($user->phone_number) }}</td>
            </tr>
            <tr>
                <td>Email</td>
                <td><a href="mailto:{{ $user->email }}">{{ $user->email }}</a></td>
            </tr>
            <tr>
                <td>Active</td>
                <td>@include('layouts.html.active-glyphicon', ['active' => $user->active]){{ $user->active ? 'Yes' : 'No' }}</td>
            </tr>
            <tr>
                <td>Last attended service request</td>
                <td>@if(!$user->serviceRequests->isEmpty()){{ $user->servicerequests->last()->f_attended_at }}@endif</td>
            </tr>
        </table>
    </div>
    <div class="box-footer">
        <a class="btn {{ $user->active ? 'btn-danger' : 'btn-success' }}" data-toggle="modal" data-target="#confirm-update" data-body="User will be {{ $user->active ? 'deactivated' : 'activated' }}! Are you sure?" data-href="{{ url('/user/' . $user->id . '/active') }}">{{ $user->active ? 'Deactivate' : 'Activate' }}</a>
        <a href="{{ url('/user/' . $user->id . '/password') }}" class="btn btn-primary">Set password</a>
        @if(!in_array($user->role_name, ['ADMINISTRATOR', 'STORE_ADMINISTRATOR']))
            <a href="{{ url('/user/' . $user->id . '/serviceRequests') }}" class="btn btn-primary">View service requests</a>
            <a href="{{ url('/user/' . $user->id . '/serviceLog') }}" class="btn btn-primary">View service log</a>
        @endif
    </div>
    @include('layouts.html.modals.update')
</div>
@endsection