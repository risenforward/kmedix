@extends('layouts.html.master')

@section('element')
    <input	id="field_{{ $name }}_id"
			type="phone"
			class="form-control {{ $required or ''}}"
			name="{{ $name }}"
		  	@if(isset($disabled)) disabled @endif
			value="@if(count($errors) > 0){{ old($name) }}@else{{ $value or '' }}@endif" />
	<span id="{{ $name }}-valid-msg" class="hide">✓ Ok</span>
	<span id="{{ $name }}-error-msg" class="hide">Invalid format</span>
    <script>
        $(document).ready(function(){
			validation = {};

        	var phoneInput = $("#field_{{ $name }}_id");
            phoneInput.intlTelInput({
                allowExtensions: false,
                autoFormat: true,
                autoHideDialCode: true,
                autoPlaceholder: true,
                nationalMode: false,
                numberType: "MOBILE",
                onlyCountries: <?php echo isset($countries) ? "['" . implode("','", $countries) . "']" : 'undefined'?>,
                utilsScript: "/plugins/intl-tel-input/utils.js"
            }).done(function(){
				phoneInput.change(function() {
					phoneInput.keyup();
				});
            });

            phoneInput.keyup(function(){
				$(this).closest('div.form-group').removeClass('has-error');
				$("#{{ $name }}-error-msg").addClass("hide");
				$("#{{ $name }}-valid-msg").addClass("hide");
            });

			phoneInput.blur(function() {
				phoneInputValidate(0);
			});

			var phoneInputValidate = function(validateIfEmpty){
				if(!validateIfEmpty && !$.trim(phoneInput.val()))
					return false;

				phoneInput.closest('div.form-group').find('div.text-red').hide(); //hide error from php validation
				if ($.trim(phoneInput.val()) && phoneInput.intlTelInput("isValidNumber")) {
					phoneInput.closest('div.form-group').removeClass('has-error');
					$("#{{ $name }}-valid-msg").attr("class", "text-green");
					$("#{{ $name }}-error-msg").addClass("hide");
				} else {
					phoneInput.closest('div.form-group').addClass('has-error');
					$("#{{ $name }}-error-msg").attr("class", "text-red");
					$("#{{ $name }}-valid-msg").addClass("hide");
				}
			}

			if ($('#field_{{ $name }}_id').hasClass('required')) {
				phoneInput.closest('form').submit(function(){
					if (!phoneInput.intlTelInput("isValidNumber")) {
						validation.phone = false;
						//show error if not displayed yet and focus on field
						phoneInputValidate(1);
						$('html, body').animate({
							scrollTop: phoneInput.parent().offset().top - 60
						}, 200);
						return false;
					}else{
						validation.phone = true;
					}
				});
			}
        });
    </script>
@overwrite