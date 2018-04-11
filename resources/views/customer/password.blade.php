@extends('layouts.app')

@section('title', '| New Password')

@section('content_header')
    @include('layouts.html.content-header', [
        'title' => 'Set new password',
        'menu' => [
            '/customers' => ['icon' => 'fa-user-md', 'name' => 'Customers'],
            '/customer/' . $customer->id .'/details' => ['icon' => '', 'name' => 'Customer details'],
            '' => ['last' => true, 'name' => 'Set new password']
        ]
    ])
@endsection

@section('content')
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">New password</h3>
    </div>
    <form method="post" action="{{ url('/customer/' . $customer->id . '/password') }}" class="form-horizontal">
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