<?php

/**
 * @param $phone
 * @return string
 */
function intl_phone($phone)
{
    //remove all non-digit symbols, and prepend "+" sign
    return '+'.preg_replace('/[^0-9]/', '', $phone);
}

/**
 * @param $phone
 * @param null $format
 * @return string
 */
function format_phone($phone, $format = \libphonenumber\PhoneNumberFormat::INTERNATIONAL)
{
    try {
        return phone_format(intl_phone($phone), '', $format);
    }catch(Exception $e){
        //return unformatted phone on any error
        return $phone;
    }
}

function date_f($date)
{
    $carbon = \Carbon\Carbon::parse($date);
    $fDate = $carbon->format(DEFAULT_DATE_FORMAT);

    switch ($carbon->diffInDays()) {
        case 0:
            $fDate = 'Today';
            break;
        case 1:
            $fDate = 'Yesterday';
            break;
    }

    return $fDate . ', ' . $carbon->format('h:i A');
}