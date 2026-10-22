<?php
if (!defined('ABSPATH')) { exit; }
/** 共通の中央ページ送り。URLには呼び出し側の絞り込み条件を維持。 */
function stage_a_pagination($total, $current = 1, $base = null, $label = 'ページ送り') {
    $total = (int) $total;
    if ($total <= 1) { return; }
    $current = max(1, min($total, (int) $current));
    $url = static function ($page) use ($base) {
        return $base === null ? get_pagenum_link($page, false) : str_replace('%#%', (string) $page, $base);
    };
    $icons = array(
        'first'=>'<path d="M6 5v14M17 5l-7 7 7 7"/>',
        'prev'=>'<path d="M15 5l-7 7 7 7"/>',
        'next'=>'<path d="M9 5l7 7-7 7"/>',
        'last'=>'<path d="M18 5v14M7 5l7 7-7 7"/>',
    );
    $item = static function ($page, $text, $description, $disabled = false) use ($url, $icons) {
        $content = isset($icons[$text]) ? '<svg class="pagination-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">' . $icons[$text] . '</svg>' : esc_html($text);
        if ($disabled) {
            echo '<li><span class="page-numbers is-disabled" aria-disabled="true" aria-label="' . esc_attr($description) . '">' . $content . '</span></li>';
        } else {
            echo '<li><a class="page-numbers" href="' . esc_url($url($page)) . '" aria-label="' . esc_attr($description) . '">' . $content . '</a></li>';
        }
    };
    echo '<nav class="pagination stage-pagination" aria-label="' . esc_attr($label) . '"><ul class="page-numbers">';
    $item(1, 'first', '最初のページへ', $current === 1);
    $item(max(1, $current - 1), 'prev', '前のページへ', $current === 1);
    if ($total <= 7) {
        $pages = range(1, $total);
    } else {
        $pages = array(1, $total);
        for ($page = max(1, $current - 1); $page <= min($total, $current + 1); $page++) { $pages[] = $page; }
        $pages = array_values(array_unique($pages)); sort($pages, SORT_NUMERIC);
        $expanded = array(); $previous = 0;
        foreach ($pages as $page) {
            if ($previous && $page - $previous === 2) { $expanded[] = $previous + 1; }
            $expanded[] = $page; $previous = $page;
        }
        $pages = $expanded;
    }
    $previous = 0;
    foreach ($pages as $page) {
        if ($previous && $page - $previous > 1) { echo '<li><span class="page-numbers dots" aria-hidden="true">…</span></li>'; }
        if ($page === $current) {
            echo '<li><span class="page-numbers current" aria-current="page" aria-label="' . esc_attr($page . 'ページ目、現在のページ') . '">' . esc_html($page) . '</span></li>';
        } else { $item($page, (string) $page, $page . 'ページ目へ'); }
        $previous = $page;
    }
    $item(min($total, $current + 1), 'next', '次のページへ', $current === $total);
    $item($total, 'last', '最後のページへ', $current === $total);
    echo '</ul></nav>';
}
