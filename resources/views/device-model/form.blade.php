<div class="box-body">
    @include('layouts.html.select', ['name' => 'supplier_id', 'caption' => 'Supplier', 'mode' => 'assoc', 'items' => $suppliers, 'selected' => isset($model) ? $model->supplier->id : 0])
    @include('layouts.html.input', ['name' => 'name', 'caption' => 'Name', 'value' => @$model->name])
    @include('layouts.html.textarea', ['name' => 'description', 'caption' => 'Description', 'value' =>  @$model->description])
    @include('layouts.html.input', ['name' => 'image', 'type' => 'file', 'caption' => 'Photo'])
    @include('layouts.html.input', ['name' => 'counter_1', 'caption' => 'Counter 1', 'value' => @$model->counter_1])
    @include('layouts.html.input', ['name' => 'counter_2', 'caption' => 'Counter 2', 'value' => @$model->counter_2])
    @include('layouts.html.input', ['name' => 'counter_3', 'caption' => 'Counter 3', 'value' => @$model->counter_3])
    @include('layouts.html.checkbox', ['name' => 'active', 'caption' => 'Active', 'checked' => @$model->active])
</div>
<div class="box-footer">
    <button type="submit" class="btn btn-default">Submit</button>
</div>