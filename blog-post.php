<?php

require_once __DIR__ . '/inc/blog.php';

$slug = isset($_GET['slug']) ? (string) $_GET['slug'] : '';

if ($slug === 'you-cant-polish-your-way-into-relevance') {
  header('Location: /blog/change-vs-polish', true, 301);
  exit;
}

$post = preg_match('/^[a-z0-9][a-z0-9-]*$/', $slug) ? blog_find_post($slug) : null;

if (!$post) {
  http_response_code(404);
  header('Content-Type: text/html; charset=utf-8');
  ?>
  <!doctype html>
  <html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Not found — Brent Young</title>
    <meta name="robots" content="noindex, nofollow">
    <?php echo blog_google_tag(); ?>
    <link rel="stylesheet" href="/css/editorial.css?v=<?php echo (int) filemtime(__DIR__ . '/css/editorial.css'); ?>">
  </head>
  <body class="blog-site">
    <?php blog_site_header(); ?>
    <main class="blog-wrap not-found">
      <span class="kicker">Return to sender</span>
      <h1>Nothing filed under that name.</h1>
      <p>The essay may have moved, or the address was mistyped.</p>
      <a href="/blog">&larr; ALL ARTICLES</a>
    </main>
    <?php blog_site_footer(); ?>
  </body>
  </html>
  <?php
  exit;
}

$rendered = blog_markdown($post['md']);
$toc = count($rendered['toc']) >= 3 ? $rendered['toc'] : array();
$related = blog_related_posts($post, 3);
$canonical = blog_post_url($post, true);
$banner = blog_banner_url($post, true);
$shareUrl = rawurlencode($canonical);
$shareTitle = rawurlencode($post['title']);

/* one set of share buttons, rendered in the desktop sidebar and again
   at the end of the article for mobile (ids must stay unique, so the
   copy button is class-based — see js/article.js) */
$shareButtons = '<span>SHARE</span>'
  . '<a href="https://www.linkedin.com/sharing/share-offsite/?url=' . $shareUrl . '" target="_blank" rel="noopener" aria-label="Share on LinkedIn" title="Share on LinkedIn">'
  . '<svg viewBox="0 0 16 16" width="14" height="14" aria-hidden="true"><path fill="currentColor" d="M3.6 1.5a1.6 1.6 0 1 1 0 3.2 1.6 1.6 0 0 1 0-3.2ZM2.2 6h2.9v8.5H2.2V6Zm4.6 0h2.7v1.2h.1c.4-.7 1.3-1.5 2.7-1.5 2.9 0 3.5 1.9 3.5 4.4v4.4h-2.9v-3.9c0-.9 0-2.1-1.3-2.1s-1.5 1-1.5 2v4H6.8V6Z"/></svg></a>'
  . '<a href="https://www.facebook.com/sharer/sharer.php?u=' . $shareUrl . '" target="_blank" rel="noopener" aria-label="Share on Facebook" title="Share on Facebook">'
  . '<svg viewBox="0 0 16 16" width="14" height="14" aria-hidden="true"><path fill="currentColor" d="M10.5 3.2H12V.6C11.7.6 10.8.5 9.8.5c-2 0-3.4 1.3-3.4 3.6v2H4v3h2.4V15h3V9.1h2.3l.4-3H9.4V4.3c0-.8.2-1.1 1.1-1.1Z"/></svg></a>'
  . '<a href="https://twitter.com/intent/tweet?url=' . $shareUrl . '&amp;text=' . $shareTitle . '" target="_blank" rel="noopener" aria-label="Share on X" title="Share on X">'
  . '<svg viewBox="0 0 16 16" width="13" height="13" aria-hidden="true"><path fill="currentColor" d="M9.5 6.8 14.9 1h-1.3L9 5.9 5.3 1H1l5.7 7.6L1 15h1.3l5-5.5L11.4 15H16L9.5 6.8Zm-1.5 2-.6-.8L2.8 2h2l3.7 4.9.6.8 4.8 6.4h-2L8 8.8Z"/></svg></a>'
  . '<a href="mailto:?subject=' . $shareTitle . '&amp;body=' . $shareUrl . '" aria-label="Share by email" title="Share by email">'
  . '<svg viewBox="0 0 16 16" width="14" height="14" aria-hidden="true"><path fill="none" stroke="currentColor" stroke-width="1.3" d="M1.5 3.5h13v9h-13zM1.5 4l6.5 5 6.5-5"/></svg></a>'
  . '<button class="copy-link" type="button" data-url="' . blog_e($canonical) . '" aria-label="Copy link" title="Copy link">'
  . '<svg viewBox="0 0 16 16" width="14" height="14" aria-hidden="true"><path fill="none" stroke="currentColor" stroke-width="1.3" d="M6.5 9.5a3 3 0 0 0 4.2 0l2.6-2.6a3 3 0 0 0-4.2-4.2L7.7 4.1M9.5 6.5a3 3 0 0 0-4.2 0L2.7 9.1a3 3 0 0 0 4.2 4.2l1.4-1.4"/></svg></button>'
  . '<span class="share-copied" hidden>COPIED</span>';

$jsonld = array(
  '@context' => 'https://schema.org',
  '@type' => 'BlogPosting',
  'headline' => $post['title'],
  'description' => $post['deck'],
  'datePublished' => $post['date'],
  'articleSection' => $post['topic'],
  'keywords' => implode(', ', $post['tags']),
  'image' => $banner,
  'wordCount' => $post['word_count'],
  'mainEntityOfPage' => $canonical,
  'author' => array('@type' => 'Person', 'name' => 'Brent Young', 'url' => blog_config('site_url')),
  'publisher' => array('@type' => 'Person', 'name' => 'Brent Young', 'url' => blog_config('site_url')),
);

header('Content-Type: text/html; charset=utf-8');
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo blog_e($post['title']); ?> — Brent Young</title>
  <meta name="description" content="<?php echo blog_e($post['deck']); ?>">
  <meta name="author" content="Brent Young">
  <meta name="robots" content="<?php echo blog_e(blog_robots_meta()); ?>">
  <meta name="theme-color" content="#f4f1ea">
  <link rel="canonical" href="<?php echo blog_e($canonical); ?>">
  <?php echo blog_google_tag(); ?>
  <link rel="alternate" type="application/rss+xml" title="Brent Young Blog" href="/feed.xml">
  <link rel="icon" href="/favicon.ico" sizes="48x48">
  <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32.png">
  <link rel="apple-touch-icon" href="/apple-touch-icon.png">
  <link rel="manifest" href="/site.webmanifest">
  <meta property="og:type" content="article">
  <meta property="og:site_name" content="Brent Young">
  <meta property="og:title" content="<?php echo blog_e($post['title']); ?>">
  <meta property="og:description" content="<?php echo blog_e($post['deck']); ?>">
  <meta property="og:url" content="<?php echo blog_e($canonical); ?>">
  <meta property="og:image" content="<?php echo blog_e($banner); ?>">
  <meta property="og:image:secure_url" content="<?php echo blog_e($banner); ?>">
  <meta property="og:image:type" content="image/jpeg">
  <meta property="og:image:width" content="1200">
  <meta property="og:image:height" content="630">
  <meta property="og:image:alt" content="<?php echo blog_e($post['banneralt']); ?>">
  <meta property="og:locale" content="en_US">
  <meta property="article:published_time" content="<?php echo blog_e($post['date']); ?>">
  <meta property="article:author" content="Brent Young">
  <meta property="article:section" content="<?php echo blog_e($post['topic']); ?>">
  <?php foreach ($post['tags'] as $tag): ?><meta property="article:tag" content="<?php echo blog_e($tag); ?>">
  <?php endforeach; ?>
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:url" content="<?php echo blog_e($canonical); ?>">
  <meta name="twitter:title" content="<?php echo blog_e($post['title']); ?>">
  <meta name="twitter:description" content="<?php echo blog_e($post['deck']); ?>">
  <meta name="twitter:image" content="<?php echo blog_e($banner); ?>">
  <meta name="twitter:image:alt" content="<?php echo blog_e($post['banneralt']); ?>">
  <script type="application/ld+json"><?php echo json_encode($jsonld, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG); ?></script>
  <link rel="stylesheet" href="/css/editorial.css?v=<?php echo (int) filemtime(__DIR__ . '/css/editorial.css'); ?>">
</head>
<body class="blog-site article-view" data-topic="<?php echo blog_e($post['topic_slug']); ?>">
<?php blog_site_header(); ?>

<main class="article-shell">
  <header class="article-hero">
    <a class="back-link" href="/blog">&larr; ALL ARTICLES</a>
    <a class="article-topic" href="<?php echo blog_e(blog_index_url(array('topic' => $post['topic_slug']))); ?>"><?php echo blog_e($post['topic']); ?></a>
    <h1><?php echo blog_e($post['title']); ?></h1>
    <p class="article-hero__deck"><?php echo blog_e($post['deck']); ?></p>
    <p class="article-hero__byline">BY BRENT YOUNG &middot; <time datetime="<?php echo blog_e($post['date']); ?>"><?php echo blog_e(strtoupper(blog_date($post['date']))); ?></time> &middot; <?php echo $post['read_minutes']; ?> MIN READ</p>
  </header>

  <figure class="article-banner">
    <img src="<?php echo blog_e(blog_banner_url($post)); ?>" alt="<?php echo blog_e($post['banneralt']); ?>" width="1200" height="630">
  </figure>

  <?php if ($toc): ?>
    <details class="mobile-contents">
      <summary>IN THIS ARTICLE</summary>
      <ol>
        <?php foreach ($toc as $heading): ?><li><a href="#<?php echo blog_e($heading['id']); ?>"><?php echo blog_e($heading['label']); ?></a></li><?php endforeach; ?>
      </ol>
    </details>
  <?php endif; ?>

  <div class="article-reading-grid">
    <aside class="desktop-contents">
      <?php if ($toc): ?>
        <p>IN THIS ARTICLE</p>
        <ol>
          <?php foreach ($toc as $heading): ?><li><a href="#<?php echo blog_e($heading['id']); ?>"><?php echo blog_e($heading['label']); ?></a></li><?php endforeach; ?>
        </ol>
      <?php endif; ?>
      <nav class="article-share" aria-label="Share this article"><?php echo $shareButtons; ?></nav>
    </aside>

    <article class="article-content">
      <div class="article-body">
        <?php echo $rendered['html']; ?>
        <span class="endmark" aria-hidden="true">&#10086;</span>
      </div>

      <footer class="article-end">
        <?php if ($post['tags']): ?>
          <div class="article-taxonomy"><span>FILED UNDER</span>
            <?php foreach ($post['tags'] as $tag): ?><a href="<?php echo blog_e(blog_index_url(array('tag' => blog_slugify($tag)))); ?>"><?php echo blog_e(strtoupper($tag)); ?></a><?php endforeach; ?>
          </div>
        <?php endif; ?>
        <?php if ($post['principle']): ?><p class="article-principle">FIRST PRINCIPLE: <a href="/#principles"><?php echo blog_e(strtoupper($post['principle'])); ?></a></p><?php endif; ?>
        <nav class="article-share article-share--mobile" aria-label="Share this article"><?php echo $shareButtons; ?></nav>
      </footer>

      <section class="article-author" aria-labelledby="articleAuthorTitle">
        <img class="article-author__avatar" src="<?php echo blog_e(blog_config('author_avatar')); ?>" alt="Brent Young">
        <div class="article-author__copy">
          <p class="article-author__label">ABOUT THE AUTHOR</p>
          <h2 id="articleAuthorTitle"><?php echo blog_e(blog_config('author')); ?></h2>
          <p><?php echo blog_e(blog_config('author_bio')); ?></p>
        </div>
      </section>
      <div class="article-subscribe"><span>SUBSCRIBE TO THE BLOG</span><a href="/feed.xml">OPEN RSS FEED &rarr;</a></div>
    </article>
  </div>

  <?php if ($related): ?>
    <section class="related-content" aria-labelledby="relatedTitle">
      <div class="archive-heading"><div><span class="kicker">Continue reading</span><h2 id="relatedTitle">Related articles</h2></div></div>
      <div class="article-grid article-grid--related">
        <?php foreach ($related as $item) echo blog_post_card($item, 'related'); ?>
      </div>
    </section>
  <?php endif; ?>
</main>

<?php blog_site_footer(); ?>
<script src="/js/article.js"></script>
</body>
</html>
