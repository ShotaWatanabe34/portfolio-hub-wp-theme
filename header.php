<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php if (!current_theme_supports('title-tag')): ?>
<title><?php echo esc_html(wp_get_document_title()); ?></title>
<?php endif; ?>
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="site-shell" class="site-shell">
<a class="skip-link" href="#main">本文へ移動</a>
<header id="top" class="site-header">
  <div class="site-header-inner">
    <a class="site-branding-link" href="<?php echo esc_url(home_url('/')); ?>">
      <?php $stage_logo = wp_get_attachment_image_url((int) get_theme_mod('custom_logo'), 'full'); ?>
      <img class="site-logo" src="<?php echo esc_url($stage_logo ?: get_theme_file_uri('/images/logo.png')); ?>" alt="" width="56" height="56">
      <span class="site-title"><?php bloginfo('name'); ?></span>
    </a>
    <form class="site-search" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
      <label class="screen-reader-text" for="site-search-input">サイト内検索</label>
      <input id="site-search-input" class="site-search-input" type="search" name="s" value="<?php echo get_search_query(); ?>" placeholder="サイト内検索">
      <button class="site-search-button" type="submit">検索</button>
    </form>
  </div>
  <nav class="global-nav" aria-label="メインナビゲーション">
    <ul class="global-nav-list">
      <li><a class="global-nav-link<?php echo is_front_page() ? ' is-current' : ''; ?>" <?php if (is_front_page()) echo 'aria-current="page"'; ?> href="<?php echo esc_url(home_url('/')); ?>">TOP</a></li>
      <?php foreach (array('info'=>'INFO', 'portfolio'=>'PORTFOLIO', 'tool'=>'TOOL', 'archive'=>'ARCHIVE') as $stage_slug => $stage_label):
        $stage_page = get_page_by_path($stage_slug, OBJECT, array('page'));
        $stage_available = $stage_page && $stage_page->post_status === 'publish';
        $stage_current = $stage_available && (is_page($stage_page->ID) || (is_home() && (int) get_option('page_for_posts') === $stage_page->ID));
      ?>
      <li>
        <?php if ($stage_available): ?>
        <a class="global-nav-link<?php echo $stage_current ? ' is-current' : ''; ?>" <?php if ($stage_current) echo 'aria-current="page"'; ?> href="<?php echo esc_url(get_permalink($stage_page)); ?>"><?php echo esc_html($stage_label); ?></a>
        <?php else: ?>
        <span class="global-nav-link is-unavailable" aria-disabled="true"><?php echo esc_html($stage_label); ?><small>準備中</small></span>
        <?php endif; ?>
      </li>
      <?php endforeach; ?>
    </ul>
  </nav>
</header>
