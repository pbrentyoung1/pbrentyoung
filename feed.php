<?php

require_once __DIR__ . '/inc/blog.php';

function feed_e($value) {
  return htmlspecialchars((string) $value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
}

header('Content-Type: application/rss+xml; charset=utf-8');
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
  <channel>
    <title>Brent Young — The Blog</title>
    <link><?php echo feed_e(blog_site_url('/blog')); ?></link>
    <description>Thoughts on communication, design, leadership, ministry, AI, and the systems that help good work survive real life.</description>
    <language>en-us</language>
    <atom:link href="<?php echo feed_e(blog_site_url('/feed.xml')); ?>" rel="self" type="application/rss+xml" />
    <?php foreach (blog_posts() as $post): ?>
    <item>
      <title><?php echo feed_e($post['title']); ?></title>
      <link><?php echo feed_e(blog_post_url($post, true)); ?></link>
      <guid isPermaLink="true"><?php echo feed_e(blog_post_url($post, true)); ?></guid>
      <pubDate><?php echo feed_e(date(DATE_RSS, strtotime($post['date'] . 'T12:00:00'))); ?></pubDate>
      <category><?php echo feed_e($post['topic']); ?></category>
      <description><?php echo feed_e($post['deck']); ?></description>
    </item>
    <?php endforeach; ?>
  </channel>
</rss>
