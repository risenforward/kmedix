@extends('layouts.app')

@section('title', '| Suppliers')

@section('content_header')
    @include('layouts.html.content-header', [
        'title' => 'Suppliers',
        'menu' => [
            '' => ['last' => true, 'name' => 'Suppliers']
        ]
    ])
@endsection

@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <a href="{{ url('/supplier') }}" class="btn btn-primary btn-xs pull-right">Add new supplier</a>
        </div>
        <div class="box-body">
            <table id="suppliers" class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Supplier name (brand)</th>
                    <th>Phone</th>
                    <th>Country</th>
                    <th class="nosort">Contact persons</th>
                    <th class="nosort">Device models</th>
                    <th class="nosort">Active</th>
                    <th class="nosort">Actions</th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <th>ID</th>
                    <th>Supplier name (brand)</th>
                    <th>Phone</th>
                    <th>Country</th>
                    <th>Contact persons</th>
                    <th>Device models</th>
                    <th>Active</th>
                    <th>Actions</th>
                </tr>
                </tfoot>
                <tbody>
                @foreach($suppliers as $supplier)
                    <tr>
                        <td>{{ $supplier->id }}</td>
                        <td>{{ $supplier->name }}</td>
                        <td>{{ phone_format($supplier->phone_number) }}</td>
                        <td>@if($supplier->country){{ Countries::getOne($supplier->country) }}@endif</td>
                        <td><a href="{{ url('/supplier/' . $supplier->id . '/persons') }}"><span class="badge bg-blue">{{ $supplier->contactPersons->count() }}</span></a></td>
                        <td><a href="{{ url('/supplier/' . $supplier->id . '/deviceModels') }}" target="_blank"><span class="badge bg-blue">{{ $supplier->deviceModels->count() }}</span></a></td>
                        <td>@include('layouts.html.active-glyphicon', ['active' => $supplier->active])</td>
                        <td>
                            <a href="{{ url('/supplier/' . $supplier->id) }}">Edit</a><br>
                            <a href="{{ url('/supplier/' . $supplier->id . '/details') }}">View details</a><br>
                            <a href="#" data-href="{{ url('/supplier/' . $supplier->id) }}" data-toggle="modal" data-target="#confirm-delete" data-body="Customer will be deleted! Are you sure?">Delete</a>
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
                id: 'suppliers',
                columns: [3],
                optionName: 'All',
            }, function () {
                $(document).resize();
            });
        });
    </script>
@endsection