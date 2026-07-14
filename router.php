<?php

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($path === '/blog.html' || $path === '/blog/') {
  header('Location: /blog', true, 301);
  return true;
}

if ($path === '/blog') {
  require __DIR__ . '/blog.php';
  return true;
}

if ($path === '/future-congregation-journey') {
  require __DIR__ . '/future-congregation-journey.php';
  return true;
}

if (preg_match('#^/blog/([a-z0-9][a-z0-9-]*)/?$#', $path, $match)) {
  $_GET['slug'] = $match[1];
  require __DIR__ . '/blog-post.php';
  return true;
}

$routes = array(
  '/feed.xml' => 'feed.php',
  '/sitemap.xml' => 'sitemap.php',
  '/robots.txt' => 'robots.php',
);

if (isset($routes[$path])) {
  require __DIR__ . '/' . $routes[$path];
  return true;
}

return false;
