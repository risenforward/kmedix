<div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
        <div class="checkbox">
            <label style="padding-left: 0px !important;">
                <input type="checkbox" name="{{ $name }}" @if(isset($checked) && $checked) checked @endif> {{ $caption }}
            </label>
        </div>
    </div>
</div>