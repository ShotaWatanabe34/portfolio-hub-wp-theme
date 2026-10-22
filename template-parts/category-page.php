<?php
/** INFO・PORTFOLIO・TOOL共通のカテゴリー一覧。 */
$stage_slug = $args['slug'];
$stage_label = $args['label'];
$stage_compact_layout = in_array($stage_slug, array('info', 'portfolio', 'tool'), true);
$stage_page_key = $stage_slug . '_page';
$stage_info_page = isset($_GET[$stage_page_key]) && is_scalar($_GET[$stage_page_key]) ? max(1, absint(wp_unslash($_GET[$stage_page_key]))) : 1;
$stage_info_url = get_permalink(get_queried_object_id());
$stage_info_posts = new WP_Query(array(
    'post_type'=>'post', 'post_status'=>'publish', 'posts_per_page'=>$stage_slug === 'tool' ? 9 : 12,
    'tax_query'=>array(array('taxonomy'=>'category', 'field'=>'slug', 'terms'=>array($stage_slug), 'include_children'=>false)),
    'paged'=>$stage_info_page, 'ignore_sticky_posts'=>true,
    'orderby'=>array('date'=>'DESC', 'ID'=>'DESC'),
));
get_header();
?>
<main id="main" class="site-main content-main<?php if ($stage_compact_layout) echo ' band-main'; ?>">
  <div class="section-inner">
    <h1 class="screen-reader-text"><?php echo esc_html($stage_label); ?></h1>
    <?php if (!$stage_compact_layout): ?><p><?php echo esc_html($stage_label); ?>カテゴリーの投稿を、新しい順に掲載しています。</p><?php endif; ?>
    <?php if (!$stage_compact_layout) get_template_part('template-parts/category-links'); ?>
    <?php if ($stage_slug === 'tool'): ?>
  </div>
  <?php stage_render_showcase(stage_showcase_query('tool', null, true), 'ピックアップ'); ?>
  <div class="section-inner">
    <?php endif; ?>
    <?php if ($stage_compact_layout): ?>
  </div>
  <div class="info-posts-band">
    <div class="section-inner">
    <?php endif; ?>
    <?php $stage_archive_page = get_page_by_path('archive', OBJECT, array('page')); ?>
    <?php if ($stage_archive_page && $stage_archive_page->post_status === 'publish'): ?>
      <p><a class="archive-entry-link" href="<?php echo esc_url(add_query_arg('arc_cat', $stage_slug, get_permalink($stage_archive_page))); ?>">年月から過去の記事を探す →</a></p>
    <?php endif; ?>
    <?php if ($stage_info_posts->have_posts()): ?>
      <div class="post-grid">
        <?php while ($stage_info_posts->have_posts()): $stage_info_posts->the_post(); ?>
          <?php get_template_part('template-parts/post-card', null, array('portfolio_direct' => $stage_slug === 'portfolio')); ?>
        <?php endwhile; ?>
      </div>
      <?php wp_reset_postdata(); ?>
      <?php if ($stage_info_posts->max_num_pages > 1): ?>
        <?php stage_a_pagination($stage_info_posts->max_num_pages, $stage_info_page, add_query_arg($stage_page_key, '%#%', $stage_info_url), $stage_label . 'のページ送り'); ?>
      <?php endif; ?>
    <?php else: ?>
      <p>このページに表示する<?php echo esc_html($stage_label); ?>の投稿はありません。</p>
      <?php if ($stage_info_page > 1): ?><p><a class="text-link" href="<?php echo esc_url($stage_info_url); ?>"><?php echo esc_html($stage_label); ?>の最初のページへ戻る</a></p><?php endif; ?>
      <?php wp_reset_postdata(); ?>
    <?php endif; ?>
  </div>
  <?php if ($stage_compact_layout): ?></div><?php endif; ?>
</main>
<?php get_footer(); ?>
