<?php
function bmfft_search($query)
{
	if (!empty($query['media_class'])) $mediaclass = $query['media_class'];

	// First, tokenikze the search string
	if (!empty($query['query'])) $terms = explode(' ', strtolower($query['query']));

	// Determine the strictness of each search term
	foreach ($terms as $key => $value) {
		if ($value[0] == '+') {
			$terms[$key] = substr($terms[$key], 1);
			$search_rules[$terms[$key]] = 'strict';
		} 
		if ($value[0] == '-') {
			$terms[$key] = substr($terms[$key], 1);
			$search_rules[$terms[$key]] = 'forbid';
		} 
	}

	// TODO Approximate tags which are not exact by levenshtien distance

	// Next search the database for them and keep an array of keys associated with the
	// number of matching terms
	$dbh = dba_open(CONFIG_TAG_DB, 'rd', 'gdbm');
	$key = dba_firstkey($dbh);
	while ($key !== false) {
		$value = dba_fetch($key, $dbh);
		$value = json_decode($value, true);

		// Filter by media class
		if ($mediaclass && $value['media_class'] != $mediaclass) {
			$key = dba_nextkey($dbh);
			continue;
		}

		// Skip most processing if we have a blank query
		if (!$terms) {
			$results[$key]++;
			$key = dba_nextkey($dbh);
			continue;
		}
		// Check the relevancy of each search term
		// Fucking hell this is bad but w/e~ I'll patch it up
		// in a more readable form sometime but I'd rather just
		// be over it for now
		foreach ($terms as $term) {
			if ($search_rules[$term] == 'forbid')
				continue;
			// +1
			$term_found = false;	// what the fuck
			foreach ($value['namespaces'] as $a) {
				if ($a[$term]) {
					$results[$key]++;
					$term_found = true; // what the fuck
					continue;
				}
			}
			// If the search term was meant to contain the search term
			// then expel it from the results
			if (!$term_found && $search_rules[$term] == 'strict') {
				unset($results[$key]);
				break;
			}
		}
		$key = dba_nextkey($dbh);
	}
	dba_close($dbh);
	arsort($results);
	return array_keys($results);
}
?>
