@extends('layouts.app')

@section('title', '| Service request details')

@section('content_header')
    <?php
        $back = URL::previous();
        $back = strpos($back, 'serviceRequest/') ? '/serviceRequests' : $back
    ?>
    @include('layouts.html.content-header', [
        'title' => 'Service request details',
        'menu' => [
            $back => ['icon' => 'fa-fax', 'name' => 'Service requests'],
            '' => ['last' => true, 'name' => 'Service request details']
        ]
    ])
@endsection

@section('content')
<div class="box box-primary">
    <div class="box-body">
        <table class="table table-bordered details">
            <tr>
                <td>Requested by</td>
                <td><a href="{{ url('/customer/' .  $request->device->customer->id .'/details') }}" target="_blank">{{ $request->device->customer->clinic_name }}</a></td>
            </tr>
            <tr>
                <td>Type</td>
                <td>{{ \App\ServiceRequest::$types[$request->type] }}</td>
            </tr>
            <tr>
                <td>Request date</td>
                <td>{{ $request->f_request_date }}</td>
            </tr>
            <tr>
                <td>Description</td>
                <td>{{ $request->description }}</td>
            </tr>
            <tr>
                <td>Status</td>
                <td>{{ \App\ServiceRequest::$statuses[$request->status] }}</td>
            </tr>
            <tr>
                <td>Assigned by</td>
                <td>@if($request->user)<a href="{{ url('/user/' . $request->user->id . '/details') }}" target="_blank">{{ $request->user->full_name }}</a>@endif</td>
            </tr>
            <tr>
                <td>Assigned at</td>
                <td>{{ $request->f_attended_at }}</td>
            </tr>
            <tr>
                <td>Completed at</td>
                <td>{{ $request->f_completed_at }}</td>
            </tr>
            <tr>
                <td>Closed at</td>
                <td>{{ $request->f_closed_at }}</td>
            </tr>
            <tr>
                <td>Service rating</td>
                <td><div class="rateit bigstars" data-rateit-value="@if(!$request->rating->isEmpty()){{ $request->rating->first()->rating }}@else{{ 0 }}@endif" data-rateit-ispreset="true" data-rateit-readonly="true"></div></td>
            </tr>
        </table>
    </div>
    <div class="box-footer">
        @if($request->status != \App\ServiceRequest::CLOSED)
            <a href="#" class="btn btn-warning" data-href="{{ url('/serviceRequest/' . $request->id . '/close') }}" data-toggle="modal" data-target="#confirm-update" data-body="Service request status will be change to <b>Close</b>! Are you sure?">Close request</a>
        @endif
        <a href="#" class="btn btn-danger" data-href="{{ url('/serviceRequest/' . $request->id) }}" data-toggle="modal" data-target="#confirm-delete" data-body="Service request will be deleted! Are you sure?">Delete</a>
    </div>
</div>
@include('layouts.html.modals.update')
@include('layouts.html.modals.delete')
@endsection