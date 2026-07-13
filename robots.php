<?php

require_once __DIR__ . '/inc/blog.php';

header('Content-Type: text/plain; charset=utf-8');
echo "User-agent: *\n";
/* Social preview crawlers must be able to fetch blog pages. The blog's
   noindex response below still keeps unpublished content out of search. */
echo "Allow: /\n";
echo "\nSitemap: " . blog_site_url('/sitemap.xml') . "\n";
