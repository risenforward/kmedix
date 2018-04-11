@extends('layouts.app')

@section('title', '| Device service log')

@section('content_header')
    @include('layouts.html.content-header', [
        'title' => 'Device service log',
        'menu' => [
            '/devices' => ['icon' => 'fa-cog', 'name' => 'Devices'],
            '/device/' . $device->id . '/details' => ['icon' => '', 'name' => 'Device detail'],
            '' => ['last' => true, 'name' => 'Device service log']
        ]
    ])
@endsection

@section('content')
    <div class="box box-primary">
        <div class="box-body">
            <table class="table details">
                <tr>
                    <td><strong>{{ $device->deviceModel->name }} device</strong></td>
                    <td>{{ $device->serial_number }}</td>
                    <td>Install date: {{ $device->getFInstallDate() }}</td>
                </tr>
                <tr>
                    <td><strong>Clinic name</strong></td>
                    <td><a href="{{ url('/customer/' . $device->customer->id . '/details') }}" target="_blank">{{ $device->customer->clinic_name }}</a></td>
                    <td></td>
                </tr>
            </table>
        </div>

        <div class="box-body">
            <table class="table details" style="border: 2px solid black;">
                <tr>
                    <td><strong>Service reports</strong></td>
                </tr>
                @foreach($device->serviceLogs as $log)
                    <tr>
                        <td class="log-item"
                            data-part-no="{{ $log->part_number }}"
                            data-quantity="{{ $log->quantity }}"
                            data-repair-date="{{ $log->f_service_date }}"
                            data-counters-names="{{ json_encode($log->device->deviceModel->getCounters()) }}"
                            data-counters-values="{{ json_encode($log->getCounters()) }}"
                            data-user-id="{{ $log->user->id }}"
                            data-by="{{ $log->user->full_name }}"><strong>{{ $log->f_service_date }} {{ $log->description }}</strong>
			    
                            @if(file_exists( SERVICE_REPORT_PATH.$log->id.'.pdf' ))
                                <a href="/uploads/reports/{{$log->id}}.pdf" target="_blank" class="btn btn-primary pull-right">View Full PDF</a>
                            @endif
			            </td>
                        
                    </tr>
                @endforeach
            </table>
        </div>
        <div class="box-footer">
            <table class="details" style="display: none;">
                <tr>
                    <td>Part #:</td>
                    <td><strong id="part-no"></strong></td>
                    <td>Quantity:</td>
                    <td><strong id="quantity"></strong></td>
                </tr>
                <tr>
                    <td>Repair date:</td>
                    <td><strong id="repair-date"></strong></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>By:</td>
                    <td><a href="#" target="_blank"><strong id="by"></strong></a></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="4"><strong>Counters</strong></td>
                </tr>
            </table>
        </div>
    </div>
    <script>
        $('.log-item').click(function() {
            $('.log-item').removeAttr('style');
            $(this).css('background-color', '#ddd');
            $('#part-no').text($(this).data('part-no'));
            $('#quantity').text($(this).data('quantity'));
            $('#repair-date').text($(this).data('repair-date'));
            $('#by').closest('a').attr('href', '/user/' + $(this).data('user-id') + '/details')
            $('#by').text($(this).data('by'));

            $('.box-footer').find('tr.counter').remove();
            var countersVal = $(this).data('counters-values');
            $.each($(this).data('counters-names'), function (key, value) {
                $('.box-footer').find('table').append('<tr class="counter"><td>' + value + '</td><td>' + countersVal[key] + '</td><td></td><td></td></tr>');
            })

            $('.box-footer').find('table').show();
        });
        $(document).ready(function () {
            $('.log-item').first().click();
        });
    </script>
@endsection