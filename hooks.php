<?php
if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Database\Capsule;

/* * API Function - Kept for future use
function raje_check_license($username, $order_id)
{
    $api_key = 'rtl90ba8612b2a7dee456358b95f32fb6'; 
    $product_id = 'New Product'; 
    $domain = $_SERVER['SERVER_NAME']; 

    $postData = [
        'api' => $api_key,
        'username' => $username,
        'order_id' => $order_id,
        'domain' => $domain,
        'pid' => $product_id
    ];

    $url = 'https://www.rtl-theme.com/oauth/';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
}
*/

/*
add_hook('ClientAreaPage', 1, function($vars) {

    if ($vars['templatefile'] !== 'homepage') {
        return;
    }

    $settings = Capsule::table('rj_config')->pluck('value', 'setting')->all();
    
    $username   = $settings['rtl_username'] ?? '';
    $order_id   = $settings['rtl_order_id'] ?? '';
    $last_status = $settings['license_status'] ?? '0';
    $last_check  = (int)($settings['last_check'] ?? 0);
    $assets_url = '../raje/modules/addons/raje/assets';

    $currentTime = time();
    $cacheTimeout = 86400;

    if (($currentTime - $last_check) > $cacheTimeout || $last_status !== '1') {
        
        if (empty($username) || empty($order_id)) {
            $newResult = '-6';
        } else {

            $newResult = raje_check_license($username, $order_id);
        }

        // Update the Cache in DB
        Capsule::table('rj_config')->where('setting', 'license_status')->update(['value' => $newResult]);
        Capsule::table('rj_config')->where('setting', 'last_check')->update(['value' => $currentTime]);
        
        $currentStatus = $newResult;
    } else {
        
        $currentStatus = $last_status;
    }

    if ($currentStatus !== '1') {
        $errors = [
            '-1' => 'API اشتباه است.',
            '-2' => 'نام کاربری اشتباه است.',
            '-3' => 'کد سفارش اشتباه است.',
            '-4' => 'کد سفارش قبلاً ثبت شده است.',
            '-5' => 'کد سفارش مربوطه به این نام کاربری نمیباشد.',
            '-6' => 'اطلاعات وارد شده در فرمت صحیح نمیباشند.',
            '-7' => 'کد سفارش مربوط به این محصول نیست.',
            '-8' => 'کد سفارش مربوطه به این نام کاربری نمیباشد.',
            'default' => 'خطای غیرمنتظره رخ داده است (خطا: ' . $currentStatus . ')'
        ];
        
        $displayError = $errors[$currentStatus] ?? $errors['default'];
        
        header('Content-Type: text/html; charset=utf-8');
        die("
        <!DOCTYPE html>
        <html dir='rtl' lang='fa'>
        <head>
            <title>خطای لایسنس</title>
            <link rel='stylesheet' href='{$assets_url}/css/raje.cp.out.css'>
        </head>
        <body>
            <div class='flex items-center justify-center h-lvh'>
                <div class='font-yekan flex flex-col gap-y-4 items-center w-xl text-xl py-6 bg-white shadow-2xl shadow-zinc-800/10 rounded-2xl ring ring-zinc-200'>
                    <h2 class='font-medium'>خطای لایسنس</h2>
                    <p class='font-bold text-red-600'>{$displayError}</p>
                </div>
            </div>
        </body>
        </html>
        ");
    }
});
*/

add_hook('ClientAreaPage', 1, function($vars) {
    
    // ---------------------------------------------------------
    // TEMPLATE VARIABLES (Logos & Width)
    // ---------------------------------------------------------
    
    // Retrieve settings (Fetching again because previous fetch was in commented block)
    $settings = Capsule::table('rj_config')->pluck('value', 'setting')->all();

    // Helper to ensure full URL is returned to the template
    $getUrl = function($path) use ($vars) {
        if (empty($path)) return '';
        // If it already has http/https, return it
        if (filter_var($path, FILTER_VALIDATE_URL)) return $path;
        // Otherwise append system URL
        return $vars['systemurl'] . $path;
    };

    if (!empty($settings['site_logo'])) {
        $return['raje_site_logo'] = $getUrl($settings['site_logo']);
    }
    
    if (!empty($settings['dark_mode_logo'])) {
        $return['raje_dark_logo'] = $getUrl($settings['dark_mode_logo']);
    }

    if (!empty($settings['logo_width'])) {
        $return['raje_logo_width'] = $settings['logo_width'];
    }

    $return['raje_show_topbar'] = isset($settings['show_topbar']) ? $settings['show_topbar'] : '1';
    $return['raje_show_announcements'] = isset($settings['show_announcements']) ? $settings['show_announcements'] : '1';
    $return['raje_show_gravatar'] = isset($settings['show_gravatar']) ? $settings['show_gravatar'] : '1';
    $return['raje_login_layout'] = isset($settings['login_layout']) ? $settings['login_layout'] : 'col';
    $return['raje_register_layout'] = isset($settings['register_layout']) ? $settings['register_layout'] : 'col';

    return $return ?? [];
});