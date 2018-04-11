@extends('layouts.app')

@section('title', '| Update supplier')

@section('content_header')
    @include('layouts.html.content-header', [
        'title' => 'Update supplier',
        'menu' => [
            '/suppliers' => ['icon' => 'fa-truck', 'name' => 'Suppliers'],
            '' => ['last' => true, 'name' => 'Update supplier']
        ]
    ])
@endsection

@section('content')
    <div class="box box-primary">
        <form method="post" action="{{ url('/supplier/' . $supplier->id) }}" class="form-horizontal" enctype="multipart/form-data">
            {{ csrf_field() }}
            <input type="hidden" name="_method" value="put">
            @include('supplier.form')
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
                @if(!empty($supplier->logo))
                    ["{{ url('/uploads/suppliers/supplier-' . $supplier->id . '/' . $supplier->logo) }}"],
                @else
                     [],
                @endif
                initialPreviewAsData: true,
                initialPreviewConfig: [{
                    caption: '{{ $supplier->logo }}',
                    url: "{{ url('supplier/' . $supplier->id . '/logo') }}",
                    extra: {
                        _method: 'delete',
                        _token: '{{ csrf_token() }}',
                    }
                }],
            });
        });
    </script>
@endsection