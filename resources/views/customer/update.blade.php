@extends('layouts.app')

@section('title', '| Update customer')

@section('content_header')
    @include('layouts.html.content-header', [
        'title' => 'Update customer',
        'menu' => [
            '/customers' => ['icon' => 'fa-user-md', 'name' => 'Customers'],
            '' => ['last' => true, 'name' => 'Update customer']
        ]
    ])
@endsection

@section('content')
    <div class="box box-primary">
        <form method="post" action="{{ url('/customer/' . $customer->id) }}" class="form-horizontal" enctype="multipart/form-data">
            {{ csrf_field() }}
            <input type="hidden" name="_method" value="put">
            <div class="box-body">
                @include('layouts.html.input', ['name' => 'clinic_name', 'caption' => 'Clinic name', 'value' => $customer->clinic_name])
                @include('layouts.html.input', ['name' => 'username', 'caption' => 'Username', 'value' => $customer->user->username])
                @include('layouts.html.input', ['name' => 'email', 'type' => 'email', 'caption' => 'Email address', 'value' => $customer->user->email])
                @include('layouts.html.phone', ['name' => 'phone_number', 'caption' => 'Phone', 'required' => 'required', 'value' => $customer->user->phone_number])
                @include('layouts.html.input', ['name' => 'address', 'caption' => 'Address', 'value' => $customer->address])
                @include('layouts.html.select', ['name' => 'specialization', 'caption' => 'Institute type', 'items' => \App\Customer::$specializations, 'mode' => 'values', 'selected' => $customer->specialization])
                @include('layouts.html.input', ['name' => 'image', 'type' => 'file', 'caption' => 'Logo'])
                @include('layouts.html.checkbox', ['name' => 'active', 'caption' => 'Blocked', 'checked' => !$customer->user->active])
            </div>
            <div class="box-footer">
                <button type="submit" class="btn btn-default">Submit</button>
            </div>
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
                    @if(!empty($customer->logo))
                        ["{{ url('/uploads/customers/customer-' . $customer->id . '/' . $customer->logo) }}"],
                    @else
                        [],
                    @endif
                initialPreviewAsData: true,
                initialPreviewConfig: [{
                    caption: '{{ $customer->logo }}',
                    url: "{{ url('customer/' . $customer->id . '/logo') }}",
                    extra: {
                        _method: 'delete',
                        _token: '{{ csrf_token() }}',
                    }
                }],
            });
        });
    </script>
@endsection