<button id="memo-menu" class="memo-menu" type="button" aria-label="メモツールを開く" aria-expanded="false" aria-controls="memo-sidebar" hidden><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg></button>
<aside id="memo-sidebar" class="memo-sidebar" aria-label="メモツール" hidden>
  <div class="memo-sidebar-head"><strong>MEMO</strong><a href="<?php echo esc_url(get_theme_file_uri('/memo.php')); ?>" target="_blank" rel="noopener noreferrer">別タブで開く ↗</a><button type="button" id="memo-close" aria-label="メモを閉じる">×</button></div>
  <iframe id="memo-frame" title="共有メモエディター" data-src="<?php echo esc_url(add_query_arg('panel','1',get_theme_file_uri('/memo.php'))); ?>"></iframe>
</aside>
