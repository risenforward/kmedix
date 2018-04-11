@extends('layouts.app')

@section('title', '| Add new person')

@section('content_header')
    @include('layouts.html.content-header', [
        'title' => 'Add new person',
        'menu' => [
            '/customers' => ['icon' => 'fa-user-md', 'name' => 'Customers'],
            '/customer/' . $customer->id . '/persons' => ['icon' => 'fa-users', 'name' => 'Contact persons'],
            '' => ['last' => true, 'name' => 'Add new person']
        ]
    ])
@endsection

@section('content')
    <div class="box box-primary">
        <form method="post" action="{{ url('/customer/' . $customer->id . '/person') }}" class="form-horizontal">
            {{ csrf_field() }}
            @include('customer.person.form')
        </form>
        @include('layouts.html.submit-loading')
    </div>
@endsection