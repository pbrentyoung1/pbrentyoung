<?php

require_once __DIR__ . '/inc/blog.php';

$entries = blog_glossary_entries(); /* parsed from glossary/*.md, pre-sorted */

$groups = array();
foreach ($entries as $entry) {
  $letter = strtoupper(substr($entry['term'], 0, 1));
  if (!isset($groups[$letter])) $groups[$letter] = array();
  $groups[$letter][] = $entry;
}

$alphabet = range('A', 'Z');
$canonical = blog_site_url('/glossary');
$title = 'A Working Glossary — Brent Young';
$description = 'The working language behind the site: brand, communication, design, creative leadership, systems, ministry, hospitality, stewardship, and Evangelistic Marketing, defined in one place.';

$jsonld = array(
  '@context' => 'https://schema.org',
  '@type' => 'DefinedTermSet',
  'name' => 'A Working Glossary',
  'description' => $description,
  'url' => $canonical,
  'hasDefinedTerm' => array_map(function ($entry) use ($canonical) {
    return array(
      '@type' => 'DefinedTerm',
      'name' => $entry['term'],
      'description' => $entry['definition'],
      'url' => $canonical . '#' . $entry['slug'],
    );
  }, $entries),
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
  <?php echo blog_google_tag(); ?>
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
  <meta property="og:image:width" content="1200">
  <meta property="og:image:height" content="630">
  <meta property="og:locale" content="en_US">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?php echo blog_e($title); ?>">
  <meta name="twitter:description" content="<?php echo blog_e($description); ?>">
  <meta name="twitter:image" content="<?php echo blog_e(blog_site_url('/images/og-image.png')); ?>">
  <script type="application/ld+json"><?php echo json_encode($jsonld, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG); ?></script>
  <link rel="stylesheet" href="/css/editorial.css">
</head>
<body class="blog-site glossary-page">
<?php blog_site_header(); ?>

<main class="blog-wrap glossary">
  <header class="blog-index__masthead">
    <span class="kicker">The working language</span>
    <h1>A working glossary</h1>
    <p>
      Language shapes how we understand problems. When your team shares the same words,
      you argue about the work instead of the vocabulary, and the work gets clearer for it.
      These are the terms behind everything on this site: the essays, the principles,
      the frameworks, and the projects. Where a word means one thing in business and
      carries different weight in the church, both are here, because the comparison
      is usually where the idea comes alive.
    </p>
    <div class="slugline">
      <span><?php echo count($entries); ?> TERMS ON FILE &middot; REVISED AS THE WORK TEACHES US</span>
      <span>DEPT. REF &middot; THE HOUSE LANGUAGE</span>
    </div>
  </header>

  <nav class="gloss-rail" aria-label="Alphabetical index">
    <?php foreach ($alphabet as $letter): ?>
      <?php if (isset($groups[$letter])): ?>
        <a href="#letter-<?php echo strtolower($letter); ?>"><?php echo $letter; ?></a>
      <?php else: ?>
        <span aria-hidden="true"><?php echo $letter; ?></span>
      <?php endif; ?>
    <?php endforeach; ?>
  </nav>

  <div class="gloss-body">
    <?php foreach ($groups as $letter => $letterEntries): ?>
      <section class="gloss-group" id="letter-<?php echo strtolower($letter); ?>">
        <h2 class="gloss-letter"><?php echo blog_e($letter); ?></h2>

        <?php foreach ($letterEntries as $entry): ?>
          <article class="gloss-entry" id="<?php echo blog_e($entry['slug']); ?>">
            <div class="gloss-term-row">
              <h3 class="gloss-term"><?php echo blog_e($entry['term']); ?></h3>
              <a class="gloss-anchor" href="#<?php echo blog_e($entry['slug']); ?>"
                 data-url="<?php echo blog_e($canonical . '#' . $entry['slug']); ?>"
                 aria-label="Copy link to <?php echo blog_e($entry['term']); ?>">
                <svg viewBox="0 0 16 16" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.3" aria-hidden="true"><path d="M6.5 9.5a3 3 0 0 0 4.2 0l2.6-2.6a3 3 0 0 0-4.2-4.2L7.7 4.1M9.5 6.5a3 3 0 0 0-4.2 0L2.7 9.1a3 3 0 0 0 4.2 4.2l1.4-1.4"/></svg>
                <span class="gloss-copied" hidden>COPIED</span>
              </a>
            </div>

            <?php if (!empty($entry['aliases'])): ?>
              <p class="gloss-alias">ALSO CALLED: <?php echo blog_e(strtoupper(implode(' · ', $entry['aliases']))); ?></p>
            <?php endif; ?>

            <p class="gloss-def"><?php echo blog_e($entry['definition']); ?></p>

            <?php if (!empty($entry['longer'])): ?>
              <p class="gloss-more"><?php echo blog_e($entry['longer']); ?></p>
            <?php endif; ?>

            <?php if (!empty($entry['business']) || !empty($entry['church']) || !empty($entry['remains'])): ?>
              <dl class="gloss-contexts">
                <?php if (!empty($entry['business'])): ?>
                  <dt>IN BUSINESS:</dt><dd><?php echo blog_e($entry['business']); ?></dd>
                <?php endif; ?>
                <?php if (!empty($entry['church'])): ?>
                  <dt>IN THE CHURCH:</dt><dd><?php echo blog_e($entry['church']); ?></dd>
                <?php endif; ?>
                <?php if (!empty($entry['remains'])): ?>
                  <dt class="gloss-remains">WHAT REMAINS TRUE:</dt><dd class="gloss-remains-dd"><?php echo blog_e($entry['remains']); ?></dd>
                <?php endif; ?>
              </dl>
            <?php endif; ?>

            <?php if (!empty($entry['principles']) || !empty($entry['links'])): ?>
              <footer class="gloss-foot">
                <?php if (!empty($entry['principles'])): ?>
                  <span class="gloss-foot__label">FIRST PRINCIPLE<?php echo count($entry['principles']) > 1 ? 'S' : ''; ?>:</span>
                  <p class="gloss-principles">
                    <?php foreach ($entry['principles'] as $i => $principle): ?><?php echo $i ? ' &middot; ' : ''; ?><a href="/#principles"><?php echo blog_e(strtoupper($principle)); ?></a><?php endforeach; ?>
                  </p>
                <?php endif; ?>
                <?php if (!empty($entry['links'])): ?>
                  <span class="gloss-foot__label">GO DEEPER:</span>
                  <div class="gloss-links">
                    <?php foreach (array_slice($entry['links'], 0, 3) as $link): ?>
                      <a href="<?php echo blog_e($link['url']); ?>"><?php echo blog_e($link['label']); ?><?php if (!empty($link['note'])): ?><em><?php echo blog_e($link['note']); ?></em><?php endif; ?></a>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>
              </footer>
            <?php endif; ?>
          </article>
        <?php endforeach; ?>
      </section>
    <?php endforeach; ?>

    <div class="slugline gloss-colophon">
      <span>A LIVING DOCUMENT &middot; TERMS ARE ADDED AS THE WORK REQUIRES THEM</span>
      <span>OK &#10003;</span>
    </div>
  </div>

  <p class="sr-live" aria-live="polite" id="glossLive"></p>
</main>

<?php blog_site_footer(); ?>
<script src="/js/glossary.js"></script>
</body>
</html>
