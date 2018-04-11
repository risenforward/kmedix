@extends('layouts.app')

@section('title', '| Service log')

@section('content_header')
    @include('layouts.html.content-header', [
        'title' => 'Service log',
        'menu' => [
            '' => ['last' => true, 'name' => 'Service log']
        ]
    ])
@endsection

@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <div class="pull-right" style="margin-left: 3px;">to: <input type="text" id="date-to"></div>
            <div class="pull-right">Date from: <input type="text" id="date-from"></div>
        </div>
        <div class="box-body">
            <table id="service-log" class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Serial number</th>
                    <th>Customer</th>
                    <th>Device type/model</th>
                    <th>Service date</th>
                    <th>Engineer</th>
                    <th>Part no</th>
                    <!--<th class="nosort">Actions</th>-->
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <th>ID</th>
                    <th>Serial number</th>
                    <th>Customer</th>
                    <th>Device type/model</th>
                    <th>Service date</th>
                    <th>Engineer</th>
                    <th>Part no</th>
                    <!--<th>Actions</th>-->
                </tr>
                </tfoot>
                <tbody>
                @foreach($logs as $log)
                    <tr>
                        <td>{{ $log->id }}</td>
                        <td><a href="{{ url('/device/' . $log->device->id . '/details') }}" target="_blank">{{ $log->device->serial_number }}</a></td>
                        <td><a href="{{ url('/customer/' . $log->device->customer->id . '/details') }}" target="_blank">{{ $log->device->customer->clinic_name }}</a></td>
                        <td>{{ $log->device->deviceModel->name }}</td>
                        <td data-order="{{ \Carbon\Carbon::parse($log->service_date)->timestamp }}">{{ $log->f_service_date }}</td>
                        <td>@if($log->user)<a href="{{ url('/user/' . $log->user->id . '/details') }}" target="_blank">{{ $log->user->full_name  }}</a>@endif</td>
                        <td>{{ $log->part_number }}</td>
                        <!--<td>
                            <a href="#">View details</a><br>
                        </td>-->
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @include('layouts.html.modals.update')
    <script>
        $(function () {
            var $table = initColumnFiltersForDataTable({
                id: 'service-log',
                columns: [2, 5],
                optionName: 'All',
            }, function () {
                $(document).resize();
            });
            initDateRange($table, 'date-from', 'date-to', 4);
            $table.draw();
        });
    </script>
@endsection