<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Modules\Products\AttributeValue;
use Nwidart\Modules\Facades\Module;
use Opilo\Farsi\JalaliDate;

function hasModule($moduleName){
    return in_array( $moduleName , Module::getByStatus(1));
}

function getCurrentAdminUser(){
    $user = auth()->user();
    return $user ? $user : false;
}

function generateUserFullName($user){
    if($user->firstname && $user->lastname){
        return $user->firstname . ' '. $user->lastname;
    }
    return 'null';
}

function tarikhFarsi($date){
    $date = Carbon::parse($date);
    return  JalaliDate::fromDateTime( $date )->format('D ، d M y');
}

function tarikhFarsiWithTime($date){
    $date = Carbon::parse($date);
    return  JalaliDate::fromDateTime( $date )->format('Y/m/d');
}

function random_color_part()
{
    return str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
}

function generateColor()
{
    return '#' . random_color_part() . random_color_part() . random_color_part();
}

function slug_utf8($title, $separator = '-')
{
    // Convert all dashes/underscores into separator
    $flip = $separator == '-' ? '_' : '-';

    $title = preg_replace('![' . preg_quote($flip) . ']+!u', $separator, $title);

    // Remove all characters that are not the separator, letters, numbers, or whitespace.
    $title = preg_replace('![^' . preg_quote($separator) . '\pL\pN\s]+!u', '', mb_strtolower($title));

    // Replace all separator characters and whitespace by a single separator
    $title = preg_replace('![' . preg_quote($separator) . '\s]+!u', $separator, $title);

    return trim($title, $separator);
}

function stringArrayConvertToIntArray ($array) {

    if(isset($array) && count($array)){

        if(is_array($array)) {
            foreach ($array as $key => $value ){
                $array[$key] = intval($value);
            }
        } else {
            $array = [intval($array)];
        }
        return $array;
    }

}

function randomStyle () {
    $data = array(
        0 => 'success',
        1 => 'danger' ,
        2 => 'warning' ,
        3 => 'info' ,
        4 => 'default'
    );
    $index = array_rand($data , 1);
    return $data[$index];
}

function getAdminRolsList(){
    $roles = \App\Admin\Role::where('name', '!=' , config('permissions.ROLE_SUPER_ADMIN'))->pluck('label' , 'id');
    return $roles;
}

function generateCafeStatus(){
    return [
        1 => 'فعال',
        0 => 'غیر فعال'
    ];
}

function getStoreTypes()
{
    return [
        'Hospital'  => 'بیمارستان دامپزشکی',
        'Clinique'  => 'کلینیک دامپزشکی',
        'Pet shop'  => 'پت شاپ'
    ];
}

//Company Helpers
function checkTitleOrUserNameCompany($company){
    $title = null;
    if(!empty($company->title)){
        $title = $company->title;
    } elseif(!empty($company->username) && $company->username != 'invalid') {
        $title = $company->username;
    } else {
        $title = 'None';
    }
    return $title;
}

function addEmptyToFirstIndexArray($array = array())
{
    return array_merge(['' => ''] , $array);
}

function sizeCompanies($index = null)
{
    $sizes = [
        '1-10'          => '1 to 10',
        '10-20'         => '10 to 20',
        '20-50'         => '20 to 50',
        '50-100'        => '50 to 100',
        '100-500'       => '100 to 500',
        'above-500'     => 'Above 500'
    ];

    if(isset($index) AND !empty($index))
        return (isset($sizes[$index])) ? $sizes[$index] : null;

    return $sizes;
}
//Company Helpers

function getBase64extension($image){
    $pathTmp = public_path('uploads/tmp') . '/' . rand(11111 , 99999) . '_' . time();
    @file_put_contents($pathTmp, @file_get_contents($image));
    $path_info = @getimagesize($pathTmp);
    $extension = @image_type_to_extension($path_info[2]);
    @unlink($pathTmp);
    return $extension;
}

//Sale Module
function generatePaymentStatusBadge($status){
    $result = '';

    switch ($status) {
        case 'Y': $result = '<span class="badge badge-success">پرداخت شده</span>'; break;
        case 'N': $result = '<span class="badge badge-danger">پرداخت نشده</span>'; break;
        default : $result = '<span class="badge badge-default">وضعیت پرداخت نامعلوم</span>'; break;
    }

    return $result;
}

function generateOrderStatusBadge($status){
    $result = '';
    switch ($status) {
        case ORDER_WAIT_TO_CONFIRM: return'<span class="badge badge-info">منتظر تایید ناظر</span>'; break;
        case ORDER_WAIT_TO_SHIPMENT: return'<span class="badge badge-warning">منتظر ارسال</span>'; break;
        case ORDER_SHIPPED: return'<span class="badge badge-success">ارسال شده</span>'; break;
        case ORDER_CANCELED: return'<span class="badge badge-danger">سفارش لغو شده</span>'; break;
        default : return'<span class="badge badge-default">وضعیت نامعلوم</span>'; break;
    }
    return $result;
}

function generateOrderTypeBadge($type){
    $result = '';
    switch ($type) {
        case OPERATOR_ORDER: return'<span>سفارش صادر شده توسط شرکت</span>'; break;
        case SITE_ORDER: return'<span>سفارش صادر شده از طریق وب سایت</span>'; break;
        default : return'<span class="badge badge-default">نوع نامعلوم</span>'; break;
    }
    return $result;
}

function orderTimeFormat($dateTime){
    return JalaliDate::fromDateTime( $dateTime )->format('D ، d M y') ;
}

function generateUnitView( $unit ){
    $result = '';
    $unists =  \Modules\Products\Unit::pluck('title' , 'id');
    $result = $unists[$unit];

    return $result;
}

function generateGoodItemTitle ($product) {
    $array = [];
    if($product->parentProduct) {
        $array[] = $product->parentProduct->title;
    } else {
        $array[] = $product->title;
    }

    $values = AttributeValue::pluck('title' , 'id');

    if($product->option_1){
        $array[] = $values[$product->option_1];
    }
    if ($product->option_2){
        $array[] = $values[$product->option_2];
    }

    $result = implode(' - ', $array);
    return $result;
}

function generateOrderBtn($status , $orderId) {
    $result = '';
    switch ($status) {
        case ORDER_WAIT_TO_CONFIRM: return'<a href="'.route('admin.order.updateStatus' , $orderId).'" class="btn btn-block green-jungle status-change farsi-text">تغییر وضعیت سفارش به آماده ارسال</a>'; break;
        case ORDER_WAIT_TO_SHIPMENT: return'<a href="'.route('admin.order.updateStatus' , $orderId).'" class="btn btn-block yellow-lemon status-change farsi-text">تغییر وضعیت سفارش به ارسال شده</a>'; break;
        default : return '' ; break;
    }
    return $result;
}

function getLimitTimingName($limit_timing){
    $result = '';
    switch ($limit_timing) {
        case PRODUCT_LIMIT_DAY: $result =  'روز'; break;
        case PRODUCT_LIMIT_WEEK: $result = 'هفته'; break;
        case PRODUCT_LIMIT_MONTH: $result = 'ماه'; break;
        case PRODUCT_LIMIT_YEAR: $result ='سال'; break;
        default : $result = 'نا معلوم'; break;
    }
    return $result;
}

function generateLimitTimingList(){
    return [
        PRODUCT_LIMIT_DAY => 'Day',
        PRODUCT_LIMIT_WEEK => 'Week',
        PRODUCT_LIMIT_MONTH => 'Month',
        PRODUCT_LIMIT_YEAR => 'Year',
    ];
}
//Sale Module

//WareHouse Module
function generateTextColor($int) {
    $result = '';
    switch (true) {
        case ( $int <= 0 ) : $result = 'text-danger'; break;
        case ( $int <= 10 &&  $int > 0) : $result =  'text-warning'; break;
        case ( $int > 10) : $result = 'text-success'; break;
        default : $result = 'text-info'; break;
    }
    return $result;
}
//WareHouse Module