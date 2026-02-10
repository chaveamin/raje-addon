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
        if (isset($_POST['sub_action']) && $_POST['sub_action'] == 'save_license') {
            $saveMessage = $this->saveLicense();
        }

        // Fetch Data
        $settings = Capsule::table('rj_config')->whereIn('setting', ['rtl_username', 'rtl_order_id', 'license_status'])->pluck('value', 'setting')->all();
        
        $username = htmlspecialchars($settings['rtl_username'] ?? '');
        $order_id = htmlspecialchars($settings['rtl_order_id'] ?? '');
        $status_val = $settings['license_status'] ?? '0';
        $assets_url = '../modules/addons/raje/assets';

        // لیست پیام‌های خطا
        $error_messages = [
            '-1' => 'API اشتباه است.',
            '-2' => 'نام کاربری اشتباه است.',
            '-3' => 'کد سفارش اشتباه است.',
            '-4' => 'کد سفارش قبلاً ثبت شده است.',
            '-5' => 'کد سفارش مربوطه به این نام کاربری نمی‌باشد.',
            '-6' => 'اطلاعات وارد شده در فرمت صحیح نمی‌باشند.',
            '-7' => 'کد سفارش مربوط به این محصول نیست.',
            '-8' => 'کد سفارش مربوطه به این نام کاربری نمی‌باشد.',
        ];

        if ($status_val == '1') {
            $status_alert = '<div class="p-4 rounded-2xl bg-lime-400/20 text-lime-700 flex items-start gap-4 transition-all"><div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0"><i class="fas fa-check-circle text-xl"></i></div><div><div class="font-bold text-green-800 text-lg">لایسنس فعال است (Active)</div><div class="text-sm text-green-600 mt-1">قالب شما با موفقیت فعال شده و آماده استفاده است.</div></div></div>';
        } elseif ($status_val == '0') {
            $status_alert = '<div class="p-4 rounded-2xl bg-yellow-400/20 text-yellow-700 flex items-start gap-4 transition-all"><div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0"><i class="fas fa-exclamation-circle text-xl"></i></div><div><div class="font-bold text-yellow-800 text-lg">لایسنس بررسی نشده (Not Checked)</div><div class="text-sm text-yellow-600 mt-1">لطفا نام کاربری و شماره سفارش را وارد کرده و دکمه "بررسی و فعال‌سازی" را بزنید.</div></div></div>';
        } else {
            $error_desc = isset($error_messages[$status_val]) ? $error_messages[$status_val] : 'خطای ناشناخته رخ داده است.';
            
            $status_alert = '<div class="p-4 rounded-2xl bg-red-500/15 text-red-700 flex items-start gap-4 transition-all"><div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0"><i class="fas fa-times-circle text-xl"></i></div><div><div class="font-bold text-red-800 text-lg">خطای فعال‌سازی</div><div class="text-sm text-red-600 mt-1">کد خطا: '.$status_val.'<br><span class="font-bold">'.$error_desc.'</span></div></div></div>';
        }

        // Render Content
        $content = <<<HTML
            <link rel="stylesheet" href="{$assets_url}/css/raje.cp.out.css">
            <form method="post" action="">
                <input type="hidden" name="sub_action" value="save_license">
                {$saveMessage}
                    <div class="flex flex-col items-start gap-y-8 w-full *:w-full">
                        <div>
                            <label class="block text-sm font-bold text-zinc-500 mb-4" for="rtl_username">نام کاربری راست‌چین (Username)</label>
                            <input id="rtl_username" type="text" name="rtl_username" class="raje-input w-full p-5 bg-zinc-50/30 font-mono" value="{$username}" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-zinc-500 mb-4" for="rtl_order_id">شماره سفارش (Order ID)</label>
                            <input id="rtl_order_id" type="text" name="rtl_order_id" class="raje-input w-full p-5 bg-zinc-50/30 font-mono" value="{$order_id}" required>
                        </div>
                        {$status_alert}
                    </div>
                <button type="submit" class="raje-btn mt-6">
                    فعالسازی
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
        if (isset($_POST['sub_action']) && $_POST['sub_action'] == 'save_general') {
            $saveMessage = $this->saveGeneral();
        }

        $settings = Capsule::table('rj_config')->whereIn('setting', [
            'site_logo',
            'dark_mode_logo',
            'logo_width',
            'show_topbar',
            'show_announcements',
            'show_gravatar',
            'login_layout',
            'register_layout',
            'login_side_image',
            'register_side_image'
        ])->pluck('value', 'setting')->all();
        
        $logo = htmlspecialchars($settings['site_logo'] ?? '');
        $dark = htmlspecialchars($settings['dark_mode_logo'] ?? '');
        $width = htmlspecialchars($settings['logo_width'] ?? '32');
        $topbar = ($settings['show_topbar'] ?? '1') == '1' ? 'checked' : '';
        $announce = ($settings['show_announcements'] ?? '1') == '1' ? 'checked' : '';
        $gravatar = ($settings['show_gravatar'] ?? '1') == '1' ? 'checked' : '';
        $loginLayout = $settings['login_layout'] ?? 'col';
        $registerLayout = $settings['register_layout'] ?? 'col';
        $loginSideImage = htmlspecialchars($settings['login_side_image'] ?? '');
        $loginSideImageUrl = $this->getImgUrl($loginSideImage);
        $loginSideImageHide = empty($loginSideImageUrl) ? 'hidden' : '';
        $loginDefaultTextHide = !empty($loginSideImageUrl) ? 'hidden' : '';
        $registerSideImage = htmlspecialchars($settings['register_side_image'] ?? '');
        $registerSideImageUrl = $this->getImgUrl($registerSideImage);
        $registerSideImageHide = empty($registerSideImageUrl) ? 'hidden' : '';
        $registerDefaultTextHide = !empty($registerSideImageUrl) ? 'hidden' : '';
        
        $logoUrl = $this->getImgUrl($logo);
        $darkUrl = $this->getImgUrl($dark);
        $logoHide = empty($logoUrl) ? 'hidden' : '';
        $darkHide = empty($darkUrl) ? 'hidden' : '';
        $assets_url = '../modules/addons/raje/assets';

        $isLoginCol = ($loginLayout === 'col') ? 'checked' : '';
        $isLoginFull = ($loginLayout === 'full') ? 'checked' : '';
        $isRegCol = ($registerLayout === 'col') ? 'checked' : '';
        $isRegFull = ($registerLayout === 'full') ? 'checked' : '';

        // Render Content
        $content = <<<HTML
            <link rel="stylesheet" href="{$assets_url}/css/raje.cp.out.css">
            <form class="relative" method="post" action="">
                <input type="hidden" name="sub_action" value="save_general">
                {$saveMessage}
                
                <div class="bg-white rounded-2xl border border-zinc-200 mb-6 overflow-hidden">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-zinc-100 bg-black/5">
                        <h3 class="font-bold text-zinc-700 mb-0">لوگو و هویت سایت</h3>
                    </div>
                    <div class="p-6 flex items-center gap-6 w-full *:w-full">
                        <div>
                            <label class="block text-sm font-bold text-zinc-700 mb-2">لوگوی سایت</label>
                            <div class="media-input-group flex flex-col gap-3">
                                <div class="bg-zinc-50/50 border border-zinc-200 rounded-2xl p-4 w-full h-32 flex items-center justify-center relative overflow-hidden">
                                    <img src="{$logoUrl}" class="preview-img h-full object-contain {$logoHide}">
                                </div>
                                <div class="flex gap-2">
                                    <input type="text" name="site_logo" value="{$logo}" class="flex-1 text-xs text-zinc-500 border border-zinc-200 rounded-xl px-3 bg-white font-mono select-none outline-0 p-3" readonly>
                                    <button class="media-select-btn raje-btn">انتخاب</button>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-zinc-700 mb-2">لوگوی دارک مود</label>
                            <div class="media-input-group flex flex-col gap-3">
                                <div class="bg-zinc-800 border border-zinc-700 rounded-2xl p-4 w-full h-32 flex items-center justify-center relative overflow-hidden">
                                    <img src="{$darkUrl}" class="preview-img h-full object-contain {$darkHide}">
                                </div>
                                <div class="flex gap-2">
                                    <input type="text" name="dark_mode_logo" value="{$dark}" class="flex-1 text-xs text-zinc-500 border border-zinc-200 rounded-xl px-3 bg-white font-mono select-none outline-0 p-3" readonly>
                                    <button class="media-select-btn raje-btn">انتخاب</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <label class="block text-sm font-bold text-zinc-700 mb-2">اندازه لوگو (پیکسل)</label>
                        <input type="number" name="logo_width" class="raje-input p-3" value="{$width}">
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-zinc-200 mb-12 overflow-hidden">
                     <div class="flex items-center justify-between px-6 py-4 border-b border-zinc-100 bg-black/5">
                        <h3 class="font-bold text-zinc-700 mb-0">عمومی</h3>
                    </div>
                    <div class="p-6 divide-y divide-zinc-950/5 *:pt-4 *:first:pt-0 *:pb-4 *:last:pb-0">
                        <div class="flex items-center gap-4">
                            <input id="show_topbar" name="show_topbar" type="checkbox" value="1" {$topbar}>
                            <label for="show_topbar" class="font-bold text-zinc-700">نمایش نوار اعلان (Top Bar)</label>
                        </div>
                        <div class="flex items-center gap-4">
                            <input id="show_announcements" name="show_announcements" type="checkbox" value="1" {$announce}>
                            <label for="show_announcements" class="font-bold text-zinc-700">نمایش اسلایدر اخبار</label>
                        </div>
                         <div class="flex items-center gap-4">
                            <input id="show_gravatar" name="show_gravatar" type="checkbox" value="1" {$gravatar}>
                            <label for="show_gravatar" class="font-bold text-zinc-700">نمایش تصویر پروفایل (Gravatar)</label>
                        </div>
                        <div class="flex items-end gap-8 mt-6">
                            <div>
                                <label class="block text-sm font-bold text-zinc-700 mb-4">طرح صفحه ورود</label>
                                <div class="flex gap-4">
                                    <label class="cursor-pointer group flex-1">
                                        <input type="radio" name="login_layout" value="col" class="hidden peer" {$isLoginCol}>
                                        <div class="p-4 bg-zinc-100 peer-checked:ring-2 peer-checked:ring-zinc-800 rounded-3xl overflow-hidden relative transition-all">
                                            <img src="{$assets_url}/img/login-style-1.webp" class="w-lg object-cover rounded-3xl">
                                            <div class="bg-zinc-800/10 mt-4 py-4 rounded-2xl text-center text-xs font-bold">دو ستونه</div>
                                        </div>
                                    </label>
                                    <label class="cursor-pointer group flex-1">
                                        <input type="radio" name="login_layout" value="full" class="hidden peer" {$isLoginFull}>
                                        <div class="p-4 bg-zinc-100 peer-checked:ring-2 peer-checked:ring-zinc-800 rounded-3xl overflow-hidden relative transition-all">
                                            <img src="{$assets_url}/img/login-style-2.webp" class="w-lg object-cover rounded-3xl">
                                            <div class="bg-zinc-800/10 mt-4 py-4 rounded-2xl text-center text-xs font-bold">تمام صفحه</div>
                                        </div>
                                    </label>
                                </div>
                                <div id="login-side-image-wrapper" class="overflow-hidden transition-all duration-500 ease-in-out max-h-0 opacity-0">
                                    <div class="pt-4">
                                        <label class="block text-sm font-bold text-zinc-700 mb-2">تصویر کناری</label>
                                        <div class="media-input-group flex flex-col gap-3">
                                            <div class="bg-zinc-50/50 border border-zinc-200 rounded-2xl p-4 w-full h-32 flex items-center justify-center relative overflow-hidden">
                                                <img src="{$loginSideImageUrl}" class="preview-img h-full object-contain {$loginSideImageHide}">
                                                <span class="text-zinc-400 text-xs {$loginDefaultTextHide}">پیش‌فرض قالب</span>
                                            </div>
                                            <div class="flex gap-2">
                                                <input type="text" name="login_side_image" value="{$loginSideImage}" class="flex-1 text-xs text-zinc-500 border border-zinc-200 rounded-xl px-3 bg-white font-mono select-none outline-0 p-3" readonly>
                                                <button class="media-select-btn raje-btn">انتخاب</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-zinc-700 mb-4">طرح صفحه ثبت‌ نام</label>
                                <div class="flex gap-4">
                                    <label class="cursor-pointer group flex-1">
                                        <input type="radio" name="register_layout" value="col" class="hidden peer" {$isRegCol}>
                                        <div class="p-4 bg-zinc-100 peer-checked:ring-2 peer-checked:ring-zinc-800 rounded-3xl overflow-hidden relative transition-all">
                                            <img src="{$assets_url}/img/register-style-1.webp" class="w-lg object-cover rounded-3xl">
                                            <div class="bg-zinc-800/10 mt-4 py-4 rounded-2xl text-center text-xs font-bold">دو ستونه</div>
                                        </div>
                                    </label>
                                    <label class="cursor-pointer group flex-1">
                                        <input type="radio" name="register_layout" value="full" class="hidden peer" {$isRegFull}>
                                        <div class="p-4 bg-zinc-100 peer-checked:ring-2 peer-checked:ring-zinc-800 rounded-3xl overflow-hidden relative transition-all">
                                            <img src="{$assets_url}/img/register-style-2.webp" class="w-lg object-cover rounded-3xl">
                                            <div class="bg-zinc-800/10 mt-4 py-4 rounded-2xl text-center text-xs font-bold">تمام صفحه</div>
                                        </div>
                                    </label>
                                </div>
                                <div id="register-side-image-wrapper" class="overflow-hidden transition-all duration-500 ease-in-out max-h-0 opacity-0">
                                    <div class="pt-4">
                                        <label class="block text-sm font-bold text-zinc-700 mb-2">تصویر کناری</label>
                                        <div class="media-input-group flex flex-col gap-3">
                                            <div class="bg-zinc-50/50 border border-zinc-200 rounded-2xl p-4 w-full h-32 flex items-center justify-center relative overflow-hidden">
                                                <img src="{$registerSideImageUrl}" class="preview-img h-full object-contain {$registerSideImageHide}">
                                                <span class="text-zinc-400 text-xs {$registerDefaultTextHide}">پیش‌فرض قالب</span>
                                            </div>
                                            <div class="flex gap-2">
                                                <input type="text" name="register_side_image" value="{$registerSideImage}" class="flex-1 text-xs text-zinc-500 border border-zinc-200 rounded-xl px-3 bg-white font-mono select-none outline-0 p-3" readonly>
                                                <button class="media-select-btn raje-btn">انتخاب</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="sticky bottom-6 right-0 rounded-3xl p-6 bg-white ring-2 ring-zinc-800/15 flex items-center gap-x-6 *:rounded-2xl *:font-bold *:transition-colors *:w-64 *:h-20">
                    <button id="save" type="submit" class=" text-white bg-zinc-800 hover:bg-zinc-700">ذخیره تغییرات</button>
                    <button type="button" class="ring ring-black/20 text-zinc-800 shadow-sm hover:bg-zinc-50" onclick="window.location.reload()">لغو</button>
                </div>
            </form>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    function setupToggle(radioName, wrapperId) {
                        const radios = document.querySelectorAll(`input[name="\${radioName}"]`);
                        const wrapper = document.getElementById(wrapperId);
                        
                        function update() {
                            const selected = document.querySelector(`input[name="\${radioName}"]:checked`).value;
                            if (selected === 'col') {
                                wrapper.classList.remove('max-h-0', 'opacity-0');
                                wrapper.classList.add('max-h-96', 'opacity-100');
                            } else {
                                wrapper.classList.remove('max-h-96', 'opacity-100');
                                wrapper.classList.add('max-h-0', 'opacity-0');
                            }
                        }
                        
                        radios.forEach(r => r.addEventListener('change', update));
                        update();
                    }

                    setupToggle('login_layout', 'login-side-image-wrapper');
                    setupToggle('register_layout', 'register-side-image-wrapper');
                });
            </script>            
HTML;

        return $this->renderPage('تنظیمات عمومی', $content, 'general', $vars);
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
        <link rel="stylesheet" href="{$assets_url}/css/raje.cp.out.css">
        <link rel="stylesheet" href="{$assets_url}/css/rjcp.css">
        <style>
            #media-grid::-webkit-scrollbar { width: 8px; }
            #media-grid::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        </style>

        <div class="raje-admin-container font-ravi rounded-3xl bg-white/90 p-16 shadow-lg ring-1 shadow-zinc-800/5 ring-zinc-900/5" dir="rtl">
            
            <div class="flex items-center justify-between mb-16">
                <div class="flex items-center gap-3">
                     <div class="size-12">
                        <img src="{$assets_url}/img/{$icon}.svg">
                    </div>
                    <h1 class="text-2xl font-bold text-zinc-800 m-0">{$title}</h1>
                </div>
                <a href="{$modulelink}" class="text-zinc-500 bg-white border border-zinc-200 px-6 py-4 rounded-xl font-semibold text-sm">
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

        $show_gravatar = isset($_POST['show_gravatar']) ? '1' : '0';
        Capsule::table('rj_config')->updateOrInsert(['setting' => 'show_gravatar'], ['value' => $show_gravatar]);

        if (isset($_POST['login_layout'])) {
            Capsule::table('rj_config')->updateOrInsert(['setting' => 'login_layout'], ['value' => $_POST['login_layout']]);
        }
        if (isset($_POST['register_layout'])) {
            Capsule::table('rj_config')->updateOrInsert(['setting' => 'register_layout'], ['value' => $_POST['register_layout']]);
        }

        if (isset($_POST['login_side_image'])) {
            Capsule::table('rj_config')->updateOrInsert(['setting' => 'login_side_image'], ['value' => trim($_POST['login_side_image'])]);
        }
        if (isset($_POST['register_side_image'])) {
            Capsule::table('rj_config')->updateOrInsert(['setting' => 'register_side_image'], ['value' => trim($_POST['register_side_image'])]);
        }

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