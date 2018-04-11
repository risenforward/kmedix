@extends('layouts.app')

@section('title', '| Pending request')

@section('content_header')
    @include('layouts.html.content-header', [
        'title' => 'Pending request',
        'menu' => [
            '/salesRequests' => ['icon' => 'fa-cart-plus', 'name' => 'Sales requests'],
            '' => ['last' => true, 'name' => 'Pending request']
        ]
    ])
@endsection

@section('content')
    <div class="box box-primary">
        <form method="post" action="{{ url('/salesRequest/' . $request->id . '/status/' . $status) }}" class="form-horizontal">
            {{ csrf_field() }}
            <input type="hidden" name="_method" value="put">
            <div class="box-body">
                <div class="form-group">
                    <label class="col-sm-2 control-label">Request details</label>
                    <div class="col-sm-10">
                        <div class="form-control" disabled>{{ $request->request_details }}</div>
                    </div>
                </div>
            </div>
            <div class="box-body">
                @include('layouts.html.textarea', ['name' => 'notes', 'caption' => 'Notes'])
                <div class="box-footer">
                    <button type="submit" class="btn btn-default">Submit</button>
                </div>
            </div>
        </form>
    </div>
@endsection