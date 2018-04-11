@extends('layouts.app')

@section('title', '| Complain details')

@section('content_header')
    @include('layouts.html.content-header', [
        'title' => 'Complain details',
        'menu' => [
            '/complains' => ['icon' => 'fa-frown-o', 'name' => 'Complains'],
            '' => ['last' => true, 'name' => 'Complain details']
        ]
    ])
@endsection

@section('content')
    <div class="box box-primary">
        <form method="post" action="{{ url('/complain/' . $complain->id) }}" class="form-horizontal">
            {{ csrf_field() }}
            <input type="hidden" name="_method" value="put">
            <div class="box-body">
                <div class="form-group">
                    <label class="col-sm-2 control-label">Customer</label>
                    <div class="col-sm-10">
                        <div class="form-control" disabled>{{ $complain->customer->clinic_name }}</div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Date</label>
                    <div class="col-sm-10">
                        <div class="form-control" disabled>{{ $complain->getFCreatedAt(DEFAULT_DATETIME_FORMAT) }}</div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Status</label>
                    <div class="col-sm-10">
                        <div class="form-control" disabled>{{ \App\Complain::$statuses[$complain->status] }}</div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Description</label>
                    <div class="col-sm-10">
                        <div class="form-control" disabled>{{ $complain->description }}</div>
                    </div>
                </div>
            </div>
            <div class="box-body">
                @include('layouts.html.textarea', ['name' => 'notes', 'caption' => 'Notes', 'value' => $complain->notes])
                <div class="box-footer">
                    <button type="submit" class="btn btn-default">Submit</button>
                </div>
            </div>
        </form>
    </div>
@endsection