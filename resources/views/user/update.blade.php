@extends('layouts.app')

@section('title', '| Update user')

@section('content_header')
    @include('layouts.html.content-header', [
        'title' => 'Update user',
        'menu' => [
            '/users' => ['icon' => 'fa-users', 'name' => 'Users'],
            '' => ['last' => true, 'name' => 'Update user']
        ]
    ])
@endsection

@section('content')
<div class="box box-primary">
    <form method="post" action="{{ url('/user/' . $user->id) }}" class="form-horizontal">
        {{ csrf_field() }}
        <input type="hidden" name="_method" value="put">
        <div class="box-body">
            @include('layouts.html.input', ['name' => 'first_name', 'caption' => 'First name', 'value' => $user->first_name])
            @include('layouts.html.input', ['name' => 'last_name', 'caption' => 'Last name', 'value' => $user->last_name])
            @include('layouts.html.input', ['name' => 'middle_name', 'caption' => 'Middle name', 'value' => $user->middle_name])
            @include('layouts.html.input', ['name' => 'email', 'type' => 'email', 'caption' => 'Email address', 'value' => $user->email])
            @include('layouts.html.phone', ['name' => 'phone_number', 'caption' => 'Phone', 'required' => 'required', 'value' => $user->phone_number])
            @include('layouts.html.select', ['name' => 'role', 'caption' => 'User type (role)', 'items' => $roles->transform(function ($role) { return ['id' => $role->id, 'name' => $role->display_name]; }), 'mode' => 'assoc', 'selected' => $user->role_id])
            @include('layouts.html.checkbox', ['name' => 'active', 'caption' => 'Active', 'checked' => $user->active])
            <div class="box-footer">
                <button type="submit" class="btn btn-default">Submit</button>
            </div>
        </div>
    </form>
    @include('layouts.html.submit-loading')
</div>
@endsection