@extends('layouts.app')

@section('title', '| Add new supplier')

@section('content_header')
    @include('layouts.html.content-header', [
        'title' => 'Add new supplier',
        'menu' => [
            '/suppliers' => ['icon' => 'fa-truck', 'name' => 'Suppliers'],
            '' => ['last' => true, 'name' => 'Add new supplier']
        ]
    ])
@endsection

@section('content')
    <div class="box box-primary">
        <form method="post" action="{{ url('/supplier') }}" class="form-horizontal" enctype="multipart/form-data">
            {{ csrf_field() }}
            @include('supplier.form')
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