@extends('layouts.app')

@section('title', '| Update device model')

@section('content_header')
    @include('layouts.html.content-header', [
        'title' => 'Update supplier',
        'menu' => [
            '/devicesModels' => ['icon' => 'fa-tags', 'name' => 'Devices models'],
            '' => ['last' => true, 'name' => 'Update device model']
        ]
    ])
@endsection

@section('content')
    <div class="box box-primary">
        <form method="post" action="{{ url('/devicesModel/' . $model->id) }}" class="form-horizontal" enctype="multipart/form-data">
            {{ csrf_field() }}
            <input type="hidden" name="_method" value="put">
            @include('device-model.form')
        </form>
        @include('layouts.html.submit-loading')
    </div>
    <script>
        $(function () {
            var input = $('input[type=file]');
            input.fileinput({
                allowedPreviewTypes: ['image'],
                allowedFileExtensions: ['png'],
                previewZoomSettings: {
                    image: {width: "auto", height: "100%"},
                },
                showUpload: false,
                showRemove: false,
                layoutTemplates: {
                    actionZoom: '',
                    actionDrag: ''
                },
                initialPreview:
                @if(!empty($model->photo))
                    ["{{ url('/uploads/models/devicemodel-' . $model->id . '/' . $model->photo) }}"],
                @else
                     [],
                @endif
                initialPreviewAsData: true,
                initialPreviewConfig: [{
                    caption: '{{ $model->photo }}',
                    url: "{{ url('/devicesModel/'  . $model->id . '/photo') }}",
                    extra: {
                        _method: 'delete',
                        _token: '{{ csrf_token() }}',
                    }
                }],
            });
        });
    </script>
@endsection