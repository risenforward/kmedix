<div class="box-body">
    @include('layouts.html.input', ['name' => 'name', 'caption' => 'Name/brand', 'value' => @$supplier->name])
    @include('layouts.html.phone', ['name' => 'phone_number', 'caption' => 'Phone', 'required' => 'required', 'value' => @$supplier->phone_number])
    @include('layouts.html.phone', ['name' => 'fax_number', 'caption' => 'Fax', 'value' =>  @$supplier->fax_number])
    @include('layouts.html.select', ['name' => 'country', 'caption' => 'Country', 'items' => Countries::getList(), 'mode' => 'aasoc', 'selected' => @$supplier->country])
    @include('layouts.html.input', ['name' => 'address', 'caption' => 'Address', 'value' =>  @$supplier->address])
    @include('layouts.html.input', ['name' => 'web_address', 'caption' => 'Website', 'value' => @$supplier->web_address])
    @include('layouts.html.input', ['name' => 'image', 'type' => 'file', 'caption' => 'Logo'])
    @include('layouts.html.checkbox', ['name' => 'active', 'caption' => 'Active', 'checked' => @$supplier->active])
</div>
<div class="box-footer">
    <button type="submit" class="btn btn-default">Submit</button>
</div>