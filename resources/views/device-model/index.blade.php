@extends('layouts.app')

@section('title', '| Devices models')

@section('content_header')
    @include('layouts.html.content-header', [
        'title' => 'Devices models',
        'menu' => [
            '' => ['last' => true, 'name' => 'Devices models']
        ]
    ])
@endsection

@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <a href="{{ url('/devicesModel') }}" class="btn btn-primary btn-xs pull-right">Add new device model</a>
        </div>
        <div class="box-body">
            <table id="device-models" class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Device type/model</th>
                    <th>Supplier</th>
                    <th>Counter 1</th>
                    <th>Counter 2</th>
                    <th>Counter 3</th>
                    <th class="nosort">Active</th>
                    <th class="nosort">Actions</th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <th>ID</th>
                    <th>Device type/model</th>
                    <th>Supplier</th>
                    <th>Counter 1</th>
                    <th>Counter 2</th>
                    <th>Counter 3</th>
                    <th>Active</th>
                    <th class="nosort">Actions</th>
                </tr>
                </tfoot>
                <tbody>
                @foreach($models as $model)
                    <tr>
                        <td>{{ $model->id }}</td>
                        <td>{{ $model->name }}</td>
                        <td><a href="{{ url('/supplier/' . $model->supplier->id . '/details') }}" target="_blank">{{ $model->supplier->name }}</a></td>
                        <td>{{ $model->counter_1 }}</td>
                        <td>{{ $model->counter_2 }}</td>
                        <td>{{ $model->counter_3 }}</td>
                        <td>@include('layouts.html.active-glyphicon', ['active' => $model->active])</td>
                        <td>
                            <a href="{{ url('/devicesModel/' . $model->id) }}">Edit</a><br>
                            <a href="#" data-href="{{ url('/devicesModel/' . $model->id) }}" data-toggle="modal" data-target="#confirm-delete" data-body="Model will be deactivated! Are you sure?">Delete</a>
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
                id: 'device-models',
                columns: [1, 2],
                optionName: 'All',
            }, function () {
                $(document).resize();
            });
        });
    </script>
@endsection