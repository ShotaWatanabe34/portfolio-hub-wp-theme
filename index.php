<?php
/** 投稿・固定ページ・検索結果などの共通フォールバック。 */
get_header();
?>
<main id="main" class="site-main content-main">
<div class="section-inner">
<?php if (is_404()): ?>
  <h1>ページが見つかりません</h1><p>URLをご確認いただくか、サイト内検索をご利用ください。</p>
  <p><a class="text-link" href="<?php echo esc_url(home_url('/')); ?>">TOPへ戻る</a></p>
<?php elseif (is_singular()): ?>
  <?php while (have_posts()): the_post(); ?>
  <article <?php post_class('entry'); ?>>
    <h1 class="entry-title"><?php the_title(); ?></h1>
    <?php if (is_single()): ?><p class="entry-date"><?php echo esc_html(get_the_date('Y.m.d')); ?></p><?php endif; ?>
    <?php if (is_single()): ?><p class="entry-categories"><?php the_category(' / '); ?></p><?php endif; ?>
    <div class="entry-content"><?php the_content(); wp_link_pages(); ?></div>
  </article>
  <?php endwhile; ?>
<?php else: ?>
  <h1 class="<?php echo is_home() ? 'screen-reader-text' : 'section-title'; ?>"><?php
    if (is_search()) echo '検索結果：' . esc_html(get_search_query(false));
    elseif (is_archive()) the_archive_title();
    elseif (is_home()) echo 'INFO';
    else echo '投稿一覧';
  ?></h1>
  <?php if (is_category()) get_template_part('template-parts/category-links'); ?>
  <?php if (have_posts()): ?>
  <div class="post-grid">
    <?php while (have_posts()): the_post(); ?>
    <?php get_template_part('template-parts/post-card'); ?>
    <?php endwhile; ?>
  </div>
  <?php global $wp_query; stage_a_pagination($wp_query->max_num_pages, max(1, get_query_var('paged'))); ?>
  <?php else: ?><p>該当する記事はありません。</p><?php endif; ?>
<?php endif; ?>
</div>
</main>
<?php get_footer(); ?>
