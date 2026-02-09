<?php
if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Database\Capsule;

/* * API Function - Kept for future use
 */
function raje_check_license($username, $order_id)
{
    // Logic temporarily disabled
    return '1';
}

add_hook('ClientAreaPage', 1, function($vars) {

    // 1. Fetch Settings
    try {
        $settings = Capsule::table('rj_config')->pluck('value', 'setting')->all();
    } catch (\Exception $e) {
        return;
    }

    // ---------------------------------------------------------
    // LICENSE CHECK (DISABLED)
    // ---------------------------------------------------------
    // We force the status to '1' so the site always loads.
    // To re-enable later, you can uncomment the API logic here.
    $currentStatus = '1'; 

    // If you want to simulate a check in the future, put the logic here.
    // For now, we simply skip the 'die()' command.
    
    if ($currentStatus !== '1') {
        // Error handling code is disabled
        return;
    }

    // ---------------------------------------------------------
    // TEMPLATE VARIABLES (Logos & Width)
    // ---------------------------------------------------------
    // Helper to ensure full URL is returned to the template
    $getUrl = function($path) use ($vars) {
        if (empty($path)) return '';
        // If it already has http/https, return it
        if (filter_var($path, FILTER_VALIDATE_URL)) return $path;
        // Otherwise append system URL
        return $vars['systemurl'] . $path;
    };

    // Site Logo
    if (!empty($settings['site_logo'])) {
        $return['raje_site_logo'] = $getUrl($settings['site_logo']);
    }
    
    // Dark Mode Logo
    if (!empty($settings['dark_mode_logo'])) {
        $return['raje_dark_logo'] = $getUrl($settings['dark_mode_logo']);
    }

    // Logo Width
    if (!empty($settings['logo_width'])) {
        $return['raje_logo_width'] = $settings['logo_width'];
    }

    // Topbar Setting
    $return['raje_show_topbar'] = isset($settings['show_topbar']) ? $settings['show_topbar'] : '1';

    // Announcements Setting
    $return['raje_show_announcements'] = isset($settings['show_announcements']) ? $settings['show_announcements'] : '1';

    return $return;
});

