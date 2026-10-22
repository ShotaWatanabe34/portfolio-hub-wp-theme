<?php $stage_categories = get_categories(array('hide_empty'=>true)); ?>
<?php if (!empty($stage_categories) && !is_wp_error($stage_categories)): ?>
<nav class="category-links" aria-label="カテゴリーから探す">
  <span class="category-links-label">カテゴリー：</span>
  <?php foreach ($stage_categories as $stage_category):
    $stage_category_url = get_category_link($stage_category->term_id);
    if (is_wp_error($stage_category_url)) { continue; }
  ?>
  <a href="<?php echo esc_url($stage_category_url); ?>" <?php if (is_category($stage_category->term_id)) echo 'aria-current="page"'; ?>><?php echo esc_html($stage_category->name); ?></a>
  <?php endforeach; ?>
</nav>
<?php endif; ?>
