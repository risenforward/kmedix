@extends('layouts.app')

@section('title', '| Add new device model')

@section('content_header')
    @include('layouts.html.content-header', [
        'title' => 'Add new device model',
        'menu' => [

            '/devicesModels' => ['icon' => 'fa-tags', 'name' => 'Devices models'],
            '' => ['last' => true, 'name' => 'Add new device model']
        ]
    ])
@endsection

@section('content')
    <div class="box box-primary">
        <form method="post" action="{{ url('/devicesModel') }}" class="form-horizontal" enctype="multipart/form-data">
            {{ csrf_field() }}
            @include('device-model.form')
        </form>
        @include('layouts.html.submit-loading')
    </div>
    <script>
        $(function () {
            $('input[type=file]').fileinput({
                allowedPreviewTypes: ['image'],
                allowedFileExtensions: ['png'],
                previewZoomSettings: {
                    image: {width: "auto", height: "100%"},
                },
                showUpload: false,
            });
        });
    </script>
@endsection