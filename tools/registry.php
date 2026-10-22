<?php
// 公開するツールだけを登録。path は portfolio-hub-wp-theme フォルダからの相対パス。
// 「//」から行末まではコメントです。下の example の例は、先頭の // を消すと登録が有効になります。
// 書式：'識別キー' => array('label' => '管理画面の表示名', 'path' => 'ツールの相対パス'),
// 識別キー：半角英小文字・数字・_・- を使用し、他のツールと重複させません。
// 投稿に保存される値なので、登録後の識別キー変更は避けてください。
// label：投稿編集画面「起動するツール」に表示する名前です。
// path：例 tools/image/index.php。先頭に / やドメイン名は付けません。
// 新規ツールのPHPは tools フォルダ内へ配置します。既存の memo.php は例外です。
// 各項目の文字列を半角のシングルクォートで囲み、登録行の末尾にカンマを付けます。
// PHPファイルを実際に配置してから登録してください。存在しないファイルは選択肢に表示されません。
if (!defined('ABSPATH')) { exit; }
return array(
    'memo' => array('label' => 'メモツール', 'path' => 'memo.php'),
    'image' => array('label' => '画像リサイズ・編集', 'path' => 'tools/image.php'),
    'batch' => array('label' => '画像の並べ替え・リネーム・PDF', 'path' => 'tools/batch.php'),
    // 'example' => array('label' => '追加ツール', 'path' => 'tools/example.php'),
);
