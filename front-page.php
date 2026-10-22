<?php
/**
 * Front page template
 *
 * TOP専用テンプレート。
 * 共通ヘッダー・ナビ・検索は header.php 側で管理する想定。
 */

get_header();
?>

<main id="main" class="site-main">
    <h1 class="screen-reader-text"><?php bloginfo('name'); ?></h1>

    <!-- 最新投稿 -->
    <section class="top-posts" aria-labelledby="top-posts-title">
        <div class="section-inner">

            <header class="section-header">
                <?php // TOPの最新投稿見出し。表示文字は下の <h2 ...> と </h2> の間を変更します。id/classはそのまま残してください。 ?>
                <h2 id="top-posts-title" class="section-title display-title">最新の記事</h2>
            </header>

            <?php
            $latest_posts = new WP_Query(
                array(
                    'post_type' => 'post',
                    'post_status' => 'publish',
                    'posts_per_page' => 5,
                    'ignore_sticky_posts' => true,
                    'no_found_rows' => true,
                    'orderby' => array('date' => 'DESC', 'ID' => 'DESC'),
                )
            );
            ?>

            <?php if ($latest_posts->have_posts()): ?>

                <div class="top-post-slider" data-post-slider>
                    <div class="slider-viewport">
                    <div id="latest-post-track" class="top-post-track" tabindex="0" role="region" aria-label="最新投稿。左右にスクロールできます">

                        <?php while ($latest_posts->have_posts()): ?>
                            <?php $latest_posts->the_post(); ?>

                            <article <?php post_class('top-post-card'); ?>>

                                <?php $stage_tool_destination = stage_a_tool_url(get_the_ID()); ?>
                                <a class="top-post-link" href="<?php echo esc_url($stage_tool_destination ?: get_permalink()); ?>"<?php if ($stage_tool_destination): ?> target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr(get_the_title() . '（新しいタブで開きます）'); ?>"<?php endif; ?>>

                                    <div class="top-post-image">

                                        <?php if (has_post_thumbnail()): ?>

                                            <?php
                                            the_post_thumbnail(
                                                'large',
                                                array(
                                                    'class' => 'top-post-thumbnail',
                                                    'loading' => 'lazy',
                                                )
                                            );
                                            ?>

                                        <?php else: ?>

                                            <div class="top-post-noimage" aria-hidden="true">
                                                <span>NO IMAGE</span>
                                            </div>

                                        <?php endif; ?>

                                    </div>

                                    <div class="top-post-meta">

                                        <h3 class="top-post-title">
                                            <?php the_title(); ?>
                                        </h3>

                                        <time class="top-post-date" datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                            <?php echo esc_html(get_the_date('Y.m.d')); ?>
                                        </time>

                                    </div>

                                </a>

                            </article>

                        <?php endwhile; ?>

                    </div>

                    </div>
                    <div class="slider-controls" hidden>
                        <button class="top-post-prev" type="button" aria-label="前の投稿を表示" aria-controls="latest-post-track"><svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M15 5 8 12l7 7"/></svg></button>
                        <button class="top-post-next" type="button" aria-label="次の投稿を表示" aria-controls="latest-post-track"><svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m9 5 7 7-7 7"/></svg></button>
                    </div>
                </div>

                <?php wp_reset_postdata(); ?>

            <?php else: ?>

                <div class="top-post-empty">
                    <p>現在、公開されている投稿はありません。</p>
                </div>

            <?php endif; ?>

        </div>
    </section>


</main>
<?php get_footer(); ?>
