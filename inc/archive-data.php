<?php
/** 公開投稿の年月・カテゴリー別件数。本文を全件読み込まず集計する。 */
if (!defined('ABSPATH')) { exit; }
function stage_a_archive_counts($year, $month, $term_id, $band = '') {
    global $wpdb;
    $published = "p.post_type = 'post' AND p.post_status = 'publish'";
    $date_where = '';
    if ($year) {
        $start = sprintf('%04d-%02d-01 00:00:00', $year, $month ?: 1);
        $end = $month ? ($month === 12 ? sprintf('%04d-01-01 00:00:00', $year + 1) : sprintf('%04d-%02d-01 00:00:00', $year, $month + 1)) : sprintf('%04d-01-01 00:00:00', $year + 1);
        $date_where = $wpdb->prepare(' AND p.post_date >= %s AND p.post_date < %s', $start, $end);
    }
    $category_where = '';
    if ($term_id) {
        $category_where = $wpdb->prepare(" AND EXISTS (SELECT 1 FROM {$wpdb->term_relationships} rel INNER JOIN {$wpdb->term_taxonomy} tax ON rel.term_taxonomy_id = tax.term_taxonomy_id WHERE rel.object_id = p.ID AND tax.taxonomy = 'category' AND tax.term_id = %d)", $term_id);
    }
    $band_where = '';
    if ($band) {
        $exists = "SELECT 1 FROM {$wpdb->term_relationships} br INNER JOIN {$wpdb->term_taxonomy} bt ON br.term_taxonomy_id=bt.term_taxonomy_id INNER JOIN {$wpdb->terms} bterm ON bterm.term_id=bt.term_id WHERE br.object_id=p.ID AND bt.taxonomy='stage_portfolio_band'";
        $band_where = $band === 'default' ? " AND NOT EXISTS ({$exists})" : $wpdb->prepare(" AND EXISTS ({$exists} AND bt.term_id=%d)", absint(str_replace('band-', '', $band)));
    }
    $months = $wpdb->get_results("SELECT YEAR(p.post_date) AS year, MONTH(p.post_date) AS month, COUNT(*) AS count FROM {$wpdb->posts} p WHERE {$published}{$category_where}{$band_where} GROUP BY YEAR(p.post_date), MONTH(p.post_date) ORDER BY year DESC, month DESC", ARRAY_A);
    $categories = $wpdb->get_results("SELECT tax.term_id, COUNT(DISTINCT p.ID) AS count FROM {$wpdb->posts} p INNER JOIN {$wpdb->term_relationships} rel ON rel.object_id = p.ID INNER JOIN {$wpdb->term_taxonomy} tax ON tax.term_taxonomy_id = rel.term_taxonomy_id WHERE {$published} AND tax.taxonomy = 'category' {$date_where} GROUP BY tax.term_id", ARRAY_A);
    $all_in_date = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} p WHERE {$published}{$date_where}");
    $portfolio_where = " AND EXISTS (SELECT 1 FROM {$wpdb->term_relationships} cr INNER JOIN {$wpdb->term_taxonomy} ct ON ct.term_taxonomy_id=cr.term_taxonomy_id INNER JOIN {$wpdb->terms} c ON c.term_id=ct.term_id WHERE cr.object_id=p.ID AND ct.taxonomy='category' AND c.slug='portfolio')";
    $bands = $wpdb->get_results("SELECT t.term_id, COUNT(DISTINCT p.ID) AS count FROM {$wpdb->posts} p INNER JOIN {$wpdb->term_relationships} r ON r.object_id=p.ID INNER JOIN {$wpdb->term_taxonomy} tx ON tx.term_taxonomy_id=r.term_taxonomy_id INNER JOIN {$wpdb->terms} t ON t.term_id=tx.term_id WHERE {$published}{$date_where}{$portfolio_where} AND tx.taxonomy='stage_portfolio_band' GROUP BY t.term_id", ARRAY_A);
    $default_band = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} p WHERE {$published}{$date_where}{$portfolio_where} AND NOT EXISTS (SELECT 1 FROM {$wpdb->term_relationships} r INNER JOIN {$wpdb->term_taxonomy} tx ON tx.term_taxonomy_id=r.term_taxonomy_id WHERE r.object_id=p.ID AND tx.taxonomy='stage_portfolio_band')");
    return array('bands'=>$bands ?: array(), 'default_band'=>$default_band, 'months' =>$months ?: array(), 'categories'=>$categories ?: array(), 'all_in_date'=>$all_in_date);
}

// 一覧の操作対象からTOP・ARCHIVE・未分類を除く。投稿データ自体は変更しない。
function stage_a_archive_category_visible($term) {
    if (!$term || is_wp_error($term)) { return false; }
    $name = trim($term->name);
    return !in_array($term->slug, array('archive','top'), true) && !in_array(strtoupper($name), array('ARCHIVE','TOP'), true)
        && $name !== '未分類' && strtolower($name) !== 'uncategorized';
}
