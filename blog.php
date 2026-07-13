<?php

require_once __DIR__ . '/inc/blog.php';

$posts = blog_posts();
$featured = blog_featured_post($posts);
$shortlist = blog_shortlist($posts);
$params = blog_query_params();
$filtered = blog_filter_posts($posts, $params['topic'], $params['tag'], $params['q']);
$hasFilters = $params['topic'] !== '' || $params['tag'] !== '' || $params['q'] !== '';

if (!$hasFilters && $featured) {
  $filtered = array_values(array_filter($filtered, function ($post) use ($featured) {
    return $post['slug'] !== $featured['slug'];
  }));
}

$perPage = (int) blog_config('posts_per_page');
$totalPages = max(1, (int) ceil(count($filtered) / $perPage));
$page = min($params['page'], $totalPages);
$pagePosts = array_slice($filtered, ($page - 1) * $perPage, $perPage);
$topics = blog_topic_counts($posts);

$canonicalParams = $params;
$canonicalParams['page'] = $page > 1 ? $page : '';
$canonical = blog_index_url($canonicalParams, true);
$title = 'The Blog — Brent Young';
$description = 'Thoughts on communication, design, leadership, ministry, AI, and the systems that help good work survive real life.';

$topicLabel = '';
if ($params['topic'] && isset($topics[$params['topic']])) $topicLabel = $topics[$params['topic']]['name'];
$activeLabel = $topicLabel;
if ($params['tag']) $activeLabel = 'Tagged ' . str_replace('-', ' ', $params['tag']);
if ($params['q']) $activeLabel = 'Search results for “' . $params['q'] . '”';

$collection = array(
  '@context' => 'https://schema.org',
  '@type' => 'CollectionPage',
  'name' => $title,
  'description' => $description,
  'url' => $canonical,
  'mainEntity' => array_map(function ($post) {
    return array('@type' => 'BlogPosting', 'headline' => $post['title'], 'url' => blog_post_url($post, true));
  }, $pagePosts),
);

header('Content-Type: text/html; charset=utf-8');
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo blog_e($title); ?></title>
  <meta name="description" content="<?php echo blog_e($description); ?>">
  <meta name="author" content="Brent Young">
  <meta name="robots" content="<?php echo blog_e(blog_robots_meta()); ?>">
  <meta name="theme-color" content="#f4f1ea">
  <link rel="canonical" href="<?php echo blog_e($canonical); ?>">
  <link rel="alternate" type="application/rss+xml" title="Brent Young Blog" href="/feed.xml">
  <link rel="icon" href="/favicon.ico" sizes="48x48">
  <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32.png">
  <link rel="apple-touch-icon" href="/apple-touch-icon.png">
  <link rel="manifest" href="/site.webmanifest">
  <meta property="og:type" content="website">
  <meta property="og:site_name" content="Brent Young">
  <meta property="og:title" content="<?php echo blog_e($title); ?>">
  <meta property="og:description" content="<?php echo blog_e($description); ?>">
  <meta property="og:url" content="<?php echo blog_e($canonical); ?>">
  <meta property="og:image" content="<?php echo blog_e(blog_site_url('/images/og-image.png')); ?>">
  <meta property="og:image:secure_url" content="<?php echo blog_e(blog_site_url('/images/og-image.png')); ?>">
  <meta property="og:image:type" content="image/png">
  <meta property="og:image:width" content="1200">
  <meta property="og:image:height" content="630">
  <meta property="og:image:alt" content="Brent Young — The right story, told the right way.">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:url" content="<?php echo blog_e($canonical); ?>">
  <meta name="twitter:title" content="<?php echo blog_e($title); ?>">
  <meta name="twitter:description" content="<?php echo blog_e($description); ?>">
  <meta name="twitter:image" content="<?php echo blog_e(blog_site_url('/images/og-image.png')); ?>">
  <meta name="twitter:image:alt" content="Brent Young — The right story, told the right way.">
  <script type="application/ld+json"><?php echo json_encode($collection, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG); ?></script>
  <link rel="stylesheet" href="/css/editorial.css">
</head>
<body class="blog-site blog-index-page">
<?php blog_site_header(); ?>

<main class="blog-wrap blog-index">
  <header class="blog-index__masthead">
    <span class="kicker">Field notes from the desk of Brent Young</span>
    <h1>The blog</h1>
    <p><?php echo blog_e($description); ?></p>
  </header>

  <div class="blog-tools">
    <nav class="topic-nav" aria-label="Blog topics">
      <a href="/blog"<?php echo !$params['topic'] ? ' class="is-active" aria-current="page"' : ''; ?>>ALL <span><?php echo count($posts); ?></span></a>
      <?php foreach ($topics as $slug => $topic): ?>
        <a href="<?php echo blog_e(blog_index_url(array('topic' => $slug))); ?>" data-topic="<?php echo blog_e($slug); ?>"<?php echo $params['topic'] === $slug ? ' class="is-active" aria-current="page"' : ''; ?>><?php echo blog_e(strtoupper($topic['name'])); ?> <span><?php echo $topic['count']; ?></span></a>
      <?php endforeach; ?>
    </nav>

    <form class="blog-search" action="/blog" method="get" role="search">
      <?php if ($params['topic']): ?><input type="hidden" name="topic" value="<?php echo blog_e($params['topic']); ?>"><?php endif; ?>
      <?php if ($params['tag']): ?><input type="hidden" name="tag" value="<?php echo blog_e($params['tag']); ?>"><?php endif; ?>
      <label for="blogSearch">SEARCH THE ARCHIVE</label>
      <div><input id="blogSearch" name="q" type="search" value="<?php echo blog_e($params['q']); ?>" placeholder="Search articles"><button type="submit">SEARCH</button></div>
    </form>
  </div>

  <?php if (!$hasFilters && $featured): ?>
    <section class="blog-lead" aria-label="Featured reading">
      <article class="lead-story" data-topic="<?php echo blog_e($featured['topic_slug']); ?>">
        <a class="lead-story__image" href="<?php echo blog_e(blog_post_url($featured)); ?>"><img src="<?php echo blog_e(blog_banner_url($featured)); ?>" alt="" width="1200" height="630"></a>
        <div class="lead-story__body">
          <div class="article-card__meta"><a class="topic-link" href="<?php echo blog_e(blog_index_url(array('topic' => $featured['topic_slug']))); ?>"><?php echo blog_e($featured['topic']); ?></a><span><?php echo $featured['read_minutes']; ?> MIN READ</span></div>
          <h2><a href="<?php echo blog_e(blog_post_url($featured)); ?>"><?php echo blog_e($featured['title']); ?></a></h2>
          <p><?php echo blog_e($featured['deck']); ?></p>
          <a class="read-link" href="<?php echo blog_e(blog_post_url($featured)); ?>">READ THE ARTICLE &rarr;</a>
        </div>
      </article>

      <aside class="blog-lead__side">
        <section class="subscribe-block">
          <p class="side-label">SUBSCRIBE TO THE BLOG</p>
          <h2>Follow the next idea.</h2>
          <a href="/feed.xml">OPEN RSS FEED &rarr;</a>
        </section>
        <?php if ($shortlist): ?>
          <section class="short-list">
            <p class="field-side-label">THE SHORT LIST</p>
            <ol class="field-shortlist">
              <?php foreach ($shortlist as $item): ?>
                <li data-topic="<?php echo blog_e($item['topic_slug']); ?>">
                  <a href="<?php echo blog_e(blog_post_url($item)); ?>">
                    <span class="field-shortlist__thumb"><img src="<?php echo blog_e(blog_thumbnail_url($item)); ?>" alt="" width="64" height="64" loading="lazy" onerror="this.parentNode.style.display='none';"></span>
                    <span class="field-shortlist__body">
                      <span class="field-shortlist__topic"><?php echo blog_e($item['topic']); ?></span>
                      <strong><?php echo blog_e($item['title']); ?></strong>
                      <small><?php echo blog_e(strtoupper(blog_date($item['date']))); ?> &middot; <?php echo (int) $item['read_minutes']; ?> MIN READ</small>
                    </span>
                  </a>
                </li>
              <?php endforeach; ?>
            </ol>
          </section>
        <?php endif; ?>
      </aside>
    </section>
  <?php endif; ?>

  <section class="blog-archive" aria-labelledby="archiveTitle">
    <div class="archive-heading">
      <div><span class="kicker">The complete file</span><h2 id="archiveTitle"><?php echo blog_e($activeLabel ?: 'Latest articles'); ?></h2></div>
      <span class="archive-count"><?php echo count($filtered); ?> ARTICLE<?php echo count($filtered) === 1 ? '' : 'S'; ?></span>
    </div>

    <?php if ($hasFilters): ?>
      <div class="active-filters">
        <?php if ($params['topic']): ?><span>TOPIC: <?php echo blog_e(strtoupper($topicLabel ?: str_replace('-', ' ', $params['topic']))); ?></span><?php endif; ?>
        <?php if ($params['tag']): ?><span>TAG: <?php echo blog_e(strtoupper(str_replace('-', ' ', $params['tag']))); ?></span><?php endif; ?>
        <?php if ($params['q']): ?><span>SEARCH: <?php echo blog_e($params['q']); ?></span><?php endif; ?>
        <a href="/blog">CLEAR FILTERS</a>
      </div>
    <?php endif; ?>

    <?php if ($pagePosts): ?>
      <div class="article-grid">
        <?php foreach ($pagePosts as $post) echo blog_post_card($post); ?>
      </div>
    <?php else: ?>
      <p class="empty-results">Nothing in the file matches that search.</p>
    <?php endif; ?>

    <?php if ($totalPages > 1): ?>
      <nav class="pagination" aria-label="Blog pages">
        <?php if ($page > 1): ?><a href="<?php echo blog_e(blog_index_url(blog_query_params(array('page' => $page - 1)))); ?>">&larr; PREVIOUS</a><?php endif; ?>
        <span>PAGE <?php echo $page; ?> OF <?php echo $totalPages; ?></span>
        <?php if ($page < $totalPages): ?><a href="<?php echo blog_e(blog_index_url(blog_query_params(array('page' => $page + 1)))); ?>">NEXT &rarr;</a><?php endif; ?>
      </nav>
    <?php endif; ?>
  </section>
</main>

<?php blog_site_footer(); ?>
</body>
</html>
