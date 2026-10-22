<?php
if (!defined('ABSPATH')) { exit; }

// 既存ページを優先し、本文やメール設定を書き換えずに導線だけを設定する。
function stage_footer_page($kind) {
    $settings = get_option('stage_footer_routes', array());
    $id = isset($settings[$kind]) ? absint($settings[$kind]) : 0;
    if (!$id && $kind === 'privacy') { $id = (int) get_option('wp_page_for_privacy_policy'); }
    $page = $id ? get_post($id) : null;
    if ($page && $page->post_type === 'page' && $page->post_status === 'publish') { return $page; }
    $slugs = array('terms'=>array('terms','terms-of-use'), 'privacy'=>array('privacy-policy','privacy'), 'contact'=>array('contact','contact-us'));
    foreach ($slugs[$kind] as $slug) {
        $page = get_page_by_path($slug, OBJECT, array('page'));
        if ($page && $page->post_status === 'publish') { return $page; }
    }
    $titles = array('terms'=>'利用規約','privacy'=>'プライバシーポリシー','contact'=>'お問い合わせ');
    $pages = get_posts(array('post_type'=>'page','post_status'=>'publish','title'=>$titles[$kind],'posts_per_page'=>2));
    return count($pages) === 1 ? $pages[0] : null;
}
function stage_footer_url($kind) {
    $page = stage_footer_page($kind);
    return $page ? get_permalink($page) . ($kind === 'contact' ? '' : '#stage-' . $kind) : '';
}
function stage_contact_form_content($page_id) {
    if (!shortcode_exists('contact-form-7')) { return ''; }
    $settings = get_option('stage_footer_routes', array());
    $id = isset($settings['form']) ? absint($settings['form']) : 0;
    if ($id && get_post_type($id) === 'wpcf7_contact_form' && get_post_status($id) === 'publish') {
        return '[contact-form-7 id="' . $id . '"]';
    }
    $content = get_post_field('post_content', $page_id);
    if (has_shortcode($content, 'contact-form-7') || has_block('contact-form-7/contact-form-selector', $content)) { return $content; }
    $forms = get_posts(array('post_type'=>'wpcf7_contact_form','post_status'=>'publish','posts_per_page'=>2));
    return count($forms) === 1 ? '[contact-form-7 id="' . $forms[0]->ID . '"]' : '';
}
add_action('admin_menu', function () {
    add_theme_page('フッターのリンク先','フッターのリンク先','manage_options','stage-footer-routes','stage_footer_settings_screen');
});
add_action('admin_init', function () {
    register_setting('stage_footer_routes_group','stage_footer_routes',array('sanitize_callback'=>function ($input) {
        if (!is_array($input)) { return array(); }
        $out = array();
        foreach (array('terms','privacy','contact','form') as $key) {
            $id = isset($input[$key]) ? absint($input[$key]) : 0;
            $type = $key === 'form' ? 'wpcf7_contact_form' : 'page';
            $out[$key] = $id && get_post_type($id) === $type && get_post_status($id) === 'publish' ? $id : 0;
        }
        if ($out['contact'] && in_array($out['contact'],array($out['terms'],$out['privacy']),true)) {
            add_settings_error('stage_footer_routes','same-contact','お問い合わせは規約ページとは別の固定ページを選択してください。');
            $out['contact'] = 0;
        }
        return $out;
    }));
});
function stage_footer_settings_screen() {
    if (!current_user_can('manage_options')) { return; }
    $settings = get_option('stage_footer_routes',array());
    $pages = get_posts(array('post_type'=>'page','post_status'=>'publish','posts_per_page'=>-1,'orderby'=>'title','order'=>'ASC'));
    $forms = get_posts(array('post_type'=>'wpcf7_contact_form','post_status'=>'publish','posts_per_page'=>-1));
    echo '<div class="wrap"><h1>フッターのリンク先</h1><p>既存の固定ページ・フォームを選択します。本文とメール設定は変更しません。</p>';
    settings_errors();
    echo '<form method="post" action="options.php">'; settings_fields('stage_footer_routes_group');
    foreach (array('terms'=>'利用規約','privacy'=>'プライバシーポリシー','contact'=>'お問い合わせ（注意事項ページ）','form'=>'Contact Form 7のフォーム') as $key=>$label) {
        echo '<p><label>' . esc_html($label) . '<br><select name="stage_footer_routes[' . esc_attr($key) . ']">';
        echo '<option value="0">自動判定</option>';
        foreach ($key === 'form' ? $forms : $pages as $item) {
            echo '<option value="' . esc_attr($item->ID) . '" ' . selected(isset($settings[$key]) ? $settings[$key] : 0,$item->ID,false) . '>' . esc_html($item->post_title . '（ID ' . $item->ID . '）') . '</option>';
        }
        echo '</select></label></p>';
    }
    echo '<p>規約とポリシーが同じページなら、本文にそれぞれ「利用規約」「プライバシーポリシー」の見出しを置いてください。該当位置へ移動します。お問い合わせには別の公開固定ページを選びます。</p>';
    echo '<p>フォームが1件だけなら自動利用します。複数ある場合はここで選択してください。フォームが未作成の場合は「お問い合わせ」メニューからContact Form 7のフォームを作成してください。</p>';
    submit_button();echo '</form></div>';
}
add_action('template_redirect', function () {
    $contact = stage_footer_page('contact');
    if ($contact && is_page($contact->ID)) {
        if (!defined('DONOTCACHEPAGE')) { define('DONOTCACHEPAGE',true); }
        nocache_headers();
    }
});
add_filter('template_include', function ($template) {
    $contact = stage_footer_page('contact');
    return $contact && is_page($contact->ID) ? get_template_directory() . '/page-contact.php' : $template;
});
add_filter('the_content', function ($content) {
    if (is_admin() || !is_page() || !in_the_loop() || !is_main_query()) { return $content; }
    foreach (array('terms'=>'利用規約','privacy'=>'プライバシーポリシー') as $kind=>$title) {
        $page = stage_footer_page($kind);
        if (!$page || (int) get_queried_object_id() !== (int) $page->ID) { continue; }
        $anchor = '<span id="stage-' . $kind . '" class="stage-policy-anchor" aria-hidden="true"></span>';
        // 同じページに二つの文書がある場合は、それぞれの見出しへアンカーを付ける。
        $count = 0;
        $content = preg_replace_callback('~<h([1-6])\b[^>]*>\s*(?:<[^>]+>\s*)*' . preg_quote($title,'~') . '\s*(?:</[^>]+>\s*)*</h\1>~u',function ($m) use ($anchor) { return $anchor . $m[0]; },$content,1,$count);
        if (!$count) { $content = $anchor . $content; }
        if ($kind === 'privacy' && stage_footer_url('contact')) {
            $content .= '<p class="stage-policy-contact"><a class="text-link" href="' . esc_url(stage_footer_url('contact')) . '">お問い合わせ前の注意事項を確認する →</a></p>';
        }
    }
    return $content;
});
