@extends('layouts.app')

@section('title', '| Devices')

@section('content_header')
    @include('layouts.html.content-header', [
        'title' => 'Devices',
        'menu' => [
            '' => ['last' => true, 'name' => 'Devices']
        ]
    ])
@endsection

@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <a href="{{ url('/device') }}" class="btn btn-primary btn-xs pull-right btn-kg-header">Add new device</a>
            <a href="{{ url('/devicesModel') }}" class="btn btn-primary btn-xs pull-right">Add new device model</a>
        </div>
        <div class="box-body">
            <table id="devices" class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Serial number</th>
                    <th>Customer</th>
                    <th>Model</th>
                    <th>Install date</th>
                    <th>Warranty end</th>
                    <th class="nosort">Actions</th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <th>ID</th>
                    <th>Serial number</th>
                    <th>Customer</th>
                    <th>Model</th>
                    <th>Install date</th>
                    <th>Warranty end</th>
                    <th>Actions</th>
                </tr>
                </tfoot>
                <tbody>
                @foreach($devices as $device)
                    <tr>
                        <td>{{ $device->id }}</td>
                        <td>{{ $device->serial_number }}</td>
                        <td><a href="{{ url('/customer/' . $device->customer->id . '/details') }}" target="_blank">{{ $device->customer->clinic_name }}</a></td>
                        <td>{{ $device->deviceModel->name }}</td>
                        <td data-order="{{ \Carbon\Carbon::parse($device->install_date)->timestamp }}">{{ $device->getFInstallDate() }}</td>
                        <?php $warrantyEnd = $device->extended_warranty ? \Carbon\Carbon::parse($device->extended_warranty_start)->addMonth($device->extended_warranty) : \Carbon\Carbon::parse($device->install_date)->addMonth($device->warranty); ?>
                        <td data-order="{{ $warrantyEnd->timestamp }}">@include('layouts.html.label-warranty', ['date' => $warrantyEnd])</td>
                        <td>
                            <a href="{{ url('/device/' . $device->id) }}">Edit</a><br>
                            <a href="{{ url('/device/' . $device->id . '/details') }}">View details</a><br>
                            {{-- @permission('delete-device-model')<a href="#" data-href="{{ url('/supplier/' . $supplier->id . '/deviceModel/' . $model->id) }}" data-toggle="modal" data-target="#confirm-delete" data-body="Model will be deleted! Are you sure?">Delete</a>@endpermission --}}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @include('layouts.html.modals.delete')
    <script>
        $(function () {
            initColumnFiltersForDataTable({
                id: 'devices',
                columns: [2, 3],
                optionName: 'All',
            }, function () {
                $(document).resize();
            });
        });
    </script>
@endsection