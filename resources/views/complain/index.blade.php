@extends('layouts.app')

@section('title', '| Complains')

@section('content_header')
    @include('layouts.html.content-header', [
        'title' => 'Complains',
        'menu' => [
            '' => ['last' => true, 'name' => 'Complains']
        ]
    ])
@endsection

@section('content')
    @if($new->count())
    <div class="box box-warning">
        <div class="box-header with-border">
            <h3 class="box-title">New complains</h3>
        </div>
        <div class="box-body">
            <table id="new-complains" class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th class="nosort">Customer</th>
                    <th class="nosort">Date</th>
                    <th class="nosort">Description</th>
                    <th class="nosort">Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($new as $complain)
                    <tr>
                        <td><a href="{{ url('/customer/' . $complain->customer->id . '/details') }}" target="_blank">{{ $complain->customer->clinic_name }}</a></td>
                        <td>{{ $complain->getFCreatedAt() }}</td>
                        <td>{{ $complain->description }}</td>
                        <td>
                            <a href="{{ url('/complain/' . $complain->id) }}">Add note</a><br>
                            <a href="{{ url('/complain/' . $complain->id . '/notification') }}">Send notification</a><br>
                            <a href="#" data-href="{{ url('/complain/' . $complain->id) }}" data-toggle="modal" data-target="#confirm-delete" data-body="Complain will be deleted! Are you sure?">Dismiss</a><br>
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
            <h3 class="box-title">Processed complains</h3>
            <div class="pull-right" style="margin-left: 3px;">to: <input type="text" id="date-to"></div>
            <div class="pull-right">Date from: <input type="text" id="date-from"></div>
        </div>
        <div class="box-body">
            <table id="complains" class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th class="nosort">Description</th>
                    <th class="nosort">Notes</th>
                    <th class="nosort">Actions</th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Notes</th>
                    <th>Actions</th>
                </tr>
                </tfoot>
                <tbody>
                @foreach($complains as $complain)
                    <tr>
                        <td>{{ $complain->id }}</td>
                        <td><a href="{{ url('/customer/' . $complain->customer->id . '/details') }}" target="_blank">{{ $complain->customer->clinic_name }}</a></td>
                        <td data-order="{{ \Carbon\Carbon::parse($complain->created_at)->timestamp }}">{{ $complain->getFCreatedAt() }}</td>
                        <td>{{ $complain->description }}</td>
                        <td>{{ $complain->notes }}</td>
                        <td>
                            <a href="{{ url('/complain/' . $complain->id) }}">Details</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @include('layouts.html.modals.delete')
    <script>
        $(function () {
            initColumnFiltersForDataTable({
                id: 'new-complains', paging: false, ordering: false, info: false, search: false
            }, function () {
                $(document).resize();
            });
            var $table = initColumnFiltersForDataTable({
                id: 'complains',
                columns: [1],
                optionName: 'All',
            }, function () {
                $(document).resize();
            });
            initDateRange($table, 'date-from', 'date-to', 2);
            $table.draw();
        });
    </script>
@endsection