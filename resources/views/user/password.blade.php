@extends('layouts.app')

@section('title', '| New Password')

@section('content_header')
    <h1>Set new password</h1>
    <ol class="breadcrumb">
        <li><a href="{{ url('/') }}"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="{{ url('/users') }}"><i class="fa fa-users"></i> Users</a></li>
        <li><a href="{{ url('/user/' . $user->id . '/details') }}"><i class="fa fa-user"></i> User details</a></li>
        <li class="active">Set new password</li>
    </ol>
@endsection

@section('content')
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">New password</h3>
    </div>
    <form method="post" action="{{ url('/user/' . $user->id . '/password') }}" class="form-horizontal">
        {{ csrf_field() }}
        <input type="hidden" name="_method" value="put">
        <div class="box-body">
            @include('layouts.html.input', ['name' => 'password', 'type' => 'password', 'caption' => 'Password'])
            @include('layouts.html.input', ['name' => 'password_confirmation', 'type' => 'password', 'caption' => 'Password confirmation'])
            <div class="box-footer">
                <button type="submit" class="btn btn-default">Submit</button>
            </div>
        </div>
    </form>
    @include('layouts.html.submit-loading')
</div>
@endsection