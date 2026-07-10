<?php

require_once __DIR__ . '/inc/blog.php';

function sitemap_e($value) {
  return htmlspecialchars((string) $value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
}

$urls = array(
  array('loc' => blog_site_url('/'), 'lastmod' => date('Y-m-d', filemtime(__DIR__ . '/index.html')), 'priority' => '1.0'),
);

if (blog_config('blog_public')) {
  $urls[] = array('loc' => blog_site_url('/blog'), 'lastmod' => date('Y-m-d', filemtime(__DIR__ . '/posts/index.json')), 'priority' => '0.8');
  foreach (blog_posts() as $post) {
    $urls[] = array('loc' => blog_post_url($post, true), 'lastmod' => $post['date'], 'priority' => $post['featured'] ? '0.9' : '0.7');
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
