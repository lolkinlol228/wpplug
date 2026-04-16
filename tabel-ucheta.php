<?php
/**
 * Plugin Name: Табель учёта рабочего времени
 * Plugin URI: https://example.com
 * Description: Система учёта рабочего времени с табелями, сотрудниками, экспортом Excel и Telegram-уведомлениями. Шорткод: [tabel_ucheta]
 * Version: 2.0.0.0
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
    if (session_status() === PHP_SESSION_ACTIVE) return; // уже активна
    if (headers_sent()) return; // заголовки уже отправлены — не можем стартовать сессию
    
    $secure = is_ssl();
    $path = parse_url(home_url(), PHP_URL_PATH) ?: '/';
    
    // На локалке иногда parse_url возвращает null — используем /
    if (!$path || $path === '') $path = '/';
    
    session_set_cookie_params([
        'lifetime' => 86400,
        'path'     => $path,
        'domain'   => '', // пустая строка = текущий домен (работает и на localhost)
        'secure'   => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    
    // Уникальное имя сессии чтобы не конфликтовать с другими плагинами
    session_name('tabel_sess');
    
    @session_start();
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
    if (version_compare($ver, '14', '<')) {
        if (!function_exists('dbDelta')) {
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        }
        tabel_create_tables();
        update_option('tabel_db_version', '14');
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
    
    // Check maintenance for non-superadmins
    if (get_option('tabel_maintenance_mode', '0') === '1' && empty($_SESSION['tabel_is_superadmin'])) {
        $perms = isset($_SESSION['tabel_perms']) ? $_SESSION['tabel_perms'] : tabel_user_perms();
        if (empty($perms['can_access_during_maintenance'])) {
            return tabel_render_maintenance();
        }
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
    
    // Maintenance mode: block non-superadmin API
    if (get_option('tabel_maintenance_mode', '0') === '1' && empty($_SESSION['tabel_is_superadmin'])) {
        $perms = isset($_SESSION['tabel_perms']) ? $_SESSION['tabel_perms'] : [];
        $can_access = !empty($perms['can_access_during_maintenance']);
        
        if (!$can_access) {
            $allowed = ['me', 'logout', 'maintenance'];
            $is_allowed = in_array($route, $allowed) || strpos($route, 'set_lang/') === 0;
            if (!$is_allowed) {
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(503);
                $msg = get_option('tabel_maintenance_message', 'Система на техническом обслуживании. Попробуйте позже.');
                echo json_encode(['error' => 'maintenance', 'message' => $msg]);
                exit;
            }
        }
    }
    
    // Close session early for read-only routes (prevents file lock blocking parallel requests)
    $write_routes = ['login', 'logout', 'switch_db', 'set_lang', 'me', 'users', 'profile'];
    $needs_write = false;
    foreach ($write_routes as $wr) {
        if (strpos($route, $wr) === 0) { $needs_write = true; break; }
    }
    if (!$needs_write) {
        session_write_close();
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

// ─── Render maintenance page ───
function tabel_render_maintenance() {
    $msg = get_option('tabel_maintenance_message', 'Система на техническом обслуживании. Попробуйте позже.');
    $api_base = home_url('/?tabel_api=');
    ob_start();
    ?>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <div style="font-family:'Inter',sans-serif;display:flex;align-items:center;justify-content:center;min-height:60vh;padding:24px">
      <div style="text-align:center;max-width:500px">
        <div style="font-size:64px;margin-bottom:16px">🔧</div>
        <h1 style="font-size:22px;font-weight:700;margin-bottom:8px;color:#24292f">Технические работы</h1>
        <p style="font-size:14px;color:#57606a;line-height:1.6"><?php echo esc_html($msg); ?></p>
        <p style="font-size:12px;color:#8c959f;margin-top:16px">Пожалуйста, попробуйте позже</p>
        <button
          onclick="tabelMaintenanceLogout()"
          style="margin-top:24px;padding:9px 20px;background:#f6f8fa;border:1px solid #d0d7de;border-radius:6px;font-size:13px;font-weight:500;color:#24292f;cursor:pointer;font-family:inherit;"
          onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='#f6f8fa'"
        >← Выйти из аккаунта</button>
      </div>
    </div>
    <script>
    async function tabelMaintenanceLogout() {
      try {
        await fetch(<?php echo json_encode($api_base . 'logout'); ?>, { method: 'GET' });
      } catch(e) {}
      window.location.reload();
    }
    </script>
    <?php
    return ob_get_clean();
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