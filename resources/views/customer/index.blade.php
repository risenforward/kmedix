@extends('layouts.app')

@section('title', '| Customers')

@section('content_header')
    @include('layouts.html.content-header', [
        'title' => 'Customers',
        'menu' => [
            '' => ['last' => true, 'name' => 'Customers']
        ]
    ])
@endsection

@section('content')
<div class="box box-primary">
    <div class="box-header with-border">
        <a href="{{ url('/customer') }}" class="btn btn-primary btn-xs pull-right">Add new customer</a>
    </div>
    <div class="box-body">
        <table id="customers" class="table table-striped table-bordered">
            <thead>
            <tr>
                <th>ID</th>
                <th>Clinic name</th>
                <th>Phone</th>
                <th>Institute type</th>
                <th>Contact persons</th>
                <th class="nosort">Blocked</th>
                <th class="nosort">Actions</th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <th>ID</th>
                <th>Clinic name</th>
                <th>Phone</th>
                <th>Institute type</th>
                <th>Contact persons</th>
                <th>Blocked</th>
                <th>Actions</th>
            </tr>
            </tfoot>
            <tbody>
            @foreach($customers as $customer)
                <tr>
                    <td>{{ $customer->id }}</td>
                    <td>{{ $customer->clinic_name }}</td>
                    <td>{{ phone_format($customer->user->phone_number) }}</td>
                    <td>{{ $customer->specialization }}</td>
                    <td><a href="{{ url('/customer/' . $customer->id . '/persons') }}"><span class="badge bg-blue">{{ $customer->contactPersons->count() }}</span></a></td>
                    <td>
                        @include('layouts.html.active-glyphicon', ['active' => $customer->user->active]){{ $customer->user->active ? 'No' : 'Yes' }}
                    </td>
                    <td>
                        <a href="{{ url('/customer/' . $customer->id) }}">Edit</a><br>
                        <a href="{{ url('/customer/' . $customer->id . '/details') }}">View details</a><br>
                        <a href="#" data-href="{{ url('/customer/' . $customer->id) }}" data-toggle="modal" data-target="#confirm-delete" data-body="Customer will be deleted! Are you sure?">Delete</a>
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
            id: 'customers',
            columns: [3],
            optionName: 'All',
        }, function () {
            $(document).resize();
        });
    });
</script>
@endsection