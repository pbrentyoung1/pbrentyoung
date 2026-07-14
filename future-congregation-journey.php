<?php

require_once __DIR__ . '/inc/blog.php';

$title = 'The Future Congregation Journey — Brent Young';
$description = 'An interactive field tool for helping churches prepare a clear and caring path from awareness to ministry.';
$canonical = blog_site_url('/future-congregation-journey');
$journey = array(
  'awareness' => array(
    'label' => 'Awareness',
    'headline' => 'Jordan first notices that we exist.',
    'summary' => 'The journey begins before we know Jordan’s name. A sign, an invitation, or a short video can become the first impression.',
    'touchpoints' => array('Campus sign', 'Personal invitation', 'Social media', 'YouTube'),
    'question' => 'Who is this church? Is there a place here for someone like me?',
    'care' => 'Give a clear first impression and one approachable next step.',
    'story' => 'We are clear, welcoming, and ready to help you take a first step.'
  ),
  'visit' => array(
    'label' => 'Visit',
    'headline' => 'Jordan explores before deciding whether to attend.',
    'summary' => 'The digital campus becomes a place to ask practical questions before the first physical visit.',
    'touchpoints' => array('Website', 'Service times', 'Beliefs', 'Plan your visit'),
    'question' => 'Could this be a place for me? Will I know what to do?',
    'care' => 'Make the first visit understandable before it happens.',
    'story' => 'You do not have to know how church works before you come.'
  ),
  'attend' => array(
    'label' => 'Attend',
    'headline' => 'Jordan arrives and experiences the church in person.',
    'summary' => 'The physical campus either confirms the promise made online or tells a different story.',
    'touchpoints' => array('Parking', 'Signage', 'Greeter', 'Children’s check-in', 'Worship'),
    'question' => 'Do I want to come back?',
    'care' => 'Prepare a clear, caring arrival and a next step after the service.',
    'story' => 'We prepared a place for you and we are glad you are here.'
  ),
  'member' => array(
    'label' => 'Member',
    'headline' => 'Jordan begins moving from attendance into belonging.',
    'summary' => 'The next season is less about finding a seat and more about finding a place in the life of the church.',
    'touchpoints' => array('Follow-up', 'Groups', 'Pastoral care', 'Classes', 'Baptism'),
    'question' => 'Where do I belong? How can I grow here?',
    'care' => 'Offer a personal path toward connection, growth, and care.',
    'story' => 'There is room for you to be known and to grow with us.'
  ),
  'minister' => array(
    'label' => 'Minister',
    'headline' => 'Jordan begins helping care for someone else.',
    'summary' => 'The journey continues as people use their gifts, serve their community, and help carry the story forward.',
    'touchpoints' => array('Serving teams', 'Leadership development', 'Stories of impact', 'Personal invitation'),
    'question' => 'How am I called to participate?',
    'care' => 'Invite people to use their gifts and help carry the story forward.',
    'story' => 'You are part of the story, and your life can help someone else find a place.'
  ),
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
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?php echo blog_e($title); ?>">
  <meta name="twitter:description" content="<?php echo blog_e($description); ?>">
  <meta name="twitter:image" content="<?php echo blog_e(blog_site_url('/images/og-image.png')); ?>">
  <link rel="stylesheet" href="/css/editorial.css">
</head>
<body class="blog-site journey-tool-page">
<?php blog_site_header(); ?>

<main class="journey-tool wrap">
  <header class="journey-tool__masthead">
    <span class="kicker">FIELD TOOL · COMMUNICATION IS MINISTRY</span>
    <h1>The Future Congregation Journey</h1>
    <p>Before someone attends, they are already experiencing church. This guide helps a team prepare a clear and caring path from awareness to ministry.</p>
  </header>

  <section class="journey-persona" aria-labelledby="personaHeading">
    <div class="journey-persona__portrait">
      <img src="/assets/img/journey/jordan-persona.png" alt="Jordan, a fictional future congregation persona">
    </div>
    <div>
      <span class="journey-persona__label">FUTURE CONGREGATION</span>
      <h2 id="personaHeading">Jordan</h2>
      <p>Recently moved to town. Invited by a friend. Looking for a church where they can belong.</p>
    </div>
    <p class="journey-persona__note">A persona is more than just a demographic. Jordan is a person God has called us to prepare a place for.</p>
  </section>

  <section class="journey-map" aria-label="Jordan's journey from awareness to ministry">
    <div class="journey-map__rail" role="tablist" aria-label="Journey stages">
      <?php $first = true; foreach ($journey as $slug => $stage): ?>
        <button class="journey-stage<?php echo $first ? ' is-active' : ''; ?>" type="button" role="tab" id="stage-<?php echo blog_e($slug); ?>" aria-selected="<?php echo $first ? 'true' : 'false'; ?>" aria-controls="journeyDetail" data-stage="<?php echo blog_e($slug); ?>"><?php echo blog_e($stage['label']); ?></button>
      <?php $first = false; endforeach; ?>
    </div>

    <article class="journey-detail" id="journeyDetail" role="tabpanel" aria-labelledby="stage-awareness" tabindex="0">
      <span class="journey-detail__label" id="journeyLabel">AWARENESS</span>
      <h2 id="journeyHeadline">Jordan first notices that we exist.</h2>
      <p class="journey-detail__summary" id="journeySummary">The journey begins before we know Jordan’s name. A sign, an invitation, or a short video can become the first impression.</p>
      <div class="journey-detail__grid">
        <section>
          <h3>What Jordan encounters</h3>
          <ul id="journeyTouchpoints"><li>Campus sign</li><li>Personal invitation</li><li>Social media</li><li>YouTube</li></ul>
        </section>
        <section>
          <h3>What Jordan may be asking</h3>
          <p id="journeyQuestion">Who is this church? Is there a place here for someone like me?</p>
        </section>
        <section>
          <h3>How we prepare a place</h3>
          <p id="journeyCare">Give a clear first impression and one approachable next step.</p>
        </section>
      </div>
      <div class="journey-detail__story">
        <span>THE STORY BEING TOLD</span>
        <p id="journeyStory">We are clear, welcoming, and ready to help you take a first step.</p>
      </div>
    </article>
  </section>

  <p class="journey-tool__note">This is a guide to a shared journey, not a fixed timeline. Some people move through it quickly. Some take years. Some take their first steps on campus, while others arrive already having moved through Awareness and Visit online. But everyone moves through the same essential journey toward belonging and ministry.</p>
</main>

<?php blog_site_footer(); ?>
<script>
  (function () {
    var journey = <?php echo json_encode($journey, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>;
    var buttons = document.querySelectorAll('.journey-stage');
    var detail = document.getElementById('journeyDetail');
    var label = document.getElementById('journeyLabel');
    var headline = document.getElementById('journeyHeadline');
    var summary = document.getElementById('journeySummary');
    var touchpoints = document.getElementById('journeyTouchpoints');
    var question = document.getElementById('journeyQuestion');
    var care = document.getElementById('journeyCare');
    var story = document.getElementById('journeyStory');
    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    var transitionTimer;
    var heightTransitionHandler;

    function updateDetail(stage, button) {
      detail.setAttribute('aria-labelledby', button.id);
      label.textContent = stage.label.toUpperCase();
      headline.textContent = stage.headline;
      summary.textContent = stage.summary;
      touchpoints.innerHTML = stage.touchpoints.map(function (item) { return '<li>' + item + '</li>'; }).join('');
      question.textContent = stage.question;
      care.textContent = stage.care;
      story.textContent = stage.story;
    }

    buttons.forEach(function (button) {
      button.addEventListener('click', function () {
        var stage = journey[button.getAttribute('data-stage')];

        if (button.classList.contains('is-active')) return;

        buttons.forEach(function (item) {
          var active = item === button;
          item.classList.toggle('is-active', active);
          item.setAttribute('aria-selected', active ? 'true' : 'false');
        });

        window.clearTimeout(transitionTimer);
        if (heightTransitionHandler) {
          detail.removeEventListener('transitionend', heightTransitionHandler);
          heightTransitionHandler = null;
        }

        if (reduceMotion.matches) {
          detail.classList.remove('is-changing');
          detail.style.height = 'auto';
          detail.style.overflow = '';
          updateDetail(stage, button);
          return;
        }

        var oldHeight = detail.offsetHeight;
        detail.style.height = oldHeight + 'px';
        detail.style.overflow = 'hidden';
        detail.classList.add('is-changing');

        transitionTimer = window.setTimeout(function () {
          updateDetail(stage, button);

          detail.style.height = 'auto';
          var newHeight = detail.offsetHeight;
          detail.style.height = oldHeight + 'px';
          void detail.offsetHeight;

          detail.classList.remove('is-changing');
          detail.style.height = newHeight + 'px';

          heightTransitionHandler = function (event) {
            if (event.propertyName !== 'height') return;
            detail.style.height = 'auto';
            detail.style.overflow = '';
            detail.removeEventListener('transitionend', heightTransitionHandler);
            heightTransitionHandler = null;
          };
          detail.addEventListener('transitionend', heightTransitionHandler);
        }, 130);
      });
    });
  })();
</script>
</body>
</html>
