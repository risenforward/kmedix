<div class="overlay" id="loading" style="display: none;">
    <i class="fa fa-refresh fa-spin"></i>
</div>
<script>
    $(function () {
        $('<?php echo empty($formSelector) ? 'form' : $formSelector ?>').submit(function () {
            var hasError = false;
            for (var key in validation) {
                if (validation.hasOwnProperty(key)) {
                    if (!validation[key]) {
                        hasError = true;
                        break;
                    }
                }
            }

            if (!hasError) {
                $('#loading').show();
            }

            return !hasError;
        });
    });
</script>