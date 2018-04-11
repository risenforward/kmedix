@extends('layouts.app')

@section('title', '| Contact persons')

@section('content_header')
    @include('layouts.html.content-header', [
        'title' => 'Contact persons (' . $customer->clinic_name . ')',
        'menu' => [
            '/customers' => ['icon' => 'fa-user-md', 'name' => 'Customers'],
            '' => ['last' => true, 'name' => 'Contact persons']
        ]
    ])
@endsection

@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <a href="{{ url('/customer/' . $customer->id . '/person') }}" class="btn btn-primary btn-xs pull-right">Add new person</a>
        </div>
        <div class="box-body">
            <table id="persons" class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Full name</th>
                    <th>Phone</th>
                    <th class="nosort">Actions</th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <th>ID</th>
                    <th>Full name</th>
                    <th>Phone</th>
                    <th>Actions</th>
                </tr>
                </tfoot>
                <tbody>
                @foreach($customer->contactPersons as $person)
                    <tr>
                        <td>{{ $person->id }}</td>
                        <td>{{ $person->full_name }}</td>
                        <td>{{ phone_format($person->phone_number) }}</td>
                        <td>
                            <a href="{{ url('/customer/' . $customer->id . '/person/' . $person->id) }}">Edit</a><br>
                            <a href="#" data-href="{{ url('/customer/' . $customer->id . '/person/' . $person->id) }}" data-toggle="modal" data-target="#confirm-delete" data-body="Contact person will be deleted! Are you sure?">Delete</a>
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
                id: 'persons',
            }, function () {
                $(document).resize();
            });
        });
    </script>
@endsection