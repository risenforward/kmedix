<div class="form-group @if($errors->has($name)) has-error @endif">
    <label for="{{ $name }}-input" class="col-sm-2 control-label">{{ $caption }}</label>
    <div class="col-sm-10">
        @yield('element')
        @if($errors->has($name))
            <span class="help-block">{{ $errors->first($name) }}</span>
        @endif
    </div>
</div>