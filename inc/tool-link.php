<?php
if (!defined('ABSPATH')) { exit; }

function stage_a_registered_tools() {
    static $tools = null;
    if ($tools !== null) { return $tools; }
    $tools = array();
    $root = realpath(get_template_directory());
    if (!$root) { return $tools; }
    $registered = require $root . '/tools/registry.php';
    if (!is_array($registered)) { return $tools; }
    foreach ($registered as $key => $entry) {
        if (!is_string($key) || !preg_match('/^[a-z0-9_-]+$/D', $key) || !is_array($entry)) { continue; }
        $path = isset($entry['path']) ? $entry['path'] : '';
        if (!is_string($path) || !preg_match('~^(?:memo\.php|tools/(?:[a-zA-Z0-9_-]+/)*[a-zA-Z0-9_-]+\.php)$~D', $path) || $path === 'tools/registry.php') { continue; }
        $file = realpath($root . '/' . $path);
        if (!$file || !is_file($file) || strpos($file, $root . DIRECTORY_SEPARATOR) !== 0) { continue; }
        $tools[$key] = array('label' => isset($entry['label']) && is_string($entry['label']) ? $entry['label'] : $key,
            'url' => get_template_directory_uri() . '/' . $path);
    }
    return $tools;
}

// 旧投稿の起動先キーを統合後のキーへ読み替える。DBの一括書き換えは行わない。
function stage_a_tool_key($key) {
    if (!is_string($key)) { return ''; }
    $aliases = array('resize'=>'image', 'convert'=>'image', 'rename'=>'batch', 'pdf'=>'batch');
    return isset($aliases[$key]) ? $aliases[$key] : $key;
}

function stage_a_tool_url($post_id) {
    if (!has_category('tool', $post_id)) { return ''; }
    $key = stage_a_tool_key(get_post_meta($post_id, '_stage_tool_key', true));
    $tools = stage_a_registered_tools();
    return is_string($key) && isset($tools[$key]) ? $tools[$key]['url'] : '';
}

add_action('add_meta_boxes', function () {
    add_meta_box('stage-tool-launch', '起動するツール', function ($post) {
        wp_nonce_field('stage_tool_launch', 'stage_tool_launch_nonce');
        $selected = stage_a_tool_key(get_post_meta($post->ID, '_stage_tool_key', true));
        $tools = stage_a_registered_tools();
        echo '<p>カテゴリー「tool」を付けると、カードから選択したツールを新しいタブで開きます。</p>';
        echo '<label for="stage-tool-key">テーマ内のツール</label><select id="stage-tool-key" name="stage_tool_key" style="width:100%"><option value="">指定なし（記事を開く）</option>';
        foreach ($tools as $key => $entry) {
            echo '<option value="' . esc_attr($key) . '" ' . selected($selected, $key, false) . '>' . esc_html($entry['label']) . '</option>';
        }
        if (is_string($selected) && $selected !== '' && !isset($tools[$selected])) {
            echo '<option value="' . esc_attr($selected) . '" selected>起動先が見つかりません（記事を開きます）</option>';
        }
        echo '</select><p>追加先：tools/registry.php。PHP本体は tools フォルダ内に配置してください。</p>';
    }, 'post', 'side');
});

add_action('save_post_post', function ($id) {
    if (wp_is_post_revision($id) || wp_is_post_autosave($id) || !current_user_can('edit_post', $id)) { return; }
    if (!isset($_POST['stage_tool_launch_nonce']) || !is_string($_POST['stage_tool_launch_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['stage_tool_launch_nonce'])), 'stage_tool_launch')) { return; }
    if (!isset($_POST['stage_tool_key']) || !is_string($_POST['stage_tool_key'])) { return; }
    $key = stage_a_tool_key(wp_unslash($_POST['stage_tool_key']));
    if ($key === '') { delete_post_meta($id, '_stage_tool_key'); return; }
    $tools = stage_a_registered_tools();
    if (isset($tools[$key])) { update_post_meta($id, '_stage_tool_key', $key); }
});
