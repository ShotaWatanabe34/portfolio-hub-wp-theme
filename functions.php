<?php
/** portfolio-hub-wp-theme: テーマ機能のみ。HTMLはここへ書かないでください。 */
if (!defined('ABSPATH')) { exit; }

add_action('after_setup_theme', function () {
    add_theme_support('post-thumbnails');
    add_theme_support('title-tag');
});

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('portfolio-hub-wp-theme-style', get_stylesheet_uri(), array(), '1.15.0');
    wp_enqueue_style('portfolio-hub-wp-theme-responsive', get_theme_file_uri('/css/responsive.css'), array('portfolio-hub-wp-theme-style'), '1.15.0');
    if (is_page('archive')) {
        wp_enqueue_script('portfolio-hub-wp-theme-archive', get_theme_file_uri('/js/archive-filters.js'), array(), '1.15.0', true);
    }
    if (is_front_page()) {
        wp_enqueue_script('portfolio-hub-wp-theme-slider', get_theme_file_uri('/js/post-slider.js'), array(), '1.15.0', true);
    }
});

// 公開側の投稿一覧・カテゴリー一覧だけを変更。管理画面や検索には影響させない。
add_action('pre_get_posts', function ($query) {
    if (is_admin() || !$query->is_main_query() || $query->is_feed()) { return; }
    // INFOをWordPressの投稿ページに指定している場合も同じカテゴリーに限定。
    if ($query->is_home() && !$query->is_front_page()) {
        $query->set('tax_query', array(array('taxonomy'=>'category', 'field'=>'slug', 'terms'=>array('info'), 'include_children'=>false)));
    }
    if ($query->is_home() || $query->is_category()) {
        $query->set('posts_per_page', 12);
        $query->set('ignore_sticky_posts', true);
        $query->set('orderby', array('date' => 'DESC', 'ID' => 'DESC'));
    }
});

require_once get_template_directory() . '/inc/archive-data.php';

// 閲覧カウンターは停止。既存の集計データは削除しない。

require_once get_template_directory() . '/inc/pagination.php';

require_once get_template_directory() . '/inc/portfolio-link.php';
require_once get_template_directory() . '/inc/tool-link.php';
require_once get_template_directory() . '/inc/contact-policy.php';

require_once get_template_directory() . '/inc/showcase.php';
add_action('wp_enqueue_scripts', function () {
    if (is_page(array('portfolio', 'tool'))) { wp_enqueue_script('stage-showcase', get_theme_file_uri('/js/showcase.js'), array(), '1.15.0', true); }
});

add_action('wp_enqueue_scripts', function () { wp_enqueue_script('stage-memo-sidebar', get_theme_file_uri('/js/memo-sidebar.js'), array(), '1.15.0', true); });

add_action('wp_enqueue_scripts', function () { wp_enqueue_style('stage-memo-layout', get_theme_file_uri('/css/memo-layout.css'), array('portfolio-hub-wp-theme-responsive'), '1.15.0'); });
