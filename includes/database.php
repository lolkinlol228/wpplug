<?php
if (!defined('ABSPATH')) exit;

function tabel_table($name) {
    global $wpdb;
    return $wpdb->prefix . 'tabel_' . $name;
}

function tabel_create_tables() {
    global $wpdb;
    $charset = $wpdb->get_charset_collate();
    
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    
    // Users table
    $t = tabel_table('users');
    dbDelta("CREATE TABLE $t (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        username varchar(100) NOT NULL,
        password_hash varchar(255) NOT NULL,
        display_name varchar(255) NOT NULL DEFAULT '',
        is_superadmin tinyint(1) NOT NULL DEFAULT 0,
        is_active tinyint(1) NOT NULL DEFAULT 1,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY username (username)
    ) $charset;");

    // Migration: add display_name if missing
    $users_t = tabel_table('users');
    $cols = $wpdb->get_results("SHOW COLUMNS FROM $users_t", ARRAY_A);
    if ($cols && !in_array('display_name', array_column($cols, 'Field'))) {
        $wpdb->query("ALTER TABLE $users_t ADD COLUMN display_name varchar(255) NOT NULL DEFAULT '' AFTER password_hash");
    }
    
    // Migration: add new permission columns if missing
    $perms_t = tabel_table('user_permissions');
    $perm_cols = $wpdb->get_results("SHOW COLUMNS FROM $perms_t", ARRAY_A);
    if ($perm_cols) {
        $existing = array_column($perm_cols, 'Field');
        $new_perm_cols = [
            'can_access_during_maintenance' => "ALTER TABLE $perms_t ADD COLUMN can_access_during_maintenance tinyint(1) NOT NULL DEFAULT 0 AFTER can_manage_experience",
            'can_toggle_maintenance'        => "ALTER TABLE $perms_t ADD COLUMN can_toggle_maintenance tinyint(1) NOT NULL DEFAULT 0 AFTER can_access_during_maintenance",
            'can_send_notifications'        => "ALTER TABLE $perms_t ADD COLUMN can_send_notifications tinyint(1) NOT NULL DEFAULT 0 AFTER can_toggle_maintenance",
            'can_manage_workflow'           => "ALTER TABLE $perms_t ADD COLUMN can_manage_workflow tinyint(1) NOT NULL DEFAULT 0 AFTER can_send_notifications",
        ];
        foreach ($new_perm_cols as $col => $sql) {
            if (!in_array($col, $existing)) {
                $wpdb->query($sql);
            }
        }
    }
    
    // User permissions
    $t = tabel_table('user_permissions');
    dbDelta("CREATE TABLE $t (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        user_id bigint(20) unsigned NOT NULL,
        can_manage_employees tinyint(1) NOT NULL DEFAULT 1,
        can_manage_positions tinyint(1) NOT NULL DEFAULT 1,
        can_edit_excel tinyint(1) NOT NULL DEFAULT 1,
        can_edit_fio tinyint(1) NOT NULL DEFAULT 1,
        can_edit_position_col tinyint(1) NOT NULL DEFAULT 1,
        can_edit_conditions tinyint(1) NOT NULL DEFAULT 1,
        can_edit_experience tinyint(1) NOT NULL DEFAULT 1,
        can_edit_hours tinyint(1) NOT NULL DEFAULT 1,
        can_edit_rate tinyint(1) NOT NULL DEFAULT 1,
        can_edit_days tinyint(1) NOT NULL DEFAULT 1,
        can_manage_databases tinyint(1) NOT NULL DEFAULT 0,
        can_view_stats tinyint(1) NOT NULL DEFAULT 1,
        can_view_history tinyint(1) NOT NULL DEFAULT 0,
        can_export_excel tinyint(1) NOT NULL DEFAULT 1,
        can_fire_employee tinyint(1) NOT NULL DEFAULT 0,
        can_manage_experience tinyint(1) NOT NULL DEFAULT 1,
        can_access_during_maintenance tinyint(1) NOT NULL DEFAULT 0,
        can_toggle_maintenance tinyint(1) NOT NULL DEFAULT 0,
        can_send_notifications tinyint(1) NOT NULL DEFAULT 0,
        can_manage_workflow tinyint(1) NOT NULL DEFAULT 0,
        PRIMARY KEY (id),
        UNIQUE KEY user_id (user_id)
    ) $charset;");
    
    // Databases registry
    $t = tabel_table('databases');
    dbDelta("CREATE TABLE $t (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        name varchar(100) NOT NULL,
        display_name varchar(255) NOT NULL,
        db_type varchar(10) NOT NULL DEFAULT 'pps',
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY name (name)
    ) $charset;");
    
    // User-database assignments
    $t = tabel_table('user_databases');
    dbDelta("CREATE TABLE $t (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        user_id bigint(20) unsigned NOT NULL,
        db_name varchar(100) NOT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY user_db (user_id, db_name)
    ) $charset;");
    
    // Export log
    $t = tabel_table('export_log');
    dbDelta("CREATE TABLE $t (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        user_id bigint(20) unsigned NOT NULL,
        username varchar(100) NOT NULL,
        db_name varchar(100) NOT NULL,
        export_type varchar(50) NOT NULL,
        year int NOT NULL,
        month int NOT NULL,
        employee_name varchar(255) DEFAULT NULL,
        file_data longblob DEFAULT NULL,
        exported_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset;");
    
    // Employees (per-database via db_name prefix)
    $t = tabel_table('employees');
    dbDelta("CREATE TABLE $t (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        db_name varchar(100) NOT NULL,
        full_name varchar(500) NOT NULL,
        position varchar(500) NOT NULL DEFAULT '',
        pay_type varchar(20) NOT NULL DEFAULT 'rate',
        rate double DEFAULT 0,
        employment_internal varchar(50) DEFAULT NULL,
        employment_external varchar(50) DEFAULT NULL,
        pedagog_experience varchar(100) DEFAULT NULL,
        actual_hours double DEFAULT NULL,
        position_id bigint(20) unsigned DEFAULT NULL,
        note text DEFAULT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY db_name (db_name)
    ) $charset;");
    
    // Positions
    $t = tabel_table('positions');
    dbDelta("CREATE TABLE $t (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        db_name varchar(100) NOT NULL,
        name_ru varchar(500) NOT NULL,
        name_kg varchar(500) NOT NULL,
        name_en varchar(500) NOT NULL,
        planned_hours int NOT NULL DEFAULT 0,
        PRIMARY KEY (id),
        KEY db_name (db_name)
    ) $charset;");
    
    // Timesheet entries
    $t = tabel_table('timesheet_entries');
    dbDelta("CREATE TABLE $t (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        db_name varchar(100) NOT NULL,
        employee_id bigint(20) unsigned NOT NULL,
        year int NOT NULL,
        month int NOT NULL,
        day int NOT NULL,
        status varchar(50) NOT NULL DEFAULT 'work',
        custom_value varchar(255) DEFAULT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY emp_date (db_name, employee_id, year, month, day),
        KEY db_name (db_name)
    ) $charset;");
    
    // Monthly settings
    $t = tabel_table('monthly_settings');
    dbDelta("CREATE TABLE $t (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        db_name varchar(100) NOT NULL,
        employee_id bigint(20) unsigned NOT NULL,
        year int NOT NULL,
        month int NOT NULL,
        position varchar(500) DEFAULT NULL,
        rate double DEFAULT NULL,
        rate_manual tinyint(1) NOT NULL DEFAULT 0,
        is_fired tinyint(1) DEFAULT 0,
        employment_internal varchar(50) DEFAULT NULL,
        employment_external varchar(50) DEFAULT NULL,
        actual_hours double DEFAULT NULL,
        pedagog_experience varchar(100) DEFAULT NULL,
        position_id bigint(20) unsigned DEFAULT NULL,
        full_name varchar(500) DEFAULT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY emp_month (db_name, employee_id, year, month),
        KEY db_name (db_name)
    ) $charset;");
    
    // Migration: add rate_manual if missing
    $ms_t = tabel_table('monthly_settings');
    $ms_cols = $wpdb->get_results("SHOW COLUMNS FROM $ms_t", ARRAY_A);
    if ($ms_cols && !in_array('rate_manual', array_column($ms_cols, 'Field'))) {
        $wpdb->query("ALTER TABLE $ms_t ADD COLUMN rate_manual tinyint(1) NOT NULL DEFAULT 0 AFTER rate");
    }
    
    // Excel settings
    $t = tabel_table('excel_settings');
    dbDelta("CREATE TABLE $t (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        db_name varchar(100) NOT NULL,
        setting_key varchar(100) NOT NULL,
        setting_value longtext NOT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY db_key (db_name, setting_key)
    ) $charset;");
    
    // Login log table
    $t = tabel_table('login_log');
    dbDelta("CREATE TABLE $t (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        username varchar(100) NOT NULL,
        user_id bigint(20) unsigned DEFAULT NULL,
        ip_address varchar(45) NOT NULL DEFAULT '',
        attempted_pass varchar(50) NOT NULL DEFAULT '',
        success tinyint(1) NOT NULL DEFAULT 0,
        created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY idx_success (success),
        KEY idx_created (created_at)
    ) $charset;");
    
    // Workflow chain configuration (per db)
    $t = tabel_table('workflow_chains');
    dbDelta("CREATE TABLE $t (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        db_name varchar(100) NOT NULL,
        step_order int NOT NULL DEFAULT 0,
        step_name varchar(200) NOT NULL DEFAULT '',
        user_ids text NOT NULL,
        is_reviewer tinyint(1) NOT NULL DEFAULT 0,
        created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY idx_db (db_name),
        KEY idx_order (db_name, step_order)
    ) $charset;");
    
    // Workflow status per db+month
    $t = tabel_table('workflow_status');
    dbDelta("CREATE TABLE $t (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        db_name varchar(100) NOT NULL,
        year int NOT NULL,
        month int NOT NULL,
        current_step int NOT NULL DEFAULT 0,
        status varchar(20) NOT NULL DEFAULT 'draft',
        activated tinyint(1) NOT NULL DEFAULT 0,
        updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY idx_db_ym (db_name, year, month)
    ) $charset;");
    
    // Workflow step log (who sent when, returns, etc)
    $t = tabel_table('workflow_log');
    dbDelta("CREATE TABLE $t (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        db_name varchar(100) NOT NULL,
        year int NOT NULL,
        month int NOT NULL,
        step_order int NOT NULL,
        user_id bigint(20) unsigned NOT NULL,
        action varchar(30) NOT NULL,
        target_step int DEFAULT NULL,
        comment text,
        created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY idx_db_ym (db_name, year, month)
    ) $charset;");
    
    // Notifications
    $t = tabel_table('notifications');
    dbDelta("CREATE TABLE $t (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        user_id bigint(20) unsigned NOT NULL,
        db_name varchar(100) NOT NULL DEFAULT '',
        year int NOT NULL DEFAULT 0,
        month int NOT NULL DEFAULT 0,
        message text NOT NULL,
        is_read tinyint(1) NOT NULL DEFAULT 0,
        created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY idx_user (user_id, is_read),
        KEY idx_created (created_at)
    ) $charset;");

    // Experience periods (стаж)
    $t = tabel_table('experience');
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

    // ─── Chat: channels ───
    $t = tabel_table('chat_channels');
    dbDelta("CREATE TABLE $t (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        channel_type varchar(20) NOT NULL DEFAULT 'global',
        db_name varchar(100) DEFAULT NULL,
        name varchar(255) NOT NULL DEFAULT '',
        created_by bigint(20) unsigned DEFAULT NULL,
        created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY idx_type (channel_type),
        KEY idx_db (db_name)
    ) $charset;");

    // ─── Chat: private channel members ───
    $t = tabel_table('chat_members');
    dbDelta("CREATE TABLE $t (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        channel_id bigint(20) unsigned NOT NULL,
        user_id bigint(20) unsigned NOT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY ch_user (channel_id, user_id),
        KEY idx_user (user_id)
    ) $charset;");

    // ─── Chat: messages ───
    $t = tabel_table('chat_messages');
    dbDelta("CREATE TABLE $t (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        channel_id bigint(20) unsigned NOT NULL,
        user_id bigint(20) unsigned NOT NULL,
        username varchar(100) NOT NULL DEFAULT '',
        display_name varchar(255) NOT NULL DEFAULT '',
        message text NOT NULL,
        is_system tinyint(1) NOT NULL DEFAULT 0,
        created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY idx_channel (channel_id, created_at),
        KEY idx_user (user_id)
    ) $charset;");

    // ─── Chat: read cursors (last read message per user per channel) ───
    $t = tabel_table('chat_read');
    dbDelta("CREATE TABLE $t (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        channel_id bigint(20) unsigned NOT NULL,
        user_id bigint(20) unsigned NOT NULL,
        last_read_id bigint(20) unsigned NOT NULL DEFAULT 0,
        PRIMARY KEY (id),
        UNIQUE KEY ch_user (channel_id, user_id)
    ) $charset;");
}

function tabel_init_master_data() {
    global $wpdb;
    $users_table = tabel_table('users');
    $perms_table = tabel_table('user_permissions');
    
    // Create superadmin if not exists
    $exists = $wpdb->get_var($wpdb->prepare(
        "SELECT id FROM $users_table WHERE username = %s", 'Frazy'
    ));
    
    if (!$exists) {
        $wpdb->insert($users_table, [
            'username' => 'Frazy',
            'password_hash' => hash('sha256', 'FrazyAmp'),
            'is_superadmin' => 1,
        ]);
        $uid = $wpdb->insert_id;
        $wpdb->insert($perms_table, [
            'user_id' => $uid,
            'can_manage_employees' => 1, 'can_manage_positions' => 1,
            'can_edit_excel' => 1, 'can_edit_fio' => 1,
            'can_edit_position_col' => 1, 'can_edit_conditions' => 1,
            'can_edit_experience' => 1, 'can_edit_hours' => 1,
            'can_edit_rate' => 1, 'can_edit_days' => 1,
            'can_manage_databases' => 1, 'can_view_stats' => 1,
            'can_view_history' => 1, 'can_export_excel' => 1,
            'can_fire_employee' => 1,
            'can_manage_experience' => 1,
            'can_access_during_maintenance' => 1,
            'can_toggle_maintenance' => 1,
            'can_send_notifications' => 1,
            'can_manage_workflow' => 1,
        ]);
    }
}

function tabel_init_data_db($db_name) {
    global $wpdb;
    $positions_table = tabel_table('positions');
    $excel_table = tabel_table('excel_settings');
    
    // Add default positions if none exist for this db
    $cnt = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $positions_table WHERE db_name = %s", $db_name
    ));
    
    if (!$cnt) {
        $defaults = [
            ['Стажёр', 'Стажёр', 'Intern', 850],
            ['Преподаватель', 'Окутуучу', 'Teacher', 850],
            ['Старший преподаватель', 'Ага окутуучу', 'Senior Teacher', 850],
            ['Доцент', 'Доцент', 'Associate Professor', 800],
            ['Профессор', 'Профессор', 'Professor', 750],
        ];
        foreach ($defaults as $d) {
            $wpdb->insert($positions_table, [
                'db_name' => $db_name,
                'name_ru' => $d[0], 'name_kg' => $d[1],
                'name_en' => $d[2], 'planned_hours' => $d[3],
            ]);
        }
    }
    
    // Add default excel settings
    $cnt2 = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $excel_table WHERE db_name = %s", $db_name
    ));
    
    if (!$cnt2) {
        $defaults_settings = [
            ['header_left1', 'Макулдашылды'],
            ['header_left2', 'ЖАЭУнун ОИ боюнча проректору,'],
            ['header_left3', '____________ м.и.к. Садырова Н.А.'],
            ['header_center1', 'Жалал-Абад эл аралык университети'],
            ['header_center2', 'Медицина факультети'],
            ['header_center3', 'Гуманитардык жана табигый дисциплиналар кафедрасы'],
            ['header_right1', 'БЕКИТЕМ'],
            ['header_right2', 'ЖАЭУнун ректору, ф-м.и.к.'],
            ['header_right3', '____________ Нарбаев М.Р.'],
            ['sig1', 'Кафедра башчысы: ______________________   ______________________ '],
            ['sig2', 'ОМБнун башчысы, ф-м.и.к. : ______________________ Канетова Д.Э.'],
            ['sig3', 'Укук иштери жана адам ресурстарынын бөлүмү: ____________________ Сыдыкова  Б.Ж.'],
            ['marks_list', '[{"symbol":"К","status":"vacation","description":"Каникулдар"},{"symbol":"О","status":"sick","description":"Оорулуу (Оорукана тастыктаган баракча болсо)"},{"symbol":"С","status":"business_trip","description":"Иш сапарлар (акы төлөнүүчү)"},{"symbol":"КӨ","status":"maternity","description":"Кош бойлуулук жана төрөт өргүү"},{"symbol":"БӨ","status":"childcare","description":"Бала багуу боюнча өргүү"},{"symbol":"ОӨ","status":"study_leave","description":"Окуусуна (сынак тапшыруусуна) байланыштуу эмгек акысыз өргүү"},{"symbol":"АӨ","status":"admin_leave","description":"Администрация тарабынан эмгек акысыз берилген дем алыш күнү"},{"symbol":"Дк","status":"absence","description":"Дайынсыз ишке келбей калуу"},{"symbol":"Кк","status":"late","description":"Ишке кечигип келүү"},{"symbol":"Эк","status":"early_leave","description":"Иштен эрте кетип калуу"}]'],
        ];
        foreach ($defaults_settings as $s) {
            $wpdb->replace($excel_table, [
                'db_name' => $db_name,
                'setting_key' => $s[0],
                'setting_value' => $s[1],
            ]);
        }
    }
}

// Helper: get active db name from session
function tabel_active_db() {
    return isset($_SESSION['tabel_active_db']) ? $_SESSION['tabel_active_db'] : null;
}

// Helper: get active db type
function tabel_active_db_type() {
    global $wpdb;
    $db_name = tabel_active_db();
    if (!$db_name) return 'pps';
    $t = tabel_table('databases');
    $type = $wpdb->get_var($wpdb->prepare("SELECT db_type FROM $t WHERE name = %s", $db_name));
    return $type ?: 'pps';
}

// Get current user
function tabel_current_user() {
    global $wpdb;
    static $cached = null;
    static $cached_uid = null;
    $uid = isset($_SESSION['tabel_user_id']) ? $_SESSION['tabel_user_id'] : null;
    if (!$uid) return null;
    if ($cached !== null && $cached_uid === $uid) return $cached;
    $t = tabel_table('users');
    $cached = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $t WHERE id = %d AND is_active = 1", $uid
    ), ARRAY_A);
    $cached_uid = $uid;
    return $cached;
}

// Get user permissions
function tabel_user_perms($uid = null) {
    global $wpdb;
    if ($uid === null) {
        $uid = isset($_SESSION['tabel_user_id']) ? $_SESSION['tabel_user_id'] : 0;
    }
    if (!$uid) return [];
    $t = tabel_table('user_permissions');
    $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $t WHERE user_id = %d", $uid), ARRAY_A);
    return $row ?: [];
}

// Hash password same as Flask version
function tabel_hash_password($pw) {
    return password_hash($pw, PASSWORD_BCRYPT);
}

function tabel_verify_password($pw, $hash) {
    // Support both legacy sha256 and new bcrypt
    if (strlen($hash) === 64 && ctype_xdigit($hash)) {
        // Legacy sha256 hash
        return hash('sha256', $pw) === $hash;
    }
    return password_verify($pw, $hash);
}

function tabel_needs_rehash($hash) {
    // Legacy sha256 hashes need rehash
    return strlen($hash) === 64 && ctype_xdigit($hash);
}

// Log export
function tabel_log_export($export_type, $year, $month, $file_data = null, $employee_name = null) {
    global $wpdb;
    $user = tabel_current_user();
    if (!$user) return;
    $db_name = tabel_active_db() ?: 'unknown';
    $wpdb->insert(tabel_table('export_log'), [
        'user_id' => $user['id'],
        'username' => $user['username'],
        'db_name' => $db_name,
        'export_type' => $export_type,
        'year' => $year,
        'month' => $month,
        'employee_name' => $employee_name,
        'file_data' => $file_data,
    ]);
}

// Calendar helpers
function tabel_get_month_calendar($year, $month) {
    $num_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $days = [];
    for ($d = 1; $d <= $num_days; $d++) {
        $ts = mktime(0, 0, 0, $month, $d, $year);
        $wd = (int)date('N', $ts) - 1; // 0=Mon, 6=Sun
        $days[] = ['day' => $d, 'weekday' => $wd, 'is_sunday' => ($wd === 6)];
    }
    return $days;
}

function tabel_get_day_names($lang) {
    if ($lang === 'ru') return ['Пн','Вт','Ср','Чт','Пт','Сб','Вс'];
    if ($lang === 'kg') return ['Дш','Шш','Шр','Бш','Жм','Иш','Жш'];
    return ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
}

// STATUS_TO_MARK mapping
// Calculate total experience from periods
function tabel_calc_experience($employee_id, $as_of_date = null) {
    global $wpdb;
    static $table_exists = null;
    $t = tabel_table('experience');
    if (!$as_of_date) $as_of_date = date('Y-m-d');
    
    if ($table_exists === null) {
        $table_exists = ($wpdb->get_var("SHOW TABLES LIKE '$t'") === $t);
    }
    if (!$table_exists) {
        return ['total_days' => 0, 'years' => 0, 'months' => 0, 'days' => 0, 'display' => '', 'periods' => []];
    }
    
    $periods = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $t WHERE employee_id = %d ORDER BY date_from", $employee_id
    ), ARRAY_A);
    
    $total_days = 0;
    foreach ($periods as $p) {
        $from = new DateTime($p['date_from']);
        $to = $p['is_current'] ? new DateTime($as_of_date) : ($p['date_to'] ? new DateTime($p['date_to']) : new DateTime($as_of_date));
        if ($to > new DateTime($as_of_date)) $to = new DateTime($as_of_date);
        if ($from > $to) continue;
        $diff = $from->diff($to);
        $total_days += $diff->days;
    }
    
    $years = floor($total_days / 365);
    $months = floor(($total_days % 365) / 30);
    $days = $total_days % 365 % 30;
    
    return [
        'total_days' => $total_days,
        'years' => $years,
        'months' => $months,
        'days' => $days,
        'display' => $years > 0 ? "{$years} г. {$months} м." : ($months > 0 ? "{$months} м. {$days} д." : "{$days} д."),
        'periods' => $periods,
    ];
}

function tabel_status_to_mark() {
    return [
        'vacation' => 'К', 'sick' => 'О', 'business_trip' => 'С',
        'maternity' => 'КӨ', 'childcare' => 'БӨ', 'study_leave' => 'ОӨ',
        'admin_leave' => 'АӨ', 'absence' => 'Дк', 'late' => 'Кк',
        'early_leave' => 'Эк',
    ];
}

function tabel_get_status_to_mark_from_db($db_name) {
    global $wpdb;
    $t = tabel_table('excel_settings');
    $val = $wpdb->get_var($wpdb->prepare(
        "SELECT setting_value FROM $t WHERE db_name = %s AND setting_key = 'marks_list'", $db_name
    ));
    if ($val) {
        $list = json_decode($val, true);
        if (is_array($list)) {
            $map = [];
            foreach ($list as $item) {
                if (!empty($item['status']) && !empty($item['symbol'])) {
                    $map[$item['status']] = $item['symbol'];
                }
            }
            if (!empty($map)) return $map;
        }
    }
    return tabel_status_to_mark();
}

// Get employee data for month with monthly overrides — efficient single-query version
// Finds the latest monthly_settings for each field up to the target month
function tabel_get_employee_for_month($employee, $year, $month, $db_name, $depth = 0) {
    global $wpdb;
    
    $t = tabel_table('monthly_settings');
    
    // Get ALL monthly_settings for this employee up to target month, ordered newest first
    $rows = $wpdb->get_results($wpdb->prepare(
        "SELECT position, rate, rate_manual, employment_internal, employment_external, 
                actual_hours, pedagog_experience, position_id, full_name, year, month
         FROM $t WHERE db_name = %s AND employee_id = %d 
         AND (year < %d OR (year = %d AND month <= %d))
         ORDER BY year DESC, month DESC",
        $db_name, $employee['id'], $year, $year, $month
    ), ARRAY_A);
    
    if (empty($rows)) return $employee;
    
    $emp = $employee; // array copy
    
    // Fields to inherit: take the first (most recent) non-null value
    $fields = ['full_name', 'position', 'rate', 'actual_hours', 'pedagog_experience', 'position_id'];
    $found = [];
    $rate_manual_found = false;
    
    foreach ($rows as $row) {
        foreach ($fields as $f) {
            if (!isset($found[$f]) && $row[$f] !== null) {
                $found[$f] = true;
                if ($f === 'rate') {
                    $emp['rate'] = (float)$row[$f];
                    $emp['rate_manual'] = (int)$row['rate_manual'];
                    $rate_manual_found = true;
                } elseif ($f === 'actual_hours') {
                    $emp['actual_hours'] = (float)$row[$f];
                } elseif ($f === 'position_id') {
                    $emp['position_id'] = (int)$row[$f];
                } else {
                    $emp[$f] = $row[$f];
                }
            }
        }
        
        // Employment: take from most recent row that has employment set
        if (!isset($found['_employment'])) {
            $has_emp_set = ($row['employment_internal'] !== null || $row['employment_external'] !== null);
            $has_other = ($row['position'] !== null || $row['rate'] !== null || 
                          $row['actual_hours'] !== null || $row['pedagog_experience'] !== null ||
                          $row['position_id'] !== null || $row['full_name'] !== null);
            
            if ($has_emp_set || !$has_other) {
                $found['_employment'] = true;
                $emp['employment_internal'] = $row['employment_internal'];
                $emp['employment_external'] = $row['employment_external'];
            }
        }
        
        // Once all fields found, stop early
        if (count($found) >= count($fields) + 1) break;
    }
    
    if (!$rate_manual_found) {
        $emp['rate_manual'] = 0;
    }
    
    return $emp;
}

// Batch version: get employee data for ALL employees in a month with ONE query
function tabel_get_employees_for_month_batch($employees, $year, $month, $db_name) {
    global $wpdb;
    
    if (empty($employees)) return [];
    
    $t = tabel_table('monthly_settings');
    
    // Get ALL monthly_settings for ALL employees up to target month
    $emp_ids = array_map(function($e) { return (int)$e['id']; }, $employees);
    $ids_str = implode(',', $emp_ids);
    
    $rows = $wpdb->get_results($wpdb->prepare(
        "SELECT employee_id, position, rate, rate_manual, employment_internal, employment_external, 
                actual_hours, pedagog_experience, position_id, full_name, year, month
         FROM $t WHERE db_name = %s AND employee_id IN ($ids_str)
         AND (year < %d OR (year = %d AND month <= %d))
         ORDER BY employee_id, year DESC, month DESC",
        $db_name, $year, $year, $month
    ), ARRAY_A);
    
    // Group rows by employee_id
    $grouped = [];
    foreach ($rows as $row) {
        $eid = (int)$row['employee_id'];
        $grouped[$eid][] = $row;
    }
    
    $result = [];
    foreach ($employees as $employee) {
        $eid = (int)$employee['id'];
        $emp = $employee;
        $emp_rows = isset($grouped[$eid]) ? $grouped[$eid] : [];
        
        if (empty($emp_rows)) {
            $emp['rate_manual'] = 0;
            $result[$eid] = $emp;
            continue;
        }
        
        $fields = ['full_name', 'position', 'rate', 'actual_hours', 'pedagog_experience', 'position_id'];
        $found = [];
        $rate_manual_found = false;
        
        foreach ($emp_rows as $row) {
            foreach ($fields as $f) {
                if (!isset($found[$f]) && $row[$f] !== null) {
                    $found[$f] = true;
                    if ($f === 'rate') {
                        $emp['rate'] = (float)$row[$f];
                        $emp['rate_manual'] = (int)$row['rate_manual'];
                        $rate_manual_found = true;
                    } elseif ($f === 'actual_hours') {
                        $emp['actual_hours'] = (float)$row[$f];
                    } elseif ($f === 'position_id') {
                        $emp['position_id'] = (int)$row[$f];
                    } else {
                        $emp[$f] = $row[$f];
                    }
                }
            }
            
            if (!isset($found['_employment'])) {
                $has_emp_set = ($row['employment_internal'] !== null || $row['employment_external'] !== null);
                $has_other = ($row['position'] !== null || $row['rate'] !== null || 
                              $row['actual_hours'] !== null || $row['pedagog_experience'] !== null ||
                              $row['position_id'] !== null || $row['full_name'] !== null);
                
                if ($has_emp_set || !$has_other) {
                    $found['_employment'] = true;
                    $emp['employment_internal'] = $row['employment_internal'];
                    $emp['employment_external'] = $row['employment_external'];
                }
            }
            
            if (count($found) >= count($fields) + 1) break;
        }
        
        if (!$rate_manual_found) {
            $emp['rate_manual'] = 0;
        }
        
        $result[$eid] = $emp;
    }
    
    return $result;
}

// Calculate employee month totals
function tabel_calc_employee_month($employee, $entries_dict, $days_info, $db_type = 'pps') {
    $rate = isset($employee['rate']) ? (float)$employee['rate'] : 0;
    $pay_type = isset($employee['pay_type']) ? $employee['pay_type'] : 'rate';
    $status_to_mark = tabel_status_to_mark();
    
    if ($db_type === 'staff') {
        $daily_value = 0;
    } else {
        $daily_value = ($pay_type === 'rate') ? $rate * 6 : 0;
    }
    
    $working_days = 0; $weekends = 0; $holidays = 0;
    $day_cells = []; $total_pay_accum = 0.0; $mark_counts = [];
    
    foreach ($days_info as $di) {
        $d = $di['day'];
        $weekday = $di['weekday'];
        $entry = isset($entries_dict[$d]) ? $entries_dict[$d] : null;
        $status = $entry ? $entry['status'] : ($di['is_sunday'] ? 'day_off' : 'work');
        $custom = $entry ? (isset($entry['custom_value']) ? $entry['custom_value'] : null) : null;
        
        $is_working = ($status === 'work');
        if ($is_working) $working_days++;
        elseif ($status === 'day_off') $weekends++;
        elseif ($status === 'holiday') $holidays++;
        
        $mark = isset($status_to_mark[$status]) ? $status_to_mark[$status] : null;
        if ($mark) {
            $mark_counts[$mark] = (isset($mark_counts[$mark]) ? $mark_counts[$mark] : 0) + 1;
        }
        // Count custom_value symbols (for user-defined marks)
        if ($custom && !$mark) {
            $mark_counts[$custom] = (isset($mark_counts[$custom]) ? $mark_counts[$custom] : 0) + 1;
        }
        
        // Display value
        if ($db_type === 'staff') {
            if ($pay_type === 'rate' && $is_working) {
                $day_val = ($weekday === 5) ? round($rate * 5, 2) : round($rate * 7, 2);
                $total_pay_accum += $day_val;
                $cell_display = $day_val;
            } else {
                if ($is_working) $cell_display = '+';
                elseif (isset($status_to_mark[$status])) $cell_display = $status_to_mark[$status];
                elseif ($status === 'sick') $cell_display = 'О';
                else $cell_display = '-';
            }
        } else {
            if ($pay_type === 'rate') {
                if ($is_working) $cell_display = round($daily_value, 2);
                elseif (isset($status_to_mark[$status])) $cell_display = $status_to_mark[$status];
                elseif ($status === 'sick') $cell_display = 'О';
                else $cell_display = '-';
            } else {
                if ($is_working) $cell_display = '+';
                elseif (isset($status_to_mark[$status])) $cell_display = $status_to_mark[$status];
                elseif ($status === 'sick') $cell_display = 'О';
                else $cell_display = '-';
            }
        }
        
        if ($custom && $status === 'work') {
            $cell_display = $custom;
        }
        
        $day_cells[] = [
            'day' => $d, 'weekday' => $weekday, 'is_sunday' => $di['is_sunday'],
            'status' => $status, 'display' => $cell_display, 'custom_value' => $custom,
        ];
    }
    
    if ($db_type === 'staff') {
        $working_hours = 0;
        $total_pay = ($pay_type === 'rate') ? round($total_pay_accum, 2) : $rate;
    } else {
        $working_hours = ($pay_type === 'rate') ? round($working_days * $daily_value, 2) : 0;
        $total_pay = ($pay_type === 'rate') ? round($working_days * $daily_value, 2) : $rate;
    }
    
    return [
        'day_cells' => $day_cells, 'working_days' => $working_days,
        'working_hours' => $working_hours, 'total_pay' => $total_pay,
        'total_days' => count($days_info), 'weekends' => $weekends,
        'holidays' => $holidays, 'daily_value' => $daily_value,
        'mark_counts' => $mark_counts,
    ];
}

// ─── Migration: add display_name to users if missing ───
function tabel_maybe_add_display_name_column() {
    global $wpdb;
    $t = tabel_table('users');
    if ($wpdb->get_var("SHOW TABLES LIKE '$t'") !== $t) return;
    $col = $wpdb->get_var("SHOW COLUMNS FROM $t LIKE 'display_name'");
    if (!$col) {
        $wpdb->query("ALTER TABLE $t ADD COLUMN display_name varchar(255) NOT NULL DEFAULT '' AFTER password_hash");
    }
}
add_action('init', 'tabel_maybe_add_display_name_column');