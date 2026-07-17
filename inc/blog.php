<?php

use League\CommonMark\GithubFlavoredMarkdownConverter;

require_once __DIR__ . '/../vendor/autoload.php';

function blog_config($key = null) {
  static $config = null;
  if ($config === null) $config = require __DIR__ . '/blog-config.php';
  return $key === null ? $config : ($config[$key] ?? null);
}

function blog_e($value) {
  return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function blog_google_tag() {
  $id = trim((string) blog_config('google_tag_id'));
  if (!preg_match('/^G-[A-Z0-9]+$/', $id)) return '';

  return '<!-- Google tag (gtag.js) -->' . "\n"
    . '<script async src="https://www.googletagmanager.com/gtag/js?id=' . blog_e($id) . '"></script>' . "\n"
    . '<script>' . "\n"
    . '  window.dataLayer = window.dataLayer || [];' . "\n"
    . '  function gtag(){dataLayer.push(arguments);}' . "\n"
    . "  gtag('js', new Date());" . "\n\n"
    . "  gtag('config', '" . $id . "');" . "\n"
    . '</script>';
}

function blog_slugify($value) {
  $value = strtolower(trim((string) $value));
  $value = preg_replace('/[^a-z0-9]+/', '-', $value);
  return trim($value, '-');
}

function blog_truthy($value) {
  if (is_bool($value)) return $value;
  return in_array(strtolower(trim((string) $value)), array('1', 'true', 'yes', 'on'), true);
}

function blog_parse_frontmatter($filename, $text) {
  $slug = preg_replace('/\.md$/i', '', basename($filename));
  $post = array(
    'slug' => $slug,
    'title' => $slug,
    'date' => '1970-01-01',
    'topic' => 'Field Notes',
    'deck' => '',
    'tags' => array(),
    'principle' => '',
    'banner' => '',
    'banneralt' => '',
    'draft' => false,
    'featured' => false,
    'shortlist' => 0,
    'md' => $text,
  );

  if (preg_match('/^---\s*\R(.*?)\R---\s*\R?/s', $text, $match)) {
    foreach (preg_split('/\R/', $match[1]) as $line) {
      if (!preg_match('/^(\w+)\s*:\s*(.*)$/u', $line, $pair)) continue;
      $key = strtolower($pair[1]);
      $post[$key] = trim($pair[2]);
    }
    $post['md'] = substr($text, strlen($match[0]));
  }

  $post['tags'] = array_values(array_filter(array_map('trim', explode(',', (string) $post['tags']))));
  $post['terms'] = blog_validate_terms($post['terms'] ?? '');
  $post['draft'] = blog_truthy($post['draft']);
  $post['featured'] = blog_truthy($post['featured']);
  $post['shortlist'] = max(0, (int) $post['shortlist']);
  $post['topic_slug'] = blog_slugify($post['topic']);
  $post['banner'] = $post['banner'] ?: 'assets/img/blog/' . $post['slug'] . '.jpg';
  $post['banneralt'] = $post['banneralt'] ?: $post['title'];
  $post['word_count'] = blog_word_count($post);
  $post['read_minutes'] = max(1, (int) round($post['word_count'] / 200));

  return $post;
}

function blog_word_count($post) {
  $text = ($post['title'] ?? '') . ' ' . ($post['deck'] ?? '') . ' ' . ($post['md'] ?? '');
  $text = preg_replace('/https?:\/\/\S+/u', ' ', $text);
  $text = preg_replace('/[#>*_`!\[\]()\-]+/u', ' ', $text);
  $words = preg_split('/\s+/u', trim($text), -1, PREG_SPLIT_NO_EMPTY);
  return count($words);
}

/* ---------- the working glossary ---------- */

/* Entries live as Markdown files in /glossary, one per term,
   edited exactly like posts. Filename = permanent slug.
   Frontmatter: term (required), aliases, principles,
   link1..link3 as "Label | /url | note", draft.
   Body: first paragraph is the definition, following paragraphs
   are the longer explanation, and optional sections use
   "## In business", "## In the church", "## What remains true". */
function blog_glossary_entries() {
  static $entries = null;
  if ($entries !== null) return $entries;

  $entries = array();
  foreach (glob(__DIR__ . '/../glossary/*.md') as $file) {
    $name = basename($file);
    if ($name[0] === '_' || $name[0] === '.') continue;
    $entry = blog_glossary_parse($name, (string) file_get_contents($file));
    if ($entry && empty($entry['draft'])) $entries[] = $entry;
  }

  usort($entries, function ($a, $b) { return strcasecmp($a['term'], $b['term']); });
  return $entries;
}

function blog_glossary_parse($filename, $text) {
  $slug = preg_replace('/\.md$/i', '', $filename);
  $meta = array('term' => '', 'aliases' => '', 'principles' => '', 'draft' => '');

  if (preg_match('/^---\s*\R(.*?)\R---\s*\R?/s', $text, $match)) {
    foreach (preg_split('/\R/', $match[1]) as $line) {
      if (preg_match('/^(\w+)\s*:\s*(.*)$/u', $line, $pair)) $meta[strtolower($pair[1])] = trim($pair[2]);
    }
    $text = substr($text, strlen($match[0]));
  }

  if ($meta['term'] === '') return null;

  /* body: definition, longer, and the labeled sections */
  $sections = preg_split('/^##\s+(.+)$/mu', trim($text), -1, PREG_SPLIT_DELIM_CAPTURE);
  $lead = trim(array_shift($sections));
  $parts = preg_split('/\R\s*\R/', $lead, 2);
  $definition = trim($parts[0] ?? '');
  $longer = trim($parts[1] ?? '');

  $labeled = array('business' => '', 'church' => '', 'remains' => '');
  for ($i = 0; $i + 1 < count($sections); $i += 2) {
    $heading = strtolower(trim($sections[$i]));
    $bodyText = trim($sections[$i + 1]);
    if (strpos($heading, 'business') !== false) $labeled['business'] = $bodyText;
    elseif (strpos($heading, 'church') !== false || strpos($heading, 'ministry') !== false) $labeled['church'] = $bodyText;
    elseif (strpos($heading, 'remains') !== false) $labeled['remains'] = $bodyText;
  }

  $links = array();
  foreach (array('link1', 'link2', 'link3') as $key) {
    if (empty($meta[$key])) continue;
    $bits = array_map('trim', explode('|', $meta[$key]));
    if (count($bits) >= 2 && $bits[0] !== '' && $bits[1] !== '') {
      $links[] = array('label' => $bits[0], 'url' => $bits[1], 'note' => $bits[2] ?? '');
    }
  }

  return array(
    'term' => $meta['term'],
    'slug' => $slug,
    'definition' => $definition,
    'longer' => $longer,
    'business' => $labeled['business'],
    'church' => $labeled['church'],
    'remains' => $labeled['remains'],
    'principles' => array_values(array_filter(array_map('trim', explode(',', $meta['principles'])))),
    'links' => $links,
    'aliases' => array_values(array_filter(array_map('trim', explode(',', $meta['aliases'])))),
    'draft' => blog_truthy($meta['draft']),
  );
}

function blog_glossary_map() {
  static $map = null;
  if ($map === null) {
    $map = array();
    foreach (blog_glossary_entries() as $entry) $map[$entry['slug']] = $entry;
  }
  return $map;
}

/* terms: frontmatter lists glossary slugs, comma-separated.
   Invalid slugs are dropped quietly; editorial order is preserved. */
function blog_validate_terms($raw) {
  $map = blog_glossary_map();
  $terms = array();
  foreach (explode(',', (string) $raw) as $slug) {
    $slug = strtolower(trim($slug));
    if ($slug !== '' && isset($map[$slug]) && !in_array($slug, $terms, true)) $terms[] = $slug;
  }
  return $terms;
}

function blog_posts() {
  static $posts = null;
  if ($posts !== null) return $posts;

  $index = json_decode((string) @file_get_contents(__DIR__ . '/../posts/index.json'), true);
  $posts = array();
  if (!is_array($index)) return $posts;

  foreach ($index as $filename) {
    $filename = basename((string) $filename);
    $text = @file_get_contents(__DIR__ . '/../posts/' . $filename);
    if ($text === false) continue;
    $post = blog_parse_frontmatter($filename, $text);
    if (!$post['draft']) $posts[] = $post;
  }

  usort($posts, function ($a, $b) {
    $date = strcmp($b['date'], $a['date']);
    return $date !== 0 ? $date : strcmp($a['title'], $b['title']);
  });
  return $posts;
}

function blog_find_post($slug) {
  foreach (blog_posts() as $post) if ($post['slug'] === $slug) return $post;
  return null;
}

function blog_featured_post($posts = null) {
  $posts = $posts ?: blog_posts();
  foreach ($posts as $post) if ($post['featured']) return $post;
  return $posts[0] ?? null;
}

function blog_shortlist($posts = null) {
  $posts = $posts ?: blog_posts();
  $list = array_values(array_filter($posts, function ($post) { return $post['shortlist'] > 0; }));
  usort($list, function ($a, $b) { return $a['shortlist'] <=> $b['shortlist']; });
  return $list;
}

function blog_topic_counts($posts = null) {
  $counts = array();
  foreach ($posts ?: blog_posts() as $post) {
    $slug = $post['topic_slug'];
    if (!isset($counts[$slug])) $counts[$slug] = array('name' => $post['topic'], 'count' => 0);
    $counts[$slug]['count']++;
  }
  return $counts;
}

function blog_filter_posts($posts, $topic = '', $tag = '', $query = '') {
  $topic = blog_slugify($topic);
  $tag = blog_slugify($tag);
  $query = trim($query);

  return array_values(array_filter($posts, function ($post) use ($topic, $tag, $query) {
    if ($topic && $post['topic_slug'] !== $topic) return false;
    if ($tag) {
      $tagSlugs = array_map('blog_slugify', $post['tags']);
      if (!in_array($tag, $tagSlugs, true)) return false;
    }
    if ($query !== '') {
      $haystack = implode(' ', array(
        $post['title'], $post['deck'], $post['topic'], $post['principle'],
        implode(' ', $post['tags']), $post['md'],
      ));
      if (mb_stripos($haystack, $query) === false) return false;
    }
    return true;
  }));
}

function blog_site_url($path = '') {
  return rtrim(blog_config('site_url'), '/') . '/' . ltrim($path, '/');
}

function blog_post_url($post, $absolute = false) {
  $path = '/blog/' . rawurlencode($post['slug']);
  return $absolute ? blog_site_url($path) : $path;
}

function blog_index_url($params = array(), $absolute = false) {
  $params = array_filter($params, function ($value) { return $value !== '' && $value !== null && $value !== false; });
  $path = '/blog' . ($params ? '?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986) : '');
  return $absolute ? blog_site_url($path) : $path;
}

function blog_banner_url($post, $absolute = false) {
  $path = '/' . ltrim($post['banner'], '/');
  if (!is_file(__DIR__ . '/..' . $path)) $path = '/images/og-image.png';
  return $absolute ? blog_site_url($path) : $path;
}

function blog_banner_meta($post) {
  $path = blog_banner_url($post);
  $info = @getimagesize(__DIR__ . '/..' . $path);

  return array(
    'width' => $info ? (int) $info[0] : 1200,
    'height' => $info ? (int) $info[1] : 630,
    'mime' => $info && isset($info['mime']) ? $info['mime'] : 'image/jpeg',
  );
}

function blog_thumbnail_url($post, $absolute = false) {
  $path = '/assets/img/blog/thumbs/' . $post['slug'] . '.jpg';
  if (!is_file(__DIR__ . '/..' . $path)) return blog_banner_url($post, $absolute);
  return $absolute ? blog_site_url($path) : $path;
}

function blog_date($iso, $format = 'F j, Y') {
  $time = strtotime($iso . 'T12:00:00');
  return $time ? date($format, $time) : $iso;
}

function blog_markdown($markdown) {
  static $converter = null;
  if ($converter === null) {
    $converter = new GithubFlavoredMarkdownConverter(array(
      'html_input' => 'strip',
      'allow_unsafe_links' => false,
    ));
  }

  $html = (string) $converter->convert($markdown);
  $html = preg_replace('/<blockquote>\s*<p>!\s*(.*?)<\/p>\s*<\/blockquote>/s', '<aside class="pull">$1</aside>', $html);

  $seen = array();
  $toc = array();
  $html = preg_replace_callback('/<h2>(.*?)<\/h2>/s', function ($match) use (&$seen, &$toc) {
    $label = trim(html_entity_decode(strip_tags($match[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    $base = blog_slugify($label) ?: 'section';
    $seen[$base] = ($seen[$base] ?? 0) + 1;
    $id = $base . ($seen[$base] > 1 ? '-' . $seen[$base] : '');
    $toc[] = array('id' => $id, 'label' => $label);
    return '<h2 id="' . blog_e($id) . '">' . $match[1] . '</h2>';
  }, $html);

  $html = preg_replace_callback('/\s(href|src)="([^"]+)"/', function ($match) {
    $url = $match[2];
    if (preg_match('~^(?:[a-z][a-z0-9+.-]*:|/|#)~i', $url)) return $match[0];
    return ' ' . $match[1] . '="/' . ltrim($url, '/') . '"';
  }, $html);

  /* A titled, standalone Markdown image becomes an editorial figure. */
  $html = preg_replace_callback('/<p>(<img\s+[^>]+\/?>)<\/p>/i', function ($match) {
    $image = $match[1];
    $caption = '';
    if (preg_match('/\stitle="([^"]*)"/i', $image, $title)) {
      $caption = html_entity_decode($title[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
      $image = preg_replace('/\s+title="[^"]*"/i', '', $image);
    }
    $figure = '<figure class="article-figure">' . $image;
    if ($caption !== '') $figure .= '<figcaption>' . blog_e($caption) . '</figcaption>';
    return $figure . '</figure>';
  }, $html);

  return array('html' => $html, 'toc' => $toc);
}

function blog_related_posts($post, $limit = 3) {
  $ranked = array();
  foreach (blog_posts() as $candidate) {
    if ($candidate['slug'] === $post['slug']) continue;
    $score = 0;
    if ($candidate['topic'] === $post['topic']) $score += 6;
    if ($candidate['principle'] && $candidate['principle'] === $post['principle']) $score += 4;
    $sharedTags = array_intersect(array_map('blog_slugify', $candidate['tags']), array_map('blog_slugify', $post['tags']));
    $score += count($sharedTags) * 2;
    $ranked[] = array('score' => $score, 'post' => $candidate);
  }
  usort($ranked, function ($a, $b) {
    if ($a['score'] !== $b['score']) return $b['score'] <=> $a['score'];
    return strcmp($b['post']['date'], $a['post']['date']);
  });
  return array_slice(array_column($ranked, 'post'), 0, $limit);
}

function blog_query_params($overrides = array()) {
  $params = array(
    'topic' => isset($_GET['topic']) ? blog_slugify($_GET['topic']) : '',
    'tag' => isset($_GET['tag']) ? blog_slugify($_GET['tag']) : '',
    'q' => isset($_GET['q']) ? trim((string) $_GET['q']) : '',
    'page' => isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1,
  );
  foreach ($overrides as $key => $value) $params[$key] = $value;
  return $params;
}

function blog_post_card($post, $variant = 'archive') {
  $topicUrl = blog_index_url(array('topic' => $post['topic_slug']));
  $html = '<article class="article-card article-card--' . blog_e($variant) . '" data-topic="' . blog_e($post['topic_slug']) . '">';
  $html .= '<a class="article-card__image" href="' . blog_e(blog_post_url($post)) . '" tabindex="-1" aria-hidden="true">';
  $html .= '<img src="' . blog_e(blog_thumbnail_url($post)) . '" alt="" loading="lazy" width="600" height="315"></a>';
  $html .= '<div class="article-card__body"><div class="article-card__meta">';
  $html .= '<a class="topic-link" href="' . blog_e($topicUrl) . '">' . blog_e($post['topic']) . '</a>';
  $html .= '<span>' . blog_e($post['read_minutes']) . ' MIN READ</span></div>';
  $html .= '<h3><a href="' . blog_e(blog_post_url($post)) . '">' . blog_e($post['title']) . '</a></h3>';
  if ($variant !== 'related' && $post['deck']) $html .= '<p>' . blog_e($post['deck']) . '</p>';
  $html .= '<time datetime="' . blog_e($post['date']) . '">' . blog_e(strtoupper(blog_date($post['date']))) . '</time>';
  $html .= '</div></article>';
  return $html;
}

function blog_robots_meta() {
  return blog_config('blog_public') ? 'index, follow' : 'noindex, follow';
}

function blog_subscribe_head() {
  ?>
  <link rel="stylesheet" href="https://sibforms.com/forms/end-form/build/sib-styles.css">
  <?php
}

function blog_subscribe_link($label = 'SUBSCRIBE BY EMAIL &rarr;') {
  $url = blog_config('brevo_form_action');
  return '<a class="subscribe-link" href="' . blog_e($url) . '" data-subscribe-open>' . $label . '</a>';
}

function blog_subscribe_dialog() {
  $url = blog_config('brevo_form_action');
  ?>
  <div class="subscribe-dialog" id="subscribeDialog" hidden>
    <div class="subscribe-dialog__backdrop" data-subscribe-close></div>
    <section class="subscribe-dialog__panel" role="dialog" aria-modal="true" aria-labelledby="subscribeTitle" tabindex="-1">
      <button class="subscribe-dialog__close" type="button" aria-label="Close subscription form" data-subscribe-close>&times;</button>
      <p class="subscribe-dialog__kicker">FIELD NOTES &middot; FROM THE DESK OF BRENT YOUNG</p>
      <h2 id="subscribeTitle">Follow the next idea.</h2>
      <p class="subscribe-dialog__intro">New field notes on communication, design, leadership, ministry, and the systems that help good work survive real life. Sent when there is something worth sharing.</p>

      <div id="sib-form-container" class="subscribe-dialog__form">
        <div id="error-message" class="sib-form-message-panel subscribe-message subscribe-message--error" role="alert">
          <div class="sib-form-message-panel__text"><span class="sib-form-message-panel__inner-text">Your subscription could not be saved. Please try again.</span></div>
        </div>
        <div id="success-message" class="sib-form-message-panel subscribe-message subscribe-message--success" role="status">
          <div class="sib-form-message-panel__text"><span class="sib-form-message-panel__inner-text">You are on the list. Check your inbox to confirm your subscription.</span></div>
        </div>
        <div id="sib-container" class="sib-container--large sib-container--vertical">
          <form id="sib-form" method="post" action="<?php echo blog_e($url); ?>" data-type="subscription">
            <div class="sib-input sib-form-block">
              <div class="form__entry entry_block">
                <div class="form__label-row">
                  <label class="entry__label" for="EMAIL" data-required="*">EMAIL ADDRESS</label>
                  <div class="subscribe-field-row entry__field">
                    <input class="input" type="email" id="EMAIL" name="EMAIL" autocomplete="email" placeholder="YOU@EXAMPLE.COM" data-required="true" required>
                    <button class="sib-form-block__button sib-form-block__button-with-loader" form="sib-form" type="submit">
                      <svg class="clickable__icon progress-indicator__icon sib-hide-loader-icon" viewBox="0 0 512 512" aria-hidden="true"><path d="M460.116 373.846l-20.823-12.022c-5.541-3.199-7.54-10.159-4.663-15.874 30.137-59.886 28.343-131.652-5.386-189.946-33.641-58.394-94.896-95.833-161.827-99.676C261.028 55.961 256 50.751 256 44.352V20.309c0-6.904 5.808-12.337 12.703-11.982 83.556 4.306 160.163 50.864 202.11 123.677 42.063 72.696 44.079 162.316 6.031 236.832-3.14 6.148-10.75 8.461-16.728 5.01z"/></svg>
                      SUBSCRIBE
                    </button>
                  </div>
                </div>
                <label class="entry__error entry__error--primary"></label>
                <p class="entry__specification">No noise. Just the next useful idea. Unsubscribe whenever you like.</p>
              </div>
            </div>
            <input type="text" name="email_address_check" value="" class="input--hidden" tabindex="-1" aria-hidden="true">
            <input type="hidden" name="locale" value="en">
          </form>
        </div>
      </div>
      <p class="subscribe-dialog__rss">PREFER A FEED READER? <a href="/feed.xml">OPEN THE RSS FEED &rarr;</a></p>
    </section>
  </div>
  <script>
    window.REQUIRED_CODE_ERROR_MESSAGE = 'Please choose a country code';
    window.LOCALE = 'en';
    window.EMAIL_INVALID_MESSAGE = window.SMS_INVALID_MESSAGE = 'Please enter a valid email address.';
    window.REQUIRED_ERROR_MESSAGE = 'Please enter your email address.';
    window.GENERIC_INVALID_MESSAGE = 'Please review the information and try again.';
    window.INVALID_NUMBER = 'Please review the information and try again.';
    window.INVALID_DATE = 'Please enter a valid date.';
    window.REQUIRED_MULTISELECT_MESSAGE = 'Please select at least one option.';
    window.translation = { common: { selectedList: '{quantity} list selected', selectedLists: '{quantity} lists selected', selectedOption: '{quantity} selected', selectedOptions: '{quantity} selected' } };
    var AUTOHIDE = false;
  </script>
  <script defer src="https://sibforms.com/forms/end-form/build/main.js"></script>
  <script defer src="/js/subscribe.js"></script>
  <?php
}

function blog_nav_menu() {
  $topics = array(
    array('brand-mission', 'Brand & Mission'),
    array('creative-leadership', 'Creative Leadership'),
    array('systems-workflow', 'Systems & Workflow'),
    array('craft', 'Craft'),
    array('ai', 'AI'),
  );
  ?>
  <div class="nav-blog">
    <a class="nav-link nav-blog-toggle" href="/blog" aria-haspopup="true" aria-expanded="false">BLOG</a>
    <div class="nav-blog-menu" aria-label="Blog sections">
      <a class="nav-blog-menu__all" href="/blog">ALL ARTICLES &rarr;</a>
      <span class="nav-blog-menu__label">TOPICS</span>
      <?php foreach ($topics as $topic): ?><a href="/blog?topic=<?php echo blog_e($topic[0]); ?>"><?php echo blog_e($topic[1]); ?></a><?php endforeach; ?>
      <span class="nav-blog-menu__label">THE SHORT LIST</span>
      <div data-nav-shortlist>
        <?php foreach (blog_shortlist() as $post): ?><a href="<?php echo blog_e(blog_post_url($post)); ?>"><?php echo blog_e($post['title']); ?></a><?php endforeach; ?>
      </div>
      <span class="nav-blog-menu__label">REFERENCE</span>
      <a href="/glossary">A Working Glossary</a>
    </div>
  </div>
  <?php
}

function blog_site_header() {
  ?>
  <header class="site-head">
    <div class="wrap">
      <a class="wordmark" href="/">BRENT YOUNG</a>
      <nav class="site-nav" id="siteNav" aria-label="Site">
        <a class="nav-link" href="/#principles">PRINCIPLES</a>
        <a class="nav-link" href="/#flat-file">THE FILE</a>
        <?php blog_nav_menu(); ?>
        <a class="nav-link" href="/#contact">CONTACT</a>
        <span class="nav-colophon">TABLE OF CONTENTS &middot; BY-2026</span>
      </nav>
      <button class="menu-toggle" id="menuToggle" type="button" aria-expanded="false" aria-controls="siteNav">MENU</button>
    </div>
  </header>
  <?php
}

function blog_site_footer() {
  $fonts = 'PLAYFAIR DISPLAY &middot; IBM PLEX SANS &middot; IBM PLEX MONO &middot; SOURCE SERIF 4 &middot; CAVEAT &middot; COVERED BY YOUR GRACE &middot; PERMANENT MARKER &middot; IMPACT LABEL / REVERSED';
  ?>
  <section class="contact" id="contact" aria-label="Contact">
    <div class="wrap">
      <div class="dept-head">
        <div class="dept-row">
          <h2>Let&rsquo;s tell the right story.</h2>
          <span class="dept-no">FINAL PROOF</span>
        </div>
      </div>
      <p class="contact-intro">
        Maybe your story is clear, but the way people experience it is not. Maybe your
        team is doing good work inside systems that make everything harder than it needs
        to be. Or maybe you know something needs to change, but you are not sure where to
        begin. I would love to hear what you are working through. No pitch and no packages,
        just a conversation about the story God has given you to tell and how we might
        help people experience it more clearly.
      </p>
      <div class="contact-grid">
        <div class="contact-info">
          <p><strong>Brent Young</strong> &middot; Montgomery, Texas</p>
          <p class="mono">PHONE: (562) 964-4562</p>
          <p class="mono">EMAIL: PBRENTYOUNG@GMAIL.COM</p>
        </div>
        <div class="hero-actions">
          <a class="btn-ink" href="mailto:pbrentyoung@gmail.com">Email Brent</a>
          <a class="btn-ghost" href="/assets/pdf/resume.pdf" target="_blank" rel="noopener">Resume</a>
        </div>
      </div>
      <div class="slugline" style="margin-top: 36px;">
        <span>JOB NO. BY-2026 &middot; APPROVED FOR RELEASE</span>
        <span>OK &#10003;</span>
      </div>
    </div>
  </section>
  <footer class="site-foot">
    <div class="wrap">
      <span>&copy; <?php echo date('Y'); ?> BRENT YOUNG</span>
      <span class="font-list">SET IN <?php echo $fonts; ?></span>
      <a class="spec-toggle" href="/glossary">GLOSSARY</a>
    </div>
  </footer>
  <script src="/js/nav.js"></script>
  <?php
}
