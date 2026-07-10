<?php

require_once __DIR__ . '/inc/blog.php';

$slug = isset($_GET['slug']) ? (string) $_GET['slug'] : '';
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
    <link rel="stylesheet" href="/css/editorial.css">
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
  <meta name="twitter:title" content="<?php echo blog_e($post['title']); ?>">
  <meta name="twitter:description" content="<?php echo blog_e($post['deck']); ?>">
  <meta name="twitter:image" content="<?php echo blog_e($banner); ?>">
  <script type="application/ld+json"><?php echo json_encode($jsonld, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG); ?></script>
  <link rel="stylesheet" href="/css/editorial.css">
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

  <nav class="article-share" aria-label="Share this article">
    <span>SHARE</span>
    <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo $shareUrl; ?>" target="_blank" rel="noopener">LINKEDIN</a>
    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $shareUrl; ?>" target="_blank" rel="noopener">FACEBOOK</a>
    <a href="https://twitter.com/intent/tweet?url=<?php echo $shareUrl; ?>&amp;text=<?php echo $shareTitle; ?>" target="_blank" rel="noopener">X</a>
    <a href="mailto:?subject=<?php echo $shareTitle; ?>&amp;body=<?php echo $shareUrl; ?>">EMAIL</a>
    <button id="copyLink" type="button" data-url="<?php echo blog_e($canonical); ?>">COPY LINK</button>
    <span class="share-copied" id="copiedNote" hidden>COPIED</span>
  </nav>

  <?php if ($toc): ?>
    <details class="mobile-contents">
      <summary>IN THIS ARTICLE</summary>
      <ol>
        <?php foreach ($toc as $index => $heading): ?><li><a href="#<?php echo blog_e($heading['id']); ?>"><span><?php echo str_pad($index + 1, 2, '0', STR_PAD_LEFT); ?></span><?php echo blog_e($heading['label']); ?></a></li><?php endforeach; ?>
      </ol>
    </details>
  <?php endif; ?>

  <div class="article-reading-grid<?php echo $toc ? '' : ' article-reading-grid--no-toc'; ?>">
    <?php if ($toc): ?>
      <aside class="desktop-contents">
        <p>IN THIS ARTICLE</p>
        <ol>
          <?php foreach ($toc as $index => $heading): ?><li><a href="#<?php echo blog_e($heading['id']); ?>"><span><?php echo str_pad($index + 1, 2, '0', STR_PAD_LEFT); ?></span><?php echo blog_e($heading['label']); ?></a></li><?php endforeach; ?>
        </ol>
      </aside>
    <?php endif; ?>

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
        <?php if ($post['principle']): ?><p class="article-principle">FIRST PRINCIPLE: <a href="/index-new.html#principles"><?php echo blog_e(strtoupper($post['principle'])); ?></a></p><?php endif; ?>
        <div class="article-subscribe"><span>SUBSCRIBE TO THE BLOG</span><a href="/feed.xml">OPEN RSS FEED &rarr;</a></div>
      </footer>
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
