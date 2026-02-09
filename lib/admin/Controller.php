<?php

namespace WHMCS\Module\Addon\Raje\Admin;

use WHMCS\Database\Capsule;

/**
 * Admin Area Controller
 */
class Controller {

    /**
     * Main Index Action (Renders the Dashboard Grid)
     */
    public function index($vars)
    {
        $modulelink = $vars['modulelink'];
        $LANG = $vars['_lang'];
        $assets_url = '../modules/addons/raje/assets';

        // Dashboard Grid View
        return <<<HTML
        <link rel="stylesheet" href="{$assets_url}/css/raje.cp.out.css">
        <div class="raje-admin-container overflow-hidden font-yekan shadow-2xl ring-1 shadow-zinc-800/10 ring-zinc-900/5 bg-white/90 p-16 h-lvh rounded-3xl relative" dir="rtl">
            <div class="px-12 py-8 w-full flex items-start justify-between shadow-xl shadow-zinc-800/5 absolute top-0 right-0">
                <div>
                    <a target="_blank" href="https://www.rtl-theme.com/author/chaveamin">
                        <img class="w-8" src="{$assets_url}/img/logo.png">
                    </a>
                </div>
                <div class="flex items-center gap-x-8">
                    <a class="group relative" target="_blank" href="https://www.rtl-theme.com/author/chaveamin" title="قالب های بیشتر">
                        <img class="w-7" src="{$assets_url}/img/more.svg">
                    </a>
                    <a class="group relative" target="_blank" href="https://docs.designesia.ir/" title="مستندات">
                        <img class="w-7" src="{$assets_url}/img/docs.svg">
                    </a>
                    <a class="group relative" target="_blank" href="https://www.rtl-theme.com/dashboard/#/ticket-send" title="پشتیبانی">
                        <img class="w-7" src="{$assets_url}/img/support.svg">
                    </a>
                </div>
            </div>
            <div class="my-32">
                <h1 class="text-3xl font-extrabold text-zinc-800 mb-4">{$LANG['title']}</h1>
                <p class="text-zinc-400 text-base">{$LANG['description']}</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-lwv mx-auto">
                <a href="{$modulelink}&action=license" class="block no-underline bg-white border border-zinc-200 rounded-3xl p-6 hover:border-zinc-600 transition duration-200 text-center">
                    <div class="size-16 bg-zinc-100 text-zinc-600 p-4 rounded-3xl flex items-center justify-center mx-auto mb-4">
                        <img src="{$assets_url}/img/license.svg">
                    </div>
                    <h2 class="font-bold text-zinc-800 mb-2">{$LANG['homepagelicense']}</h2>
                </a>
                <a href="{$modulelink}&action=general" class="block no-underline bg-white border border-zinc-200 rounded-3xl p-6 hover:border-zinc-600 transition duration-200 text-center">
                    <div class="size-16 bg-zinc-100 text-zinc-600 p-4 rounded-3xl flex items-center justify-center mx-auto mb-4">
                        <img src="{$assets_url}/img/general.svg">
                    </div>
                    <h2 class="font-bold text-zinc-800 mb-2">{$LANG['homepagegeneral']}</h2>
                </a>
                <a href="{$modulelink}&action=style" class="block no-underline bg-white border border-zinc-200 rounded-2xl p-6 hover:border-zinc-600 transition duration-200 text-center">
                    <div class="size-16 bg-zinc-100 text-zinc-600 p-4 rounded-3xl flex items-center justify-center mx-auto mb-4">
                        <img src="{$assets_url}/img/style-color.svg">
                    </div>
                    <h2 class="font-bold text-zinc-800 mb-2">{$LANG['homepagestyle']}</h2>
                </a>
            </div>
        </div>
HTML;
    }

    /**
     * License Page Action
     */
    public function license($vars)
    {
        $saveMessage = '';
        // Handle POST save
        if (isset($_POST['action']) && $_POST['action'] == 'save_license') {
            $saveMessage = $this->saveLicense();
        }

        // Fetch Data
        $settings = Capsule::table('rj_config')->whereIn('setting', ['rtl_username', 'rtl_order_id', 'license_status'])->pluck('value', 'setting')->all();
        
        $username = htmlspecialchars($settings['rtl_username'] ?? '');
        $order_id = htmlspecialchars($settings['rtl_order_id'] ?? '');
        $status_val = $settings['license_status'] ?? '0';
        $assets_url = '../modules/addons/raje/assets';

        // Alert Logic
        if ($status_val == '1') {
            $status_alert = '<div class="p-4 rounded-lg bg-green-50 border border-green-200 flex items-start gap-4 transition-all"><div class="w-10 h-10 rounded-full bg-green-100 text-green-600 flex items-center justify-center shrink-0"><i class="fas fa-check-circle text-xl"></i></div><div><div class="font-bold text-green-800 text-lg">لایسنس فعال است (Active)</div><div class="text-sm text-green-600 mt-1">قالب شما با موفقیت فعال شده و آماده استفاده است.</div></div></div>';
        } elseif ($status_val == '0') {
            $status_alert = '<div class="p-4 rounded-lg bg-yellow-50 border border-yellow-200 flex items-start gap-4 transition-all"><div class="w-10 h-10 rounded-full bg-yellow-100 text-yellow-600 flex items-center justify-center shrink-0"><i class="fas fa-exclamation-circle text-xl"></i></div><div><div class="font-bold text-yellow-800 text-lg">لایسنس بررسی نشده (Not Checked)</div><div class="text-sm text-yellow-600 mt-1">لطفا نام کاربری و شماره سفارش را وارد کرده و دکمه "بررسی و فعال‌سازی" را بزنید.</div></div></div>';
        } else {
            $status_alert = '<div class="p-4 rounded-lg bg-red-50 border border-red-200 flex items-start gap-4 transition-all"><div class="w-10 h-10 rounded-full bg-red-100 text-red-600 flex items-center justify-center shrink-0"><i class="fas fa-times-circle text-xl"></i></div><div><div class="font-bold text-red-800 text-lg">خطای فعال‌سازی</div><div class="text-sm text-red-600 mt-1">کد خطا: '.$status_val.'</div></div></div>';
        }

        // Render Content
        $content = <<<HTML
            <link rel="stylesheet" href="{$assets_url}/css/raje.cp.out.css">
            <form method="post" action="">
                <input type="hidden" name="action" value="save_license">
                {$saveMessage}
                    <div class="flex flex-col items-start gap-y-8 w-full *:w-full">
                        <div>
                            <label class="block text-sm font-bold text-zinc-500 mb-4" for="rtl_username">نام کاربری راست‌چین (Username)</label>
                            <input id="rtl_username" type="text" name="rtl_username" class="bg-zinc-100 outline-0 focus:ring-2 focus:ring-zinc-300 p-5 rounded-xl text-sm text-zinc-800 transition-all w-full" value="{$username}" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-zinc-500 mb-4" for="rtl_order_id">شماره سفارش (Order ID)</label>
                            <input id="rtl_order_id" type="text" name="rtl_order_id" class="bg-zinc-100 outline-0 focus:ring-2 focus:ring-zinc-300 p-5 rounded-xl text-sm text-zinc-800 transition-all w-full" value="{$order_id}" required>
                        </div>
                        {$status_alert}
                    </div>
                <button type="submit" class="mt-12 bg-zinc-800 hover:bg-zinc-700 text-white font-bold py-6 px-8 rounded-2xl flex items-center gap-2">
                    فعال‌سازی
                </button>
            </form>
HTML;

        return $this->renderPage('فعال‌سازی لایسنس', $content, 'license', $vars);
    }

    /**
     * General Settings Page Action
     */
    public function general($vars)
    {
        $saveMessage = '';
        // Handle POST save
        if (isset($_POST['action']) && $_POST['action'] == 'save_general') {
            $saveMessage = $this->saveGeneral();
        }

        // Fetch Data
        $settings = Capsule::table('rj_config')->whereIn('setting', ['site_logo', 'dark_mode_logo', 'logo_width', 'show_topbar', 'show_announcements'])->pluck('value', 'setting')->all();
        
        $logo = htmlspecialchars($settings['site_logo'] ?? '');
        $dark = htmlspecialchars($settings['dark_mode_logo'] ?? '');
        $width = htmlspecialchars($settings['logo_width'] ?? '32');
        $topbar = ($settings['show_topbar'] ?? '1') == '1' ? 'checked' : '';
        $announce = ($settings['show_announcements'] ?? '1') == '1' ? 'checked' : '';
        
        $logoUrl = $this->getImgUrl($logo);
        $darkUrl = $this->getImgUrl($dark);
        $logoHide = empty($logoUrl) ? 'hidden' : '';
        $darkHide = empty($darkUrl) ? 'hidden' : '';
        $assets_url = '../modules/addons/raje/assets';

        // Render Content
        $content = <<<HTML
            <link rel="stylesheet" href="{$assets_url}/css/raje.cp.out.css">
            <form method="post" action="">
                <input type="hidden" name="action" value="save_general">
                {$saveMessage}
                
                <div class="bg-white rounded-xl shadow-sm border border-zinc-200 mb-6 overflow-hidden">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-zinc-100 bg-zinc-50/50">
                        <h3 class="font-bold text-zinc-700">لوگو و هویت سایت</h3>
                    </div>
                    <div class="p-6">
                        <div class="mb-6 border-b border-dashed border-zinc-200 pb-6">
                            <label class="block text-sm font-bold text-zinc-700 mb-2">لوگوی سایت</label>
                            <div class="media-input-group flex flex-col gap-3">
                                <div class="bg-zinc-50 border border-zinc-200 rounded-lg p-4 w-full h-32 flex items-center justify-center relative overflow-hidden">
                                    <img src="{$logoUrl}" class="preview-img h-full object-contain {$logoHide}">
                                </div>
                                <div class="flex gap-2">
                                    <input type="text" name="site_logo" value="{$logo}" class="flex-1 text-xs text-zinc-500 border border-zinc-200 rounded px-3 bg-zinc-50" readonly>
                                    <button class="media-select-btn bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold">انتخاب رسانه</button>
                                </div>
                            </div>
                        </div>

                        <div class="mb-6 border-b border-dashed border-zinc-200 pb-6">
                            <label class="block text-sm font-bold text-zinc-700 mb-2">لوگوی دارک مود</label>
                            <div class="media-input-group flex flex-col gap-3">
                                <div class="bg-zinc-800 border border-zinc-700 rounded-lg p-4 w-full h-32 flex items-center justify-center relative overflow-hidden">
                                    <img src="{$darkUrl}" class="preview-img h-full object-contain {$darkHide}">
                                </div>
                                <div class="flex gap-2">
                                    <input type="text" name="dark_mode_logo" value="{$dark}" class="flex-1 text-xs text-zinc-500 border border-zinc-200 rounded px-3 bg-zinc-50" readonly>
                                    <button class="media-select-btn bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold">انتخاب رسانه</button>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-zinc-700 mb-2">اندازه لوگو (پیکسل)</label>
                            <input type="number" name="logo_width" class="w-32 text-center border border-zinc-300 rounded-lg px-4 py-2" value="{$width}">
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-zinc-200 mb-6 overflow-hidden">
                     <div class="flex items-center justify-between px-6 py-4 border-b border-zinc-100 bg-zinc-50/50">
                        <h3 class="font-bold text-zinc-700">تنظیمات نمایش</h3>
                    </div>
                    <div class="p-6">
                        <div class="flex items-start gap-4">
                            <input id="show_topbar" name="show_topbar" type="checkbox" value="1" class="w-5 h-5" {$topbar}>
                            <label for="show_topbar" class="font-bold text-zinc-700">نمایش نوار اعلان (Top Bar)</label>
                        </div>
                        <div class="flex items-start gap-4 mt-4 pt-4 border-t border-dashed border-zinc-200">
                            <input id="show_announcements" name="show_announcements" type="checkbox" value="1" class="w-5 h-5" {$announce}>
                            <label for="show_announcements" class="font-bold text-zinc-700">نمایش اسلایدر اخبار</label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg shadow-lg flex items-center gap-2">
                    <i class="fas fa-save"></i> ذخیره تغییرات
                </button>
            </form>
HTML;

        return $this->renderPage('تنظیمات عمومی', $content, 'cogs', $vars);
    }


    /**
     * Style Settings Page Action (Placeholder)
     */
    public function style($vars)
    
    {
        $assets_url = '../modules/addons/raje/assets';

        $content = <<<HTML
        <link rel="stylesheet" href="{$assets_url}/css/raje.cp.out.css">
        <div class="bg-white rounded-xl shadow-sm border border-zinc-200 p-12 text-center">
            <i class="fas fa-paint-brush text-6xl text-zinc-200 mb-6"></i>
            <h3 class="text-xl font-bold text-zinc-700 mb-2">به زودی</h3>
            <p class="text-zinc-500">تنظیمات استایل و رنگ‌بندی در آپدیت‌های آینده اضافه خواهد شد.</p>
        </div>
HTML;
        return $this->renderPage('استایل و رنگ‌بندی', $content, 'paint-brush', $vars);
    }


    /**
     * Handle Media Manager AJAX Requests (Unchanged)
     */
    public function media($vars)
    {
        // Clean output buffer to ensure JSON validity
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');
        
        $root_dir = str_replace('\\', '/', ROOTDIR);
        // Adjusted path based on new structure location
        $module_dir = dirname(dirname(__DIR__)); 
        $template_uploads_dir = $root_dir . '/templates/raje/img/uploads/';
        $defaults_dir = $module_dir . '/assets/defaults/';
        $tabs = ['photos', 'illustrations', 'icons', 'uploaded'];

        // Ensure directories exist
        foreach ($tabs as $tab) {
            $targetDir = $template_uploads_dir . $tab . '/';
            if (!file_exists($targetDir)) mkdir($targetDir, 0755, true);
        }

        $action = $_REQUEST['media_action'];

        // --- SUB-ACTION: DELETE ---
        if ($action == 'delete_file') {
            $filename = $_POST['file'] ?? '';
            $tab = $_POST['tab'] ?? '';

            if (!in_array($tab, $tabs) || empty($filename) || strpos($filename, '/') !== false) {
                echo json_encode(['status'=>'error', 'msg'=>'Invalid request']); exit;
            }

            $targetPath = $template_uploads_dir . $tab . '/' . $filename;
            if (file_exists($targetPath) && unlink($targetPath)) {
                echo json_encode(['status'=>'success', 'msg'=>'File deleted']);
            } else {
                echo json_encode(['status'=>'error', 'msg'=>'Delete failed']);
            }
            exit;
        }

        // --- SUB-ACTION: SYNC DEFAULTS ---
        if ($action == 'sync_defaults') {
            $total_synced = 0;
            $sync_tabs = ['photos', 'illustrations', 'icons'];
            
            foreach ($sync_tabs as $tab) {
                $src = $defaults_dir . $tab . '/';
                $dst = $template_uploads_dir . $tab . '/';
                
                if (!file_exists($src)) continue;

                foreach (scandir($src) as $f) {
                    if ($f === '.' || $f === '..') continue;
                    $destPath = $dst . $f;
                    if (!file_exists($destPath)) {
                        copy($src . $f, $destPath);
                        $total_synced++;
                    }
                }
            }
            echo json_encode(['status' => 'success', 'msg' => "Synced $total_synced new files."]);
            exit;
        }

        // --- SUB-ACTION: LIST ---
        if ($action == 'list_media') {
            $tab = $_REQUEST['tab'] ?? 'uploaded';
            if (!in_array($tab, $tabs)) $tab = 'uploaded';
            
            $targetDir = $template_uploads_dir . $tab . '/';
            $baseUrl   = '../templates/raje/img/uploads/' . $tab . '/';
            $result = [];
            
            if (is_dir($targetDir)) {
                $files = scandir($targetDir);
                foreach ($files as $file) {
                    if ($file === '.' || $file === '..') continue;
                    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'svg', 'webp'])) {
                        $result[] = [
                            'name' => $file,
                            'url'  => $baseUrl . $file . '?t=' . filemtime($targetDir . $file),
                            'path' => 'templates/raje/img/uploads/' . $tab . '/' . $file,
                            'time' => filemtime($targetDir . $file)
                        ];
                    }
                }
                usort($result, function($a, $b) { return $b['time'] - $a['time']; });
            }
            echo json_encode(['status' => 'success', 'files' => $result]);
            exit;
        }

        // --- SUB-ACTION: UPLOAD ---
        if ($action == 'upload_file') {
            if (!isset($_FILES['file'])) { echo json_encode(['status'=>'error','msg'=>'No file']); exit; }
            
            $file = $_FILES['file'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'svg', 'webp'])) {
                echo json_encode(['status'=>'error','msg'=>'Invalid format']); exit;
            }

            $newName = preg_replace('/[^a-zA-Z0-9_-]/', '', pathinfo($file['name'], PATHINFO_FILENAME)) . '_' . time() . '.' . $ext;
            $target = $template_uploads_dir . 'uploaded/' . $newName;

            if (move_uploaded_file($file['tmp_name'], $target)) {
                echo json_encode(['status'=>'success', 'msg'=>'Uploaded']);
            } else {
                echo json_encode(['status'=>'error', 'msg'=>'Upload failed']);
            }
            exit;
        }

        exit;
    }

    //===========================================================================
    // PRIVATE HELPER METHODS
    //===========================================================================

    /**
     * Renders inner pages with a standardized wrapper, header, and assets.
     */
    private function renderPage($title, $content, $icon, $vars) {
        $modulelink = $vars['modulelink'];
        $assets_url = '../modules/addons/raje/assets';
        $timestamp = time(); // For cache busting

        return <<<HTML
        <link rel="stylesheet" href="../templates/raje/css/theme.css">
        <style>
            #media-grid::-webkit-scrollbar { width: 8px; }
            #media-grid::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        </style>

        <div class="raje-admin-container font-ravi rounded-3xl bg-white/90 p-16 h-lvh shadow-lg ring-1 shadow-zinc-800/5 ring-zinc-900/5" dir="rtl">
            
            <div class="flex items-center justify-between mb-16">
                <div class="flex items-center gap-3">
                     <div class="size-12">
                        <img src="{$assets_url}/img/{$icon}.svg">
                    </div>
                    <h1 class="text-2xl font-bold text-zinc-800 m-0">{$title}</h1>
                </div>
                <a href="{$modulelink}" class="flex items-center gap-2 text-zinc-500 transition-colors bg-white border border-zinc-200 px-4 py-2 rounded-xl font-bold text-sm">
                 بازگشت به داشبورد
                </a>
            </div>

            <div class="animate-fade-in-up">
                {$content}
            </div>
        </div>

        <script src="{$assets_url}/js/media-manager.js?v={$timestamp}"></script>
        <script src="{$assets_url}/js/admin.js?v=5"></script>
HTML;
    }

    private function saveGeneral() {
        if (isset($_POST['site_logo'])) Capsule::table('rj_config')->updateOrInsert(['setting' => 'site_logo'], ['value' => trim($_POST['site_logo'])]);
        if (isset($_POST['dark_mode_logo'])) Capsule::table('rj_config')->updateOrInsert(['setting' => 'dark_mode_logo'], ['value' => trim($_POST['dark_mode_logo'])]);
        if (isset($_POST['logo_width'])) Capsule::table('rj_config')->updateOrInsert(['setting' => 'logo_width'], ['value' => (int)$_POST['logo_width']]);
        
        $show_topbar = isset($_POST['show_topbar']) ? '1' : '0';
        Capsule::table('rj_config')->updateOrInsert(['setting' => 'show_topbar'], ['value' => $show_topbar]);

        $show_announcements = isset($_POST['show_announcements']) ? '1' : '0';
        Capsule::table('rj_config')->updateOrInsert(['setting' => 'show_announcements'], ['value' => $show_announcements]);

        return '<div class="mb-4 p-4 rounded-md bg-green-50 border border-green-200 text-green-700 text-center font-bold">تنظیمات عمومی ذخیره شد.</div>';
    }

    private function saveLicense() {
        $username = trim($_POST['rtl_username']);
        $order_id = trim($_POST['rtl_order_id']);
        
        $old_order = Capsule::table('rj_config')->where('setting', 'rtl_order_id')->value('value');
        if ($old_order != $order_id) {
            Capsule::table('rj_config')->updateOrInsert(['setting' => 'license_status'], ['value' => '0']);
            Capsule::table('rj_config')->updateOrInsert(['setting' => 'last_check'], ['value' => '0']);
        }

        Capsule::table('rj_config')->updateOrInsert(['setting' => 'rtl_username'], ['value' => $username]);
        Capsule::table('rj_config')->updateOrInsert(['setting' => 'rtl_order_id'], ['value' => $order_id]);

        return '<div class="mb-4 p-4 rounded-md bg-green-50 border border-green-200 text-green-700 text-center font-bold">تنظیمات لایسنس ذخیره شد.</div>';
    }

    private function getImgUrl($path) {
        if(empty($path)) return '';
        return (strpos($path, 'http') === 0) ? $path : '../' . $path;
    }
}