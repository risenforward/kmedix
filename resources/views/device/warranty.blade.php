@extends('layouts.app')

@section('title', '| Add extended warranty')

@section('content_header')
    @include('layouts.html.content-header', [
        'title' => 'Add extended warranty',
        'menu' => [
            '/devices' => ['icon' => 'fa-cog', 'name' => 'Devices'],
             '/device/' . $device->id . '/details' => ['icon' => '', 'name' => 'Device details'],
            '' => ['last' => true, 'name' => 'Add extended warranty']
        ]
    ])
@endsection

@section('content')
    <div class="box box-primary">
        <form method="post" action="{{ url('/device/' . $device->id . '/warranty') }}" class="form-horizontal">
            {{ csrf_field() }}
            <input type="hidden" name="_method" value="put">
            <div class="box-body">
                @include('layouts.html.datepicker', ['name' => 'extended_warranty_start', 'caption' => 'Start date', 'start' => \Carbon\Carbon::now()->format('Y/m/d'), 'value' => $device->extended_warranty_start])
                @include('layouts.html.input', ['name' => 'extended_warranty', 'caption' => 'Duration', 'value' => $device->extended_warranty])
            </div>
            <div class="box-footer">
                <button type="submit" class="btn btn-default">Submit</button>
            </div>
        </form>
        @include('layouts.html.submit-loading')
    </div>
@endsection