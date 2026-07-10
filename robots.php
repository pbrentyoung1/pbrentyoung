<?php

require_once __DIR__ . '/inc/blog.php';

header('Content-Type: text/plain; charset=utf-8');
echo "User-agent: *\n";
if (blog_config('blog_public')) {
  echo "Allow: /\n";
} else {
  echo "Disallow: /blog\n";
}
echo "\nSitemap: " . blog_site_url('/sitemap.xml') . "\n";
