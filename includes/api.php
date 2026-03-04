<?php
if (!defined('ABSPATH')) exit;

function tabel_json($data, $code = 200) {
    header('Content-Type: application/json; charset=utf-8');
    if ($code !== 200) http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function tabel_input() {
    return json_decode(file_get_contents('php://input'), true) ?: [];
}

function tabel_check_perm($key) {
    $user = tabel_current_user();
    if (!$user) tabel_json(['error' => 'unauthorized'], 401);
    if (!empty($user['is_superadmin'])) return true;
    $perms = tabel_user_perms();
    if (empty($perms[$key])) tabel_json(['error' => 'forbidden', 'perm' => $key], 403);
    return true;
}

function tabel_require_db() {
    $db = tabel_active_db();
    if (!$db) tabel_json(['error' => 'no_db', 'message' => 'Выберите базу данных'], 400);
    return $db;
}

function tabel_check_workflow_block($db_name, $year, $month) {
    global $wpdb;
    $user = tabel_current_user();
    if (!$user) return;
    if (!empty($user['is_superadmin'])) return; // superadmin never blocked
    $ws_t = tabel_table('workflow_status');
    $wc_t = tabel_table('workflow_chains');
    $status = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $ws_t WHERE db_name = %s AND year = %d AND month = %d", $db_name, $year, $month
    ), ARRAY_A);
    if (!$status) return; // no workflow = no block
    if ($status['status'] === 'completed') {
        tabel_json(['error' => 'Табель согласован и заблокирован'], 403);
    }
    if (!(int)$status['activated']) return; // not activated yet
    $chains = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $wc_t WHERE db_name = %s AND step_order = %d", $db_name, (int)$status['current_step']
    ), ARRAY_A);
    $uid = (int)$user['id'];
    foreach ($chains as $c) {
        $uids = json_decode($c['user_ids'], true) ?: [];
        if (in_array($uid, $uids)) return; // user is on current step = allowed
    }
    tabel_json(['error' => 'Ожидайте вашей очереди в цепочке'], 403);
}

function tabel_dispatch_api($route) {
    global $wpdb;
    $method = $_SERVER['REQUEST_METHOD'];
    
    // /api/me
    if ($route === 'me') { tabel_api_me(); return; }
    
    // /api/switch_db
    if ($route === 'switch_db' && $method === 'POST') { tabel_api_switch_db(); return; }
    
    // /api/employees
    if ($route === 'employees' && $method === 'GET') { tabel_api_get_employees(); return; }
    if ($route === 'employees' && $method === 'POST') { tabel_api_add_employee(); return; }
    if ($route === 'employees/search') { tabel_api_search_employees(); return; }
    
    // /api/employees/<id> PUT/DELETE
    if (preg_match('/^employees\/(\d+)$/', $route, $m)) {
        $eid = (int)$m[1];
        if ($method === 'PUT') { tabel_api_update_employee($eid); return; }
        if ($method === 'DELETE') { tabel_api_delete_employee($eid); return; }
    }
    
    // /api/employees/<id>/name
    if (preg_match('/^employees\/(\d+)\/name$/', $route, $m) && $method === 'POST') {
        tabel_api_update_employee_name((int)$m[1]); return;
    }
    
    // /api/positions
    if ($route === 'positions' && $method === 'GET') { tabel_api_get_positions(); return; }
    if ($route === 'positions' && $method === 'POST') { tabel_api_add_position(); return; }
    if (preg_match('/^positions\/(\d+)$/', $route, $m)) {
        $pid = (int)$m[1];
        if ($method === 'PUT') { tabel_api_update_position($pid); return; }
        if ($method === 'DELETE') { tabel_api_delete_position($pid); return; }
    }
    
    // /api/timesheet/<year>/<month>
    if (preg_match('/^timesheet\/(\d+)\/(\d+)$/', $route, $m)) {
        tabel_api_get_timesheet((int)$m[1], (int)$m[2]); return;
    }
    
    // /api/timesheet/entry
    if ($route === 'timesheet/entry' && $method === 'POST') { tabel_api_set_entry(); return; }
    if ($route === 'timesheet/batch' && $method === 'POST') { tabel_api_batch_entries(); return; }
    if ($route === 'timesheet/bulk' && $method === 'POST') { tabel_api_bulk_entry(); return; }
    if ($route === 'timesheet/sunday_bulk' && $method === 'POST') { tabel_api_sunday_bulk(); return; }
    
    // /api/monthly_settings
    if ($route === 'monthly_settings' && $method === 'POST') { tabel_api_update_monthly_settings(); return; }
    
    // /api/employee/* routes
    if ($route === 'employee/fire' && $method === 'POST') { tabel_api_fire_employee(); return; }
    if ($route === 'employee/update_position' && $method === 'POST') { tabel_api_update_emp_position(); return; }
    if ($route === 'employee/update_employment' && $method === 'POST') { tabel_api_update_emp_employment(); return; }
    if ($route === 'employee/update_field' && $method === 'POST') { tabel_api_update_emp_field(); return; }
    
    // /api/excel_settings
    if ($route === 'excel_settings' && $method === 'GET') { tabel_api_get_excel_settings(); return; }
    if ($route === 'excel_settings' && $method === 'POST') { tabel_api_save_excel_settings(); return; }
    
    // /api/users
    if ($route === 'users' && $method === 'GET') { tabel_api_get_users(); return; }
    if ($route === 'users' && $method === 'POST') { tabel_api_create_user(); return; }
    if (preg_match('/^users\/(\d+)$/', $route, $m)) {
        $uid = (int)$m[1];
        if ($method === 'PUT') { tabel_api_update_user($uid); return; }
        if ($method === 'DELETE') { tabel_api_delete_user($uid); return; }
    }
    
    // /api/databases
    if ($route === 'databases' && $method === 'GET') { tabel_api_get_databases(); return; }
    if ($route === 'databases' && $method === 'POST') { tabel_api_create_database(); return; }
    if (preg_match('/^databases\/([^\/]+)$/', $route, $m) && $method === 'DELETE') {
        tabel_api_delete_database($m[1]); return;
    }
    if (preg_match('/^databases\/([^\/]+)\/assign$/', $route, $m) && $method === 'POST') {
        tabel_api_assign_db($m[1]); return;
    }
    if (preg_match('/^databases\/([^\/]+)\/users$/', $route, $m)) {
        tabel_api_get_db_users($m[1]); return;
    }
    
    // /api/stats/<year>/<month>
    if (preg_match('/^stats\/(\d+)\/(\d+)$/', $route, $m)) {
        tabel_api_get_stats((int)$m[1], (int)$m[2]); return;
    }
    
    // /api/export_history
    if ($route === 'export_history') { tabel_api_get_export_history(); return; }
    if (preg_match('/^export_history\/(\d+)\/download$/', $route, $m)) {
        tabel_api_download_export((int)$m[1]); return;
    }
    // DELETE single export log
    if (preg_match('/^export_history\/(\d+)\/delete$/', $route, $m)) {
        tabel_api_delete_export((int)$m[1]); return;
    }
    // CLEAR all export history
    if ($route === 'export_history_clear') {
        tabel_api_clear_export_history(); return;
    }
    
    // Login logs (superadmin only)
    if ($route === 'login_logs') { tabel_api_get_login_logs(); return; }
    
    // Workflow chains
    if ($route === 'workflow/chains' && $method === 'GET') { tabel_api_get_chains(); return; }
    if ($route === 'workflow/chains' && $method === 'POST') { tabel_api_save_chains(); return; }
    if ($route === 'workflow/status' && $method === 'GET') { tabel_api_get_workflow_status(); return; }
    if ($route === 'workflow/submit' && $method === 'POST') { tabel_api_workflow_submit(); return; }
    if ($route === 'workflow/return' && $method === 'POST') { tabel_api_workflow_return(); return; }
    if ($route === 'workflow/admin_action' && $method === 'POST') { tabel_api_workflow_admin_action(); return; }
    if ($route === 'workflow/log' && $method === 'GET') { tabel_api_get_workflow_log(); return; }
    
    // Notifications
    if ($route === 'notifications' && $method === 'GET') { tabel_api_get_notifications(); return; }
    if ($route === 'notifications/read' && $method === 'POST') { tabel_api_mark_notifications_read(); return; }
    
    // Experience (стаж)
    if (preg_match('/^experience\/(\d+)$/', $route, $m) && $method === 'GET') { tabel_api_get_experience((int)$m[1]); return; }
    if (preg_match('/^experience\/(\d+)$/', $route, $m) && $method === 'POST') { tabel_api_save_experience_period((int)$m[1]); return; }
    if (preg_match('/^experience\/period\/(\d+)$/', $route, $m) && $method === 'DELETE') { tabel_api_delete_experience_period((int)$m[1]); return; }
    if ($route === 'experience/all' && $method === 'GET') { tabel_api_get_all_employees_experience(); return; }
    
    tabel_json(['error' => 'not_found'], 404);
}

// ─── /api/me ───
function tabel_api_me() {
    global $wpdb;
    $user = tabel_current_user();
    if (!$user) tabel_json(['logged_in' => false]);
    
    $perms = tabel_user_perms();
    $db_t = tabel_table('databases');
    $ud_t = tabel_table('user_databases');
    
    if (!empty($user['is_superadmin'])) {
        $dbs = $wpdb->get_results("SELECT * FROM $db_t ORDER BY display_name", ARRAY_A);
    } else {
        $dbs = $wpdb->get_results($wpdb->prepare(
            "SELECT d.* FROM $db_t d JOIN $ud_t ud ON ud.db_name = d.name WHERE ud.user_id = %d ORDER BY d.display_name",
            $user['id']
        ), ARRAY_A);
    }
    
    $active_db = tabel_active_db();
    if (!$active_db && !empty($dbs)) {
        $active_db = $dbs[0]['name'];
        $_SESSION['tabel_active_db'] = $active_db;
    }
    
    tabel_json([
        'logged_in' => true,
        'user_id' => (int)$user['id'],
        'username' => $user['username'],
        'is_superadmin' => (bool)$user['is_superadmin'],
        'perms' => array_map('intval', $perms ?: []),
        'active_db' => $active_db,
        'accessible_dbs' => $dbs,
    ]);
}

// ─── /api/switch_db ───
function tabel_api_switch_db() {
    global $wpdb;
    $data = tabel_input();
    $db_name = isset($data['db_name']) ? trim($data['db_name']) : '';
    $user = tabel_current_user();
    $db_t = tabel_table('databases');
    $ud_t = tabel_table('user_databases');
    
    $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $db_t WHERE name = %s", $db_name), ARRAY_A);
    if (!$row) tabel_json(['ok' => false, 'error' => 'БД не найдена'], 404);
    
    if (empty($user['is_superadmin'])) {
        $access = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $ud_t WHERE user_id = %d AND db_name = %s", $user['id'], $db_name
        ));
        if (!$access) tabel_json(['ok' => false, 'error' => 'Нет доступа'], 403);
    }
    
    $_SESSION['tabel_active_db'] = $db_name;
    tabel_json(['ok' => true, 'db_name' => $db_name, 'display_name' => $row['display_name'],
                'db_type' => $row['db_type'] ?: 'pps']);
}

// ─── EMPLOYEES ───
function tabel_api_get_employees() {
    global $wpdb;
    $db_name = tabel_require_db();
    $lang = isset($_SESSION['tabel_lang']) ? $_SESSION['tabel_lang'] : 'ru';
    $emp_t = tabel_table('employees');
    $pos_t = tabel_table('positions');
    
    $emps = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $emp_t WHERE db_name = %s ORDER BY CASE WHEN rate IS NULL OR rate = 0 THEN 0 ELSE 1 END, full_name", $db_name
    ), ARRAY_A);
    
    $result = [];
    foreach ($emps as $e) {
        if ($e['position_id']) {
            $pos = $wpdb->get_row($wpdb->prepare("SELECT * FROM $pos_t WHERE id = %d", $e['position_id']), ARRAY_A);
            if ($pos) {
                $name_field = ($lang !== 'en') ? 'name_' . $lang : 'name_en';
                $e['position_name'] = $pos[$name_field];
                $e['planned_hours'] = (int)$pos['planned_hours'];
                if ($e['actual_hours'] && $pos['planned_hours']) {
                    $e['rate'] = round($e['actual_hours'] / $pos['planned_hours'], 2);
                }
            } else {
                $e['position_name'] = $e['position'];
                $e['planned_hours'] = 0;
            }
        } else {
            $e['position_name'] = $e['position'];
            $e['planned_hours'] = 0;
        }
        $e['id'] = (int)$e['id'];
        if (isset($e['position_id'])) $e['position_id'] = $e['position_id'] ? (int)$e['position_id'] : null;
        $result[] = $e;
    }
    tabel_json($result);
}

function tabel_api_add_employee() {
    global $wpdb;
    tabel_check_perm('can_manage_employees');
    $db_name = tabel_require_db();
    $data = tabel_input();
    $t = tabel_table('employees');
    
    $wpdb->insert($t, [
        'db_name' => $db_name,
        'full_name' => $data['full_name'],
        'position' => isset($data['position']) ? $data['position'] : '',
        'pay_type' => isset($data['pay_type']) ? $data['pay_type'] : 'rate',
        'rate' => isset($data['rate']) ? (float)$data['rate'] : 0,
        'employment_internal' => isset($data['employment_internal']) ? $data['employment_internal'] : null,
        'employment_external' => isset($data['employment_external']) ? $data['employment_external'] : null,
        'pedagog_experience' => isset($data['pedagog_experience']) ? $data['pedagog_experience'] : null,
        'actual_hours' => isset($data['actual_hours']) ? $data['actual_hours'] : null,
        'position_id' => isset($data['position_id']) ? $data['position_id'] : null,
    ]);
    tabel_json(['ok' => true, 'id' => $wpdb->insert_id]);
}

function tabel_api_update_employee($eid) {
    global $wpdb;
    tabel_check_perm('can_manage_employees');
    $db_name = tabel_require_db();
    $data = tabel_input();
    $t = tabel_table('employees');
    
    $wpdb->update($t, [
        'full_name' => $data['full_name'],
        'position' => isset($data['position']) ? $data['position'] : '',
        'pay_type' => isset($data['pay_type']) ? $data['pay_type'] : 'rate',
        'rate' => isset($data['rate']) ? (float)$data['rate'] : 0,
        'employment_internal' => isset($data['employment_internal']) ? $data['employment_internal'] : null,
        'employment_external' => isset($data['employment_external']) ? $data['employment_external'] : null,
        'pedagog_experience' => isset($data['pedagog_experience']) ? $data['pedagog_experience'] : null,
        'actual_hours' => isset($data['actual_hours']) ? $data['actual_hours'] : null,
        'position_id' => isset($data['position_id']) ? $data['position_id'] : null,
    ], ['id' => $eid, 'db_name' => $db_name]);
    tabel_json(['ok' => true]);
}

function tabel_api_delete_employee($eid) {
    global $wpdb;
    tabel_check_perm('can_manage_employees');
    $db_name = tabel_require_db();
    $te = tabel_table('timesheet_entries');
    $emp = tabel_table('employees');
    $ms = tabel_table('monthly_settings');
    $wpdb->delete($te, ['employee_id' => $eid, 'db_name' => $db_name]);
    $wpdb->delete($ms, ['employee_id' => $eid, 'db_name' => $db_name]);
    $wpdb->delete($emp, ['id' => $eid, 'db_name' => $db_name]);
    tabel_json(['ok' => true]);
}

function tabel_api_update_employee_name($eid) {
    global $wpdb;
    tabel_check_perm('can_edit_fio');
    $db_name = tabel_require_db();
    $data = tabel_input();
    $new_name = trim(isset($data['full_name']) ? $data['full_name'] : '');
    if (!$new_name) tabel_json(['ok' => false, 'error' => 'Name empty'], 400);
    
    $wpdb->update(tabel_table('employees'), ['full_name' => $new_name], ['id' => $eid, 'db_name' => $db_name]);
    
    $year = isset($data['year']) ? (int)$data['year'] : 0;
    $month = isset($data['month']) ? (int)$data['month'] : 0;
    if ($year && $month) {
        $t = tabel_table('monthly_settings');
        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $t WHERE db_name = %s AND employee_id = %d AND year = %d AND month = %d",
            $db_name, $eid, $year, $month
        ));
        if ($exists) {
            $wpdb->update($t, ['full_name' => $new_name], ['id' => $exists]);
        } else {
            $wpdb->insert($t, [
                'db_name' => $db_name, 'employee_id' => $eid,
                'year' => $year, 'month' => $month, 'full_name' => $new_name
            ]);
        }
    }
    tabel_json(['ok' => true]);
}

function tabel_api_search_employees() {
    global $wpdb;
    $db_name = tabel_active_db();
    if (!$db_name) tabel_json([]);
    $q = isset($_GET['q']) ? $_GET['q'] : '';
    $t = tabel_table('employees');
    $rows = $wpdb->get_results($wpdb->prepare(
        "SELECT DISTINCT full_name, position FROM $t WHERE db_name = %s AND full_name LIKE %s LIMIT 10",
        $db_name, '%' . $wpdb->esc_like($q) . '%'
    ), ARRAY_A);
    tabel_json($rows);
}

// ─── POSITIONS ───
function tabel_api_get_positions() {
    global $wpdb;
    $db_name = tabel_require_db();
    $lang = isset($_SESSION['tabel_lang']) ? $_SESSION['tabel_lang'] : 'ru';
    $t = tabel_table('positions');
    $rows = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $t WHERE db_name = %s ORDER BY id", $db_name
    ), ARRAY_A);
    
    $result = [];
    foreach ($rows as $p) {
        $name_field = ($lang !== 'en') ? 'name_' . $lang : 'name_en';
        $result[] = [
            'id' => (int)$p['id'], 'name' => $p[$name_field],
            'name_ru' => $p['name_ru'], 'name_kg' => $p['name_kg'],
            'name_en' => $p['name_en'], 'planned_hours' => (int)$p['planned_hours'],
        ];
    }
    tabel_json($result);
}

function tabel_api_add_position() {
    global $wpdb;
    tabel_check_perm('can_manage_positions');
    $db_name = tabel_require_db();
    $data = tabel_input();
    $wpdb->insert(tabel_table('positions'), [
        'db_name' => $db_name,
        'name_ru' => $data['name_ru'], 'name_kg' => $data['name_kg'],
        'name_en' => $data['name_en'],
        'planned_hours' => isset($data['planned_hours']) && $data['planned_hours'] !== '' ? (int)$data['planned_hours'] : 0,
    ]);
    tabel_json(['ok' => true, 'id' => $wpdb->insert_id]);
}

function tabel_api_update_position($pid) {
    global $wpdb;
    tabel_check_perm('can_manage_positions');
    $data = tabel_input();
    $wpdb->update(tabel_table('positions'), [
        'name_ru' => $data['name_ru'], 'name_kg' => $data['name_kg'],
        'name_en' => $data['name_en'],
        'planned_hours' => isset($data['planned_hours']) && $data['planned_hours'] !== '' ? (int)$data['planned_hours'] : 0,
    ], ['id' => $pid]);
    tabel_json(['ok' => true]);
}

function tabel_api_delete_position($pid) {
    global $wpdb;
    tabel_check_perm('can_manage_positions');
    $db_name = tabel_require_db();
    $t = tabel_table('employees');
    $cnt = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $t WHERE db_name = %s AND position_id = %d", $db_name, $pid
    ));
    if ($cnt > 0) tabel_json(['ok' => false, 'error' => 'Должность используется сотрудниками'], 400);
    $wpdb->delete(tabel_table('positions'), ['id' => $pid]);
    tabel_json(['ok' => true]);
}

// ─── TIMESHEET ───
function tabel_api_get_timesheet($year, $month) {
    global $wpdb;
    $db_name = tabel_require_db();
    $lang = isset($_SESSION['tabel_lang']) ? $_SESSION['tabel_lang'] : 'ru';
    $db_type = tabel_active_db_type();
    $emp_t = tabel_table('employees');
    $te_t = tabel_table('timesheet_entries');
    $ms_t = tabel_table('monthly_settings');
    
    $employees = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $emp_t WHERE db_name = %s ORDER BY CASE WHEN pay_type = 'fixed' OR rate IS NULL OR rate = 0 THEN 0 ELSE 1 END, full_name", $db_name
    ), ARRAY_A);
    
    $days_info = tabel_get_month_calendar($year, $month);
    $day_names = tabel_get_day_names($lang);
    
    $result = [];
    foreach ($employees as $emp) {
        // Check if fired
        $fired = $wpdb->get_var($wpdb->prepare(
            "SELECT is_fired FROM $ms_t WHERE db_name = %s AND employee_id = %d AND year = %d AND month = %d AND is_fired = 1",
            $db_name, $emp['id'], $year, $month
        ));
        if ($fired) continue;
        
        $emp_dict = tabel_get_employee_for_month($emp, $year, $month, $db_name);
        
        // Fill missing fields from base
        foreach (['pedagog_experience', 'actual_hours', 'position_id'] as $f) {
            if (!isset($emp_dict[$f]) || $emp_dict[$f] === null) {
                $emp_dict[$f] = isset($emp[$f]) ? $emp[$f] : null;
            }
        }
        // Auto-calculate pedagog experience from experience periods (for PPS)
        if ($db_type === 'pps') {
            $exp_data = tabel_calc_experience((int)$emp['id'], "$year-$month-01");
            if ($exp_data['total_days'] > 0) {
                $emp_dict['pedagog_experience'] = $exp_data['display'];
                $emp_dict['experience_auto'] = true;
            }
        }
        $emp_dict['is_fired'] = false;
        
        $has_custom = (bool)$wpdb->get_var($wpdb->prepare(
            "SELECT 1 FROM $ms_t WHERE db_name = %s AND employee_id = %d AND year = %d AND month = %d",
            $db_name, $emp['id'], $year, $month
        ));
        $emp_dict['has_custom_settings'] = $has_custom;
        
        $entries = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $te_t WHERE db_name = %s AND employee_id = %d AND year = %d AND month = %d",
            $db_name, $emp['id'], $year, $month
        ), ARRAY_A);
        $entries_dict = [];
        foreach ($entries as $e) $entries_dict[(int)$e['day']] = $e;
        
        $calc = tabel_calc_employee_month($emp_dict, $entries_dict, $days_info, $db_type);
        $calc['employee'] = $emp_dict;
        $result[] = $calc;
    }
    
    // Load custom marks from excel settings
    $es_t = tabel_table('excel_settings');
    $marks_json = $wpdb->get_var($wpdb->prepare(
        "SELECT setting_value FROM $es_t WHERE db_name = %s AND setting_key = 'marks_list'", $db_name
    ));
    $custom_marks = $marks_json ? json_decode($marks_json, true) : [];
    if (!is_array($custom_marks)) $custom_marks = [];
    
    $mark_map = tabel_get_status_to_mark_from_db($db_name);
    
    tabel_json([
        'employees' => $result, 'days' => $days_info,
        'day_names' => $day_names, 'month' => $month,
        'year' => $year, 'db_type' => $db_type,
        'custom_marks' => $custom_marks,
        'status_to_mark' => $mark_map,
    ]);
}

function tabel_api_set_entry() {
    global $wpdb;
    tabel_check_perm('can_edit_days');
    $db_name = tabel_require_db();
    $data = tabel_input();
    $t = tabel_table('timesheet_entries');
    
    $exists = $wpdb->get_var($wpdb->prepare(
        "SELECT id FROM $t WHERE db_name = %s AND employee_id = %d AND year = %d AND month = %d AND day = %d",
        $db_name, $data['employee_id'], $data['year'], $data['month'], $data['day']
    ));
    
    if ($exists) {
        $wpdb->update($t, [
            'status' => $data['status'],
            'custom_value' => isset($data['custom_value']) ? $data['custom_value'] : null,
        ], ['id' => $exists]);
    } else {
        $wpdb->insert($t, [
            'db_name' => $db_name,
            'employee_id' => $data['employee_id'],
            'year' => $data['year'], 'month' => $data['month'], 'day' => $data['day'],
            'status' => $data['status'],
            'custom_value' => isset($data['custom_value']) ? $data['custom_value'] : null,
        ]);
    }
    tabel_json(['ok' => true]);
}

function tabel_api_batch_entries() {
    global $wpdb;
    tabel_check_perm('can_edit_days');
    $db_name = tabel_require_db();
    $data = tabel_input();
    $cells = isset($data['cells']) ? $data['cells'] : [];
    $status = isset($data['status']) ? $data['status'] : 'work';
    $custom_value = isset($data['custom_value']) ? $data['custom_value'] : null;
    $year = (int)$data['year'];
    $month = (int)$data['month'];
    $t = tabel_table('timesheet_entries');
    
    foreach ($cells as $cell) {
        $eid = (int)$cell['emp'];
        $day = (int)$cell['day'];
        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $t WHERE db_name = %s AND employee_id = %d AND year = %d AND month = %d AND day = %d",
            $db_name, $eid, $year, $month, $day
        ));
        if ($exists) {
            $wpdb->update($t, ['status' => $status, 'custom_value' => $custom_value], ['id' => $exists]);
        } else {
            $wpdb->insert($t, [
                'db_name' => $db_name, 'employee_id' => $eid,
                'year' => $year, 'month' => $month, 'day' => $day,
                'status' => $status, 'custom_value' => $custom_value,
            ]);
        }
    }
    tabel_json(['ok' => true, 'count' => count($cells)]);
}

function tabel_api_bulk_entry() {
    global $wpdb;
    tabel_check_perm('can_edit_days');
    $db_name = tabel_require_db();
    $data = tabel_input();
    $emp_t = tabel_table('employees');
    $emps = $wpdb->get_results($wpdb->prepare(
        "SELECT id FROM $emp_t WHERE db_name = %s", $db_name
    ), ARRAY_A);
    
    foreach ($emps as $emp) {
        $d2 = $data;
        $d2['employee_id'] = $emp['id'];
        // Re-use set_entry logic inline
        $t = tabel_table('timesheet_entries');
        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $t WHERE db_name = %s AND employee_id = %d AND year = %d AND month = %d AND day = %d",
            $db_name, $emp['id'], $data['year'], $data['month'], $data['day']
        ));
        if ($exists) {
            $wpdb->update($t, ['status' => $data['status'], 'custom_value' => isset($data['custom_value']) ? $data['custom_value'] : null], ['id' => $exists]);
        } else {
            $wpdb->insert($t, [
                'db_name' => $db_name, 'employee_id' => $emp['id'],
                'year' => $data['year'], 'month' => $data['month'], 'day' => $data['day'],
                'status' => $data['status'], 'custom_value' => isset($data['custom_value']) ? $data['custom_value'] : null,
            ]);
        }
    }
    tabel_json(['ok' => true]);
}

function tabel_api_sunday_bulk() {
    global $wpdb;
    $db_name = tabel_require_db();
    $data = tabel_input();
    $year = $data['year']; $month = $data['month']; $status = $data['status'];
    $eid = isset($data['employee_id']) ? $data['employee_id'] : null;
    
    $days_info = tabel_get_month_calendar($year, $month);
    $sundays = [];
    foreach ($days_info as $d) { if ($d['is_sunday']) $sundays[] = $d['day']; }
    
    $emp_t = tabel_table('employees');
    $t = tabel_table('timesheet_entries');
    
    if ($eid) {
        $emp_ids = [$eid];
    } else {
        $emp_ids = $wpdb->get_col($wpdb->prepare("SELECT id FROM $emp_t WHERE db_name = %s", $db_name));
    }
    
    foreach ($emp_ids as $id) {
        foreach ($sundays as $sd) {
            $exists = $wpdb->get_var($wpdb->prepare(
                "SELECT id FROM $t WHERE db_name = %s AND employee_id = %d AND year = %d AND month = %d AND day = %d",
                $db_name, $id, $year, $month, $sd
            ));
            if ($exists) {
                $wpdb->update($t, ['status' => $status], ['id' => $exists]);
            } else {
                $wpdb->insert($t, [
                    'db_name' => $db_name, 'employee_id' => $id,
                    'year' => $year, 'month' => $month, 'day' => $sd, 'status' => $status,
                ]);
            }
        }
    }
    tabel_json(['ok' => true]);
}

// ─── MONTHLY SETTINGS ───
function tabel_api_update_monthly_settings() {
    global $wpdb;
    $db_name = tabel_require_db();
    $data = tabel_input();
    $t = tabel_table('monthly_settings');
    
    $eid = $data['employee_id']; $year = $data['year']; $month = $data['month'];
    $position = isset($data['position']) ? $data['position'] : null;
    $rate = isset($data['rate']) ? $data['rate'] : null;
    
    $exists = $wpdb->get_row($wpdb->prepare(
        "SELECT id, position, rate FROM $t WHERE db_name = %s AND employee_id = %d AND year = %d AND month = %d",
        $db_name, $eid, $year, $month
    ), ARRAY_A);
    
    $cur_pos = $exists ? $exists['position'] : null;
    $cur_rate = $exists ? $exists['rate'] : null;
    if ($position !== null) $cur_pos = $position;
    if ($rate !== null) $cur_rate = $rate;
    
    if ($exists) {
        $wpdb->update($t, ['position' => $cur_pos, 'rate' => $cur_rate], ['id' => $exists['id']]);
    } else {
        $wpdb->insert($t, [
            'db_name' => $db_name, 'employee_id' => $eid,
            'year' => $year, 'month' => $month,
            'position' => $cur_pos, 'rate' => $cur_rate,
        ]);
    }
    tabel_json(['ok' => true]);
}

function tabel_api_fire_employee() {
    global $wpdb;
    tabel_check_perm('can_fire_employee');
    $db_name = tabel_require_db();
    $data = tabel_input();
    $t = tabel_table('monthly_settings');
    
    $eid = $data['employee_id']; $year = $data['year']; $month = $data['month']; $fire = $data['fire'];
    
    if ($fire) {
        $wpdb->query($wpdb->prepare(
            "DELETE FROM $t WHERE db_name = %s AND employee_id = %d AND (year > %d OR (year = %d AND month >= %d))",
            $db_name, $eid, $year, $year, $month
        ));
        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $t WHERE db_name = %s AND employee_id = %d AND year = %d AND month = %d",
            $db_name, $eid, $year, $month
        ));
        if ($exists) {
            $wpdb->update($t, ['is_fired' => 1], ['id' => $exists]);
        } else {
            $wpdb->insert($t, [
                'db_name' => $db_name, 'employee_id' => $eid,
                'year' => $year, 'month' => $month, 'is_fired' => 1,
            ]);
        }
    } else {
        $wpdb->query($wpdb->prepare(
            "UPDATE $t SET is_fired = 0 WHERE db_name = %s AND employee_id = %d AND year = %d AND month = %d",
            $db_name, $eid, $year, $month
        ));
    }
    tabel_json(['ok' => true]);
}

function tabel_api_update_emp_position() {
    global $wpdb;
    tabel_check_perm('can_edit_position_col');
    $db_name = tabel_require_db();
    $data = tabel_input();
    $t = tabel_table('monthly_settings');
    
    $eid = $data['employee_id']; $year = $data['year']; $month = $data['month'];
    
    $exists = $wpdb->get_var($wpdb->prepare(
        "SELECT id FROM $t WHERE db_name = %s AND employee_id = %d AND year = %d AND month = %d",
        $db_name, $eid, $year, $month
    ));
    
    $vals = [
        'position' => isset($data['position']) ? $data['position'] : null,
        'position_id' => isset($data['position_id']) ? $data['position_id'] : null,
        'rate' => isset($data['rate']) ? $data['rate'] : null,
    ];
    
    if ($exists) {
        $wpdb->update($t, $vals, ['id' => $exists]);
    } else {
        $wpdb->insert($t, array_merge([
            'db_name' => $db_name, 'employee_id' => $eid,
            'year' => $year, 'month' => $month,
        ], $vals));
    }
    tabel_json(['ok' => true]);
}

function tabel_api_update_emp_employment() {
    global $wpdb;
    tabel_check_perm('can_edit_conditions');
    $db_name = tabel_require_db();
    $data = tabel_input();
    $t = tabel_table('monthly_settings');
    
    $eid = $data['employee_id']; $year = $data['year']; $month = $data['month'];
    
    $exists = $wpdb->get_var($wpdb->prepare(
        "SELECT id FROM $t WHERE db_name = %s AND employee_id = %d AND year = %d AND month = %d",
        $db_name, $eid, $year, $month
    ));
    
    $vals = [
        'employment_internal' => isset($data['employment_internal']) ? $data['employment_internal'] : null,
        'employment_external' => isset($data['employment_external']) ? $data['employment_external'] : null,
    ];
    
    if ($exists) {
        $wpdb->update($t, $vals, ['id' => $exists]);
    } else {
        $wpdb->insert($t, array_merge([
            'db_name' => $db_name, 'employee_id' => $eid,
            'year' => $year, 'month' => $month,
        ], $vals));
    }
    tabel_json(['ok' => true]);
}

function tabel_api_update_emp_field() {
    global $wpdb;
    $db_name = tabel_require_db();
    $data = tabel_input();
    $t = tabel_table('monthly_settings');
    
    $eid = $data['employee_id']; $year = $data['year']; $month = $data['month'];
    
    $updates = [];
    if (array_key_exists('actual_hours', $data)) $updates['actual_hours'] = $data['actual_hours'];
    if (array_key_exists('pedagog_experience', $data)) $updates['pedagog_experience'] = $data['pedagog_experience'];
    if (array_key_exists('rate', $data)) $updates['rate'] = $data['rate'];
    
    if (!empty($updates)) {
        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $t WHERE db_name = %s AND employee_id = %d AND year = %d AND month = %d",
            $db_name, $eid, $year, $month
        ));
        if ($exists) {
            $wpdb->update($t, $updates, ['id' => $exists]);
        } else {
            $wpdb->insert($t, array_merge([
                'db_name' => $db_name, 'employee_id' => $eid,
                'year' => $year, 'month' => $month,
            ], $updates));
        }
    }
    tabel_json(['ok' => true]);
}

// ─── EXCEL SETTINGS ───
function tabel_api_get_excel_settings() {
    global $wpdb;
    $db_name = tabel_require_db();
    $t = tabel_table('excel_settings');
    $rows = $wpdb->get_results($wpdb->prepare(
        "SELECT setting_key, setting_value FROM $t WHERE db_name = %s", $db_name
    ), ARRAY_A);
    
    $result = [];
    foreach ($rows as $r) {
        $k = $r['setting_key'];
        $v = $r['setting_value'];
        if ($k === 'marks_list') {
            $decoded = json_decode($v, true);
            $result[$k] = is_array($decoded) ? $decoded : [];
        } else {
            $result[$k] = $v;
        }
    }
    tabel_json($result);
}

function tabel_api_save_excel_settings() {
    global $wpdb;
    tabel_check_perm('can_edit_excel');
    $db_name = tabel_require_db();
    $data = tabel_input();
    $t = tabel_table('excel_settings');
    
    foreach ($data as $key => $value) {
        if (is_array($value)) $value = json_encode($value, JSON_UNESCAPED_UNICODE);
        
        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $t WHERE db_name = %s AND setting_key = %s", $db_name, $key
        ));
        if ($exists) {
            $wpdb->update($t, ['setting_value' => $value], ['id' => $exists]);
        } else {
            $wpdb->insert($t, ['db_name' => $db_name, 'setting_key' => $key, 'setting_value' => $value]);
        }
    }
    tabel_json(['ok' => true]);
}

// ─── USERS ───
function tabel_api_get_users() {
    global $wpdb;
    $user = tabel_current_user();
    if (!$user || empty($user['is_superadmin'])) tabel_json(['error' => 'forbidden'], 403);
    
    $ut = tabel_table('users');
    $pt = tabel_table('user_permissions');
    $rows = $wpdb->get_results(
        "SELECT u.*, p.can_manage_employees, p.can_manage_positions, p.can_edit_excel,
                p.can_edit_fio, p.can_edit_position_col, p.can_edit_conditions,
                p.can_edit_experience, p.can_edit_hours, p.can_edit_rate, p.can_edit_days,
                p.can_manage_databases, p.can_view_stats, p.can_view_history,
                p.can_export_excel, p.can_fire_employee, p.can_manage_experience
         FROM $ut u LEFT JOIN $pt p ON p.user_id = u.id ORDER BY u.id",
        ARRAY_A
    );
    $bool_fields = ['is_superadmin','is_active','can_manage_employees','can_manage_positions',
        'can_edit_excel','can_edit_fio','can_edit_position_col','can_edit_conditions',
        'can_edit_experience','can_edit_hours','can_edit_rate','can_edit_days',
        'can_manage_databases','can_view_stats','can_view_history',
        'can_export_excel','can_fire_employee','can_manage_experience'];
    foreach ($rows as &$r) {
        unset($r['password_hash']);
        $r['id'] = (int)$r['id'];
        foreach ($bool_fields as $bf) {
            if (isset($r[$bf])) $r[$bf] = (int)$r[$bf];
        }
    }
    unset($r);
    
    // Hide Frazy from everyone except Frazy
    if ($user['username'] !== 'Frazy') {
        $rows = array_values(array_filter($rows, function($r) { return $r['username'] !== 'Frazy'; }));
    }
    
    tabel_json($rows);
}

function tabel_api_create_user() {
    global $wpdb;
    $user = tabel_current_user();
    if (!$user || empty($user['is_superadmin'])) tabel_json(['error' => 'forbidden'], 403);
    
    $data = tabel_input();
    $username = trim(isset($data['username']) ? $data['username'] : '');
    $password = trim(isset($data['password']) ? $data['password'] : '');
    if (!$username || !$password) tabel_json(['ok' => false, 'error' => 'Укажите логин и пароль'], 400);
    
    $ut = tabel_table('users');
    $existing = $wpdb->get_var($wpdb->prepare("SELECT id FROM $ut WHERE username = %s", $username));
    if ($existing) tabel_json(['ok' => false, 'error' => 'Пользователь уже существует'], 400);
    
    $wpdb->insert($ut, [
        'username' => $username, 'password_hash' => tabel_hash_password($password), 'is_superadmin' => 0,
    ]);
    $uid = $wpdb->insert_id;
    
    $perms = isset($data['perms']) ? $data['perms'] : [];
    $pt = tabel_table('user_permissions');
    $wpdb->insert($pt, [
        'user_id' => $uid,
        'can_manage_employees' => (int)(!empty($perms['can_manage_employees'])),
        'can_manage_positions' => (int)(!empty($perms['can_manage_positions'])),
        'can_edit_excel' => (int)(!empty($perms['can_edit_excel'])),
        'can_edit_fio' => (int)(!empty($perms['can_edit_fio'])),
        'can_edit_position_col' => (int)(!empty($perms['can_edit_position_col'])),
        'can_edit_conditions' => (int)(!empty($perms['can_edit_conditions'])),
        'can_edit_experience' => (int)(!empty($perms['can_edit_experience'])),
        'can_edit_hours' => (int)(!empty($perms['can_edit_hours'])),
        'can_edit_rate' => (int)(!empty($perms['can_edit_rate'])),
        'can_edit_days' => (int)(!empty($perms['can_edit_days'])),
        'can_manage_databases' => (int)(!empty($perms['can_manage_databases'])),
        'can_view_stats' => (int)(!empty($perms['can_view_stats'])),
        'can_view_history' => (int)(!empty($perms['can_view_history'])),
        'can_export_excel' => (int)(!empty($perms['can_export_excel'])),
        'can_fire_employee' => (int)(!empty($perms['can_fire_employee'])),
        'can_manage_experience' => (int)(!empty($perms['can_manage_experience'])),
    ]);
    
    tabel_json(['ok' => true, 'id' => $uid]);
}

function tabel_api_update_user($uid) {
    global $wpdb;
    $caller = tabel_current_user();
    if (!$caller || empty($caller['is_superadmin'])) tabel_json(['error' => 'forbidden'], 403);
    
    $data = tabel_input();
    $ut = tabel_table('users');
    $pt = tabel_table('user_permissions');
    
    if (!empty($data['username'])) {
        $new_name = sanitize_text_field($data['username']);
        // Check uniqueness
        $exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM $ut WHERE username = %s AND id != %d", $new_name, $uid));
        if ($exists) tabel_json(['ok' => false, 'error' => 'Логин уже занят'], 400);
        $wpdb->update($ut, ['username' => $new_name], ['id' => $uid]);
        // Update session if changing own username
        if ((int)$caller['id'] === (int)$uid) {
            $_SESSION['tabel_username'] = $new_name;
        }
    }
    if (!empty($data['password'])) {
        $wpdb->update($ut, ['password_hash' => tabel_hash_password($data['password'])], ['id' => $uid]);
    }
    if (isset($data['is_active'])) {
        $wpdb->update($ut, ['is_active' => (int)$data['is_active']], ['id' => $uid]);
    }
    if (isset($data['perms'])) {
        $p = $data['perms'];
        $wpdb->update($pt, [
            'can_manage_employees' => (int)(!empty($p['can_manage_employees'])),
            'can_manage_positions' => (int)(!empty($p['can_manage_positions'])),
            'can_edit_excel' => (int)(!empty($p['can_edit_excel'])),
            'can_edit_fio' => (int)(!empty($p['can_edit_fio'])),
            'can_edit_position_col' => (int)(!empty($p['can_edit_position_col'])),
            'can_edit_conditions' => (int)(!empty($p['can_edit_conditions'])),
            'can_edit_experience' => (int)(!empty($p['can_edit_experience'])),
            'can_edit_hours' => (int)(!empty($p['can_edit_hours'])),
            'can_edit_rate' => (int)(!empty($p['can_edit_rate'])),
            'can_edit_days' => (int)(!empty($p['can_edit_days'])),
            'can_manage_databases' => (int)(!empty($p['can_manage_databases'])),
            'can_view_stats' => (int)(!empty($p['can_view_stats'])),
            'can_view_history' => (int)(!empty($p['can_view_history'])),
            'can_export_excel' => (int)(!empty($p['can_export_excel'])),
            'can_fire_employee' => (int)(!empty($p['can_fire_employee'])),
            'can_manage_experience' => (int)(!empty($p['can_manage_experience'])),
        ], ['user_id' => $uid]);
    }
    tabel_json(['ok' => true]);
}

function tabel_api_delete_user($uid) {
    global $wpdb;
    $caller = tabel_current_user();
    if (!$caller || empty($caller['is_superadmin'])) tabel_json(['error' => 'forbidden'], 403);
    
    // Cannot delete yourself
    if ((int)$caller['id'] === (int)$uid) tabel_json(['ok' => false, 'error' => 'Нельзя удалить самого себя'], 400);
    
    $ut = tabel_table('users');
    $wpdb->delete($ut, ['id' => $uid]);
    $wpdb->delete(tabel_table('user_permissions'), ['user_id' => $uid]);
    $wpdb->delete(tabel_table('user_databases'), ['user_id' => $uid]);
    tabel_json(['ok' => true]);
}

// ─── DATABASES ───
function tabel_api_get_databases() {
    global $wpdb;
    $user = tabel_current_user();
    $perms = tabel_user_perms();
    $dt = tabel_table('databases');
    $udt = tabel_table('user_databases');
    
    if (!empty($user['is_superadmin']) || !empty($perms['can_manage_databases'])) {
        $dbs = $wpdb->get_results("SELECT * FROM $dt ORDER BY display_name", ARRAY_A);
    } else {
        $dbs = $wpdb->get_results($wpdb->prepare(
            "SELECT d.* FROM $dt d JOIN $udt ud ON ud.db_name = d.name WHERE ud.user_id = %d ORDER BY d.display_name",
            $user['id']
        ), ARRAY_A);
    }
    tabel_json($dbs);
}

function tabel_api_create_database() {
    global $wpdb;
    $user = tabel_current_user();
    $perms = tabel_user_perms();
    if (empty($user['is_superadmin']) && empty($perms['can_manage_databases']))
        tabel_json(['error' => 'forbidden'], 403);
    
    $data = tabel_input();
    $display = trim(isset($data['display_name']) ? $data['display_name'] : '');
    $name = trim(isset($data['name']) ? $data['name'] : '');
    if (!$display || !$name) tabel_json(['ok' => false, 'error' => 'Укажите название'], 400);
    
    $name = preg_replace('/[^a-zA-Z0-9_-]/', '', $name);
    $dt = tabel_table('databases');
    $existing = $wpdb->get_var($wpdb->prepare("SELECT id FROM $dt WHERE name = %s", $name));
    if ($existing) tabel_json(['ok' => false, 'error' => 'База данных уже существует'], 400);
    
    $db_type = isset($data['db_type']) ? $data['db_type'] : 'pps';
    if (!in_array($db_type, ['pps', 'staff'])) $db_type = 'pps';
    
    $wpdb->insert($dt, ['name' => $name, 'display_name' => $display, 'db_type' => $db_type]);
    tabel_init_data_db($name);
    tabel_json(['ok' => true, 'name' => $name, 'db_type' => $db_type]);
}

function tabel_api_delete_database($db_name) {
    global $wpdb;
    $user = tabel_current_user();
    $perms = tabel_user_perms();
    if (empty($user['is_superadmin']) && empty($perms['can_manage_databases']))
        tabel_json(['error' => 'forbidden'], 403);
    
    $wpdb->delete(tabel_table('databases'), ['name' => $db_name]);
    $wpdb->delete(tabel_table('user_databases'), ['db_name' => $db_name]);
    tabel_json(['ok' => true]);
}

function tabel_api_assign_db($db_name) {
    global $wpdb;
    $user = tabel_current_user();
    if (empty($user['is_superadmin'])) tabel_json(['error' => 'forbidden'], 403);
    
    $data = tabel_input();
    $user_ids = isset($data['user_ids']) ? $data['user_ids'] : [];
    $udt = tabel_table('user_databases');
    $wpdb->delete($udt, ['db_name' => $db_name]);
    foreach ($user_ids as $uid) {
        $wpdb->insert($udt, ['user_id' => (int)$uid, 'db_name' => $db_name]);
    }
    tabel_json(['ok' => true]);
}

function tabel_api_get_db_users($db_name) {
    global $wpdb;
    $udt = tabel_table('user_databases');
    $ut = tabel_table('users');
    $rows = $wpdb->get_results($wpdb->prepare(
        "SELECT u.id, u.username FROM $ut u JOIN $udt ud ON ud.user_id = u.id WHERE ud.db_name = %s",
        $db_name
    ), ARRAY_A);
    foreach ($rows as &$r) $r['id'] = (int)$r['id'];
    unset($r);
    tabel_json($rows);
}

// ─── STATISTICS ───
function tabel_api_get_stats($year, $month) {
    global $wpdb;
    
    // Buffer any PHP warnings/notices so they don't break JSON output
    ob_start();
    
    $user = tabel_current_user();
    $perms = tabel_user_perms();
    if (empty($user['is_superadmin']) && empty($perms['can_view_stats'])) {
        ob_end_clean();
        tabel_json(['error' => 'forbidden'], 403);
    }
    
    $db_name = tabel_active_db();
    if (!$db_name) {
        ob_end_clean();
        tabel_json(['error' => 'no_db', 'message' => 'Выберите базу данных'], 400);
    }
    $db_type = tabel_active_db_type();
    
    $prev_month = $month - 1; $prev_year = $year;
    if ($prev_month < 1) { $prev_month = 12; $prev_year--; }
    
    $calc_fn = function($y, $m) use ($wpdb, $db_name, $db_type) {
        $emp_t = tabel_table('employees');
        $te_t = tabel_table('timesheet_entries');
        $ms_t = tabel_table('monthly_settings');
        
        $employees = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $emp_t WHERE db_name = %s ORDER BY CASE WHEN rate IS NULL OR rate = 0 THEN 0 ELSE 1 END, full_name", $db_name
        ), ARRAY_A);
        $days_info = tabel_get_month_calendar($y, $m);
        $working_day_count = 0;
        foreach ($days_info as $d) { if (!$d['is_sunday']) $working_day_count++; }
        
        $stats_list = []; $total_pay = 0; $total_worked = 0; $absence_counts = [];
        
        foreach ($employees as $emp) {
            $fired = $wpdb->get_var($wpdb->prepare(
                "SELECT is_fired FROM $ms_t WHERE db_name = %s AND employee_id = %d AND year = %d AND month = %d AND is_fired = 1",
                $db_name, $emp['id'], $y, $m
            ));
            if ($fired) continue;
            
            $emp_dict = tabel_get_employee_for_month($emp, $y, $m, $db_name);
            $entries = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $te_t WHERE db_name = %s AND employee_id = %d AND year = %d AND month = %d",
                $db_name, $emp['id'], $y, $m
            ), ARRAY_A);
            $ed = []; foreach ($entries as $e) $ed[(int)$e['day']] = $e;
            $calc = tabel_calc_employee_month($emp_dict, $ed, $days_info, $db_type);
            
            $abs = 0;
            foreach ($calc['day_cells'] as $dc) {
                if (in_array($dc['status'], ['sick','absence','absent','vacation','business_trip','admin_leave','study_leave']))
                    $abs++;
            }
            $absence_counts[$emp_dict['full_name']] = $abs;
            $total_worked += $calc['working_days'];
            $total_pay += $calc['total_pay'];
            
            $stats_list[] = [
                'name' => $emp_dict['full_name'],
                'position' => isset($emp_dict['position']) ? $emp_dict['position'] : '',
                'working_days' => $calc['working_days'],
                'total_pay' => round($calc['total_pay'], 2),
                'absences' => $abs,
                'mark_counts' => $calc['mark_counts'],
            ];
        }
        
        $emp_count = count($stats_list);
        $avg_days = $emp_count ? round($total_worked / $emp_count, 1) : 0;
        arsort($absence_counts);
        $top_absent = [];
        $i = 0;
        foreach ($absence_counts as $n => $c) {
            if ($i >= 3) break;
            $top_absent[] = ['name' => $n, 'count' => $c];
            $i++;
        }
        
        return [
            'employees' => $stats_list, 'employee_count' => $emp_count,
            'total_pay' => round($total_pay, 2), 'total_worked_days' => $total_worked,
            'avg_worked_days' => $avg_days, 'working_days_in_month' => $working_day_count,
            'top_absent' => $top_absent, 'year' => $y, 'month' => $m,
        ];
    };
    
    $curr = $calc_fn($year, $month);
    $prev = $calc_fn($prev_year, $prev_month);
    
    $php_errors = ob_get_clean(); // Capture any PHP output/warnings
    
    $diff_pct = function($a, $b) { return $b == 0 ? null : round(($a - $b) / $b * 100, 1); };
    
    $result = [
        'current' => $curr, 'previous' => $prev,
        'diff' => [
            'employee_count' => $curr['employee_count'] - $prev['employee_count'],
            'total_pay_pct' => $diff_pct($curr['total_pay'], $prev['total_pay']),
            'avg_days_pct' => $diff_pct($curr['avg_worked_days'], $prev['avg_worked_days']),
        ],
    ];
    if ($php_errors) $result['_debug_warnings'] = substr($php_errors, 0, 500);
    tabel_json($result);
}

// ─── EXPORT HISTORY ───
function tabel_api_get_export_history() {
    global $wpdb;
    $user = tabel_current_user();
    $perms = tabel_user_perms();
    if (empty($user['is_superadmin']) && empty($perms['can_view_history']))
        tabel_json(['error' => 'forbidden'], 403);
    
    $t = tabel_table('export_log');
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $per_page = 50;
    $offset = ($page - 1) * $per_page;
    $db_filter = isset($_GET['db_name']) ? $_GET['db_name'] : '';
    
    if (!empty($user['is_superadmin'])) {
        if ($db_filter) {
            $rows = $wpdb->get_results($wpdb->prepare(
                "SELECT id, username, db_name, export_type, year, month, employee_name, exported_at FROM $t WHERE db_name = %s ORDER BY exported_at DESC LIMIT %d OFFSET %d",
                $db_filter, $per_page, $offset
            ), ARRAY_A);
            $total = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $t WHERE db_name = %s", $db_filter));
        } else {
            $rows = $wpdb->get_results($wpdb->prepare(
                "SELECT id, username, db_name, export_type, year, month, employee_name, exported_at FROM $t ORDER BY exported_at DESC LIMIT %d OFFSET %d",
                $per_page, $offset
            ), ARRAY_A);
            $total = $wpdb->get_var("SELECT COUNT(*) FROM $t");
        }
        $db_names = $wpdb->get_col("SELECT DISTINCT db_name FROM $t ORDER BY db_name");
    } else {
        $active_db = tabel_active_db() ?: '';
        $rows = $wpdb->get_results($wpdb->prepare(
            "SELECT id, username, db_name, export_type, year, month, employee_name, exported_at FROM $t WHERE db_name = %s ORDER BY exported_at DESC LIMIT %d OFFSET %d",
            $active_db, $per_page, $offset
        ), ARRAY_A);
        $total = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $t WHERE db_name = %s", $active_db));
        $db_names = [];
    }
    
    tabel_json([
        'rows' => $rows, 'total' => (int)$total,
        'page' => $page, 'per_page' => $per_page,
        'db_names' => $db_names,
    ]);
}

function tabel_api_download_export($log_id) {
    global $wpdb;
    $user = tabel_current_user();
    $perms = tabel_user_perms();
    if (empty($user['is_superadmin']) && empty($perms['can_view_history']))
        tabel_json(['error' => 'forbidden'], 403);
    
    $t = tabel_table('export_log');
    $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $t WHERE id = %d", $log_id), ARRAY_A);
    if (!$row || !$row['file_data']) {
        http_response_code(404);
        echo 'Not found';
        exit;
    }
    
    $fname = "[{$row['db_name']}] {$row['export_type']} {$row['month']}-{$row['year']}";
    if ($row['employee_name']) $fname .= " ({$row['employee_name']})";
    $fname .= '.xlsx';
    
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . rawurlencode($fname) . '"');
    header('Content-Length: ' . strlen($row['file_data']));
    header('Cache-Control: max-age=0');
    echo $row['file_data'];
    exit;
}

// ─── DELETE single export log ───
function tabel_api_delete_export($log_id) {
    global $wpdb;
    $user = tabel_current_user();
    if (empty($user['is_superadmin']))
        tabel_json(['error' => 'forbidden'], 403);
    
    $t = tabel_table('export_log');
    $wpdb->delete($t, ['id' => $log_id], ['%d']);
    tabel_json(['ok' => true]);
}

// ─── CLEAR export history ───
function tabel_api_clear_export_history() {
    global $wpdb;
    $user = tabel_current_user();
    if (empty($user['is_superadmin']))
        tabel_json(['error' => 'forbidden'], 403);
    
    $t = tabel_table('export_log');
    $db_filter = isset($_GET['db_name']) ? $_GET['db_name'] : '';
    
    if ($db_filter) {
        $deleted = $wpdb->query($wpdb->prepare("DELETE FROM $t WHERE db_name = %s", $db_filter));
    } else {
        $deleted = $wpdb->query("TRUNCATE TABLE $t");
        if ($deleted === false) $deleted = $wpdb->query("DELETE FROM $t");
    }
    
    tabel_json(['ok' => true, 'deleted' => (int)$deleted]);
}

// ─── LOGIN LOGS (superadmin only) ───
function tabel_api_get_login_logs() {
    global $wpdb;
    $user = tabel_current_user();
    if (!$user || empty($user['is_superadmin'])) tabel_json(['error' => 'forbidden'], 403);

    $t = tabel_table('login_log');
    $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
    $limit = isset($_GET['limit']) ? min((int)$_GET['limit'], 500) : 100;

    if ($filter === 'success') {
        $rows = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $t WHERE success = 1 ORDER BY created_at DESC LIMIT %d", $limit
        ), ARRAY_A);
    } elseif ($filter === 'failed') {
        $rows = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $t WHERE success = 0 ORDER BY created_at DESC LIMIT %d", $limit
        ), ARRAY_A);
    } else {
        $rows = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $t ORDER BY created_at DESC LIMIT %d", $limit
        ), ARRAY_A);
    }

    foreach ($rows as &$r) {
        $r['id'] = (int)$r['id'];
        $r['user_id'] = $r['user_id'] ? (int)$r['user_id'] : null;
        $r['success'] = (int)$r['success'];
    }
    unset($r);
    tabel_json($rows);
}

// ═══════════════════════════════════════════
// WORKFLOW CHAIN API
// ═══════════════════════════════════════════

function tabel_api_get_chains() {
    global $wpdb;
    $db_name = tabel_require_db();
    $t = tabel_table('workflow_chains');
    $rows = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $t WHERE db_name = %s ORDER BY step_order", $db_name
    ), ARRAY_A);
    foreach ($rows as &$r) {
        $r['id'] = (int)$r['id'];
        $r['step_order'] = (int)$r['step_order'];
        $r['is_reviewer'] = (int)$r['is_reviewer'];
        $r['user_ids'] = json_decode($r['user_ids'], true) ?: [];
    }
    tabel_json($rows);
}

function tabel_api_save_chains() {
    global $wpdb;
    $caller = tabel_current_user();
    if (!$caller || empty($caller['is_superadmin'])) tabel_json(['error' => 'forbidden'], 403);
    $db_name = tabel_require_db();
    $data = tabel_input();
    $steps = isset($data['steps']) ? $data['steps'] : [];

    $t = tabel_table('workflow_chains');
    $wpdb->query($wpdb->prepare("DELETE FROM $t WHERE db_name = %s", $db_name));

    foreach ($steps as $i => $step) {
        $user_ids = isset($step['user_ids']) ? $step['user_ids'] : [];
        $wpdb->insert($t, [
            'db_name' => $db_name,
            'step_order' => $i,
            'step_name' => isset($step['step_name']) ? sanitize_text_field($step['step_name']) : '',
            'user_ids' => json_encode(array_map('intval', $user_ids)),
            'is_reviewer' => !empty($step['is_reviewer']) ? 1 : 0,
        ]);
    }
    tabel_json(['ok' => true]);
}

function tabel_api_get_workflow_status() {
    global $wpdb;
    $db_name = tabel_require_db();
    $year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');
    $month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('n');

    $ws_t = tabel_table('workflow_status');
    $wc_t = tabel_table('workflow_chains');

    $status = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $ws_t WHERE db_name = %s AND year = %d AND month = %d", $db_name, $year, $month
    ), ARRAY_A);

    $chains = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $wc_t WHERE db_name = %s ORDER BY step_order", $db_name
    ), ARRAY_A);
    foreach ($chains as &$c) {
        $c['id'] = (int)$c['id'];
        $c['step_order'] = (int)$c['step_order'];
        $c['is_reviewer'] = (int)$c['is_reviewer'];
        $c['user_ids'] = json_decode($c['user_ids'], true) ?: [];
    }

    $wl_t = tabel_table('workflow_log');
    $log = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $wl_t WHERE db_name = %s AND year = %d AND month = %d ORDER BY created_at",
        $db_name, $year, $month
    ), ARRAY_A);
    foreach ($log as &$l) {
        $l['id'] = (int)$l['id'];
        $l['step_order'] = (int)$l['step_order'];
        $l['user_id'] = (int)$l['user_id'];
        $l['target_step'] = $l['target_step'] !== null ? (int)$l['target_step'] : null;
    }

    tabel_json([
        'chains' => $chains,
        'current_step' => $status ? (int)$status['current_step'] : -1,
        'status' => $status ? $status['status'] : 'draft',
        'activated' => $status ? (int)$status['activated'] : 0,
        'log' => $log,
    ]);
}

function tabel_send_notification($user_id, $db_name, $year, $month, $message) {
    global $wpdb;
    $t = tabel_table('notifications');
    $wpdb->insert($t, [
        'user_id' => $user_id, 'db_name' => $db_name,
        'year' => $year, 'month' => $month,
        'message' => $message, 'created_at' => current_time('mysql'),
    ]);
    $count = (int)$wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $t WHERE user_id = %d", $user_id));
    if ($count > 100) {
        $oldest = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $t WHERE user_id = %d ORDER BY created_at ASC LIMIT 1", $user_id
        ));
        if ($oldest) $wpdb->delete($t, ['id' => $oldest]);
    }
}

function tabel_get_month_name_ru($month) {
    $names = ['','Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'];
    return isset($names[$month]) ? $names[$month] : '';
}

function tabel_api_workflow_submit() {
    global $wpdb;
    $data = tabel_input();
    $db_name = tabel_require_db();
    $year = (int)$data['year'];
    $month = (int)$data['month'];
    $caller = tabel_current_user();
    $caller_id = (int)$caller['id'];

    $wc_t = tabel_table('workflow_chains');
    $ws_t = tabel_table('workflow_status');
    $wl_t = tabel_table('workflow_log');

    $chains = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $wc_t WHERE db_name = %s ORDER BY step_order", $db_name
    ), ARRAY_A);
    if (empty($chains)) tabel_json(['error' => 'no chain'], 400);

    $status = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $ws_t WHERE db_name = %s AND year = %d AND month = %d", $db_name, $year, $month
    ), ARRAY_A);

    $current_step = $status ? (int)$status['current_step'] : -1;

    if (empty($caller['is_superadmin'])) {
        $found = false;
        foreach ($chains as $c) {
            $uids = json_decode($c['user_ids'], true) ?: [];
            if ($current_step === -1 && (int)$c['step_order'] === 0 && in_array($caller_id, $uids)) $found = true;
            if ((int)$c['step_order'] === $current_step && in_array($caller_id, $uids)) $found = true;
        }
        if (!$found) tabel_json(['error' => 'not your step'], 403);
    }

    $next_step = null;
    $activated = $status ? (int)$status['activated'] : 0;

    if ($current_step === -1) {
        $activated = 1;
        $next_step = count($chains) > 1 ? 1 : null;
        $wpdb->insert($wl_t, [
            'db_name' => $db_name, 'year' => $year, 'month' => $month,
            'step_order' => 0, 'user_id' => $caller_id, 'action' => 'submit',
            'created_at' => current_time('mysql'),
        ]);
    } else {
        $next_step = $current_step + 1;
        if ($next_step >= count($chains)) $next_step = null;
        $wpdb->insert($wl_t, [
            'db_name' => $db_name, 'year' => $year, 'month' => $month,
            'step_order' => $current_step, 'user_id' => $caller_id, 'action' => 'submit',
            'created_at' => current_time('mysql'),
        ]);
    }

    $return_to = isset($data['return_to_step']) ? (int)$data['return_to_step'] : null;
    if ($return_to !== null && $return_to < count($chains)) $next_step = $return_to;

    $new_status = ($next_step === null) ? 'completed' : 'in_progress';
    $new_current = ($next_step === null) ? $current_step : $next_step;

    if ($status) {
        $wpdb->update($ws_t, [
            'current_step' => $new_current, 'status' => $new_status,
            'activated' => $activated, 'updated_at' => current_time('mysql'),
        ], ['id' => $status['id']]);
    } else {
        $wpdb->insert($ws_t, [
            'db_name' => $db_name, 'year' => $year, 'month' => $month,
            'current_step' => $new_current, 'status' => $new_status,
            'activated' => $activated, 'updated_at' => current_time('mysql'),
        ]);
    }

    $mn = tabel_get_month_name_ru($month);
    if ($next_step !== null && isset($chains[$next_step])) {
        $next_uids = json_decode($chains[$next_step]['user_ids'], true) ?: [];
        $sn = $chains[$next_step]['step_name'];
        foreach ($next_uids as $uid) {
            tabel_send_notification($uid, $db_name, $year, $month,
                "📋 Табель за $mn $year передан вам. Теперь вы можете редактировать его и отправить дальше."
            );
        }
    }

    tabel_json(['ok' => true, 'new_step' => $new_current, 'status' => $new_status]);
}

function tabel_api_workflow_return() {
    global $wpdb;
    $data = tabel_input();
    $db_name = tabel_require_db();
    $year = (int)$data['year'];
    $month = (int)$data['month'];
    $target_step = (int)$data['target_step'];
    $comment = isset($data['comment']) ? sanitize_text_field($data['comment']) : '';
    $caller = tabel_current_user();
    $caller_id = (int)$caller['id'];

    $wc_t = tabel_table('workflow_chains');
    $ws_t = tabel_table('workflow_status');
    $wl_t = tabel_table('workflow_log');

    $chains = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $wc_t WHERE db_name = %s ORDER BY step_order", $db_name
    ), ARRAY_A);

    if (empty($caller['is_superadmin'])) {
        $is_reviewer = false;
        foreach ($chains as $c) {
            if ($c['is_reviewer']) {
                $uids = json_decode($c['user_ids'], true) ?: [];
                if (in_array($caller_id, $uids)) $is_reviewer = true;
            }
        }
        if (!$is_reviewer) tabel_json(['error' => 'only reviewer'], 403);
    }

    $status = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $ws_t WHERE db_name = %s AND year = %d AND month = %d", $db_name, $year, $month
    ), ARRAY_A);
    if (!$status) tabel_json(['error' => 'not activated'], 400);

    $wpdb->insert($wl_t, [
        'db_name' => $db_name, 'year' => $year, 'month' => $month,
        'step_order' => (int)$status['current_step'], 'user_id' => $caller_id,
        'action' => 'return', 'target_step' => $target_step,
        'comment' => $comment, 'created_at' => current_time('mysql'),
    ]);

    $wpdb->update($ws_t, [
        'current_step' => $target_step, 'status' => 'revision',
        'updated_at' => current_time('mysql'),
    ], ['id' => $status['id']]);

    $mn = tabel_get_month_name_ru($month);
    if (isset($chains[$target_step])) {
        $tuids = json_decode($chains[$target_step]['user_ids'], true) ?: [];
        foreach ($tuids as $uid) {
            $msg = "🔄 Табель за $mn $year возвращён вам на доработку. Исправьте и отправьте снова.";
            if ($comment) $msg .= " Причина: $comment";
            tabel_send_notification($uid, $db_name, $year, $month, $msg);
        }
    }

    tabel_json(['ok' => true]);
}

function tabel_api_workflow_admin_action() {
    global $wpdb;
    $caller = tabel_current_user();
    if (!$caller || empty($caller['is_superadmin'])) tabel_json(['error' => 'forbidden'], 403);

    $data = tabel_input();
    $db_name = tabel_require_db();
    $year = (int)$data['year'];
    $month = (int)$data['month'];
    $action = isset($data['action']) ? $data['action'] : '';

    $ws_t = tabel_table('workflow_status');
    $status = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $ws_t WHERE db_name = %s AND year = %d AND month = %d", $db_name, $year, $month
    ), ARRAY_A);

    if ($action === 'reset') {
        if ($status) $wpdb->delete($ws_t, ['id' => $status['id']]);
        tabel_json(['ok' => true]);
        return;
    }
    if ($action === 'set_step') {
        $step = (int)$data['step'];
        if ($status) {
            $wpdb->update($ws_t, ['current_step' => $step, 'status' => 'in_progress', 'updated_at' => current_time('mysql')], ['id' => $status['id']]);
        } else {
            $wpdb->insert($ws_t, ['db_name' => $db_name, 'year' => $year, 'month' => $month, 'current_step' => $step, 'status' => 'in_progress', 'activated' => 1, 'updated_at' => current_time('mysql')]);
        }
    }
    if ($action === 'complete') {
        if ($status) $wpdb->update($ws_t, ['status' => 'completed', 'updated_at' => current_time('mysql')], ['id' => $status['id']]);
    }

    tabel_json(['ok' => true]);
}

function tabel_api_get_workflow_log() {
    global $wpdb;
    $db_name = tabel_require_db();
    $year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');
    $month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('n');
    $t = tabel_table('workflow_log');
    $rows = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $t WHERE db_name = %s AND year = %d AND month = %d ORDER BY created_at DESC",
        $db_name, $year, $month
    ), ARRAY_A);
    tabel_json($rows);
}

// ═══════════════════════════════════════════
// NOTIFICATIONS API
// ═══════════════════════════════════════════

function tabel_api_get_notifications() {
    global $wpdb;
    $user = tabel_current_user();
    if (!$user) tabel_json(['error' => 'forbidden'], 403);
    $t = tabel_table('notifications');
    $rows = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $t WHERE user_id = %d ORDER BY created_at DESC LIMIT 100", (int)$user['id']
    ), ARRAY_A);
    foreach ($rows as &$r) {
        $r['id'] = (int)$r['id'];
        $r['user_id'] = (int)$r['user_id'];
        $r['is_read'] = (int)$r['is_read'];
    }
    $unread = (int)$wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $t WHERE user_id = %d AND is_read = 0", (int)$user['id']
    ));
    tabel_json(['items' => $rows, 'unread' => $unread]);
}

function tabel_api_mark_notifications_read() {
    global $wpdb;
    $user = tabel_current_user();
    if (!$user) tabel_json(['error' => 'forbidden'], 403);
    $t = tabel_table('notifications');
    $wpdb->query($wpdb->prepare("UPDATE $t SET is_read = 1 WHERE user_id = %d AND is_read = 0", (int)$user['id']));
    tabel_json(['ok' => true]);
}

// ═══════════════════════════════════════════
// EXPERIENCE (СТАЖ) API
// ═══════════════════════════════════════════

function tabel_api_get_experience($employee_id) {
    $exp = tabel_calc_experience($employee_id);
    tabel_json($exp);
}

function tabel_api_save_experience_period($employee_id) {
    global $wpdb;
    $data = tabel_input();
    $t = tabel_table('experience');
    
    // Auto-create table if not exists
    if ($wpdb->get_var("SHOW TABLES LIKE '$t'") !== $t) {
        if (!function_exists('dbDelta')) require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        $charset = $wpdb->get_charset_collate();
        dbDelta("CREATE TABLE $t (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            employee_id bigint(20) unsigned NOT NULL,
            date_from date NOT NULL,
            date_to date DEFAULT NULL,
            is_current tinyint(1) NOT NULL DEFAULT 0,
            note varchar(500) DEFAULT '',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY idx_emp (employee_id)
        ) $charset;");
    }
    
    $date_from = $data['date_from'];
    $date_to = !empty($data['is_current']) ? null : (isset($data['date_to']) ? $data['date_to'] : null);
    $is_current = !empty($data['is_current']) ? 1 : 0;
    $note = isset($data['note']) ? sanitize_text_field($data['note']) : '';
    
    if (!empty($data['id'])) {
        $wpdb->update($t, [
            'date_from' => $date_from, 'date_to' => $date_to,
            'is_current' => $is_current, 'note' => $note,
        ], ['id' => (int)$data['id']]);
    } else {
        $result = $wpdb->insert($t, [
            'employee_id' => $employee_id,
            'date_from' => $date_from, 'date_to' => $date_to,
            'is_current' => $is_current, 'note' => $note,
        ]);
        if ($result === false) {
            tabel_json(['ok' => false, 'error' => 'DB error: ' . $wpdb->last_error], 500);
            return;
        }
    }
    $exp = tabel_calc_experience($employee_id);
    tabel_json(['ok' => true, 'experience' => $exp]);
}

function tabel_api_delete_experience_period($period_id) {
    global $wpdb;
    $t = tabel_table('experience');
    $period = $wpdb->get_row($wpdb->prepare("SELECT * FROM $t WHERE id = %d", $period_id), ARRAY_A);
    if (!$period) tabel_json(['error' => 'not found'], 404);
    $wpdb->delete($t, ['id' => $period_id]);
    $exp = tabel_calc_experience($period['employee_id']);
    tabel_json(['ok' => true, 'experience' => $exp]);
}

function tabel_api_get_all_employees_experience() {
    global $wpdb;
    $emp_t = tabel_table('employees');
    $exp_t = tabel_table('experience');
    
    // Get all employees across all DBs
    $emps = $wpdb->get_results("SELECT id, full_name, db_name FROM $emp_t ORDER BY full_name", ARRAY_A);
    
    $result = [];
    foreach ($emps as $emp) {
        $exp = tabel_calc_experience((int)$emp['id']);
        $result[] = [
            'id' => (int)$emp['id'],
            'full_name' => $emp['full_name'],
            'db_name' => $emp['db_name'],
            'experience' => $exp,
        ];
    }
    tabel_json($result);
}
