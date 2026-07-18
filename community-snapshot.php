<?php

require_once __DIR__ . '/inc/blog.php';
require_once __DIR__ . '/inc/community-snapshot.php';

$title = 'Community Snapshot — Brent Young';
$description = 'A simple demographic field tool for understanding the community surrounding a church campus.';
$canonical = blog_site_url('/community-snapshot');
$address = trim((string) ($_POST['address'] ?? ''));
$report = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // A cold Census request can take longer than PHP's common 30-second default.
  // The browser keeps the user on the clean GET page while this request runs.
  @set_time_limit(120);
  try {
    if (!empty($_POST['company'])) throw new RuntimeException('Please try again.');
    $report = community_snapshot_build($address);
  } catch (Throwable $exception) {
    $error = $exception->getMessage();
  }
}

function snapshot_page_value($value, $format = 'percent') {
  if ($value === null || !is_finite((float) $value)) return 'Not available';
  if ($format === 'number') return number_format((float) $value, 0);
  if ($format === 'decimal') return number_format((float) $value, 2);
  if ($format === 'money') return '$' . number_format((float) $value, 0);
  return number_format((float) $value * 100, 1) . '%';
}

function snapshot_page_difference($current, $earlier, $format = 'percent') {
  if ($current === null || $earlier === null) return 'Not available';
  if ($format === 'number') {
    $difference = community_snapshot_ratio($current - $earlier, $earlier);
    if ($difference === null) return 'Not available';
    return ($difference >= 0 ? '+' : '') . number_format($difference * 100, 1) . '%';
  }
  $points = ($current - $earlier) * 100;
  return ($points >= 0 ? '+' : '') . number_format($points, 1) . ' pts';
}

function snapshot_export_value($value, $format) {
  if ($value === null || !is_finite((float) $value)) return '';
  if ($format === 'percent') return number_format((float) $value * 100, 1, '.', '') . '%';
  if ($format === 'decimal') return number_format((float) $value, 2, '.', '');
  return (string) round((float) $value);
}

function snapshot_export_unit($format) {
  if ($format === 'percent') return 'Percent';
  if ($format === 'money') return 'USD';
  if ($format === 'decimal') return 'Average';
  return 'People or households';
}

function snapshot_export_payload($report, $sections, $trendRows) {
  $rows = array(
    array('COMMUNITY SNAPSHOT'),
    array('Campus address', $report['location']['matched_address']),
    array('Study area', $report['radius_miles'] . '-mile straight-line radius'),
    array('Campus county', $report['location']['county_name']),
    array('Current ACS period', ($report['current_year'] - 4) . '–' . $report['current_year'] . ' five-year estimate'),
    array('Earlier ACS period', ($report['trend_year'] - 4) . '–' . $report['trend_year'] . ' five-year estimate'),
    array('Data source', 'U.S. Census Bureau, American Community Survey five-year estimates'),
    array(),
    array('Section', 'Measure', '15-mile community', $report['location']['county_name'], 'United States', 'Unit'),
  );

  foreach ($sections as $sectionName => $measures) {
    foreach ($measures as $measure) {
      $rows[] = array(
        $sectionName,
        $measure[0],
        snapshot_export_value($report['current'][$measure[1]], $measure[2]),
        snapshot_export_value($report['county'][$measure[1]], $measure[2]),
        snapshot_export_value($report['us'][$measure[1]], $measure[2]),
        snapshot_export_unit($measure[2]),
      );
    }
  }

  $rows[] = array();
  $rows[] = array('THEN AND NOW');
  $rows[] = array('Section', 'Measure', 'ACS ' . $report['trend_year'], 'ACS ' . $report['current_year'], 'Change', 'Unit');
  foreach ($trendRows as $measure) {
    $rows[] = array(
      'Then and Now',
      $measure[0],
      snapshot_export_value($report['trend'][$measure[1]], $measure[2]),
      snapshot_export_value($report['current'][$measure[1]], $measure[2]),
      snapshot_page_difference($report['current'][$measure[1]], $report['trend'][$measure[1]], $measure[2]),
      snapshot_export_unit($measure[2]),
    );
  }

  $rows[] = array();
  $rows[] = array('METHOD NOTE');
  $rows[] = array('Local figures are estimates assembled from Census block groups and tracts. Boundary geographies are weighted by the share of their land area inside the circle, which assumes people are distributed evenly within each geography. A fifteen-mile straight-line radius is not the same as a fifteen-mile drive.');
  $rows[] = array('ARDA county report', $report['arda_url']);

  $filenameAddress = strtolower($report['location']['matched_address']);
  $filenameAddress = preg_replace('/[^a-z0-9]+/', '-', $filenameAddress);
  $filenameAddress = trim((string) $filenameAddress, '-');

  return array(
    'filename' => 'community-snapshot-' . ($filenameAddress ?: 'report') . '.csv',
    'rows' => $rows,
  );
}

/* ---------- chart rows: server-rendered, print-safe ---------- */

function snapshot_chart_positions($values, $cap = 1.2) {
  $finite = array_filter($values, function ($v) { return $v !== null && is_finite((float) $v); });
  $max = $finite ? max($finite) : 0;
  if ($max <= 0) return array_map(function () { return null; }, $values);
  $scale = $max * $cap;
  return array_map(function ($v) use ($scale) {
    if ($v === null || !is_finite((float) $v)) return null;
    return max(0, min(100, ((float) $v / $scale) * 100));
  }, $values);
}

function snapshot_chart_row($label, $local, $county, $us, $format) {
  /* Raw counts (population, households) are not comparable across
     geographies of different sizes; they get values without a track. */
  $charted = $format !== 'number';
  $html = '<div class="csrow' . ($charted ? '' : ' csrow--plain') . '">';
  $html .= '<span class="csrow__label">' . blog_e($label) . '</span>';
  if ($charted) {
    $positions = snapshot_chart_positions(array($local, $county, $us));
    $html .= '<div class="csrow__track" aria-hidden="true">';
    if ($positions[0] !== null) $html .= '<div class="csrow__bar" style="width:' . number_format($positions[0], 2, '.', '') . '%"></div>';
    if ($positions[1] !== null) $html .= '<span class="csrow__tick csrow__tick--county" style="left:' . number_format($positions[1], 2, '.', '') . '%"></span>';
    if ($positions[2] !== null) $html .= '<span class="csrow__tick csrow__tick--us" style="left:' . number_format($positions[2], 2, '.', '') . '%"></span>';
    $html .= '</div>';
  } else {
    $html .= '<div class="csrow__spacer" aria-hidden="true"></div>';
  }
  $html .= '<span class="csrow__values">';
  $html .= '<em class="v-local"><span class="sr-only">15-mile community </span>' . snapshot_page_value($local, $format) . '</em>';
  $html .= '<em class="v-county"><span class="sr-only">County </span>' . snapshot_page_value($county, $format) . '</em>';
  $html .= '<em class="v-us"><span class="sr-only">United States </span>' . snapshot_page_value($us, $format) . '</em>';
  $html .= '</span></div>';
  return $html;
}

function snapshot_trend_row($label, $then, $now, $format) {
  $positions = snapshot_chart_positions(array($then, $now));
  $html = '<div class="csrow csrow--trend">';
  $html .= '<span class="csrow__label">' . blog_e($label) . '</span>';
  $html .= '<div class="csrow__track" aria-hidden="true">';
  if ($positions[0] !== null && $positions[1] !== null) {
    $left = min($positions[0], $positions[1]);
    $width = abs($positions[1] - $positions[0]);
    $html .= '<span class="cs-dumbbell__line" style="left:' . number_format($left, 2, '.', '') . '%;width:' . number_format($width, 2, '.', '') . '%"></span>';
  }
  if ($positions[0] !== null) $html .= '<span class="cs-dumbbell__dot cs-dumbbell__dot--then" style="left:' . number_format($positions[0], 2, '.', '') . '%"></span>';
  if ($positions[1] !== null) $html .= '<span class="cs-dumbbell__dot cs-dumbbell__dot--now" style="left:' . number_format($positions[1], 2, '.', '') . '%"></span>';
  $html .= '</div>';
  $html .= '<span class="csrow__values">';
  $html .= '<em class="v-then"><span class="sr-only">Earlier </span>' . snapshot_page_value($then, $format) . '</em>';
  $html .= '<em class="v-now"><span class="sr-only">Current </span>' . snapshot_page_value($now, $format) . '</em>';
  $html .= '<em class="v-change">' . snapshot_page_difference($now, $then, $format) . '</em>';
  $html .= '</span></div>';
  return $html;
}

$sections = array(
  'People' => array(
    array('Population', 'population', 'number'),
    array('Under 18', 'under_18_share', 'percent'),
    array('Age 65 and older', 'age_65_plus_share', 'percent'),
    array('Hispanic or Latino', 'hispanic_share', 'percent'),
    array('White, not Hispanic', 'white_non_hispanic_share', 'percent'),
    array('Black, not Hispanic', 'black_non_hispanic_share', 'percent'),
    array('Asian, not Hispanic', 'asian_non_hispanic_share', 'percent'),
    array('Language other than English at home', 'non_english_home_share', 'percent'),
    array('Spanish spoken at home', 'spanish_home_share', 'percent'),
  ),
  'Households' => array(
    array('Households', 'households', 'number'),
    array('Average household size', 'average_household_size', 'decimal'),
    array('Households with children', 'households_with_children_share', 'percent'),
    array('People living alone', 'living_alone_share', 'percent'),
  ),
  'Daily context' => array(
    array('Estimated median household income', 'median_household_income', 'money'),
    array('Below the poverty line', 'poverty_share', 'percent'),
    array("Bachelor's degree or higher", 'bachelors_plus_share', 'percent'),
    array('Homeowners', 'owner_share', 'percent'),
    array('Renters', 'renter_share', 'percent'),
    array('Moved in the last year', 'moved_last_year_share', 'percent'),
    array('Internet subscription', 'internet_subscription_share', 'percent'),
  ),
);

$trendRows = array(
  array('Population', 'population', 'number'),
  array('Under 18', 'under_18_share', 'percent'),
  array('Age 65 and older', 'age_65_plus_share', 'percent'),
  array('Households with children', 'households_with_children_share', 'percent'),
  array('Hispanic or Latino', 'hispanic_share', 'percent'),
  array('Language other than English at home', 'non_english_home_share', 'percent'),
  array('Homeowners', 'owner_share', 'percent'),
  array('Internet subscription', 'internet_subscription_share', 'percent'),
);

header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-store, max-age=0');
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo blog_e($title); ?></title>
  <meta name="description" content="<?php echo blog_e($description); ?>">
  <meta name="author" content="Brent Young">
  <meta name="robots" content="noindex, nofollow">
  <meta name="theme-color" content="#f4f1ea">
  <link rel="canonical" href="<?php echo blog_e($canonical); ?>">
  <?php echo blog_google_tag(); ?>
  <link rel="icon" href="/favicon.ico" sizes="48x48">
  <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32.png">
  <link rel="apple-touch-icon" href="/apple-touch-icon.png">
  <link rel="manifest" href="/site.webmanifest">
  <link rel="stylesheet" href="/css/editorial.css?v=<?php echo (int) filemtime(__DIR__ . '/css/editorial.css'); ?>">
</head>
<body class="blog-site snapshot-page">
<?php blog_site_header(); ?>

<main class="snapshot wrap">
  <header class="snapshot__masthead">
    <span class="kicker">FIELD TOOL · START WITH LISTENING</span>
    <h1>Community Snapshot</h1>
    <p class="snapshot__dek">A clear first portrait of the people living around your church, built from public Census estimates.</p>
  </header>

  <section class="snapshot-form" aria-labelledby="snapshotFormHeading">
    <div class="snapshot-form__copy">
      <span class="snapshot-label">BEGIN HERE</span>
      <h2 id="snapshotFormHeading">Where is your church located?</h2>
      <p>Enter a complete campus address. We will use it to estimate the community within a fifteen-mile straight-line radius.</p>
    </div>
    <form method="post" action="/community-snapshot" class="snapshot-form__fields">
      <label for="snapshotAddress">Street address, city, state, and ZIP</label>
      <div class="snapshot-form__row">
        <input id="snapshotAddress" name="address" type="text" value="<?php echo blog_e($address); ?>" placeholder="12400 Walden Road, Montgomery, TX 77356" autocomplete="street-address" maxlength="240" required>
        <button class="btn-ink" type="submit" data-state="<?php echo $report ? 'clear' : 'build'; ?>"><?php echo $report ? 'Clear data' : 'Build snapshot'; ?></button>
      </div>
      <div class="snapshot-honeypot" aria-hidden="true"><label>Company <input type="text" name="company" tabindex="-1" autocomplete="off"></label></div>
      <p class="snapshot-form__privacy">We send this address to the U.S. Census Bureau to find its location. If Census cannot match it, we use ArcGIS as a backup. We do not save the address or result.</p>
    </form>
  </section>

  <?php if ($error): ?>
    <div class="snapshot-error" role="alert">
      <span>THE REPORT COULD NOT BE BUILT</span>
      <p><?php echo blog_e($error); ?></p>
    </div>
  <?php endif; ?>

  <div class="sr-only" id="snapshotResultStatus" aria-live="polite"></div>

  <?php if ($report): ?>
    <article class="snapshot-report" aria-labelledby="snapshotReportHeading">
      <header class="snapshot-report__header">
        <div>
          <span class="snapshot-label">COMMUNITY ON FILE · ACS <?php echo (int) $report['current_year']; ?></span>
          <h2 id="snapshotReportHeading"><?php echo blog_e($report['location']['matched_address']); ?></h2>
          <p><?php echo snapshot_page_value($report['current']['population'], 'number'); ?> people estimated within a <?php echo snapshot_page_value($report['radius_miles'], 'number'); ?>-mile straight-line radius.</p>
        </div>
        <dl class="snapshot-report__folio">
          <div><dt>Campus county</dt><dd><?php echo blog_e($report['location']['county_name']); ?></dd></div>
          <div><dt>Local geography</dt><dd><?php echo (int) $report['diagnostics']['current_block_groups']; ?> block groups</dd></div>
          <div><dt>Source</dt><dd>U.S. Census Bureau</dd></div>
        </dl>
      </header>

      <div class="snapshot-report__actions">
        <button class="snapshot-download" type="button">Download spreadsheet <span>CSV</span></button>
      </div>
      <script class="snapshot-export-data" type="application/json"><?php echo json_encode(snapshot_export_payload($report, $sections, $trendRows), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG); ?></script>

      <div class="snapshot-note">
        <strong>Demographics show us where to listen.</strong>
        <span>Interviews help us understand the people who live there.</span>
      </div>

      <div class="snapshot-legend">
        <span><i class="lg lg--local"></i>15-MILE COMMUNITY</span>
        <span><i class="lg lg--county"></i><?php echo blog_e(strtoupper($report['location']['county_name'])); ?></span>
        <span><i class="lg lg--us"></i>UNITED STATES</span>
      </div>

      <?php foreach ($sections as $sectionName => $rows): ?>
        <section class="snapshot-section" aria-labelledby="section-<?php echo blog_e(strtolower(str_replace(' ', '-', $sectionName))); ?>">
          <h3 id="section-<?php echo blog_e(strtolower(str_replace(' ', '-', $sectionName))); ?>"><?php echo blog_e($sectionName); ?></h3>
          <div class="snapshot-rows">
            <?php foreach ($rows as $row): ?>
              <?php echo snapshot_chart_row($row[0], $report['current'][$row[1]], $report['county'][$row[1]], $report['us'][$row[1]], $row[2]); ?>
            <?php endforeach; ?>
          </div>
        </section>
      <?php endforeach; ?>

      <section class="snapshot-section snapshot-trend" aria-labelledby="snapshotTrendHeading">
        <span class="snapshot-label">THEN AND NOW</span>
        <h3 id="snapshotTrendHeading">A community is not a snapshot.</h3>
        <p>These changes help show how the people surrounding this campus are shifting over time. The periods do not overlap.</p>
        <div class="snapshot-legend snapshot-legend--trend">
          <span><i class="lg lg--then"></i>ACS <?php echo (int) $report['trend_year']; ?></span>
          <span><i class="lg lg--local"></i>ACS <?php echo (int) $report['current_year']; ?></span>
        </div>
        <div class="snapshot-rows">
          <?php foreach ($trendRows as $row): ?>
            <?php echo snapshot_trend_row($row[0], $report['trend'][$row[1]], $report['current'][$row[1]], $row[2]); ?>
          <?php endforeach; ?>
        </div>
      </section>

      <section class="snapshot-listen" aria-labelledby="snapshotListenHeading">
        <span class="snapshot-label">THE NEXT STEP</span>
        <h3 id="snapshotListenHeading">The data gives you a place to begin. It cannot listen for you.</h3>
        <p>It cannot tell you what your neighbors fear, what they hope for, why they are looking for a church, or what might help them trust you. Use what you see here to ask better questions, then talk with real people.</p>
        <div class="snapshot-links">
          <a href="/blog/a-persona-is-more-than-a-demographic">Read A Persona Is More Than a Demographic &rarr;</a>
          <a href="/future-congregation-journey">Explore the Future Congregation Journey &rarr;</a>
        </div>
      </section>

      <section class="snapshot-resources" aria-labelledby="snapshotResourcesHeading">
        <span class="snapshot-label">GO DEEPER</span>
        <h3 id="snapshotResourcesHeading">Use the right instrument for the next question.</h3>
        <div class="snapshot-resources__grid">
          <a href="<?php echo blog_e($report['arda_url']); ?>" target="_blank" rel="noopener"><strong>Religious landscape</strong><span>Explore <?php echo blog_e($report['location']['county_name']); ?> on ARDA.</span></a>
          <a href="https://data.census.gov/" target="_blank" rel="noopener"><strong>Detailed Census tables</strong><span>Explore the source data in greater detail.</span></a>
          <a href="https://www.pewresearch.org/religion/" target="_blank" rel="noopener"><strong>Belief and practice</strong><span>Explore national religious research from Pew.</span></a>
        </div>
        <p class="snapshot-resources__note">ARDA describes congregations and reported adherents. It does not measure the beliefs or attendance of every resident.</p>
      </section>

      <footer class="snapshot-method">
        <span class="snapshot-label">HOW THIS ESTIMATE WORKS</span>
        <p>The report combines <?php echo (int) $report['current_year']; ?> American Community Survey five-year estimates from Census block groups and tracts. Geographies along the edge are weighted by the share of their land area inside the circle. That assumes people are distributed evenly within each geography, so the local figures should be read as useful estimates rather than exact counts. A fifteen-mile straight-line radius is not the same as a fifteen-mile drive.</p>
        <?php if (($report['location']['geocoder_source'] ?? 'census') === 'arcgis'): ?><p class="snapshot-method__source">Address matched with the ArcGIS World Geocoding Service. Demographic estimates remain from the U.S. Census Bureau.</p><?php endif; ?>
      </footer>
    </article>
  <?php endif; ?>
</main>

<?php blog_site_footer(); ?>
<script>
  (function () {
    var form = document.querySelector('.snapshot-form__fields');
    if (!form) return;
    var formSection = document.querySelector('.snapshot-form');
    var status = document.getElementById('snapshotResultStatus');
    var submitButton = form.querySelector('button[type="submit"]');
    var activeRequest = null;

    function setButtonState(state) {
      if (!submitButton) return;
      submitButton.dataset.state = state;
      submitButton.disabled = state === 'building';
      if (state === 'building') submitButton.textContent = 'Building snapshot…';
      else if (state === 'clear') submitButton.textContent = 'Clear data';
      else submitButton.textContent = 'Build snapshot';
    }

    function clearSnapshot() {
      if (activeRequest) activeRequest.abort();
      activeRequest = null;
      var report = document.querySelector('.snapshot-report');
      var error = document.querySelector('.snapshot-error');
      if (report) report.remove();
      if (error) error.remove();
      form.reset();
      document.getElementById('snapshotAddress').value = '';
      form.removeAttribute('aria-busy');
      setButtonState('build');
      if (status) status.textContent = 'The community snapshot has been cleared.';
      window.history.replaceState({}, document.title, '/community-snapshot');
      formSection.scrollIntoView({
        behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth',
        block: 'start'
      });
    }

    document.addEventListener('click', function (event) {
      var downloadButton = event.target.closest('.snapshot-download');
      if (!downloadButton) return;
      var report = downloadButton.closest('.snapshot-report');
      var dataElement = report ? report.querySelector('.snapshot-export-data') : null;
      if (!dataElement) return;

      try {
        var payload = JSON.parse(dataElement.textContent);
        var csv = payload.rows.map(function (row) {
          return row.map(function (value) {
            var text = value === null || value === undefined ? '' : String(value);
            return '"' + text.replace(/"/g, '""') + '"';
          }).join(',');
        }).join('\r\n');
        var blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8' });
        var url = URL.createObjectURL(blob);
        var link = document.createElement('a');
        link.href = url;
        link.download = payload.filename || 'community-snapshot.csv';
        document.body.appendChild(link);
        link.click();
        link.remove();
        window.setTimeout(function () { URL.revokeObjectURL(url); }, 1000);
        if (status) status.textContent = 'The spreadsheet download is ready.';
      } catch (error) {
        if (status) status.textContent = 'The spreadsheet could not be created.';
      }
    });

    form.addEventListener('submit', function (event) {
      event.preventDefault();
      if (!submitButton) return;
      if (submitButton.dataset.state === 'clear') {
        clearSnapshot();
        return;
      }
      var currentReport = document.querySelector('.snapshot-report');

      setButtonState('building');
      form.setAttribute('aria-busy', 'true');
      if (currentReport) currentReport.classList.add('is-loading');
      if (status) status.textContent = 'Building the community snapshot.';
      activeRequest = new AbortController();

      fetch(form.action, {
        method: 'POST',
        body: new FormData(form),
        headers: { 'X-Requested-With': 'CommunitySnapshot' },
        signal: activeRequest.signal
      })
        .then(function (response) {
          if (!response.ok) throw new Error('The report service returned an error.');
          return response.text();
        })
        .then(function (html) {
          var parsed = new DOMParser().parseFromString(html, 'text/html');
          var incomingReport = parsed.querySelector('.snapshot-report');
          var incomingError = parsed.querySelector('.snapshot-error');
          var oldReport = document.querySelector('.snapshot-report');
          var oldError = document.querySelector('.snapshot-error');
          if (oldError) oldError.remove();

          if (incomingReport) {
            var newReport = document.importNode(incomingReport, true);
            if (oldReport) oldReport.replaceWith(newReport);
            else formSection.insertAdjacentElement('afterend', newReport);
            setButtonState('clear');
            if (status) status.textContent = 'The community snapshot is ready.';
            newReport.scrollIntoView({
              behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth',
              block: 'start'
            });
            return;
          }

          if (oldReport) oldReport.classList.remove('is-loading');
          if (incomingError) {
            var newError = document.importNode(incomingError, true);
            formSection.insertAdjacentElement('afterend', newError);
            setButtonState('build');
            if (status) status.textContent = newError.textContent.trim();
            return;
          }

          throw new Error('The report could not be built.');
        })
        .catch(function (error) {
          if (error && error.name === 'AbortError') return;
          var oldReport = document.querySelector('.snapshot-report');
          if (oldReport) oldReport.classList.remove('is-loading');
          var oldError = document.querySelector('.snapshot-error');
          if (oldError) oldError.remove();
          var message = document.createElement('div');
          message.className = 'snapshot-error';
          message.setAttribute('role', 'alert');
          message.innerHTML = '<span>THE REPORT COULD NOT BE BUILT</span><p>The data service took too long to respond. Please try again.</p>';
          formSection.insertAdjacentElement('afterend', message);
          setButtonState('build');
          if (status) status.textContent = 'The report could not be built. Please try again.';
        })
        .finally(function () {
          activeRequest = null;
          if (submitButton.dataset.state === 'building') setButtonState('build');
          form.removeAttribute('aria-busy');
        });
    });
  }());
</script>
</body>
</html>
