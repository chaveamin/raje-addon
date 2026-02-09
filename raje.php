<?php
/**
 * Raje Theme Control Panel
 *
 * @author Amin Chavepour
 */

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Database\Capsule;
use WHMCS\Module\Addon\Raje\Admin\AdminDispatcher;

function raje_config()
{
    return [
        'name' => 'کنترل پنل قالب راژه',
        'description' => 'طرح‌بندی، منوها، استایل‌ها، صفحات قالب راژه را به راحتی و بدون دانش کدنویسی شخص سازی کنید',
        'author' => '<a target="_blank" href="https://www.rtl-theme.com/author/chaveamin">Amin Chavepour<a/>',
        'language' => 'farsi',
        'version' => '1.0',
        'fields' => []
    ];
}

function raje_activate()
{
    try {
        if (!Capsule::schema()->hasTable('rj_config')) {
            Capsule::schema()->create('rj_config', function ($table) {
                $table->string('setting', 64)->primary();
                $table->text('value');
            });
        }
        
        // Default Settings
        $defaults = [
            'rtl_username'   => '',
            'rtl_order_id'   => '',
            'license_status' => '0',
            'last_check'     => '0',
            'site_logo'      => '',
            'dark_mode_logo' => '',
            'logo_width'     => '32'
        ];

        foreach ($defaults as $setting => $value) {
            $exists = Capsule::table('rj_config')->where('setting', $setting)->count();
            if (!$exists) {
                Capsule::table('rj_config')->insert(['setting' => $setting, 'value' => $value]);
            }
        }

        // Copy Default Assets
        $root_dir = str_replace('\\', '/', ROOTDIR);
        $module_dir = str_replace('\\', '/', __DIR__);
        $template_uploads_dir = $root_dir . '/templates/raje/img/uploads/';
        $defaults_dir = $module_dir . '/assets/defaults/';
        $tabs = ['photos', 'illustrations', 'icons'];

        foreach ($tabs as $tab) {
            $targetDir = $template_uploads_dir . $tab . '/';
            $sourceDir = $defaults_dir . $tab . '/';

            if (!file_exists($targetDir)) mkdir($targetDir, 0755, true);

            if (file_exists($sourceDir)) {
                $files = scandir($sourceDir);
                foreach ($files as $file) {
                    if ($file === '.' || $file === '..') continue;
                    if (!file_exists($targetDir . $file)) {
                        copy($sourceDir . $file, $targetDir . $file);
                    }
                }
            }
        }

    } catch (\Exception $e) {
        return ['status' => 'error', 'description' => 'DB Error: ' . $e->getMessage()];
    }
    
    return ['status' => 'success', 'description' => 'Module Activated'];
}

function raje_deactivate()
{
    return ['status' => 'success', 'description' => 'Module Deactivated'];
}

function raje_output($vars)
{
    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
    
    // Intercept Media AJAX requests immediately
    if (isset($_REQUEST['media_action'])) {
        $action = 'media';
    }

    $dispatcher = new AdminDispatcher();
    echo $dispatcher->dispatch($action, $vars);
}