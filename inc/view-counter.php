<?php
/** 集計値と短期のランダム識別子のみを保存する簡易カウンター。 */
if (!defined('ABSPATH')) { exit; }

add_action('admin_init', function () {
    if (!current_user_can('manage_options') || get_option('stage_a_counter_schema') === '1') { return; }
    global $wpdb;
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    $charset = $wpdb->get_charset_collate();
    dbDelta("CREATE TABLE {$wpdb->prefix}stage_a_counts (
        bucket varchar(20) NOT NULL,
        views bigint(20) unsigned NOT NULL DEFAULT 0,
        PRIMARY KEY  (bucket)
    ) ENGINE=InnoDB {$charset};");
    dbDelta("CREATE TABLE {$wpdb->prefix}stage_a_active (
        token char(64) NOT NULL,
        last_view char(32) NOT NULL DEFAULT '',
        seen bigint(20) unsigned NOT NULL DEFAULT 0,
        PRIMARY KEY  (token),
        KEY seen (seen)
    ) ENGINE=InnoDB {$charset};");
    $tables = array($wpdb->prefix . 'stage_a_counts', $wpdb->prefix . 'stage_a_active');
    foreach ($tables as $table) {
        if ($wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($table))) !== $table) { return; }
    }
    update_option('stage_a_counter_schema', '1', false);
});

function stage_a_counter_snapshot() {
    global $wpdb;
    $month = wp_date('Y-m');
    $counts = $wpdb->get_results($wpdb->prepare("SELECT bucket, views FROM {$wpdb->prefix}stage_a_counts WHERE bucket IN ('total', %s)", $month), OBJECT_K);
    $online = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}stage_a_active WHERE seen >= %d", time() - 180));
    return array('total'=>isset($counts['total']) ? (int) $counts['total']->views : 0, 'month'=>isset($counts[$month]) ? (int) $counts[$month]->views : 0, 'online'=>(int) $online);
}

add_action('rest_api_init', function () {
    register_rest_route('stage-a/v1', '/counter', array(
        'methods'=>array('GET', 'POST'), 'permission_callback'=>'__return_true',
        'callback'=>function ($request) {
            if (get_option('stage_a_counter_schema') !== '1') { return new WP_Error('counter_setup', '管理画面を開いて初期化してください。', array('status'=>503)); }
            global $wpdb;
            $active = $wpdb->prefix . 'stage_a_active';
            $counts = $wpdb->prefix . 'stage_a_counts';
            if ($request->get_method() === 'POST') {
                // ブラウザからの別サイト経由の書き込みを拒否。公開統計の完全な改ざん防止ではない。
                $origin = $request->get_header('origin');
                $home = wp_parse_url(home_url('/'));
                $source = $origin ? wp_parse_url($origin) : false;
                if ($origin && (!$source || ($source['scheme'] ?? '') !== ($home['scheme'] ?? '') || ($source['host'] ?? '') !== ($home['host'] ?? '') || ($source['port'] ?? null) !== ($home['port'] ?? null))) {
                    return new WP_Error('counter_origin', 'Invalid origin.', array('status'=>403));
                }
                $token = $request->get_param('token'); $view = $request->get_param('view');
                if (!is_string($token) || !preg_match('/^[a-f0-9]{32}$/D', $token) || !is_string($view) || !preg_match('/^[a-f0-9]{32}$/D', $view)) {
                    return new WP_Error('counter_input', 'Invalid input.', array('status'=>400));
                }
                $hash = hash_hmac('sha256', $token, wp_salt('auth'));
                $now = time();
                // 同時送信でも同じページ表示を重複計上しないよう、タブ単位でロックする。
                $wpdb->query('START TRANSACTION');
                $ok = $wpdb->query($wpdb->prepare("INSERT IGNORE INTO {$active} (token, last_view, seen) VALUES (%s, '', 0)", $hash));
                $row = $wpdb->get_row($wpdb->prepare("SELECT last_view, seen FROM {$active} WHERE token = %s FOR UPDATE", $hash));
                if ($ok === false || !$row) { $wpdb->query('ROLLBACK'); return new WP_Error('counter_write', 'Counter unavailable.', array('status'=>503)); }
                // heartbeat=true は滞在更新のみ。タブを再表示してもPVは増やさない。
                $is_view = $request->get_param('heartbeat') !== true;
                if ($is_view && $row->last_view !== $view) {
                    $ok = $wpdb->query($wpdb->prepare("INSERT INTO {$counts} (bucket, views) VALUES ('total', 1), (%s, 1) ON DUPLICATE KEY UPDATE views = views + 1", wp_date('Y-m')));
                }
                if ($ok !== false) {
                    $ok = $wpdb->query($wpdb->prepare("UPDATE {$active} SET last_view = %s, seen = %d WHERE token = %s", $is_view ? $view : $row->last_view, $now, $hash));
                }
                if ($ok === false) { $wpdb->query('ROLLBACK'); return new WP_Error('counter_write', 'Counter unavailable.', array('status'=>503)); }
                if ($wpdb->query('COMMIT') === false) { return new WP_Error('counter_write', 'Counter unavailable.', array('status'=>503)); }
            }
            $wpdb->query($wpdb->prepare("DELETE FROM {$active} WHERE seen < %d", time() - 180));
            $response = new WP_REST_Response(stage_a_counter_snapshot());
            $response->header('Cache-Control', 'no-store, max-age=0');
            return $response;
        }
    ));
});

add_action('wp_enqueue_scripts', function () {
    if (is_404() || is_preview()) { return; }
    wp_enqueue_script('portfolio-hub-wp-theme-counter', get_theme_file_uri('/js/view-counter.js'), array(), '1.6.0', true);
    wp_localize_script('portfolio-hub-wp-theme-counter', 'stageCounter', array('url'=>rest_url('stage-a/v1/counter'), 'readOnly'=>current_user_can('manage_options')));
});
