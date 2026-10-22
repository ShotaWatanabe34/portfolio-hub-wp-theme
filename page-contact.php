<?php
/** お問い合わせ固定ページ（スラッグ contact）：注意事項→同意→フォーム。 */
get_header();
$stage_privacy_url = stage_footer_url('privacy');
$stage_contact_page = get_queried_object_id();
$stage_contact_content = stage_contact_form_content($stage_contact_page);
$stage_contact_ready = $stage_contact_content !== '';
$stage_contact_accepted = isset($_POST['stage_contact_agree'], $_POST['stage_contact_nonce'])
    && $_POST['stage_contact_agree'] === '1'
    && is_string($_POST['stage_contact_nonce'])
    && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['stage_contact_nonce'])), 'stage_contact_notice');
?>
<main id="main" class="site-main content-main">
  <div class="section-inner">
    <article class="entry stage-contact">
      <h1>お問い合わせ</h1>
      <?php if (!$stage_contact_accepted): ?>
        <h2>お問い合わせ前の注意事項</h2>
        <ul>
          <li>お問い合わせフォームの内容は、本サイトのサーバーとメール配送の仕組みを経由して運営者へ送信されます。ブラウザ内で完結する画像・メモツールとは異なります。</li>
          <li>返信用メールアドレス、件名、お問い合わせ本文を、回答・事実確認・必要な連絡のために使用します。</li>
          <li>パスワード、秘密鍵、機密ファイル、対応に不要な第三者の個人情報は記載しないでください。</li>
          <li>不具合については、ブラウザ名・操作手順・表示されたメッセージをお知らせください。添付ファイルは受け付けません。</li>
          <li>内容によっては回答に時間を要する場合や、回答できない場合があります。</li>
        </ul>
        <?php if ($stage_privacy_url): ?><p><a class="text-link" href="<?php echo esc_url($stage_privacy_url); ?>" target="_blank" rel="noopener noreferrer">プライバシーポリシーを確認する（新しいタブ）</a></p><?php else: ?><p>プライバシーポリシーのリンク先が未設定です。</p><?php endif; ?>
        <?php if ($stage_privacy_url): ?><form method="post" action="<?php echo esc_url(get_permalink($stage_contact_page)); ?>" class="stage-contact-consent">
          <?php wp_nonce_field('stage_contact_notice', 'stage_contact_nonce'); ?>
          <label><input type="checkbox" name="stage_contact_agree" value="1" required> 注意事項とプライバシーポリシーを確認し、記載された情報の取扱いに同意します。</label>
          <p><button type="submit">同意してお問い合わせフォームへ</button></p>
        </form><?php endif; ?>
      <?php elseif (!$stage_contact_ready): ?>
        <p>注意事項への同意を確認しました。お問い合わせフォームはまだ設定されていません。</p>
        <?php if (current_user_can('manage_options')): ?>
          <p><a class="text-link" href="<?php echo esc_url(admin_url('themes.php?page=stage-footer-routes')); ?>">外観 → フッターのリンク先で、使用するフォームを選択してください。</a></p>
        <?php endif; ?>
      <?php else: ?>
        <p>注意事項への同意を確認しました。送信前にも入力内容と確認欄をご確認ください。</p>
        <div class="entry-content"><?php echo apply_filters('the_content', $stage_contact_content); ?></div>
        <p><a class="text-link" href="<?php echo esc_url(get_permalink($stage_contact_page)); ?>">注意事項へ戻る</a></p>
      <?php endif; ?>
    </article>
  </div>
</main>
<?php get_footer(); ?>
