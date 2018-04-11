@extends('layouts.app')

@section('title', '| Sales request details')

@section('content_header')
    @include('layouts.html.content-header', [
        'title' => 'Sales request details',
        'menu' => [
            '/salesRequests' => ['icon' => 'fa-cart-plus', 'name' => 'Sales requests'],
            '' => ['last' => true, 'name' => 'Sales request details']
        ]
    ])
@endsection

@section('content')
<div class="box box-primary">
    <div class="box-body">
        <table class="table table-bordered"style="width: 60%;">
            <tr>
                <td width="25%">Requested by</td>
                <td><a href="{{ url('/customer/' .  $request->customer->id .'/details') }}" target="_blank">{{ $request->customer->clinic_name }}</a></td>
            </tr>
            <tr>
                <td>Request date</td>
                <td>{{ $request->f_request_date }}</td>
            </tr>
            <tr>
                <td>Requested details</td>
                <td>{{ $request->request_details }}</td>
            </tr>
            <tr>
                <td>Notes</td>
                <td>{{ $request->notes }}</td>
            </tr>
            <tr>
                <td>Status</td>
                <td>{{ \App\SalesRequest::$statuses[$request->status] }}</td>
            </tr>
        </table>
    </div>
</div>
@endsection