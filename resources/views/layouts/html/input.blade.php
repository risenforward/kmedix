@extends('layouts.html.master')
@section('element')
    <input
            id="field_{{ $name }}_id"
            type="{{ $type or 'text' }}"
            name="{{ $name }}"
            class="form-control"
            id="{{ $name }}-input"
            placeholder="{{ $caption }}"
            @if(isset($disabled)) disabled @endif
            value="@if(count($errors) > 0){{ old($name) }}@else{{ $value or '' }}@endif"
    >
@overwrite
