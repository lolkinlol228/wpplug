<?php
/**
 * Plugin Name: Табель учёта рабочего времени
 * Plugin URI: https://example.com
 * Description: Система учёта рабочего времени с табелями, сотрудниками, экспортом Excel и Telegram-уведомлениями. Шорткод: [tabel_ucheta]
 * Version: 1.0.1
 * Author: Rustamov Ismail
 * Text Domain: tabel-ucheta
 * License: GPL v2
 */

if (!defined('ABSPATH')) exit;

define('TABEL_VERSION', '1.0.1');
define('TABEL_PATH', plugin_dir_path(__FILE__));
define('TABEL_URL', plugin_dir_url(__FILE__));

// ─── Session: start early and reliably ───
function tabel_ensure_session() {
    if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
        // Set cookie params to match WordPress site
        $secure = is_ssl();
        $path = parse_url(home_url(), PHP_URL_PATH) ?: '/';
        session_set_cookie_params([
            'lifetime' => 86400,
            'path' => $path,
            'domain' => '',
            'secure' => $secure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        @session_start();
    }
}
add_action('init', 'tabel_ensure_session', 1);

// Include core files
require_once TABEL_PATH . 'includes/database.php';
require_once TABEL_PATH . 'includes/auth.php';
require_once TABEL_PATH . 'includes/api.php';
require_once TABEL_PATH . 'includes/export.php';
require_once TABEL_PATH . 'includes/translations.php';

// Activation hook
register_activation_hook(__FILE__, 'tabel_activate');
function tabel_activate() {
    tabel_create_tables();
    tabel_init_master_data();
}

// Auto-migrate: ensure new tables/columns exist
add_action('init', 'tabel_auto_migrate', 0);
function tabel_auto_migrate() {
    $ver = get_option('tabel_db_version', '0');
    if (version_compare($ver, '10', '<')) {
        if (!function_exists('dbDelta')) {
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        }
        tabel_create_tables();
        update_option('tabel_db_version', '10');
    }
}

// Register shortcode
add_shortcode('tabel_ucheta', 'tabel_shortcode_render');

// Track if shortcode is on the current page
global $tabel_shortcode_active;
$tabel_shortcode_active = false;

function tabel_shortcode_render($atts) {
    global $tabel_shortcode_active;
    $tabel_shortcode_active = true;
    
    tabel_ensure_session();
    
    if (!isset($_SESSION['tabel_user_id'])) {
        return tabel_render_login();
    }
    return tabel_render_app();
}

// ─── Remove WordPress styles/scripts on pages with our shortcode ───
add_action('wp_enqueue_scripts', 'tabel_remove_wp_styles', 999);
function tabel_remove_wp_styles() {
    global $post;
    if (!$post || !has_shortcode($post->post_content, 'tabel_ucheta')) return;
    
    // Dequeue all default WP styles
    $styles_to_remove = [
        'wp-block-library', 'wp-block-library-theme', 'wc-blocks-style',
        'global-styles', 'classic-theme-styles',
    ];
    foreach ($styles_to_remove as $handle) {
        wp_dequeue_style($handle);
        wp_deregister_style($handle);
    }
    
    // Dequeue theme styles
    global $wp_styles;
    if ($wp_styles && !empty($wp_styles->registered)) {
        foreach ($wp_styles->registered as $handle => $style) {
            $src = $style->src ?? '';
            if (is_string($src) && (
                strpos($src, '/themes/') !== false ||
                strpos($src, 'wp-includes/css') !== false
            )) {
                wp_dequeue_style($handle);
            }
        }
    }
}

// ─── Use a blank page template (no header/footer) ───
add_filter('template_include', 'tabel_override_template', 999);
function tabel_override_template($template) {
    global $post;
    if (!$post || !has_shortcode($post->post_content, 'tabel_ucheta')) return $template;
    
    // Use our own blank template
    $our_template = TABEL_PATH . 'includes/templates/blank-page.php';
    if (file_exists($our_template)) {
        return $our_template;
    }
    return $template;
}

// ─── API routes ───
add_action('wp_loaded', 'tabel_handle_routes');
function tabel_handle_routes() {
    if (!isset($_GET['tabel_api'])) return;
    
    tabel_ensure_session();
    
    $route = sanitize_text_field($_GET['tabel_api']);
    
    // Login doesn't require auth
    if ($route === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        tabel_api_login();
        exit;
    }
    if ($route === 'logout') {
        tabel_api_logout();
        exit;
    }
    
    // Set lang doesn't require auth
    if (preg_match('/^set_lang\/(\w+)$/', $route, $m)) {
        tabel_api_set_lang($m[1]);
        exit;
    }
    
    // All other routes require auth
    if (!isset($_SESSION['tabel_user_id'])) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(401);
        echo json_encode(['error' => 'unauthorized']);
        exit;
    }
    
    // Route dispatcher
    tabel_dispatch_api($route);
    exit;
}

// ─── Export routes (file download) ───
add_action('wp_loaded', 'tabel_handle_exports');
function tabel_handle_exports() {
    if (!isset($_GET['tabel_export'])) return;
    
    tabel_ensure_session();
    
    if (!isset($_SESSION['tabel_user_id'])) {
        wp_redirect(home_url());
        exit;
    }
    
    $route = sanitize_text_field($_GET['tabel_export']);
    tabel_dispatch_export($route);
    exit;
}

// ─── Render login ───
function tabel_render_login() {
    $api_base = home_url('/?tabel_api=');
    ob_start();
    include TABEL_PATH . 'includes/templates/login.php';
    return ob_get_clean();
}

// ─── Render app ───
function tabel_render_app() {
    $lang = isset($_SESSION['tabel_lang']) ? $_SESSION['tabel_lang'] : 'ru';
    $T = tabel_get_translations($lang);
    $MK = tabel_get_month_keys();
    $now = new DateTime();
    $current_year = (int)$now->format('Y');
    $current_month = (int)$now->format('n');
    
    $api_base = home_url('/?tabel_api=');
    $export_base = home_url('/?tabel_export=');
    
    ob_start();
    include TABEL_PATH . 'includes/templates/app.php';
    $html = ob_get_clean();
    
    if (empty(trim($html))) {
        return '<div style="padding:40px;text-align:center;color:red;">Ошибка: шаблон приложения пуст. Проверьте PHP error log.</div>';
    }
    
    return $html;
}
