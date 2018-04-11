@extends('layouts.html.master')
@section('element')
    <?php $mode = isset($mode) ? $mode : 'values'; //default mode is to use values only ?>
    <?php $selected = count($errors) > 0 ? old($name) : (isset($selected) ? $selected : '');?>
    <select name="{{ $name }}" class="form-control" @if(isset($disabled)) disabled @endif>
        <option value="0">{{ $zero or  '-- choose --'}}</option>
        @foreach($items as $key => $value)
            <?php switch($mode) {
                case 'values': $key = $value; break;
                case 'assoc' :
                    if(is_object($value)) {
                        $key = $value->id; $value = $value->name;
                    } else {
                        $key = $value['id']; $value = $value['name'];
                    }
                    break;
                case 'pairs' : default: break; //use original key=>value pairs
            } ?>
            <option value="{{ $key }}" @if((string)$key === (string)$selected) selected="1" @endif>{{ $value }}</option>
        @endforeach
    </select>
@overwrite