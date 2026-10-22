<?php
$stage_destination = !empty($args['portfolio_direct']) ? stage_a_portfolio_url(get_the_ID()) : stage_a_tool_url(get_the_ID());
?>
<article <?php post_class('top-post-card'); ?>>
  <a class="top-post-link" href="<?php echo esc_url($stage_destination ?: get_permalink(), array('http', 'https')); ?>"<?php if ($stage_destination): ?> target="_blank" rel="noopener noreferrer"<?php endif; ?>>
    <div class="top-post-image">
      <?php if (has_post_thumbnail()): ?>
        <?php the_post_thumbnail('large', array('class'=>'top-post-thumbnail', 'loading'=>'lazy')); ?>
      <?php else: ?><div class="top-post-noimage" aria-hidden="true">NO IMAGE</div><?php endif; ?>
    </div>
    <div class="top-post-meta">
      <h2 class="top-post-title"><?php echo esc_html(get_the_title() ?: '無題'); ?><?php if ($stage_destination): ?><span aria-hidden="true"> ↗</span><span class="screen-reader-text">（新しいタブで開きます）</span><?php endif; ?></h2>
      <time class="top-post-date" datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date('Y.m.d')); ?></time>
    </div>
  </a>
</article>
