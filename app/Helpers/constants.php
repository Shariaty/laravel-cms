<?php
$server_name = 'oiltalen';
$base_url_ec = env('BASE_URL_EC', '//www.oiltalent.net');

define('PATH_ROOT',  getcwd());
//define('PATH_ROOT', '/home/'.$server_name.'/public_html');   for global

//Be careful maximum is 2
define('CONFIG_LIMIT_COMBINATION_PRODUCTS_MODULE', 2);

// Common notify messages  ---------------------------------------------------------------------------
define('LBL_COMMON_UPDATE_SUCCESSFUL', 'عملیات با موفقیت انجام شد');
define('LBL_COMMON_ERROR', 'متاسفانه امکان این درخواست وجود ندارد');
define('LBL_COMMON_DELETE_SUCCESSFUL', 'Item has been successfully removed.');
define('LBL_COMMON_DELETE_ERROR', 'There was a problem in removing this item, please try again later if the problem exist, contact the site administrator.');
define('LBL_CONTACT_MESSAGE_SEND', 'پیغام شما با موفقیت دریافت شد. با تشکر از تماس شما.');

define('LBL_RESTRICT_ADDING_SUB_PRODUCT', 'You cannot add sub products, Because of category settings');
// Common notify messages  ---------------------------------------------------------------------------

//Public User Status -------------------------------------------------
define('PUS_WAIT_FOR_CONFIRM', 0);
define('PUS_ACTIVE', 1);
define('PUS_DISABLED', 2);
//Public User Status -------------------------------------------------

//Product Types ------------------------------------------------------
define('PRODUCT_TYPE_NORMAL', 1);
define('PRODUCT_TYPE_COMPLEX', 2);
define('PRODUCT_TYPE_RAW_MATERIAL', 3);

define('PRODUCT_LIMIT_DAY', 1);
define('PRODUCT_LIMIT_WEEK', 2);
define('PRODUCT_LIMIT_MONTH', 3);
define('PRODUCT_LIMIT_YEAR', 4);

//Product Types ------------------------------------------------------

//Order Invoice Types ------------------------------------------------
define('OPERATOR_ORDER', 1);
define('SITE_ORDER', 2);
//Order Invoice Types ------------------------------------------------

//Order Invoice Status ------------------------------------------------
define('ORDER_WAIT_TO_CONFIRM', 1);
define('ORDER_WAIT_TO_SHIPMENT', 2);
define('ORDER_SHIPPED', 3);
define('ORDER_CANCELED', 5);
//Order Invoice Status ------------------------------------------------

//Comments Status Types ----------------------------------------------
define('COMMENT_TYPE_WAIT_TO_CONFIRM', 0);
define('PRODUCT_TYPE_CONFIRMED', 1);
//Comments Status Types ----------------------------------------------
