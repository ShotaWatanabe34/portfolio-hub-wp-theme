<?php
/** Read only the first authored hyperlink; never fetch or execute its destination. */
if (!defined('ABSPATH')) { exit; }

function stage_a_portfolio_url($post_id) {
    if (!has_category('portfolio', $post_id) || !class_exists('WP_HTML_Tag_Processor')) { return ''; }
    $content = get_post_field('post_content', $post_id, 'raw');
    $processor = new WP_HTML_Tag_Processor($content);
    while ($processor->next_tag('A')) {
        $href = $processor->get_attribute('href');
        if ($href === null) { continue; }
        // An invalid first link must not silently select a different link.
        if (!is_string($href)) { return ''; }
        $href = trim($href);
        if (preg_match('/[\x00-\x20\x7f]/', $href) || strpos($href, chr(92)) !== false) { return ''; }
        $parts = wp_parse_url($href);
        if (!is_array($parts) || empty($parts['host']) || empty($parts['scheme'])
            || !in_array(strtolower($parts['scheme']), array('http', 'https'), true)
            || isset($parts['user']) || isset($parts['pass'])) { return ''; }
        return esc_url_raw($href, array('http', 'https'));
    }
    return '';
}
