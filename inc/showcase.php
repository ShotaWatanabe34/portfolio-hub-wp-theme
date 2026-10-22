<?php
if (!defined('ABSPATH')) { exit; }
add_action('init', function () {
    register_taxonomy('stage_portfolio_band', 'post', array(
        'labels'=>array('name'=>'ポートフォリオ分類', 'singular_name'=>'ポートフォリオ分類', 'add_new_item'=>'黒帯の分類を追加'),
        'hierarchical'=>true, 'public'=>false, 'show_ui'=>true, 'show_in_rest'=>true, 'show_admin_column'=>true,
        'rewrite'=>false,
    ));
});
add_action('add_meta_boxes', function () {
    add_meta_box('stage-tool-featured', 'TOOLの上部掲載', function ($post) {
        wp_nonce_field('stage_tool_featured', 'stage_tool_nonce');
        echo '<label><input type="checkbox" name="stage_tool_featured" value="1" ' . checked(get_post_meta($post->ID, '_stage_tool_featured', true), '1', false) . '> 上部カルーセルに掲載する</label><p>カテゴリー「tool」の投稿に適用。下の一覧にも掲載されます。</p>';
    }, 'post', 'side');
});
add_action('save_post_post', function ($id) {
    if (wp_is_post_revision($id) || wp_is_post_autosave($id) || !current_user_can('edit_post', $id)) { return; }
    if (!isset($_POST['stage_tool_nonce']) || !is_string($_POST['stage_tool_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['stage_tool_nonce'])), 'stage_tool_featured')) { return; }
    if (isset($_POST['stage_tool_featured']) && $_POST['stage_tool_featured'] === '1') { update_post_meta($id, '_stage_tool_featured', '1'); }
    else { delete_post_meta($id, '_stage_tool_featured'); }
});
function stage_band_clause($band) {
    return $band === 'default' ? array('taxonomy'=>'stage_portfolio_band', 'operator'=>'NOT EXISTS') : array('taxonomy'=>'stage_portfolio_band', 'field'=>'term_id', 'terms'=>array(absint(str_replace('band-', '', $band))), 'include_children'=>false);
}
function stage_showcase_query($category, $band = null, $featured = false) {
    $args = array('post_type'=>'post', 'post_status'=>'publish', 'posts_per_page'=>-1, 'no_found_rows'=>true, 'ignore_sticky_posts'=>true,
        'orderby'=>array('date'=>'DESC','ID'=>'DESC'),
        'tax_query'=>array(array('taxonomy'=>'category','field'=>'slug','terms'=>array($category),'include_children'=>false)));
    if ($band !== null) { $args['tax_query'][] = stage_band_clause($band); }
    if ($featured) { $args['meta_query'] = array(array('key'=>'_stage_tool_featured','value'=>'1')); }
    return new WP_Query($args);
}
function stage_showcase_bands() {
    $bands = array('default'=>'ポートフォリオ');
    $terms = get_terms(array('taxonomy'=>'stage_portfolio_band','hide_empty'=>false,'orderby'=>'name','order'=>'ASC'));
    if (!is_wp_error($terms)) { foreach ($terms as $term) { $bands['band-' . $term->term_id] = $term->name; } }
    return $bands;
}
function stage_render_showcase($query, $label, $portfolio = false) {
    $id = wp_unique_id('showcase-'); ?>
    <section class="showcase-band<?php if (!$portfolio) echo ' tool-showcase-band'; ?>" aria-labelledby="<?php echo esc_attr($id); ?>">
      <div class="section-inner">
        <h2 class="showcase-heading" id="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?></h2>
        <?php if (!$portfolio || $query->have_posts()): ?>
        <div class="showcase" data-showcase>
          <div class="showcase-viewport"><div class="showcase-track" tabindex="0" role="region" aria-label="<?php echo esc_attr($label . '。左右矢印キーで作品を切り替え'); ?>">
          <?php if (!$portfolio) get_template_part('template-parts/memo-card'); ?>
          <?php while ($query->have_posts()): $query->the_post(); get_template_part('template-parts/post-card', null, array('portfolio_direct'=>$portfolio)); endwhile; ?>
          </div></div>
          <button class="showcase-prev" type="button" aria-label="前の作品" hidden>‹</button>
          <button class="showcase-next" type="button" aria-label="次の作品" hidden>›</button>
          <span class="screen-reader-text" aria-live="polite" data-showcase-status></span>
        </div>
        <?php else: ?><p class="showcase-empty"><?php echo $portfolio ? '公開作品はまだありません。' : '上部掲載のツールは準備中です。'; ?></p><?php endif; ?>
      </div>
    </section>
    <?php wp_reset_postdata();
}
