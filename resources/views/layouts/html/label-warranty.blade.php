<?php
    $class = 'success';
    if ($date < \Carbon\Carbon::now()) {
        $class = 'danger';
    } else if ($date < \Carbon\Carbon::now()->addMonth(1)) {
        $class = 'warning';
    }
?>
<span class="label label-{{ $class }}">{{ $date->format(DEFAULT_DATE_FORMAT) }}</span>