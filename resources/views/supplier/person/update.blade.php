@extends('layouts.app')

@section('title', '| Update person')

@section('content_header')
    @include('layouts.html.content-header', [
        'title' => 'Update person',
        'menu' => [
            '/suppliers' => ['icon' => 'fa-truck', 'name' => 'Suppliers'],
            '/supplier/' . $supplier->id . '/persons' => ['icon' => 'fa-users', 'name' => 'Contact persons'],
            '' => ['last' => true, 'name' => 'Update person']
        ]
    ])
@endsection

@section('content')
    <div class="box box-primary">
        <form method="post" action="{{ url('/supplier/' . $supplier->id . '/person/' . $person->id) }}" class="form-horizontal">
            {{ csrf_field() }}
            <input type="hidden" name="_method" value="put">
            @include('supplier.person.form')
        </form>
        @include('layouts.html.submit-loading')
    </div>
@endsection