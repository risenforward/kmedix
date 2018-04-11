@extends('layouts.html.master')

@section('element')
    <?php $format = isset($format) ? $format : 'yyyy/mm/dd' ?>
    <input
            id="field_{{ $name }}_id"
            type="text"
            class="form-control"
            name="{{ $name }}"
            @if(isset($disabled)) disabled @endif
            value="@if(count($errors) > 0){{ old($name) }}@else{{ $value or '' }}@endif">
    <script>
        $(function () {
            $('#field_{{ $name }}_id').datepicker({
                autoclose: true,
                orientation: 'top',
                todayHighlight: true,
                format:'@if(isset($format)){{ $format }}@endif'
                @if(isset($start)), startDate: '{{ $start }}'@endif
            });
        });
    </script>
@overwrite