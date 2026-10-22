<?php
/** 固定ページ archive: 年月・カテゴリーをクリックして探す。 */
$stage_value = static function ($key) {
    return isset($_GET[$key]) && is_scalar($_GET[$key]) ? sanitize_text_field(wp_unslash($_GET[$key])) : '';
};
$stage_year = absint($stage_value('arc_year'));
if ($stage_year < 1 || $stage_year > 9998) { $stage_year = 0; }
$stage_month = absint($stage_value('arc_month'));
if (!$stage_year || $stage_month > 12) { $stage_month = 0; }
$stage_category = sanitize_title($stage_value('arc_cat'));
if ($stage_category && !stage_a_archive_category_visible(get_term_by('slug', $stage_category, 'category'))) { $stage_category = ''; }
$stage_band = $stage_category === 'portfolio' ? sanitize_title($stage_value('arc_band')) : '';
$stage_bands = stage_showcase_bands();
$stage_term = $stage_category ? get_term_by('slug', $stage_category, 'category') : false;
$stage_order = 'desc'; // 一覧は新しい順に固定。
$stage_page = max(1, absint($stage_value('arc_page')));
$stage_base = get_permalink(get_queried_object_id());
$stage_state = array_filter(array('arc_year'=>$stage_year, 'arc_month'=>$stage_month, 'arc_cat'=>$stage_category, 'arc_band'=>$stage_band));
$stage_url = static function ($changes = array()) use ($stage_base, $stage_state) {
    if (array_key_exists('arc_cat', $changes) && !array_key_exists('arc_band', $changes)) { $changes['arc_band'] = ''; }
    return add_query_arg(array_filter(array_merge($stage_state, $changes)), $stage_base);
};
$stage_query_args = array('post_type'=>'post', 'post_status'=>'publish', 'posts_per_page'=>12, 'paged'=>$stage_page, 'ignore_sticky_posts'=>true, 'orderby'=>array('date'=>strtoupper($stage_order), 'ID'=>strtoupper($stage_order)));
if ($stage_year) { $stage_query_args['year'] = $stage_year; }
if ($stage_month) { $stage_query_args['monthnum'] = $stage_month; }
if ($stage_category) {
    $stage_query_args['tax_query'] = array(array('taxonomy'=>'category', 'field'=>'slug', 'terms'=>array($stage_category), 'include_children'=>false));
}
if ($stage_band) { $stage_query_args['tax_query'][] = stage_band_clause($stage_band); }
$stage_posts = new WP_Query($stage_query_args);
$stage_counts = stage_a_archive_counts($stage_year, $stage_month, $stage_category ? ($stage_term ? (int) $stage_term->term_id : -1) : 0, $stage_band);
$stage_band_counts = array('default'=>$stage_counts['default_band']);
foreach ($stage_counts['bands'] as $row) { $stage_band_counts['band-' . $row['term_id']] = (int) $row['count']; }
$stage_years = array();
foreach ($stage_counts['months'] as $row) {
    $y = (int) $row['year'];
    if (!isset($stage_years[$y])) { $stage_years[$y] = array('count'=>0, 'months'=>array()); }
    $stage_years[$y]['count'] += (int) $row['count'];
    $stage_years[$y]['months'][(int) $row['month']] = (int) $row['count'];
}
$stage_category_counts = array();
foreach ($stage_counts['categories'] as $row) { $stage_category_counts[(int) $row['term_id']] = (int) $row['count']; }
$stage_terms = get_terms(array('taxonomy'=>'category', 'hide_empty'=>false));
if (!is_wp_error($stage_terms)) { $stage_terms = array_filter($stage_terms, 'stage_a_archive_category_visible'); }
$stage_total_category = array_sum(array_column($stage_years, 'count'));
$stage_count_label = static function ($count) { return '(' . sprintf('%02d', $count) . ')'; };
$stage_filter_label = ($stage_year ? $stage_year . '年' . ($stage_month ? $stage_month . '月' : '') : 'すべての年月') . ' / ' . ($stage_category ? ($stage_term ? $stage_term->name : '該当カテゴリーなし') : 'すべてのカテゴリー');
if ($stage_band) { $stage_filter_label .= ' / ' . ($stage_bands[$stage_band] ?? '該当分類なし'); }
get_header();
?>
<main id="main" class="site-main content-main">
<div class="section-inner">
  <h1 class="screen-reader-text">ARCHIVE</h1>
  <p>年月やカテゴリーを選んで、過去の記事・ツールを探せます。</p>
  <a class="archive-mobile-jump text-link" href="#archive-filters">絞り込み条件へ移動</a>
  <div class="archive-layout">
    <section aria-labelledby="archive-results-title">
      <div class="archive-results-heading">
        <h2 id="archive-results-title">公開記事・ツール</h2>
        <span><?php echo esc_html(number_format_i18n($stage_posts->found_posts)); ?>件</span>
      </div>
      <p class="archive-current"><?php echo esc_html($stage_filter_label); ?></p>
      <?php if ($stage_posts->have_posts()): ?>
      <ol class="archive-list">
        <?php while ($stage_posts->have_posts()): $stage_posts->the_post(); ?>
        <li class="archive-list-item">
          <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date('Y.m.d')); ?></time>
          <h3><a href="<?php the_permalink(); ?>"><?php echo esc_html(get_the_title() ?: '無題'); ?></a></h3>
          <?php $entry_terms = array_filter(get_the_category(), 'stage_a_archive_category_visible'); if ($entry_terms): ?>
          <div class="archive-entry-categories">
            <?php foreach ($entry_terms as $entry_term): ?>
            <a href="<?php echo esc_url($stage_url(array('arc_cat'=>$entry_term->slug))); ?>"><?php echo esc_html($entry_term->name); ?></a>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
        </li>
        <?php endwhile; ?>
      </ol>
      <?php wp_reset_postdata(); ?>
      <?php else: ?>
      <p class="archive-empty">この条件・ページに該当する公開記事はありません。</p>
      <a class="text-link" href="<?php echo esc_url($stage_url()); ?>">この条件の最初のページへ戻る</a>
      <?php wp_reset_postdata(); ?>
      <?php endif; ?>
    </section>
    <aside id="archive-filters" class="archive-filters" aria-label="記事の絞り込み">
      <section class="archive-filter-group" aria-label="年月">
        <a class="archive-filter-link" href="<?php echo esc_url($stage_url(array('arc_year'=>0, 'arc_month'=>0))); ?>" <?php if (!$stage_year) echo 'aria-current="true"'; ?>>すべての年月 <span><?php echo esc_html($stage_count_label($stage_total_category)); ?></span></a>
        <ul class="archive-bullets archive-years">
        <?php
        // 運用開始年と、現在選択している年は0件でも表示する。
        if (!isset($stage_years[2026])) { $stage_years[2026] = array('count'=>0, 'months'=>array()); }
        if ($stage_year && !isset($stage_years[$stage_year])) { $stage_years[$stage_year] = array('count'=>0, 'months'=>array()); }
        krsort($stage_years, SORT_NUMERIC);
        foreach ($stage_years as $year=>$year_data): ?>
          <li>
            <div class="archive-year-row">
              <button class="archive-year-toggle" type="button" aria-label="<?php echo esc_attr($year . '年の月を開閉'); ?>" aria-expanded="true" aria-controls="archive-months-<?php echo esc_attr($year); ?>" data-initial-open="<?php echo $year === $stage_year ? 'true' : 'false'; ?>" hidden><span aria-hidden="true">▼</span></button>
              <a class="archive-filter-link" href="<?php echo esc_url($stage_url(array('arc_year'=>$year, 'arc_month'=>0))); ?>" <?php if ($year === $stage_year) echo 'aria-current="true"'; ?>><?php echo esc_html($year); ?></a>
              <span class="archive-count"><?php echo esc_html($stage_count_label($year_data['count'])); ?></span>
            </div>
            <ul id="archive-months-<?php echo esc_attr($year); ?>" class="archive-bullets archive-month-list">
            <?php for ($month = ($year === 2026 ? 10 : 1); $month <= 12; $month++): ?>
              <li><a class="archive-filter-link" href="<?php echo esc_url($stage_url(array('arc_year'=>$year, 'arc_month'=>$month))); ?>" <?php if ($year === $stage_year && $month === $stage_month) echo 'aria-current="true"'; ?>><?php echo esc_html($month); ?>月 <span><?php echo esc_html($stage_count_label($year_data['months'][$month] ?? 0)); ?></span></a></li>
            <?php endfor; ?>
            </ul>
          </li>
        <?php endforeach; ?>
        </ul>
      </section>
      <section class="archive-filter-group" aria-label="カテゴリー">
        <a class="archive-filter-link" href="<?php echo esc_url($stage_url(array('arc_cat'=>''))); ?>" <?php if (!$stage_category) echo 'aria-current="true"'; ?>>すべてのカテゴリー <span><?php echo esc_html($stage_count_label($stage_counts['all_in_date'])); ?></span></a>
        <ul class="archive-bullets archive-category-list">
        <?php if (!is_wp_error($stage_terms)): foreach ($stage_terms as $term): ?>
          <li<?php if ($term->slug === 'portfolio') echo ' class="has-bands"'; ?>>
            <div class="archive-year-row">
              <?php if ($term->slug === 'portfolio'): ?><button class="archive-year-toggle" type="button" aria-label="PORTFOLIOの分類を開閉" aria-expanded="true" aria-controls="archive-portfolio-bands" data-initial-open="<?php echo $stage_category === 'portfolio' ? 'true' : 'false'; ?>" hidden><span aria-hidden="true">▼</span></button><?php endif; ?>
              <a class="archive-filter-link" href="<?php echo esc_url($stage_url(array('arc_cat'=>$term->slug))); ?>" <?php if ($stage_category === $term->slug && !$stage_band) echo 'aria-current="true"'; ?>><?php echo esc_html($term->name); ?> <span><?php echo esc_html($stage_count_label($stage_category_counts[$term->term_id] ?? 0)); ?></span></a>
            </div>
            <?php if ($term->slug === 'portfolio'): ?>
            <ul id="archive-portfolio-bands" class="archive-bullets archive-month-list">
              <?php foreach ($stage_bands as $band=>$label): ?>
              <li><a class="archive-filter-link" href="<?php echo esc_url($stage_url(array('arc_cat'=>'portfolio', 'arc_band'=>$band))); ?>" <?php if ($stage_band === $band) echo 'aria-current="true"'; ?>><?php echo esc_html($label); ?> <span><?php echo esc_html($stage_count_label($stage_band_counts[$band] ?? 0)); ?></span></a></li>
              <?php endforeach; ?>
            </ul>
            <?php endif; ?>
          </li>
        <?php endforeach; endif; ?>
        </ul>
      </section>
    </aside>
  </div>
  <?php if ($stage_posts->post_count > 0 && $stage_posts->max_num_pages > 1): ?>
  <?php stage_a_pagination($stage_posts->max_num_pages, $stage_page, $stage_url(array('arc_page'=>'%#%')), 'アーカイブのページ送り'); ?>
  <?php endif; ?>
</div>
</main>
<?php get_footer(); ?>
