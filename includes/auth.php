<?php
if (!defined('ABSPATH')) exit;

function tabel_get_client_ip() {
    $keys = ['HTTP_X_FORWARDED_FOR','HTTP_X_REAL_IP','HTTP_CLIENT_IP','REMOTE_ADDR'];
    foreach ($keys as $k) {
        if (!empty($_SERVER[$k])) {
            $ip = explode(',', $_SERVER[$k])[0];
            $ip = trim($ip);
            if (filter_var($ip, FILTER_VALIDATE_IP)) return $ip;
        }
    }
    return '0.0.0.0';
}

function tabel_mask_password($pw) {
    $len = mb_strlen($pw);
    if ($len <= 2) return str_repeat('*', $len);
    return mb_substr($pw, 0, 2) . str_repeat('*', min($len - 2, 8));
}

function tabel_log_login($username, $user_id, $success, $password = '') {
    global $wpdb;
    $t = tabel_table('login_log');
    $masked = '';
    if (!$success && $password) {
        $masked = tabel_mask_password($password);
    }
    $wpdb->insert($t, [
        'username'       => substr($username, 0, 100),
        'user_id'        => $user_id,
        'ip_address'     => tabel_get_client_ip(),
        'attempted_pass' => $masked,
        'success'        => $success ? 1 : 0,
        'created_at'     => current_time('mysql'),
    ]);
}

function tabel_check_rate_limit($username) {
    global $wpdb;
    $t = tabel_table('login_log');
    $ip = tabel_get_client_ip();
    
    // Max 10 failed attempts per IP in last 15 minutes
    $count = (int)$wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $t WHERE ip_address = %s AND success = 0 AND created_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE)",
        $ip
    ));
    if ($count >= 10) return false;
    
    // Max 5 failed attempts per username in last 15 minutes
    $count2 = (int)$wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $t WHERE username = %s AND success = 0 AND created_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE)",
        $username
    ));
    if ($count2 >= 5) return false;
    
    return true;
}

function tabel_api_login() {
    global $wpdb;
    header('Content-Type: application/json; charset=utf-8');
    
    $input = json_decode(file_get_contents('php://input'), true);
    $username = isset($input['username']) ? trim($input['username']) : '';
    $password = isset($input['password']) ? $input['password'] : '';
    
    if (!$username || !$password) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'Введите логин и пароль']);
        return;
    }
    
    // Rate limiting
    if (!tabel_check_rate_limit($username)) {
        tabel_log_login($username, null, false, $password);
        http_response_code(429);
        echo json_encode(['ok' => false, 'error' => 'Слишком много попыток. Подождите 15 минут.']);
        return;
    }
    
    $t = tabel_table('users');
    $user = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $t WHERE username = %s AND is_active = 1", $username
    ), ARRAY_A);
    
    if ($user && tabel_verify_password($password, $user['password_hash'])) {
        // Rehash legacy sha256 to bcrypt
        if (tabel_needs_rehash($user['password_hash'])) {
            $wpdb->update($t, ['password_hash' => tabel_hash_password($password)], ['id' => $user['id']]);
        }
        
        $_SESSION['tabel_user_id'] = (int)$user['id'];
        $_SESSION['tabel_username'] = $user['username'];
        $_SESSION['tabel_is_superadmin'] = (bool)$user['is_superadmin'];
        $_SESSION['tabel_perms'] = tabel_user_perms($user['id']);
        
        tabel_log_login($username, (int)$user['id'], true);
        
        if (get_option('tabel_maintenance_mode', '0') === '1' && !$user['is_superadmin']) {
            // Check if user has explicit permission to access during maintenance
            $perms = tabel_user_perms((int)$user['id']);
            $can_access = !empty($perms['can_access_during_maintenance']);
            
            if (!$can_access) {
                unset($_SESSION['tabel_user_id'], $_SESSION['tabel_username'], $_SESSION['tabel_is_superadmin'], $_SESSION['tabel_perms']);
                $msg = get_option('tabel_maintenance_message', 'Система на техническом обслуживании.');
                http_response_code(503);
                echo json_encode(['ok' => false, 'error' => '🔧 ' . $msg, 'maintenance' => true]);
                return;
            }
        }
        
        echo json_encode(['ok' => true, 'is_superadmin' => (bool)$user['is_superadmin']]);
    } else {
        tabel_log_login($username, $user ? (int)$user['id'] : null, false, $password);
        http_response_code(401);
        echo json_encode(['ok' => false, 'error' => 'Неверный логин или пароль']);
    }
}

function tabel_api_logout() {
    $keys = ['tabel_user_id', 'tabel_username', 'tabel_is_superadmin', 
             'tabel_perms', 'tabel_active_db', 'tabel_lang'];
    foreach ($keys as $k) unset($_SESSION[$k]);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => true]);
    exit;
}

function tabel_api_set_lang($lang) {
    if (in_array($lang, ['ru', 'kg', 'en'])) $_SESSION['tabel_lang'] = $lang;
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => true]);
}