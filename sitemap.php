<?php

require_once __DIR__ . '/inc/blog.php';

function sitemap_e($value) {
  return htmlspecialchars((string) $value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
}

function sitemap_lastmod($path) {
  $dates = blog_config('sitemap_lastmod');
  $value = is_array($dates) ? ($dates[$path] ?? '') : '';
  return preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $value) ? $value : '2026-08-13';
}

$urls = array(
  array('loc' => blog_site_url('/'), 'lastmod' => sitemap_lastmod('/'), 'priority' => '1.0'),
  array('loc' => blog_site_url('/evangelistic-marketing-framework'), 'lastmod' => sitemap_lastmod('/evangelistic-marketing-framework'), 'priority' => '0.8'),
  array('loc' => blog_site_url('/future-congregation-journey'), 'lastmod' => sitemap_lastmod('/future-congregation-journey'), 'priority' => '0.7'),
  array('loc' => blog_site_url('/community-snapshot'), 'lastmod' => sitemap_lastmod('/community-snapshot'), 'priority' => '0.8'),
  array('loc' => blog_site_url('/glossary'), 'lastmod' => sitemap_lastmod('/glossary'), 'priority' => '0.6'),
);

if (blog_config('blog_public')) {
  $posts = blog_posts();
  $blogModified = array_reduce($posts, function ($latest, $post) {
    return strcmp($post['modified'], $latest) > 0 ? $post['modified'] : $latest;
  }, '1970-01-01');

  $urls[] = array('loc' => blog_site_url('/blog'), 'lastmod' => $blogModified, 'priority' => '0.8');

  foreach ($posts as $post) {
    $urls[] = array('loc' => blog_post_url($post, true), 'lastmod' => $post['modified'], 'priority' => $post['featured'] ? '0.9' : '0.7');
  }
}

header('Content-Type: application/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($urls as $url): ?>
  <url>
    <loc><?php echo sitemap_e($url['loc']); ?></loc>
    <lastmod><?php echo sitemap_e($url['lastmod']); ?></lastmod>
    <priority><?php echo sitemap_e($url['priority']); ?></priority>
  </url>
<?php endforeach; ?>
</urlset>
