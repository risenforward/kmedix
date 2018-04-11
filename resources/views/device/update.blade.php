@extends('layouts.app')

@section('title', '| Update supplier')

@section('content_header')
    @include('layouts.html.content-header', [
        'title' => 'Update device',
        'menu' => [
            '/devices' => ['icon' => 'fa-cog', 'name' => 'Devices'],
            '' => ['last' => true, 'name' => 'Update device']
        ]
    ])
@endsection

@section('content')
    <div class="box box-primary">
        <form method="post" action="{{ url('/device/' . $device->id) }}" class="form-horizontal" enctype="multipart/form-data">
            {{ csrf_field() }}
            <input type="hidden" name="_method" value="put">
            @include('device.form')
        </form>
        @include('layouts.html.submit-loading')
    </div>
@endsection