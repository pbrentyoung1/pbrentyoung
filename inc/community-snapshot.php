<?php

/**
 * Server-side data service for the Community Snapshot field tool.
 *
 * The browser never talks to Census services directly. API credentials, raw
 * geographies, and the weighting work remain on the server.
 */

const COMMUNITY_SNAPSHOT_RADIUS_MILES = 15.0;
const COMMUNITY_SNAPSHOT_CURRENT_YEAR = 2024;
const COMMUNITY_SNAPSHOT_TREND_YEAR = 2019;

function community_snapshot_api_key() {
  $key = getenv('CENSUS_API_KEY');
  if (is_string($key) && trim($key) !== '') return trim($key);

  $localFile = __DIR__ . '/secrets.local.php';
  if (is_file($localFile)) {
    $secrets = require $localFile;
    if (is_array($secrets) && !empty($secrets['census_api_key'])) {
      return trim((string) $secrets['census_api_key']);
    }
  }

  throw new RuntimeException('The Census API key is not configured.');
}

function community_snapshot_http_json($url, $timeout = 30, $cacheIdentity = null, $cacheTtl = 0) {
  $cacheFile = null;
  if (is_string($cacheIdentity) && $cacheIdentity !== '' && $cacheTtl > 0) {
    $cacheDirectory = dirname(__DIR__) . '/var/cache/community-snapshot';
    $cacheFile = $cacheDirectory . '/' . hash('sha256', $cacheIdentity) . '.json';
    if (is_file($cacheFile) && filemtime($cacheFile) >= time() - $cacheTtl) {
      $cached = json_decode((string) file_get_contents($cacheFile), true);
      if (is_array($cached)) return $cached;
    }
  }

  $handle = curl_init($url);
  curl_setopt_array($handle, array(
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_TIMEOUT => $timeout,
    CURLOPT_USERAGENT => 'pbrentyoung.com Community Snapshot/0.1',
    CURLOPT_HTTPHEADER => array('Accept: application/json'),
  ));

  $body = curl_exec($handle);
  $status = (int) curl_getinfo($handle, CURLINFO_RESPONSE_CODE);
  $error = curl_error($handle);
  if ($body === false || $error !== '') {
    throw new RuntimeException('A public-data service could not be reached.');
  }

  if ($status < 200 || $status >= 300) {
    throw new RuntimeException('A public-data service returned an unexpected response.');
  }

  $data = json_decode($body, true);
  if (!is_array($data)) {
    throw new RuntimeException('A public-data service returned unreadable data.');
  }

  if ($cacheFile !== null) {
    $cacheDirectory = dirname($cacheFile);
    if (is_dir($cacheDirectory) || @mkdir($cacheDirectory, 0775, true)) {
      $temporary = $cacheFile . '.' . bin2hex(random_bytes(4)) . '.tmp';
      if (file_put_contents($temporary, json_encode($data), LOCK_EX) !== false) {
        @rename($temporary, $cacheFile);
      } else {
        @unlink($temporary);
      }
    }
  }

  return $data;
}

function community_snapshot_geocode($address) {
  $address = trim((string) $address);
  if ($address === '') throw new InvalidArgumentException('Enter a complete campus address.');
  if (mb_strlen($address) > 240) throw new InvalidArgumentException('That address is too long.');

  $query = http_build_query(array(
    'address' => $address,
    'benchmark' => 'Public_AR_Current',
    'vintage' => 'Current_Current',
    'format' => 'json',
  ), '', '&', PHP_QUERY_RFC3986);

  try {
    $data = community_snapshot_http_json(
      'https://geocoding.geo.census.gov/geocoder/geographies/onelineaddress?' . $query
    );
    $matches = $data['result']['addressMatches'] ?? array();
  } catch (Throwable $exception) {
    $matches = array();
  }

  if ($matches) {
    $match = $matches[0];
    $county = $match['geographies']['Counties'][0] ?? null;
    $state = $match['geographies']['States'][0] ?? null;
    if ($county && $state) {
      return community_snapshot_location_record(
        (string) ($match['matchedAddress'] ?? $address),
        (float) $match['coordinates']['y'],
        (float) $match['coordinates']['x'],
        $state,
        $county,
        'census'
      );
    }
  }

  return community_snapshot_geocode_arcgis($address);
}

function community_snapshot_geocode_arcgis($address) {
  $query = http_build_query(array(
    'SingleLine' => $address,
    'f' => 'json',
    'outFields' => 'Match_addr,Addr_type,City,Region,Postal',
    'maxLocations' => '1',
    'forStorage' => 'false',
    'countryCode' => 'USA',
  ), '', '&', PHP_QUERY_RFC3986);
  $data = community_snapshot_http_json(
    'https://geocode.arcgis.com/arcgis/rest/services/World/GeocodeServer/findAddressCandidates?' . $query
  );
  $candidate = $data['candidates'][0] ?? null;
  $allowedTypes = array('PointAddress', 'PointAddressInt', 'Subaddress', 'StreetAddress');

  if (!$candidate
      || (float) ($candidate['score'] ?? 0) < 90
      || !in_array((string) ($candidate['attributes']['Addr_type'] ?? ''), $allowedTypes, true)) {
    throw new RuntimeException('We could not match that address. Add the city, state, and ZIP, then try again.');
  }

  $longitude = (float) $candidate['location']['x'];
  $latitude = (float) $candidate['location']['y'];
  $geographies = community_snapshot_geographies_at_point($latitude, $longitude);
  $county = $geographies['Counties'][0] ?? null;
  $state = $geographies['States'][0] ?? null;
  if (!$county || !$state) throw new RuntimeException('We matched the address but could not identify its county.');

  return community_snapshot_location_record(
    (string) ($candidate['address'] ?? $address),
    $latitude,
    $longitude,
    $state,
    $county,
    'arcgis'
  );
}

function community_snapshot_geographies_at_point($latitude, $longitude) {
  $query = http_build_query(array(
    'x' => $longitude,
    'y' => $latitude,
    'benchmark' => 'Public_AR_Current',
    'vintage' => 'Current_Current',
    'format' => 'json',
  ), '', '&', PHP_QUERY_RFC3986);
  $data = community_snapshot_http_json(
    'https://geocoding.geo.census.gov/geocoder/geographies/coordinates?' . $query
  );
  return $data['result']['geographies'] ?? array();
}

function community_snapshot_location_record($matchedAddress, $latitude, $longitude, $state, $county, $source) {
  return array(
    'matched_address' => $matchedAddress,
    'longitude' => $longitude,
    'latitude' => $latitude,
    'state_fips' => str_pad((string) $state['STATE'], 2, '0', STR_PAD_LEFT),
    'state_name' => (string) ($state['NAME'] ?? ''),
    'county_fips' => str_pad((string) $county['COUNTY'], 3, '0', STR_PAD_LEFT),
    'county_geoid' => str_pad((string) $county['GEOID'], 5, '0', STR_PAD_LEFT),
    'county_name' => (string) ($county['NAME'] ?? 'County'),
    'geocoder_source' => $source,
  );
}

function community_snapshot_tiger_url($year, $level = 'block-groups') {
  if ((int) $year === 2019) {
    $layer = $level === 'tracts' ? 8 : 10;
    return 'https://tigerweb.geo.census.gov/arcgis/rest/services/TIGERweb/tigerWMS_ACS2019/MapServer/' . $layer . '/query';
  }

  $layer = $level === 'tracts' ? 7 : 8;
  return 'https://tigerweb.geo.census.gov/arcgis/rest/services/TIGERweb/Tracts_Blocks/MapServer/' . $layer . '/query';
}

function community_snapshot_geographies($latitude, $longitude, $year, $radiusMiles = COMMUNITY_SNAPSHOT_RADIUS_MILES, $level = 'block-groups') {
  $outFields = $level === 'tracts' ? 'GEOID,STATE,COUNTY,TRACT' : 'GEOID,STATE,COUNTY,TRACT,BLKGRP';
  $query = http_build_query(array(
    'where' => '1=1',
    'geometry' => $longitude . ',' . $latitude,
    'geometryType' => 'esriGeometryPoint',
    'inSR' => '4326',
    'spatialRel' => 'esriSpatialRelIntersects',
    'distance' => (string) $radiusMiles,
    'units' => 'esriSRUnit_StatuteMile',
    'outFields' => $outFields,
    'returnGeometry' => 'true',
    'outSR' => '4326',
    'geometryPrecision' => '6',
    'f' => 'geojson',
  ), '', '&', PHP_QUERY_RFC3986);

  $data = community_snapshot_http_json(community_snapshot_tiger_url($year, $level) . '?' . $query, 60);
  $features = $data['features'] ?? array();
  if (!$features) throw new RuntimeException('No Census block groups were found around that location.');

  $weighted = array();
  foreach ($features as $feature) {
    $properties = $feature['properties'] ?? array();
    $geometry = $feature['geometry'] ?? array();
    $geoid = (string) ($properties['GEOID'] ?? '');
    if ($geoid === '' || empty($geometry['coordinates'])) continue;

    $weight = community_snapshot_geometry_weight(
      $geometry,
      (float) $latitude,
      (float) $longitude,
      (float) $radiusMiles
    );
    if ($weight <= 0.000001) continue;

    $weighted[$geoid] = array(
      'geoid' => $geoid,
      'state' => str_pad((string) ($properties['STATE'] ?? substr($geoid, 0, 2)), 2, '0', STR_PAD_LEFT),
      'county' => str_pad((string) ($properties['COUNTY'] ?? substr($geoid, 2, 3)), 3, '0', STR_PAD_LEFT),
      'tract' => str_pad((string) ($properties['TRACT'] ?? substr($geoid, 5, 6)), 6, '0', STR_PAD_LEFT),
      'block_group' => $level === 'block-groups' ? (string) ($properties['BLKGRP'] ?? substr($geoid, 11, 1)) : null,
      'weight' => $weight,
    );
  }

  if (!$weighted) throw new RuntimeException('The study area did not overlap any usable Census block groups.');
  return $weighted;
}

function community_snapshot_geometry_weight($geometry, $centerLat, $centerLon, $radiusMiles) {
  $type = $geometry['type'] ?? '';
  $coordinates = $geometry['coordinates'] ?? array();
  $polygons = $type === 'MultiPolygon' ? $coordinates : array($coordinates);
  $totalArea = 0.0;
  $insideArea = 0.0;

  foreach ($polygons as $polygon) {
    foreach ($polygon as $ring) {
      $points = array();
      foreach ($ring as $coordinate) {
        $points[] = community_snapshot_project_point(
          (float) $coordinate[1],
          (float) $coordinate[0],
          $centerLat,
          $centerLon
        );
      }
      $totalArea += community_snapshot_ring_area($points);
      $insideArea += community_snapshot_ring_circle_area($points, $radiusMiles);
    }
  }

  $totalArea = abs($totalArea);
  $insideArea = abs($insideArea);
  if ($totalArea < 0.0000001) return 0.0;
  return max(0.0, min(1.0, $insideArea / $totalArea));
}

function community_snapshot_project_point($latitude, $longitude, $centerLat, $centerLon) {
  $milesPerDegreeLat = 69.0;
  $milesPerDegreeLon = 69.172 * cos(deg2rad($centerLat));
  return array(
    ($longitude - $centerLon) * $milesPerDegreeLon,
    ($latitude - $centerLat) * $milesPerDegreeLat,
  );
}

function community_snapshot_ring_area($points) {
  $area = 0.0;
  $count = count($points);
  for ($i = 0; $i < $count - 1; $i++) {
    $area += community_snapshot_cross($points[$i], $points[$i + 1]) / 2.0;
  }
  return $area;
}

function community_snapshot_ring_circle_area($points, $radius) {
  $area = 0.0;
  $count = count($points);
  for ($i = 0; $i < $count - 1; $i++) {
    $area += community_snapshot_segment_circle_area($points[$i], $points[$i + 1], $radius);
  }
  return $area;
}

function community_snapshot_segment_circle_area($a, $b, $radius) {
  $dx = $b[0] - $a[0];
  $dy = $b[1] - $a[1];
  $aa = $dx * $dx + $dy * $dy;
  if ($aa < 0.000000000001) return 0.0;

  $bb = 2.0 * ($a[0] * $dx + $a[1] * $dy);
  $cc = $a[0] * $a[0] + $a[1] * $a[1] - $radius * $radius;
  $discriminant = $bb * $bb - 4.0 * $aa * $cc;
  $cuts = array(0.0, 1.0);

  if ($discriminant > 0.0) {
    $root = sqrt($discriminant);
    $t1 = (-$bb - $root) / (2.0 * $aa);
    $t2 = (-$bb + $root) / (2.0 * $aa);
    if ($t1 > 0.0 && $t1 < 1.0) $cuts[] = $t1;
    if ($t2 > 0.0 && $t2 < 1.0) $cuts[] = $t2;
  }

  sort($cuts, SORT_NUMERIC);
  $area = 0.0;
  for ($i = 0; $i < count($cuts) - 1; $i++) {
    $tStart = $cuts[$i];
    $tEnd = $cuts[$i + 1];
    $p = array($a[0] + $dx * $tStart, $a[1] + $dy * $tStart);
    $q = array($a[0] + $dx * $tEnd, $a[1] + $dy * $tEnd);
    $midT = ($tStart + $tEnd) / 2.0;
    $midX = $a[0] + $dx * $midT;
    $midY = $a[1] + $dy * $midT;

    if ($midX * $midX + $midY * $midY <= $radius * $radius + 0.000000001) {
      $area += community_snapshot_cross($p, $q) / 2.0;
    } else {
      $area += $radius * $radius * atan2(community_snapshot_cross($p, $q), community_snapshot_dot($p, $q)) / 2.0;
    }
  }

  return $area;
}

function community_snapshot_cross($a, $b) {
  return $a[0] * $b[1] - $a[1] * $b[0];
}

function community_snapshot_dot($a, $b) {
  return $a[0] * $b[0] + $a[1] * $b[1];
}

function community_snapshot_block_group_variables() {
  $variables = array(
    'B01003_001E',
    'B03002_001E', 'B03002_003E', 'B03002_004E', 'B03002_005E', 'B03002_006E',
    'B03002_007E', 'B03002_008E', 'B03002_009E', 'B03002_012E',
    'B11001_001E', 'B11001_008E',
    'B11005_001E', 'B11005_002E',
    'B25008_001E',
    'B15003_001E', 'B15003_022E', 'B15003_023E', 'B15003_024E', 'B15003_025E',
    'B25003_001E', 'B25003_002E', 'B25003_003E',
    'B28002_001E', 'B28002_002E',
  );

  for ($i = 3; $i <= 6; $i++) $variables[] = sprintf('B01001_%03dE', $i);
  for ($i = 20; $i <= 25; $i++) $variables[] = sprintf('B01001_%03dE', $i);
  for ($i = 27; $i <= 30; $i++) $variables[] = sprintf('B01001_%03dE', $i);
  for ($i = 44; $i <= 49; $i++) $variables[] = sprintf('B01001_%03dE', $i);
  for ($i = 2; $i <= 17; $i++) $variables[] = sprintf('B19001_%03dE', $i);
  $variables[] = 'B19001_001E';

  return array_values(array_unique($variables));
}

function community_snapshot_tract_variables() {
  return array(
    'C16001_001E', 'C16001_002E', 'C16001_003E',
    'B17001_001E', 'B17001_002E',
    'B07001_001E', 'B07001_017E',
  );
}

function community_snapshot_variables() {
  return array_merge(community_snapshot_block_group_variables(), community_snapshot_tract_variables());
}

function community_snapshot_census_rows($year, $scope, $variables) {
  $chunks = array_chunk($variables, 42);
  $rowsByGeoid = array();
  $key = community_snapshot_api_key();

  foreach ($chunks as $chunk) {
    $params = array(
      'get' => implode(',', array_merge(array('NAME'), $chunk)),
      'key' => $key,
    );
    if ($scope['type'] === 'block-groups') {
      $params['for'] = 'block group:*';
      $params['in'] = 'state:' . $scope['state'] . ' county:' . $scope['county'] . ' tract:*';
    } elseif ($scope['type'] === 'tracts') {
      $params['for'] = 'tract:*';
      $params['in'] = 'state:' . $scope['state'] . ' county:' . $scope['county'];
    } elseif ($scope['type'] === 'county') {
      $params['for'] = 'county:' . $scope['county'];
      $params['in'] = 'state:' . $scope['state'];
    } else {
      $params['for'] = 'us:*';
    }

    $url = 'https://api.census.gov/data/' . (int) $year . '/acs/acs5?'
      . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
    $cacheIdentity = json_encode(array('acs5', (int) $year, $scope, $chunk));
    $table = community_snapshot_http_json($url, 60, $cacheIdentity, 30 * 86400);
    if (count($table) < 2) continue;
    $headers = array_shift($table);

    foreach ($table as $row) {
      $record = array_combine($headers, $row);
      if ($scope['type'] === 'block-groups') {
        $geoid = str_pad((string) $record['state'], 2, '0', STR_PAD_LEFT)
          . str_pad((string) $record['county'], 3, '0', STR_PAD_LEFT)
          . str_pad((string) $record['tract'], 6, '0', STR_PAD_LEFT)
          . (string) $record['block group'];
      } elseif ($scope['type'] === 'tracts') {
        $geoid = str_pad((string) $record['state'], 2, '0', STR_PAD_LEFT)
          . str_pad((string) $record['county'], 3, '0', STR_PAD_LEFT)
          . str_pad((string) $record['tract'], 6, '0', STR_PAD_LEFT);
      } elseif ($scope['type'] === 'county') {
        $geoid = str_pad((string) $record['state'], 2, '0', STR_PAD_LEFT)
          . str_pad((string) $record['county'], 3, '0', STR_PAD_LEFT);
      } else {
        $geoid = 'US';
      }

      if (!isset($rowsByGeoid[$geoid])) $rowsByGeoid[$geoid] = array();
      foreach ($chunk as $variable) {
        $value = $record[$variable] ?? null;
        $rowsByGeoid[$geoid][$variable] = is_numeric($value) && (float) $value > -999999999
          ? (float) $value
          : null;
      }
    }
  }

  return $rowsByGeoid;
}

function community_snapshot_weighted_counts($geographies, $year, $variables, $level = 'block-groups') {
  $countyGroups = array();
  foreach ($geographies as $geography) {
    $key = $geography['state'] . $geography['county'];
    $countyGroups[$key] = array('state' => $geography['state'], 'county' => $geography['county']);
  }

  $data = array();
  foreach ($countyGroups as $county) {
    $rows = community_snapshot_census_rows($year, array(
      'type' => $level,
      'state' => $county['state'],
      'county' => $county['county'],
    ), $variables);
    $data += $rows;
  }

  $totals = array_fill_keys($variables, 0.0);
  $coverage = array_fill_keys($variables, 0);
  foreach ($geographies as $geoid => $geography) {
    if (!isset($data[$geoid])) continue;
    foreach ($variables as $variable) {
      if ($data[$geoid][$variable] === null) continue;
      $totals[$variable] += $data[$geoid][$variable] * $geography['weight'];
      $coverage[$variable]++;
    }
  }

  foreach ($variables as $variable) {
    if ($coverage[$variable] === 0) $totals[$variable] = null;
  }
  return $totals;
}

function community_snapshot_sum($counts, $variables) {
  $sum = 0.0;
  foreach ($variables as $variable) $sum += (float) ($counts[$variable] ?? 0.0);
  return $sum;
}

function community_snapshot_ratio($part, $whole) {
  if ($whole === null || (float) $whole <= 0.0 || $part === null) return null;
  return (float) $part / (float) $whole;
}

function community_snapshot_median_income($counts) {
  $bounds = array(
    array(0, 10000), array(10000, 15000), array(15000, 20000), array(20000, 25000),
    array(25000, 30000), array(30000, 35000), array(35000, 40000), array(40000, 45000),
    array(45000, 50000), array(50000, 60000), array(60000, 75000), array(75000, 100000),
    array(100000, 125000), array(125000, 150000), array(150000, 200000), array(200000, 250000),
  );
  $total = (float) ($counts['B19001_001E'] ?? 0.0);
  if ($total <= 0.0) return null;
  $target = $total / 2.0;
  $cumulative = 0.0;

  foreach ($bounds as $index => $bound) {
    $count = (float) ($counts[sprintf('B19001_%03dE', $index + 2)] ?? 0.0);
    if ($cumulative + $count >= $target && $count > 0.0) {
      $fraction = ($target - $cumulative) / $count;
      return $bound[0] + ($bound[1] - $bound[0]) * $fraction;
    }
    $cumulative += $count;
  }

  return 250000.0;
}

function community_snapshot_metrics($counts) {
  $population = $counts['B01003_001E'] ?? null;
  $under18 = community_snapshot_sum($counts, array_merge(
    array_map(fn($i) => sprintf('B01001_%03dE', $i), range(3, 6)),
    array_map(fn($i) => sprintf('B01001_%03dE', $i), range(27, 30))
  ));
  $age65 = community_snapshot_sum($counts, array_merge(
    array_map(fn($i) => sprintf('B01001_%03dE', $i), range(20, 25)),
    array_map(fn($i) => sprintf('B01001_%03dE', $i), range(44, 49))
  ));
  $households = $counts['B11001_001E'] ?? null;
  $occupiedHousing = $counts['B25003_001E'] ?? null;
  $educationTotal = $counts['B15003_001E'] ?? null;
  $bachelorsPlus = community_snapshot_sum($counts, array('B15003_022E', 'B15003_023E', 'B15003_024E', 'B15003_025E'));

  return array(
    'population' => $population,
    'under_18_share' => community_snapshot_ratio($under18, $population),
    'age_18_64_share' => community_snapshot_ratio($population - $under18 - $age65, $population),
    'age_65_plus_share' => community_snapshot_ratio($age65, $population),
    'white_non_hispanic_share' => community_snapshot_ratio($counts['B03002_003E'] ?? null, $counts['B03002_001E'] ?? null),
    'black_non_hispanic_share' => community_snapshot_ratio($counts['B03002_004E'] ?? null, $counts['B03002_001E'] ?? null),
    'asian_non_hispanic_share' => community_snapshot_ratio($counts['B03002_006E'] ?? null, $counts['B03002_001E'] ?? null),
    'hispanic_share' => community_snapshot_ratio($counts['B03002_012E'] ?? null, $counts['B03002_001E'] ?? null),
    'non_english_home_share' => community_snapshot_ratio(
      ($counts['C16001_001E'] ?? 0) - ($counts['C16001_002E'] ?? 0),
      $counts['C16001_001E'] ?? null
    ),
    'spanish_home_share' => community_snapshot_ratio($counts['C16001_003E'] ?? null, $counts['C16001_001E'] ?? null),
    'households' => $households,
    'average_household_size' => community_snapshot_ratio($counts['B25008_001E'] ?? null, $occupiedHousing),
    'households_with_children_share' => community_snapshot_ratio($counts['B11005_002E'] ?? null, $counts['B11005_001E'] ?? null),
    'living_alone_share' => community_snapshot_ratio($counts['B11001_008E'] ?? null, $households),
    'median_household_income' => community_snapshot_median_income($counts),
    'poverty_share' => community_snapshot_ratio($counts['B17001_002E'] ?? null, $counts['B17001_001E'] ?? null),
    'bachelors_plus_share' => community_snapshot_ratio($bachelorsPlus, $educationTotal),
    'owner_share' => community_snapshot_ratio($counts['B25003_002E'] ?? null, $occupiedHousing),
    'renter_share' => community_snapshot_ratio($counts['B25003_003E'] ?? null, $occupiedHousing),
    'moved_last_year_share' => community_snapshot_ratio(
      ($counts['B07001_001E'] ?? 0) - ($counts['B07001_017E'] ?? 0),
      $counts['B07001_001E'] ?? null
    ),
    'internet_subscription_share' => community_snapshot_ratio($counts['B28002_002E'] ?? null, $counts['B28002_001E'] ?? null),
  );
}

function community_snapshot_build($address) {
  $location = community_snapshot_geocode($address);
  $blockGroupVariables = community_snapshot_block_group_variables();
  $tractVariables = community_snapshot_tract_variables();
  $variables = array_merge($blockGroupVariables, $tractVariables);

  $currentGeographies = community_snapshot_geographies(
    $location['latitude'], $location['longitude'], COMMUNITY_SNAPSHOT_CURRENT_YEAR
  );
  $trendGeographies = community_snapshot_geographies(
    $location['latitude'], $location['longitude'], COMMUNITY_SNAPSHOT_TREND_YEAR
  );
  $currentTracts = community_snapshot_geographies(
    $location['latitude'], $location['longitude'], COMMUNITY_SNAPSHOT_CURRENT_YEAR,
    COMMUNITY_SNAPSHOT_RADIUS_MILES, 'tracts'
  );
  $trendTracts = community_snapshot_geographies(
    $location['latitude'], $location['longitude'], COMMUNITY_SNAPSHOT_TREND_YEAR,
    COMMUNITY_SNAPSHOT_RADIUS_MILES, 'tracts'
  );

  $currentCounts = community_snapshot_weighted_counts($currentGeographies, COMMUNITY_SNAPSHOT_CURRENT_YEAR, $blockGroupVariables);
  $currentCounts += community_snapshot_weighted_counts($currentTracts, COMMUNITY_SNAPSHOT_CURRENT_YEAR, $tractVariables, 'tracts');
  $trendCounts = community_snapshot_weighted_counts($trendGeographies, COMMUNITY_SNAPSHOT_TREND_YEAR, $blockGroupVariables);
  $trendCounts += community_snapshot_weighted_counts($trendTracts, COMMUNITY_SNAPSHOT_TREND_YEAR, $tractVariables, 'tracts');

  $countyRows = community_snapshot_census_rows(COMMUNITY_SNAPSHOT_CURRENT_YEAR, array(
    'type' => 'county',
    'state' => $location['state_fips'],
    'county' => $location['county_fips'],
  ), $variables);
  $usRows = community_snapshot_census_rows(COMMUNITY_SNAPSHOT_CURRENT_YEAR, array('type' => 'us'), $variables);

  return array(
    'location' => $location,
    'radius_miles' => COMMUNITY_SNAPSHOT_RADIUS_MILES,
    'current_year' => COMMUNITY_SNAPSHOT_CURRENT_YEAR,
    'trend_year' => COMMUNITY_SNAPSHOT_TREND_YEAR,
    'current' => community_snapshot_metrics($currentCounts),
    'trend' => community_snapshot_metrics($trendCounts),
    'county' => community_snapshot_metrics(reset($countyRows) ?: array()),
    'us' => community_snapshot_metrics(reset($usRows) ?: array()),
    'diagnostics' => array(
      'current_block_groups' => count($currentGeographies),
      'trend_block_groups' => count($trendGeographies),
      'current_partial_block_groups' => count(array_filter($currentGeographies, fn($item) => $item['weight'] < 0.999999)),
      'trend_partial_block_groups' => count(array_filter($trendGeographies, fn($item) => $item['weight'] < 0.999999)),
      'current_tracts' => count($currentTracts),
      'trend_tracts' => count($trendTracts),
    ),
    'arda_url' => 'https://www.thearda.com/us-religion/census/congregational-membership?c=' . rawurlencode($location['county_geoid']) . '&t=0&y=2020',
  );
}
