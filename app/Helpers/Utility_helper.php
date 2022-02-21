<?php
use App\Models\Utility\UtilityModel;
function generateRecordNumber($id)
{
    if ($id) {
        return "Rec-" . sprintf('%03s', $id);
    } else {
        return false;
    }

}

function removeUrlCharacter($text)
{
    $text = trim($text);
    return urldecode($text);
}

function trimNumber($num)
{
    return preg_replace('/[^0-9]/', '', $num);
}

function trimMobileNumber($phone, $removeCountry = false)
{
    $m = trimNumber($phone);
    if ($removeCountryCode) {
        $m = removeCountryCode($m);
    }
    return ltrim($m, 0);
}

function generateRandomNumber($digit, $from = false)
{
    // code...
    $to   = str_pad(9, $digit, "9", STR_PAD_LEFT);
    $from = $from || str_pad(1, $digit, "0", STR_PAD_RIGHT);
    return mt_rand($from, $to); //random 4 disgit
}

function generateKey($name)
{
    $model = new UtilityModel();
    return $model->generateId($name);
}
