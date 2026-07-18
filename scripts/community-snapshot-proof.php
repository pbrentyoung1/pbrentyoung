<?php

require_once dirname(__DIR__) . '/inc/community-snapshot.php';

$address = trim(implode(' ', array_slice($argv, 1)));
if ($address === '') $address = '12400 Walden Road, Montgomery, Texas 77356';

function proof_number($value) {
  return $value === null ? 'n/a' : number_format((float) $value);
}

function proof_percent($value) {
  return $value === null ? 'n/a' : number_format((float) $value * 100, 1) . '%';
}

function proof_money($value) {
  return $value === null ? 'n/a' : '$' . number_format((float) $value, 0);
}

try {
  $report = community_snapshot_build($address);
  $current = $report['current'];
  $trend = $report['trend'];

  echo "COMMUNITY SNAPSHOT — DATA PROOF\n";
  echo $report['location']['matched_address'] . "\n";
  echo $report['radius_miles'] . "-mile straight-line radius\n";
  echo $report['location']['county_name'] . ', ' . $report['location']['state_name'] . "\n\n";

  echo "PEOPLE\n";
  echo 'Population: ' . proof_number($current['population']) . "\n";
  echo 'Under 18: ' . proof_percent($current['under_18_share']) . "\n";
  echo 'Age 18–64: ' . proof_percent($current['age_18_64_share']) . "\n";
  echo 'Age 65+: ' . proof_percent($current['age_65_plus_share']) . "\n";
  echo 'Hispanic or Latino: ' . proof_percent($current['hispanic_share']) . "\n";
  echo 'White, not Hispanic: ' . proof_percent($current['white_non_hispanic_share']) . "\n";
  echo 'Black, not Hispanic: ' . proof_percent($current['black_non_hispanic_share']) . "\n";
  echo 'Asian, not Hispanic: ' . proof_percent($current['asian_non_hispanic_share']) . "\n";
  echo 'Language other than English at home: ' . proof_percent($current['non_english_home_share']) . "\n";
  echo 'Spanish at home: ' . proof_percent($current['spanish_home_share']) . "\n\n";

  echo "HOUSEHOLDS AND DAILY CONTEXT\n";
  echo 'Households: ' . proof_number($current['households']) . "\n";
  echo 'Average household size: ' . ($current['average_household_size'] === null ? 'n/a' : number_format($current['average_household_size'], 2)) . "\n";
  echo 'Households with children: ' . proof_percent($current['households_with_children_share']) . "\n";
  echo 'Living alone: ' . proof_percent($current['living_alone_share']) . "\n";
  echo 'Estimated median household income: ' . proof_money($current['median_household_income']) . "\n";
  echo 'Below poverty line: ' . proof_percent($current['poverty_share']) . "\n";
  echo "Bachelor's degree or higher: " . proof_percent($current['bachelors_plus_share']) . "\n";
  echo 'Homeowners: ' . proof_percent($current['owner_share']) . "\n";
  echo 'Renters: ' . proof_percent($current['renter_share']) . "\n";
  echo 'Moved in the last year: ' . proof_percent($current['moved_last_year_share']) . "\n";
  echo 'Internet subscription: ' . proof_percent($current['internet_subscription_share']) . "\n\n";

  echo "THEN AND NOW\n";
  echo $report['trend_year'] . ' population: ' . proof_number($trend['population']) . "\n";
  echo $report['current_year'] . ' population: ' . proof_number($current['population']) . "\n";
  $growth = community_snapshot_ratio($current['population'] - $trend['population'], $trend['population']);
  echo 'Estimated change: ' . proof_percent($growth) . "\n\n";

  echo "DIAGNOSTICS\n";
  echo 'Current block groups: ' . $report['diagnostics']['current_block_groups'] . "\n";
  echo 'Current partial block groups: ' . $report['diagnostics']['current_partial_block_groups'] . "\n";
  echo 'Earlier block groups: ' . $report['diagnostics']['trend_block_groups'] . "\n";
  echo 'Earlier partial block groups: ' . $report['diagnostics']['trend_partial_block_groups'] . "\n";
  echo 'Current tracts for limited measures: ' . $report['diagnostics']['current_tracts'] . "\n";
  echo 'Earlier tracts for limited measures: ' . $report['diagnostics']['trend_tracts'] . "\n";
  echo 'ARDA: ' . $report['arda_url'] . "\n\n";
  echo "METHOD NOTE\n";
  echo "Local figures are estimates assembled from ACS five-year block-group data. Boundary block groups are weighted by the share of their land area inside the circle, which assumes people are distributed evenly within each block group.\n";
} catch (Throwable $error) {
  fwrite(STDERR, 'Community Snapshot failed: ' . $error->getMessage() . "\n");
  exit(1);
}
