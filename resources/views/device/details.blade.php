@extends('layouts.app')

@section('title', '| Device details')

@section('content_header')
    @include('layouts.html.content-header', [
        'title' => 'Device details',
        'menu' => [
            '/devices' => ['icon' => 'fa-cog', 'name' => 'Devices'],
            '' => ['last' => true, 'name' => 'Device details']
        ]
    ])
@endsection

@section('content')
<div class="box box-primary">
    <div class="box-body">
        <table class="table table-bordered details" style="float:left;">
            <tr>
                <td>Serial number</td>
                <td>{{ $device->serial_number }}</td>
            </tr>
            <tr>
                <td>Supplier</td>
                <td><a href="{{ url('/supplier/' . $device->deviceModel->supplier->id . '/details') }}" target="_blank">{{ $device->deviceModel->supplier->name }}</a></td>
            </tr>
            <tr>
                <td>Type/Model</td>
                <td>{{ $device->deviceModel->name }}</td>
            </tr>
            <tr>
                <td>Customer</td>
                <td><a href="{{ url('/customer/' . $device->customer->id . '/details') }}" target="_blank">{{ $device->customer->clinic_name }}</a></td>
            </tr>
            <tr>
                <td>Install date</td>
                <td>{{ $device->getFInstallDate() }}</td>
            </tr>
            <tr>
                <td>Warranty</td>
                <td>{{ $device->warranty }} {{ str_plural('month', $device->warranty) }}</td>
            </tr>
            <tr>
                <td>Installed by</td>
                <td><a href="{{ url('/user/' . $device->user->id . '/details') }}" target="_blank">{{ $device->user->full_name }}</a></td>
            </tr>
            @if($device->extended_warranty)
            <tr>
                <td>Extended warranty</td>
                <td>{{ $device->extended_warranty }} {{ str_plural('month', $device->extended_warranty) }}, ends at {{ \Carbon\Carbon::parse($device->extended_warranty_start)->addMonth($device->extended_warranty)->format(DEFAULT_DATE_FORMAT) }}</td>
            </tr>
            @endif
            <tr>
                <td>Last service request</td>
                <td>@if(!$device->serviceRequests->isEmpty()){{ $device->serviceRequests->last()->f_request_date }}@endif</td>
            </tr>
            <tr>
                <td>Next preventive maint.</td>
                <td>@if(!$device->preventiveMaintenances->isEmpty()){{ $device->preventiveMaintenances->first()->f_maintenance_date }}@endif</td>
            </tr>
        </table>
        <img @if($device->deviceModel->photo)src="/uploads/models/devicemodel-{{ $device->deviceModel->id }}/{{ $device->deviceModel->photo }}"@else src="/assets/img/404-logo.png"@endif style="max-height: 300px; max-width: 300px; margin-left: 10px;">
    </div>
    <div class="box-footer">
        @if(!$device->extended_warranty)<a href="{{ url('/device/' . $device->id . '/warranty') }}" class="btn btn-primary">Add extended warranty</a>@endif
        <a href="{{ url('/device/' . $device->id . '/serviceRequests') }}" class="btn btn-primary">View service requests</a>
        <a href="{{ url('/device/' . $device->id . '/serviceLog') }}" class="btn btn-primary">View service log</a>
        <a href="{{ url('/device/' . $device->id . '/serviceReport') }}" class="btn btn-primary" target="_blank">Print service report</a>
    </div>
</div>
@endsection