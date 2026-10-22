<div class="footer-return section-inner"><a class="back-to-top" href="#top">ページの先頭へ戻る ↑</a></div>
<footer class="site-footer">
  <div class="section-inner">
    <ul class="footer-policies">
      <?php foreach (array('terms'=>'利用規約','privacy'=>'プライバシーポリシー','contact'=>'お問い合わせ') as $stage_kind=>$stage_label):
          $stage_url = stage_footer_url($stage_kind); ?>
      <li class="footer-policy footer-contact">
        <?php if ($stage_url): ?>
          <a href="<?php echo esc_url($stage_url); ?>"><?php echo esc_html($stage_label); ?></a>
        <?php else: ?>
          <span><?php echo esc_html($stage_label); ?>（準備中）</span>
        <?php endif; ?>
      </li>
      <?php endforeach; ?>
    </ul>
  </div>
</footer>
</div><!-- .site-shell -->
<?php get_template_part('template-parts/memo-sidebar'); ?>
<?php wp_footer(); ?>
</body>
</html>
