@extends('layouts.app')

@section('title', '| Service requests')

@section('content_header')
    @include('layouts.html.content-header', [
        'title' => 'Service requests',
        'menu' => [
            '' => ['last' => true, 'name' => 'Service requests']
        ]
    ])
@endsection

@section('content')
    @if($unattended && $unattended->count())
    <div class="box box-danger">
        <div class="box-header with-border">
            <h3 class="box-title">Unattended requests</h3>
        </div>
        <div class="box-body">
            <table id="unattended-service-requests" class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th>Serial number</th>
                    <th>Customer</th>
                    <th>Device type/model</th>
                    <th>Request date</th>
                    <th>Request type</th>
                    <th class="nosort">Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($unattended as $request)
                    <tr>
                        <td><a href="{{ url('/device/' . $request->device->id . '/details') }}" target="_blank">{{ $request->device->serial_number }}</a></td>
                        <td><a href="{{ url('/customer/' . $request->device->customer->id . '/details') }}" target="_blank">{{ $request->device->customer->clinic_name }}</a></td>
                        <td>{{ $request->device->deviceModel->name }}</td>
                        <td>{{ $request->f_request_date }}</td>
                        <td>{{ \App\ServiceRequest::$types[$request->type] }}</td>
                        <td>
                            <a href="{{ url('/serviceRequest/' . $request->id . '/details') }}">View details</a><br>
                            <a href="#" data-href="{{ url('/serviceRequest/' . $request->id . '/close') }}" data-toggle="modal" data-target="#confirm-update" data-body="Service request status will be change to <b>Close</b>! Are you sure?">Close</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">All requests</h3>
            <div class="pull-right" style="margin-left: 3px;">to: <input type="text" id="date-to"></div>
            <div class="pull-right">Date from: <input type="text" id="date-from"></div>
        </div>
        <div class="box-body">
            <table id="service-requests" class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Serial number</th>
                    <th>Customer</th>
                    <th>Device type/model</th>
                    <th>Request date</th>
                    <th>Request type</th>
                    <th>Engineer</th>
                    <th>Status</th>
                    <th class="nosort">Actions</th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <th>ID</th>
                    <th>Serial number</th>
                    <th>Customer</th>
                    <th>Device type/model</th>
                    <th>Request date</th>
                    <th>Request type</th>
                    <th>Engineer</th>
                    <th>Status</th>
                    <th class="nosort">Actions</th>
                </tr>
                </tfoot>
                <tbody>
                @foreach($requests as $request)
                    <tr>
                        <td>{{ $request->id }}</td>
                        <td><a href="{{ url('/device/' . $request->device->id . '/details') }}" target="_blank">{{ $request->device->serial_number }}</a></td>
                        <td><a href="{{ url('/customer/' . $request->device->customer->id . '/details') }}" target="_blank">{{ $request->device->customer->clinic_name }}</a></td>
                        <td>{{ $request->device->deviceModel->name }}</td>
                        <td data-order="{{ \Carbon\Carbon::parse($request->request_date)->timestamp }}">{{ $request->f_request_date }}</td>
                        <td>{{ \App\ServiceRequest::$types[$request->type] }}</td>
                        <td>@if($request->user)<a href="{{ url('/user/' . $request->user->id . '/details') }}" target="_blank">{{ $request->user->full_name  }}</a>@endif</td>
                        <td>{{ \App\ServiceRequest::$statuses[$request->status] }}</td>
                        <td>
                            <a href="{{ url('/serviceRequest/' . $request->id . '/details') }}">View details</a><br>
                            @if($request->status != \App\ServiceRequest::CLOSED)
                                <a href="#" data-href="{{ url('/serviceRequest/' . $request->id . '/close') }}" data-toggle="modal" data-target="#confirm-update" data-body="Service request status will be change to <b>Close</b>! Are you sure?">Close</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @include('layouts.html.modals.update')
    <script>
        $(function () {
            initColumnFiltersForDataTable({
                id: 'unattended-service-requests', paging: false, ordering: false, info: false, search: false
            }, function () {
                $(document).resize();
            });
            var $table = initColumnFiltersForDataTable({
                id: 'service-requests',
                columns: [2, 5, 6, 7],
                optionName: 'All',
            }, function () {
                $(document).resize();
            });
            initDateRange($table, 'date-from', 'date-to', 4);
            $table.draw();
        });
    </script>
@endsection