@extends('layouts.app')

@section('title', '| Add new user')

@section('content_header')
    @include('layouts.html.content-header', [
        'title' => 'Add new user',
        'menu' => [
            '/users' => ['icon' => 'fa-users', 'name' => 'Users'],
            '' => ['last' => true, 'name' => 'Add new user']
        ]
    ])
@endsection

@section('content')
<div class="box box-primary">
    <form method="post" action="{{ url('/user') }}" class="form-horizontal">
        {{ csrf_field() }}
        <div class="box-body">
            @include('layouts.html.input', ['name' => 'first_name', 'caption' => 'First name'])
            @include('layouts.html.input', ['name' => 'last_name', 'caption' => 'Last name'])
            @include('layouts.html.input', ['name' => 'middle_name', 'caption' => 'Middle name'])
            @include('layouts.html.input', ['name' => 'email', 'type' => 'email', 'caption' => 'Email address'])
            @include('layouts.html.input', ['name' => 'password', 'type' => 'text', 'caption' => 'Password', 'value' => str_random(DEFAULT_PASSWORD_LENGTH)])
            @include('layouts.html.phone', ['name' => 'phone_number', 'caption' => 'Phone', 'required' => 'required'])
            @include('layouts.html.select', ['name' => 'role', 'caption' => 'User type (role)', 'items' => $roles->transform(function ($role) { return ['id' => $role->id, 'name' => $role->display_name]; }), 'mode' => 'assoc'])
            @include('layouts.html.checkbox', ['name' => 'active', 'caption' => 'Active'])
            <div class="box-footer">
                <button type="submit" class="btn btn-default">Submit</button>
            </div>
        </div>
    </form>
    @include('layouts.html.submit-loading')
</div>
@endsection