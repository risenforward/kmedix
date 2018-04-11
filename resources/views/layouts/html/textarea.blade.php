@extends('layouts.html.master')
@section('element')
    <textarea name="{{ $name }}" class="form-control" rows="3" id="{{ $name }}-input" placeholder="{{ $caption }}">@if(count($errors) > 0){{ old($name) }}@else{{ $value or '' }}@endif</textarea>
@overwrite