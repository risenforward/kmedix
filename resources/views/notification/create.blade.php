@extends('layouts.app')

@section('title', '| Send notification')

@section('content_header')
    @include('layouts.html.content-header', [
        'title' => 'Send notification',
        'menu' => [
            $back['url'] => ['icon' => '', 'name' => $back['name']],
            '' => ['last' => true, 'name' => 'Send notification']
        ]
    ])
@endsection

@section('content')
    <div class="box box-primary">
        <form method="post" action="{{ url('/' . $route . '/' . $model->id . '/notification') }}" class="form-horizontal">
            {{ csrf_field() }}
            <div class="box-body">
                @include('layouts.html.select', ['name' => 'customer', 'caption' => 'Customer', 'items' => $customers, 'mode' => 'assoc', 'selected' => $model->customer->id, 'zero' => 'All'])
                @include('layouts.html.textarea', ['name' => 'message', 'caption' => 'Message'])
                <div class="box-footer">
                    <button type="submit" class="btn btn-default">Submit</button>
                </div>
            </div>
        </form>
    </div>
@endsection