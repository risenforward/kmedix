<div class="box-body">
    @include('layouts.html.input', ['name' => 'serial_number', 'caption' => 'Serial number', 'value' => @$device->serial_number])
    @include('layouts.html.select', ['name' => 'supplier', 'caption' => 'Supplier', 'items' => $suppliers, 'mode' => 'assoc', 'selected' => @$device->deviceModel->supplier_id])
    @include('layouts.html.select', ['name' => 'device_model_id', 'caption' => 'Model', 'items' => [], 'disabled' => true, 'selected' => @$device->device_model_id])
    @include('layouts.html.select', ['name' => 'customer_id', 'caption' => 'Customer', 'items' => $customers, 'mode' => 'assoc', 'selected' => @$device->customer_id])
    @include('layouts.html.datepicker', ['name' => 'install_date', 'caption' => 'Install date', 'value' => isset($device) ? $device->getFInstallDate(DEFAULT_DB_FORMAT) : ''])
    @include('layouts.html.input', ['name' => 'warranty', 'caption' => 'General warranty', 'value' => @$device->warranty])
    @include('layouts.html.input', ['name' => 'consumable_warranty', 'caption' => 'Consumable warranty', 'value' => @$device->consumable_warranty])
    @include('layouts.html.select', ['name' => 'installed_by', 'caption' => 'Installed by', 'items' => $users, 'mode' => 'assoc', 'selected' => @$device->installed_by])
    @include('layouts.html.checkbox', ['name' => 'extended_warranty_active', 'caption' => 'Extended warranty', 'checked' => isset($device) && $device->extended_warranty ? true : false])
    @include('layouts.html.select', ['name' => 'contract_level', 'caption' => 'Service contract level', 'items' => [1, 2, 3, 4], 'selected' => isset($device) ? $device->contract_level : 1, 'disabled' => true])
    @include('layouts.html.datepicker', ['name' => 'extended_warranty_start', 'caption' => 'Start date', 'disabled' => true, 'value' => isset($device) && $device->extended_warranty_start ? $device->getFExtWarrantyStartDate(DEFAULT_DB_FORMAT) : ''])
    @include('layouts.html.input', ['name' => 'extended_warranty', 'caption' => 'Duration (months)', 'disabled' => true, 'value' => @$device->extended_warranty])
    @include('layouts.html.checkbox', ['name' => 'preventive_maintenance', 'caption' => 'Preventive maintenance', 'checked' => isset($device) && $device->preventive_maintenance ? true : false])
</div>
<div class="box-footer">
    <button type="submit" class="btn btn-default">Submit</button>
</div>
<script>
    var data = {
        model_id: "{{ isset($device) ? $device->device_model_id : '' }}",
        old: {
            model_id: "{{ old('device_model_id') or '' }}",
            ext_warranty: "{{ old('extended_warranty_active') or '' }}"
        }
    };
    var supplier = function ($this) {
        var supplier_id = $this.val();
        if (supplier_id != 0) {
            $('#loading').show();
            $.get('/supplier/' + supplier_id + '/deviceModels', function (r) {
                if (r.models.length) {
                    var $s = $('select[name=device_model_id]');
                    $s.removeAttr('disabled').empty().append('<option value="0">-- choose --</option>');
                    $.each(r.models, function(key, model) {
                        $s.append('<option value="' + model.id + '">' + model.name + '</option>');
                    });
                    if (data.model_id != '') {
                        $s.val(data.model_id)
                    }
                    if (data.old.model_id != '') {
                        $s.val(data.old.model_id)
                    }
                }
                $('#loading').hide();
            });
        }
    };

    $(function () {
        var $exts = $('select[name=contract_level], input[name=extended_warranty_start], input[name=extended_warranty]');

        $('select[name=supplier]').on('change', function () {
            supplier($(this));
        });

        $('input[name=extended_warranty_active]').on('ifChanged', function (e) {
            if (e.target.checked) {
                $exts.removeAttr('disabled');
            } else {
                $exts.attr('disabled', 'disabled');
            }
        });

        $(document).ready(function () {
            if ($('select[name=supplier]').val() != 0) {
                supplier($('select[name=supplier]'));
            }

            if (data.old.ext_warranty != '') {
                $('input[name=extended_warranty_active]').iCheck('check');
                $exts.removeAttr('disabled');
            } else if ($('input[name=extended_warranty_active]').is(':checked')) {
                $exts.removeAttr('disabled');
            }
        });
    });
</script>
