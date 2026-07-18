<?php

require_once dirname(__DIR__) . '/inc/community-snapshot.php';

function snapshot_test_close($actual, $expected, $tolerance, $message) {
  if (abs($actual - $expected) > $tolerance) {
    throw new RuntimeException($message . ': expected ' . $expected . ', got ' . $actual);
  }
}

$insideSquare = array(array(-1, -1), array(1, -1), array(1, 1), array(-1, 1), array(-1, -1));
$outsideSquare = array(array(20, 20), array(21, 20), array(21, 21), array(20, 21), array(20, 20));
$largeSquare = array(array(-20, -20), array(20, -20), array(20, 20), array(-20, 20), array(-20, -20));

snapshot_test_close(abs(community_snapshot_ring_circle_area($insideSquare, 15)), 4.0, 0.000001, 'Inside polygon area');
snapshot_test_close(abs(community_snapshot_ring_circle_area($outsideSquare, 15)), 0.0, 0.000001, 'Outside polygon area');
snapshot_test_close(abs(community_snapshot_ring_circle_area($largeSquare, 15)), M_PI * 225, 0.001, 'Circle-contained polygon area');
snapshot_test_close(community_snapshot_median_income(array(
  'B19001_001E' => 100,
  'B19001_002E' => 20,
  'B19001_003E' => 30,
  'B19001_004E' => 50,
)), 15000.0, 0.001, 'Grouped median income');

echo "Community Snapshot calculations passed.\n";
