# portfolio-hub-wp-theme

学習記録・制作物・ツールをまとめて公開するために制作している、個人ポートフォリオ向けのWordPressオリジナルテーマです。

現在は制作・学習途中のテーマで、実際のサイト運用に合わせながら機能やデザインを更新しています。

## 概要

- テーマ名: `portfolio-hub-wp-theme`
- バージョン: `1.15.0`
- 種別: WordPress クラシックテーマ
- 用途: 学習記録、制作物、ツール、記事の集約

## 主な機能

- TOPページで最新記事をカルーセル表示
- INFO / PORTFOLIO / TOOL / ARCHIVE の専用ページ構成
- 投稿カテゴリーを利用したコンテンツ分類
- PORTFOLIO投稿から外部制作物へのリンク
- TOOL投稿とテーマ内ツールの連携
- ポートフォリオ分類用の独自タクソノミー
- 記事アーカイブの年月・カテゴリー絞り込み
- ブラウザ内で利用できるメモ機能
- 画像編集・一括リネーム・PDF作成などのローカルツール
- レスポンシブ対応
- キーボード操作やフォーカス表示を考慮したUI
- Contact Form 7との任意連携

## ディレクトリ構成

```text
portfolio-hub-wp-theme/
├─ css/                # 共通・レスポンシブ・メモ用CSS
├─ images/             # テーマ内で使用する画像
├─ inc/                # テーマ機能を分割したPHP
├─ js/                 # カルーセル、フィルター等のJavaScript
├─ template-parts/     # 再利用するテンプレート部品
├─ tools/              # ブラウザ上で動作するローカルツール
├─ front-page.php      # TOPページ
├─ functions.php       # テーマ機能の読み込み
├─ header.php          # ヘッダー・グローバルナビ
├─ footer.php          # フッター
├─ index.php           # 投稿一覧等の基本テンプレート
├─ memo.php            # メモツール
├─ page-info.php       # INFO
├─ page-portfolio.php  # PORTFOLIO
├─ page-tool.php       # TOOL
├─ page-archive.php    # ARCHIVE
├─ page-contact.php    # CONTACT
└─ style.css           # テーマ情報・メインスタイル
```

## 動作環境

このテーマはWordPress上での利用を前提としています。

- PHP 7.0以上の構文を使用しています
- WordPress 6.2以降では、`WP_HTML_Tag_Processor` を利用するPORTFOLIO向け機能を使用できます
- Contact Form 7はお問い合わせフォームを利用する場合のみ必要です
- 一部のローカルツールのファイル保存機能は、HTTPS環境とFile System Access APIに対応したChrome / Edge等を前提としています

> 実際に動作確認したWordPress・PHPのバージョンは、ローカル環境で確認後に追記予定です。

## インストール

1. このリポジトリをダウンロードまたはcloneします。
2. `portfolio-hub-wp-theme` フォルダをWordPressの次の場所へ配置します。

```text
wp-content/themes/portfolio-hub-wp-theme/
```

3. WordPress管理画面の **外観 → テーマ** を開きます。
4. `portfolio-hub-wp-theme` を有効化します。

現在使用中のテーマから切り替える場合は、既存テーマを削除せず、ローカル環境などで表示と機能を確認してから切り替えることを推奨します。

## ページ構成

グローバルナビゲーションでは、次のスラッグを持つ公開固定ページを検出してリンクを表示します。

| 表示名 | スラッグ |
| --- | --- |
| INFO | `info` |
| PORTFOLIO | `portfolio` |
| TOOL | `tool` |
| ARCHIVE | `archive` |

対象ページが存在しない場合は「準備中」として表示されます。

## 投稿カテゴリー

主に次のカテゴリーを使用する構成です。

- `info` : お知らせ・情報系の記事
- `portfolio` : 制作物
- `tool` : ツール紹介・ツール起動用の記事

PORTFOLIOでは、投稿本文内の最初の有効な外部リンクを制作物のリンク先として利用する機能があります。

TOOLでは、投稿編集画面からテーマ内ツールを選択し、カードから直接ツールを開くことができます。ツールの登録先は `tools/registry.php` です。

## お問い合わせ

Contact Form 7が利用可能な場合、テーマ設定から既存フォームとお問い合わせページを関連付けられます。

WordPress管理画面の **外観 → フッターのリンク先** から、次のページ・フォームを指定できます。

- 利用規約
- プライバシーポリシー
- お問い合わせページ
- Contact Form 7のフォーム

Contact Form 7を使用しない場合でも、テーマ自体の基本表示は利用できます。

## メモ機能について

テーマ内のメモ機能では、ブラウザのLocalStorage・IndexedDB・SessionStorageを使用しています。

テーマ名を `stage-a` から `portfolio-hub-wp-theme` へ変更した際、既存の保存データや設定との互換性を維持するため、一部の内部識別子には次のような旧名称が残っています。

```text
stage-a-memo-files
stage-a-memo-v1
stage-a-memo-active
stage-a-counter-tab
stage-a/v1
```

これらは表示上のテーマ名とは別の内部キーです。既存データの移行処理を用意せずに変更すると、ブラウザに保存したデータ等を参照できなくなる可能性があるため、現時点では維持しています。

## 開発状況

このリポジトリは学習・制作を兼ねた開発中のプロジェクトです。

デザイン、ファイル構成、機能、内部APIなどは今後変更する可能性があります。

## ライセンス

現時点ではライセンスを指定していません。

ライセンスを追加するまでは、第三者による利用・改変・再配布を許可するオープンソースライセンスは付与されていません。
