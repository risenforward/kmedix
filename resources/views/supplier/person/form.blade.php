<div class="box-body">
    @include('layouts.html.input', ['name' => 'first_name', 'caption' => 'First name', 'value' => @$person->first_name])
    @include('layouts.html.input', ['name' => 'last_name', 'caption' => 'Last name', 'value' => @$person->last_name])
    @include('layouts.html.input', ['name' => 'middle_name', 'caption' => 'Middle name', 'value' => @$person->middle_name])
    @include('layouts.html.input', ['name' => 'email', 'type' => 'email', 'caption' => 'Email address', 'value' => @$person->email])
    @include('layouts.html.phone', ['name' => 'phone_number', 'caption' => 'Phone', 'required' => 'required', 'value' => @$person->phone_number])
</div>
<div class="box-footer">
    <button type="submit" class="btn btn-default">Submit</button>
</div>