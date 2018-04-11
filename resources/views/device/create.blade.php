@extends('layouts.app')

@section('title', '| Add new device')

@section('content_header')
    @include('layouts.html.content-header', [
        'title' => 'Add new device',
        'menu' => [
            '/devices' => ['icon' => 'fa-cog', 'name' => 'Devices'],
            '' => ['last' => true, 'name' => 'Add new device']
        ]
    ])
@endsection

@section('content')
    <div class="box box-primary">
        <form method="post" action="{{ url('/device') }}" class="form-horizontal" enctype="multipart/form-data">
            {{ csrf_field() }}
            @include('device.form')
        </form>
        @include('layouts.html.submit-loading')
    </div>
@endsection