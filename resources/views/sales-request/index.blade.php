@extends('layouts.app')

@section('title', '| Sales requests')

@section('content_header')
    @include('layouts.html.content-header', [
        'title' => 'Sales requests',
        'menu' => [
            '' => ['last' => true, 'name' => 'Sales requests']
        ]
    ])
@endsection

@section('content')
    @if($new->count())
    <div class="box box-warning">
        <div class="box-header with-border">
            <h3 class="box-title">New requests</h3>
        </div>
        <div class="box-body">
            <table id="new-requests" class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Request details</th>
                    <th>Request date</th>
                    <th class="nosort">Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($new as $request)
                    <tr>
                        <td>{{ $request->id }}</td>
                        <td><a href="{{ url('/customer/' . $request->customer->id . '/details') }}" target="_blank">{{ $request->customer->clinic_name }}</a></td>
                        <td>{{ $request->request_details }}</td>
                        <td>{{ $request->f_request_date }}</td>
                        <td>
                            <a href="{{ url('/salesRequest/' . $request->id . '/status/' . \App\SalesRequest::PROCESSED) }}">Processed</a><br>
                            <a href="{{ url('/salesRequest/' . $request->id . '/notification') }}">Send notification</a><br>
                            <a href="#" data-href="{{ url('/salesRequest/' . $request->id . '/status/' . \App\SalesRequest::DISMISS) }}" data-toggle="modal" data-target="#confirm-update" data-body="Sales request status will be change to <b>Dismiss</b>! Are you sure?">Dismiss</a>
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
            <h3 class="box-title">Pending requests</h3>
            <div class="pull-right" style="margin-left: 3px;">to: <input type="text" id="date-to"></div>
            <div class="pull-right">Date from: <input type="text" id="date-from"></div>
        </div>
        <div class="box-body">
            <table id="pending-requests" class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Request details</th>
                    <th>Request date</th>
                    <th>Notes</th>
                    <th class="nosort">Actions</th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Request details</th>
                    <th>Request date</th>
                    <th>Notes</th>
                    <th class="nosort">Actions</th>
                </tr>
                </tfoot>
                <tbody>
                @foreach($requests as $request)
                    <tr>
                        <td>{{ $request->id }}</td>
                        <td><a href="{{ url('/customer/' . $request->customer->id . '/details') }}" target="_blank">{{ $request->customer->clinic_name }}</a></td>
                        <td>{{ $request->request_details }}</td>
                        <td data-order="{{ \Carbon\Carbon::parse($request->request_date)->timestamp }}">{{ $request->f_request_date }}</td>
                        <td>{{ $request->notes }}</td>
                        <td><a href="{{ url('/salesRequests/' . $request->id) }}">Details</a></td>
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
                id: 'new-requests', paging: false, ordering: false, info: false, search: false
            }, function () {
                $(document).resize();
            });
            var $table = initColumnFiltersForDataTable({
                id: 'pending-requests',
                columns: [1],
                optionName: 'All',
            }, function () {
                $(document).resize();
            });
            initDateRange($table, 'date-from', 'date-to', 3);
            $table.draw();
        });
    </script>
@endsection