@extends('layouts.app')

@section('title', '| Users')

@section('content_header')
    @include('layouts.html.content-header', [
        'title' => 'Users',
        'menu' => [
            '' => ['last' => true, 'name' => 'Users']
        ]
    ])
@endsection

@section('content')
<div class="box box-primary">
    <div class="box-header with-border">
        <a href="{{ url('/user') }}" class="btn btn-primary btn-xs pull-right">Add new user</a>
    </div>
    <div class="box-body">
        <table id="users" class="table table-striped table-bordered">
            <thead>
            <tr>
                <th>ID</th>
                <th>Email address</th>
                <th>Full name</th>
                <th class="nosort">Type</th>
                <th class="nosort">Active</th>
                <th class="nosort">Actions</th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <th>ID</th>
                <th>Email</th>
                <th>Full name</th>
                <th>Type</th>
                <th>Active</th>
                <th>Actions</th>
            </tr>
            </tfoot>
            <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td><a href="mailto:{{ $user->email }}">{{ $user->email }}</a></td>
                    <td>{{ $user->full_name }}</td>
                    <td>{{ $user->role }}</td>
                    <td>
                        @include('layouts.html.active-glyphicon', ['active' => $user->active])
                    </td>
                    <td>
                        <a href="{{ url('/user/' . $user->id) }}">Edit</a><br>
                        <a href="{{ url('/user/' . $user->id . '/details') }}">View details</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
<script>
    $(function () {
        initColumnFiltersForDataTable({
            id: 'users',
            columns: [2],
            optionName: 'All',
        }, function () {
            $(document).resize();
        });
    });
</script>
@endsection