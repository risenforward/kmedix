@extends('layouts.app')

@section('title', '| Add new customer')

@section('content_header')
    @include('layouts.html.content-header', [
        'title' => 'Add new customer',
        'menu' => [
            '/customers' => ['icon' => 'fa-user-md', 'name' => 'Customers'],
            '' => ['last' => true, 'name' => 'Add new customer']
        ]
    ])
@endsection

@section('content')
    <div class="box box-primary">
        <form method="post" action="{{ url('/customer') }}" class="form-horizontal" enctype="multipart/form-data">
            <div class="box-body">
                {{ csrf_field() }}
                @include('layouts.html.input', ['name' => 'clinic_name', 'caption' => 'Clinic name'])
                @include('layouts.html.input', ['name' => 'username', 'caption' => 'Username'])
                @include('layouts.html.input', ['name' => 'email', 'type' => 'email', 'caption' => 'Email address'])
                @include('layouts.html.input', ['name' => 'password', 'type' => 'text', 'caption' => 'Password', 'value' => str_random(DEFAULT_PASSWORD_LENGTH)])
                @include('layouts.html.phone', ['name' => 'phone_number', 'caption' => 'Phone', 'required' => 'required'])
                @include('layouts.html.input', ['name' => 'address', 'caption' => 'Address'])
                @include('layouts.html.select', ['name' => 'specialization', 'caption' => 'Institute type', 'items' => \App\Customer::$specializations, 'mode' => 'values'])
                @include('layouts.html.input', ['name' => 'image', 'type' => 'file', 'caption' => 'Logo'])
                @include('layouts.html.checkbox', ['name' => 'active', 'caption' => 'Blocked'])
            </div>
            <div class="box-footer">
                <button type="submit" class="btn btn-default">Submit</button>
            </div>
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