<?php
get_header(); ?>
<main id="main" class="site-main content-main portfolio-main band-main">
  <h1 class="screen-reader-text">PORTFOLIO</h1>
  <?php foreach (stage_showcase_bands() as $band=>$label) {
      $query = stage_showcase_query('portfolio', $band);
      if ($band === 'default' || $query->post_count) { stage_render_showcase($query, $label, true); }
  } ?>
</main>
<?php get_footer(); ?>
