<?php
function hooya_search($query)
{
	if (!empty($query['media_class'])) $mediaclass = $query['media_class'];

	// First, tokenikze the search string
	if (!empty($query['query']))
		$terms = explode(' ', strtolower($query['query']));
	unset($query['query']);

	// Extract all properties
	if (!empty($query['properties']))
		$properties = $query['properties'];

	$tagspaces = db_get_tagspaces();
	$members = db_get_allmembers();

	foreach ($terms as $key => $value) {
		// Determine the *inclusive* strictness of each term
		if ($value[0] == '+') {
			$value = substr($terms[$key], 1);
			$terms[$key] = $value;
			$search_rules[$value] = 'strict';
		}
		// Determine the *exclusive* strictness of each term
		if ($value[0] == '-') {
			$value = substr($terms[$key], 1);
			$terms[$key] = $value;
			$search_rules[$value] = 'forbid';
		}
		// Determine if the term is a tagspace:member pair
		$index = strpos($value, ':');
		if ($index) {
			$space = substr($value, 0, $index);
			if ($tagspaces[$space]) {
				$value = substr($value, $index+1);
				$terms[$key] = $value;
				$search_spaces[$value] = $space;
			}
		}
		// Special handling if you typed something that isn't a tag
		if (!$members[$value]) {
			$terms[$key] = hooya_bestguess($members, $value);
			$query['query'] = join(' ', $terms);
			$message = "<span>Did you mean <a href='?"
			. http_build_query($query) . "'>"
			. $query['query']
			. "?</a></span>";
			return ['message' => $message];
		}
	}

	$dbh = mysqli_connect(CONFIG_MYSQL_HOOYA_HOST,
		CONFIG_MYSQL_HOOYA_USER,
		CONFIG_MYSQL_HOOYA_PASSWORD,
		CONFIG_MYSQL_HOOYA_DATABASE);
	// TODO Approximate tags which are not exact by levenshtien distance

	$query = "SELECT Files.Id, Class, COUNT(*) AS Relevance FROM Files";
	if (!empty($terms))
		$query .= ", TagMap, Tags";
	if (isset($properties, $mediaclass)) {
		$query .= ", " . $mediaclass;
		$query .= " WHERE $mediaclass.Id = Files.Id";
		foreach ($properties as $property => $value) {
			if (empty($property) || empty($value)) continue;
			$query .= " AND $property = $value";
		}
		$query .= " AND";
	}
	else if (isset($mediaclass) || !empty($terms)) {
		$query .= " WHERE";
	}
	if (isset($mediaclass)) $query .= " Files.Class = '$mediaclass'";
	if (!empty($terms)) {
		if (isset($mediaclass)) $query .= " AND";
		$query .= " Files.Id = TagMap.FileId AND TagMap.TagId = Tags.Id ";
		$query .= "AND (";
		foreach ($terms as $i => $term) {
	/*		if ($search_rules[$term] == 'forbid') {
				if ($i > 0) $query .= " OR";
				$query .= " Member != '$term'";
			}*/
	//		else {
				if ($i > 0) $query .= " OR ";
				$query .= "(Member = '$term'";
				// Account for any tagspace specification like series:
				if ($space = $search_spaces[$term])
					$query .= " AND Space = '$space'";
				$query .= ")";
	//		}
		}
		$query .= ")";
	}

	$query .= " GROUP BY Files.Id ORDER BY Relevance DESC, Files.Id DESC";
	$res = mysqli_query($dbh, $query);
	while ($row = mysqli_fetch_assoc($res)) {
		$results[] = ['key' => $row['Id'], 'class' => $row['Class']];
	}
	return $results;
}
function hooya_bestguess($members, $member)
{
	// Maybe you typed it in first, family name order
	if (strpos($member, '_')) {
		list($first, $last) = explode('_', $member);
		if ($members[($last . '_' . $first)]) {
			$bestguess = $last . '_' . $first;
			return $bestguess;
		}
	}
	// Maybe you only put in a first name
	foreach ($members as $m => $a) {
		if (!strpos($m, '_')) continue;
		$first = array_pop(explode('_', $m));
		if ($first === $member) {
			$bestguess = $m;
			return $bestguess;
		}
	}
	// Maybe you just can't spell
	foreach ($members as $m => $a) {
		$d = levenshtein($m, $member);
		if ($d < $d_closest || !isset($d_closest)) {
			$d_closest = $d;
			$bestguess = $m;
		}
	}
	return $bestguess;
}
?>
