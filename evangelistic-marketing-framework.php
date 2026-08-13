<?php

require_once __DIR__ . '/inc/blog.php';

$title = 'The Evangelistic Marketing Framework | Brent Young';
$description = 'A connected framework for helping churches align calling, ministry, story, experience, Brand, and invitation around the transformation God is calling people toward.';
$canonical = blog_site_url('/evangelistic-marketing-framework');

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
  <link rel="icon" type="image/svg+xml" href="/favicon.svg">
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
  <link rel="stylesheet" href="/css/framework-map.css">
</head>
<body class="blog-site framework-page">
<?php blog_site_header(); ?>

<main class="framework wrap">
  <header class="framework__masthead">
    <div class="framework__masthead-copy">
      <span class="kicker">FOUNDATIONS · EVANGELISTIC MARKETING</span>
      <h1>The Evangelistic Marketing Framework</h1>
      <p>Every church has a story to tell and people it is called to serve. This framework connects what God has entrusted to you with the ministries you build, the story people experience, and the next faithful step you invite them to take.</p>
      <div class="framework__actions">
        <a class="btn-ink" href="#framework-map">Explore the map</a>
        <a class="btn-ghost" href="#entry-points">Choose an entry point</a>
      </div>
    </div>
    <img class="framework__masthead-art" src="/assets/img/evangelistic-marketing-logo.png" alt="" width="938" height="943">
  </header>

  <section class="framework-intro" aria-labelledby="frameworkIntroHeading">
    <div>
      <span class="framework-label">WHY THIS EXISTS</span>
      <h2 id="frameworkIntroHeading">Give every idea a place.</h2>
    </div>
    <div class="framework-intro__copy">
      <p>Church communications gets complicated when one word is asked to do five jobs. Brand becomes another name for the logo. Mission becomes a sentence on the wall. Strategy becomes the calendar. Marketing becomes promotion. Before long, everyone is using the same words but talking about different things.</p>
      <p>This framework gives each idea a place. It helps us ask better questions, see how the answers affect one another, and decide what kind of problem we are actually trying to solve.</p>
      <p>It is not a recipe to follow from beginning to end. Calling shapes mission. Mission guides strategy. Ministries create touchpoints. Touchpoints shape Brand. What people experience can send us back to reconsider the strategy. The ideas keep working on one another.</p>
      <blockquote>The goal is not to learn more language. It is to make better decisions together.</blockquote>
    </div>
  </section>

  <section class="framework-map-shell" id="framework-map" aria-labelledby="frameworkMapHeading">
    <div class="framework-map-shell__head">
      <div>
        <span class="framework-label">INTERACTIVE FRAMEWORK</span>
        <h2 id="frameworkMapHeading">Follow the relationships</h2>
      </div>
      <p>Begin with the idea that brought you here, then follow its connections into the rest of the framework.</p>
    </div>

    <!-- Stable integration point for the interactive mind map. -->
    <div class="framework-map-mount" id="evangelisticMarketingMap" data-framework-map-mount>
      <div class="framework-map-placeholder">
        <span>THE EVANGELISTIC MARKETING FRAMEWORK</span>
        <p>Explore the relationships among calling, ministry, story, communication, touchpoints, Brand, and transformation.</p>
      </div>
    </div>
  </section>

  <nav class="framework-entry" id="entry-points" aria-labelledby="entryPointsHeading">
    <div class="framework-entry__head">
      <span class="framework-label">ENTER ANYWHERE</span>
      <h2 id="entryPointsHeading">Start with the question in front of you.</h2>
      <p>You do not have to understand the whole framework before it becomes useful. Start where the work feels unclear, then follow that idea into the others it touches.</p>
    </div>
    <div class="framework-entry__grid">
      <a href="#foundations"><strong>Foundations</strong><span>What has God entrusted to us?</span></a>
      <a href="#ministry-strategy"><strong>Ministry &amp; Strategy</strong><span>How does calling take practical form?</span></a>
      <a href="#story-expression"><strong>Story &amp; Expression</strong><span>How do we carry what is true?</span></a>
      <a href="#encounter-perception"><strong>Encounter &amp; Perception</strong><span>What do people actually experience?</span></a>
      <a href="#invitation-transformation"><strong>Invitation &amp; Transformation</strong><span>How do we help people take a faithful next step?</span></a>
    </div>
  </nav>

  <section class="framework-neighborhood" id="foundations" aria-labelledby="foundationsHeading">
    <div class="framework-neighborhood__title">
      <span class="framework-label">FOUNDATIONS</span>
      <h2 id="foundationsHeading">Start with what God has given you.</h2>
    </div>
    <div class="framework-neighborhood__body">
      <p>Before we decide what to build or how to communicate it, we need to know what we are trying to be faithful to. These five ideas help us name that foundation.</p>

      <dl class="framework-term-guide">
        <div>
          <dt>Calling</dt>
          <dd><strong>What has God placed in your hands?</strong> Calling is the work, burden, gift, or responsibility God keeps putting in front of you. It often begins personally, then helps shape the work a church takes on together.</dd>
        </div>
        <div>
          <dt>Identity</dt>
          <dd><strong>Who are you?</strong> Identity is who your church really is. It grows from your people, place, history, gifts, and calling. It is discovered and lived, not invented to make the church sound more appealing.</dd>
        </div>
        <div>
          <dt>Mission</dt>
          <dd><strong>Why do you exist together?</strong> Mission names the work you share. It turns calling into a common purpose and gives the church a reason to organize, act, and make decisions together.</dd>
        </div>
        <div>
          <dt>Vision</dt>
          <dd><strong>What future are you moving toward?</strong> Vision helps people picture what greater faithfulness could look like if the church keeps living out its mission.</dd>
        </div>
        <div>
          <dt>Stewardship</dt>
          <dd><strong>What will you do with what you have been given?</strong> Stewardship is more than protecting resources. It is faithfully investing your calling, people, time, trust, creativity, and opportunity.</dd>
        </div>
      </dl>

    </div>
    <aside class="framework-neighborhood__rail">
      <span class="framework-label">WORKING TERMS</span>
      <ul>
        <li>Calling</li>
        <li>Identity</li>
        <li>Mission</li>
        <li>Vision</li>
        <li><a href="/glossary#stewardship">Stewardship</a></li>
      </ul>
      <a class="read-link" href="/blog/the-right-story-told-the-right-way">READ THE FOUNDATIONS ESSAY &rarr;</a>
    </aside>
    <div class="framework-connection">
      <span>HOW THEY WORK TOGETHER</span>
      <p>Calling shapes the mission. Identity keeps that mission truthful. Vision helps people see where the mission could lead. Stewardship asks whether we are investing what God has given us to move in that direction.</p>
    </div>
  </section>

  <section class="framework-neighborhood" id="ministry-strategy" aria-labelledby="ministryStrategyHeading">
    <div class="framework-neighborhood__title">
      <span class="framework-label">MINISTRY &amp; STRATEGY</span>
      <h2 id="ministryStrategyHeading">How does calling take practical form?</h2>
    </div>
    <div class="framework-neighborhood__body">
      <p>Calling does not become ministry because we wrote it into a mission statement. It takes practical form through the choices we make, the work we organize, and the experiences we prepare for people.</p>

      <dl class="framework-term-guide">
        <div>
          <dt>Strategy</dt>
          <dd><strong>What path will you choose?</strong> Strategy turns conviction into choices. It decides what deserves attention now, what can wait, and what the church may need to stop doing so the mission can move forward.</dd>
        </div>
        <div>
          <dt>Ministry</dt>
          <dd><strong>What is the church called to do?</strong> Ministry is the whole work of serving God and people. It is bigger than any department, program, event, or Sunday service.</dd>
        </div>
        <div>
          <dt>ministries</dt>
          <dd><strong>How will you organize that work?</strong> Ministries give parts of the work a home. Children, students, worship, groups, care, hospitality, outreach, and technology are organized expressions of one shared Ministry.</dd>
        </div>
        <div>
          <dt>Ministry Elements</dt>
          <dd><strong>What decisions support the ministry?</strong> Ministry elements are the decisions that support and serve a ministry, such as service times, check-in, coffee, curriculum, volunteer roles, and follow-up. Each decision shapes the others, and each becomes a touchpoint the moment someone interacts with it.</dd>
        </div>
        <div>
          <dt>Systems</dt>
          <dd><strong>How will the work hold together?</strong> Systems create dependable ways to plan, decide, request, approve, deliver, and follow up. Healthy systems do not replace people. They give people room to do better work together.</dd>
        </div>
        <div>
          <dt>Change vs. Polish</dt>
          <dd><strong>What kind of problem are you solving?</strong> Some things need clearer words, better design, or stronger execution. Other things need a different decision, ministry, process, or promise. Wisdom is knowing whether the surface needs polish or the work underneath it needs to change.</dd>
        </div>
      </dl>

    </div>
    <aside class="framework-neighborhood__rail">
      <span class="framework-label">WORKING TERMS</span>
      <ul>
        <li>Strategy</li>
        <li><a href="/glossary#ministry">Ministry</a></li>
        <li><a href="/glossary#ministries">ministries</a></li>
        <li><a href="/glossary#ministry-elements">Ministry Elements</a></li>
        <li>Systems</li>
        <li><a href="/glossary#change-vs-polish">Change vs. Polish</a></li>
      </ul>
      <a class="read-link" href="/blog/change-vs-polish">READ CHANGE VS. POLISH &rarr;</a>
    </aside>
    <div class="framework-connection">
      <span>HOW THEY WORK TOGETHER</span>
      <p>Strategy chooses a path for the mission. Ministries organize the work along that path. Ministry elements make the work tangible, and systems help people sustain it. Change vs. Polish helps us decide whether to improve what exists or build something different.</p>
    </div>
  </section>

  <section class="framework-neighborhood" id="story-expression" aria-labelledby="storyExpressionHeading">
    <div class="framework-neighborhood__title">
      <span class="framework-label">STORY &amp; EXPRESSION</span>
      <h2 id="storyExpressionHeading">How do we carry what is true?</h2>
    </div>
    <div class="framework-neighborhood__body">
      <p>A true story can still be difficult to see. These ideas help the church carry what is true in ways people can understand, remember, and recognize across many different encounters.</p>

      <dl class="framework-term-guide">
        <div>
          <dt>Story</dt>
          <dd><strong>What are people experiencing and remembering?</strong> Story is the truthful narrative carried by our words, decisions, behaviors, and artifacts. It is not spin. It is the meaning people gather as moments begin to connect.</dd>
        </div>
        <div>
          <dt>Communication</dt>
          <dd><strong>How is the story carried?</strong> Communication includes everything that helps people receive and understand the story, from sermons and conversations to websites, signs, emails, environments, and the way a decision is explained.</dd>
        </div>
        <div>
          <dt>Creativity</dt>
          <dd><strong>What else could be possible?</strong> Creativity helps us see beyond the first answer. It makes connections, reframes problems, and discovers a clearer or more faithful way forward.</dd>
        </div>
        <div>
          <dt>Design</dt>
          <dd><strong>How can we make the meaning clear and useful?</strong> Design gives ideas form, order, and purpose. It is not decoration added at the end. It helps people understand what matters and what to do next.</dd>
        </div>
        <div>
          <dt>branding</dt>
          <dd><strong>How will you intentionally express who you are?</strong> Branding is the collection of visible and verbal signals a church can shape, including its language, logo, colors, typography, photography, music, environments, signage, and tone.</dd>
        </div>
      </dl>

    </div>
    <aside class="framework-neighborhood__rail">
      <span class="framework-label">WORKING TERMS</span>
      <ul>
        <li><a href="/glossary#story">Story</a></li>
        <li>Communication</li>
        <li>Creativity</li>
        <li><a href="/glossary#design">Design</a></li>
        <li><a href="/glossary#branding">branding</a></li>
      </ul>
      <a class="read-link" href="/blog/your-church-already-has-a-brand">BRAND IS MORE THAN A LOGO &rarr;</a>
    </aside>
    <div class="framework-connection">
      <span>HOW THEY WORK TOGETHER</span>
      <p>Identity keeps the story truthful. Communication carries it. Creativity finds ways through the problem. Design gives the answer clear form, and branding helps those expressions feel like they came from the same church. Touchpoints are where people finally encounter all of it.</p>
    </div>
  </section>

  <section class="framework-neighborhood" id="encounter-perception" aria-labelledby="encounterPerceptionHeading">
    <div class="framework-neighborhood__title">
      <span class="framework-label">ENCOUNTER &amp; PERCEPTION</span>
      <h2 id="encounterPerceptionHeading">What do people actually experience?</h2>
    </div>
    <div class="framework-neighborhood__body">
      <p>People rarely form an opinion about a church from one message. They gather it through repeated moments, many of which happen before the church knows their name and some of which the church never planned.</p>

      <dl class="framework-term-guide">
        <div>
          <dt>Touchpoints</dt>
          <dd><strong>Where does someone encounter your church?</strong> A touchpoint is any encounter that begins shaping perception. It may be planned, like a website or welcome desk, or unplanned, like an overheard conversation, a friend’s story, or an old memory.</dd>
        </div>
        <div>
          <dt>Hospitality</dt>
          <dd><strong>Have you prepared a place for them?</strong> Hospitality notices what another person may need before asking them to navigate the experience alone. It removes unnecessary anxiety and makes welcome tangible.</dd>
        </div>
        <div>
          <dt>Trust</dt>
          <dd><strong>Do your words and actions keep agreeing?</strong> Trust grows when repeated encounters confirm the same truthful story. It weakens when promises, behavior, and experience pull in different directions.</dd>
        </div>
        <div>
          <dt>Brand</dt>
          <dd><strong>What do people carry away?</strong> Brand is the perception that remains in someone else’s mind. It includes what people believe, remember, feel, and expect after encountering the church.</dd>
        </div>
      </dl>

    </div>
    <aside class="framework-neighborhood__rail">
      <span class="framework-label">WORKING TERMS</span>
      <ul>
        <li><a href="/glossary#touchpoints">Touchpoints</a></li>
        <li><a href="/glossary#hospitality">Hospitality</a></li>
        <li><a href="/glossary#trust">Trust</a></li>
        <li><a href="/glossary#brand">Brand</a></li>
      </ul>
      <a class="read-link" href="/future-congregation-journey">EXPLORE THE FUTURE CONGREGATION JOURNEY &rarr;</a>
    </aside>
    <div class="framework-connection">
      <span>HOW THEY WORK TOGETHER</span>
      <p>Hospitality shapes the way we prepare touchpoints. Repeated touchpoints build or erode trust. Trust becomes part of Brand, and Brand shows us whether the story people experience agrees with the story we intended to tell.</p>
    </div>
  </section>

  <section class="framework-neighborhood" id="invitation-transformation" aria-labelledby="invitationTransformationHeading">
    <div class="framework-neighborhood__title">
      <span class="framework-label">INVITATION &amp; TRANSFORMATION</span>
      <h2 id="invitationTransformationHeading">How do we help people take a faithful next step?</h2>
    </div>
    <div class="framework-neighborhood__body">
      <p>Invitation begins by paying attention. Before we decide what to say, we need to understand whom we are trying to serve, what they are carrying, and what faithful next step we are actually prepared to offer.</p>

      <dl class="framework-term-guide">
        <div>
          <dt>Audience</dt>
          <dd><strong>Whom are you called to serve?</strong> Audience brings focus to the people a message, ministry, or invitation is meant to help. It does not say other people do not matter. It helps the church communicate with enough care to be understood.</dd>
        </div>
        <div>
          <dt>Persona</dt>
          <dd><strong>Who is that person beyond a demographic?</strong> A persona helps us picture a real person with hopes, pressures, questions, relationships, habits, and a history. It helps us communicate with empathy instead of writing for a statistic.</dd>
        </div>
        <div>
          <dt>Marketing</dt>
          <dd><strong>How can you generously invite someone toward meaningful change?</strong> Marketing listens, tells honest stories that resonate, earns trust, and helps the right people recognize a next step that may genuinely serve them.</dd>
        </div>
        <div>
          <dt>Evangelistic Marketing</dt>
          <dd><strong>How can you help people move toward who God has called them to be?</strong> Evangelistic Marketing submits invitation to the Gospel. It serves transformation rather than attention, attendance, or growth for its own sake.</dd>
        </div>
        <div>
          <dt>Call to Action</dt>
          <dd><strong>What is the next faithful step?</strong> A call to action turns a broad invitation into something a person can understand and do. It should be clear, honest, appropriate to the relationship, and connected to a real place the church is ready to receive them.</dd>
        </div>
        <div>
          <dt>Transformation</dt>
          <dd><strong>What change are you hoping to serve?</strong> Transformation is not a metric the church manufactures. It is the movement toward greater truth, wholeness, belonging, faithfulness, and participation that God is already inviting people into.</dd>
        </div>
      </dl>

    </div>
    <aside class="framework-neighborhood__rail">
      <span class="framework-label">WORKING TERMS</span>
      <ul>
        <li><a href="/glossary#audience">Audience</a></li>
        <li><a href="/glossary#persona">Persona</a></li>
        <li><a href="/glossary#marketing">Marketing</a></li>
        <li><a href="/glossary#evangelistic-marketing">Evangelistic Marketing</a></li>
        <li><a href="/glossary#call-to-action">Call to Action</a></li>
        <li><a href="/glossary#transformation">Transformation</a></li>
      </ul>
      <a class="read-link" href="/blog/your-church-isnt-for-everyone">READ ABOUT SERVING THE RIGHT PEOPLE &rarr;</a>
    </aside>
    <div class="framework-connection">
      <span>HOW THEY WORK TOGETHER</span>
      <p>Audience gives the invitation focus, and a persona helps us bring empathy to that focus. Marketing carries an honest invitation. Evangelistic Marketing gives it a Gospel-shaped purpose. A call to action prepares the next step, and Ministry gives that step somewhere real to lead.</p>
    </div>
  </section>

  <section class="framework-related" aria-labelledby="relatedHeading">
    <div class="framework-related__head">
      <span class="framework-label">FOLLOW ONE IDEA FARTHER</span>
      <h2 id="relatedHeading">Field Notes from the framework</h2>
    </div>
    <div class="framework-related__grid">
      <a href="/blog/the-right-story-told-the-right-way"><img src="/assets/img/blog/the-right-story-told-the-right-way.jpg" alt="" width="1024" height="559" loading="lazy"><span>FOUNDATIONS</span><strong>The Right Story, Told the Right Way</strong></a>
      <a href="/blog/your-church-already-has-a-brand"><img src="/assets/img/blog/your-church-already-has-a-brand.jpg" alt="" width="1024" height="559" loading="lazy"><span>BRAND &amp; MISSION</span><strong>Your Church Already Has a Brand</strong></a>
      <a href="/blog/everyone-falls-in-love-the-same-way"><img src="/assets/img/blog/everyone-falls-in-love-the-same-way.jpg" alt="" width="1024" height="559" loading="lazy"><span>ENCOUNTER</span><strong>Everyone Falls in Love the Same Way</strong></a>
      <a href="/blog/change-vs-polish"><img src="/assets/img/blog/change-vs-polish.jpg" alt="" width="1024" height="559" loading="lazy"><span>STRATEGY</span><strong>Change vs. Polish</strong></a>
      <a href="/blog/why-your-church-communications-feel-chaotic"><img src="/assets/img/blog/why-your-church-communications-feel-chaotic.jpg" alt="" width="1024" height="559" loading="lazy"><span>SYSTEMS</span><strong>Why Your Church Communications Feel Chaotic</strong></a>
    </div>
    <div class="framework-related__footer">
      <p>Need the precise definition of a term?</p>
      <a class="btn-ghost" href="/glossary">Explore the working glossary</a>
    </div>
  </section>
</main>

<?php blog_site_footer(); ?>
<script src="/js/framework-map.js" defer></script>
</body>
</html>
