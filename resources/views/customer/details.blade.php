@extends('layouts.app')

@section('title', '| Customer details')

@section('content_header')
    @include('layouts.html.content-header', [
        'title' => 'Customer details',
        'menu' => [
            '/customers' => ['icon' => 'fa-user-md', 'name' => 'Customers'],
            '' => ['last' => true, 'name' => 'Customer details']
        ]
    ])
@endsection

@section('content')
    <div class="box box-primary">
        <div class="box-body">
            <table class="table table-bordered" style="width: 60%;">
                <tr>
                    <td colspan="2" width="85%" align="center">
                        <strong style="font-size: 20px;">{{ $customer->clinic_name }}</strong><br>
                        <strong>Offical phone: {{ phone_format($customer->user->phone_number) }}</strong>
                    </td>
                    <td width="15%" rowspan="{{ 7 + (!$customer->contactPersons->isEmpty() ? !$customer->contactPersons->count() + 1 : 0)  }}" style="background-color: #ffffff; text-align: center;">
                        <img @if($customer->logo)src="/uploads/customers/customer-{{ $customer->id }}/{{ $customer->logo }}"@else src="/assets/img/404-logo.png"@endif style="max-height: 200px; max-width: 200px;">
                    </td>
                </tr>
                @if(!$customer->contactPersons->isEmpty())
                    <tr style="background-color: #f9f9f9;">
                        <td colspan="2"><strong>Contact persons</strong></td>
                    </tr>
                    @foreach($customer->contactPersons as $person)
                        <tr>
                            <td>{{ $person->full_name }}</td>
                            <td>{{ phone_format($person->phone_number) }}</td>
                        </tr>
                    @endforeach
                @endif
                <tr style="background-color: #f9f9f9;">
                    <td colspan="2"><strong>Other information:</strong></td>
                </tr>
                <tr>
                    <td>Username</td>
                    <td>{{ $customer->user->username }}</td>
                </tr>
                <tr>
                    <td>Email address</td>
                    <td><a href="mailto:{{ $customer->user->email }}">{{ $customer->user->email }}</a></td>
                </tr>
                <tr>
                    <td width="20%">Institute type</td>
                    <td>{{ $customer->specialization }}</td>
                </tr>
                <tr>
                    <td>Address</td>
                    <td>{{ $customer->address }}</td>
                </tr>
                <tr>
                    <td>Blocked</td>
                    <td>@include('layouts.html.active-glyphicon', ['active' => $customer->user->active]){{ $customer->user->active ? 'No' : 'Yes' }}</td>
                </tr>
            </table>
        </div>
        <div class="box-footer">
            <a class="btn {{ $customer->user->active ? 'btn-danger' : 'btn-success' }}" data-toggle="modal" data-target="#confirm-update" data-body="Customer will be {{ $customer->user->active ? 'blocked' : 'unblocked' }}! Are you sure?" data-href="{{ url('/customer/' . $customer->id . '/active') }}">{{ $customer->user->active ? 'Block' : 'Unblock' }}</a>
            <a href="{{ url('/customer/' . $customer->id . '/password') }}" class="btn btn-primary">Set password</a>
            <a href="{{ url('/customer/' . $customer->id . '/devices') }}" class="btn btn-primary">View devices</a>
            <a href="{{ url('/customer/' . $customer->id . '/serviceRequests') }}" class="btn btn-primary">View service requests</a>
        </div>
        @include('layouts.html.modals.update')
    </div>
@endsection