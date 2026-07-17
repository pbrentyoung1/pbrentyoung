<?php

require_once __DIR__ . '/inc/blog.php';

function sitemap_e($value) {
  return htmlspecialchars((string) $value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
}

$urls = array(
  array('loc' => blog_site_url('/'), 'lastmod' => date('Y-m-d', filemtime(__DIR__ . '/index.php')), 'priority' => '1.0'),
  array('loc' => blog_site_url('/future-congregation-journey'), 'lastmod' => date('Y-m-d', filemtime(__DIR__ . '/future-congregation-journey.php')), 'priority' => '0.7'),
  array('loc' => blog_site_url('/glossary'), 'lastmod' => date('Y-m-d', max(array_map('filemtime', glob(__DIR__ . '/glossary/*.md') ?: array(__FILE__)))), 'priority' => '0.6'),
);

if (blog_config('blog_public')) {
  $posts = blog_posts();
  $blogModified = filemtime(__DIR__ . '/posts/index.json');

  foreach ($posts as $post) {
    $postFile = __DIR__ . '/posts/' . $post['slug'] . '.md';
    if (is_file($postFile)) $blogModified = max($blogModified, filemtime($postFile));
  }

  $urls[] = array('loc' => blog_site_url('/blog'), 'lastmod' => date('Y-m-d', $blogModified), 'priority' => '0.8');

  foreach ($posts as $post) {
    $postFile = __DIR__ . '/posts/' . $post['slug'] . '.md';
    $lastmod = is_file($postFile) ? date('Y-m-d', filemtime($postFile)) : $post['date'];
    $urls[] = array('loc' => blog_post_url($post, true), 'lastmod' => $lastmod, 'priority' => $post['featured'] ? '0.9' : '0.7');
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
