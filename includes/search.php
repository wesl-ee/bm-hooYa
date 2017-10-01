<?php
function hooya_search($query, $page)
{
	if (!empty($query['media_class'])) $mediaclass = $query['media_class'];

	// First, tokenikze the search string
	if (!empty($query['query']))
		$terms = explode(',', strtolower($query['query']));
	unset($query['query']);

	// Extract all properties
	if (!empty($query['properties']))
		$properties = $query['properties'];

	foreach ($terms as $key => $value) {
		// No capital letters allowed!
		$terms[$key] = strtolower($value);
		// Remove any space at the beginning (spaces after commas)
		if ($value[0] == ' ') {
			$value = substr($terms[$key], 1);
			$terms[$key] = $value;
		}
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
			if (db_is_space($space)) {
				$value = substr($value, $index+1);
				$terms[$key] = $value;
				$search_spaces[$value] = $space;
			}
		}

		// Special handling if you typed something that isn't a tag
		if ($alias = db_get_alias($value)) {
			$terms[$key] = $alias;
		}
		else if (!db_is_member($value)) {
			$terms[$key] = hooya_bestguess($value);
			$query['query'] = join(',', $terms);
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

	$query = "SELECT Files.Id, Class, COUNT(*) AS Relevance, Indexed FROM Files";
	if (!empty($terms))
		$query .= ", TagMap, Tags";
	if (isset($mediaclass)) {
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

	$query .= " GROUP BY Files.Id ORDER BY Relevance DESC";
	foreach (DB_FILE_EXTENDED_PROPERTIES[$mediaclass] as $ext => $value) {
		if ($value['Sort'])
			$query .= ", `$ext`+0 ASC";
	}
	$query .= ", Files.Indexed DESC, Files.Id DESC";
	$res = mysqli_query($dbh, $query);
	$results['Count'] = mysqli_num_rows($res);
	$query .= " LIMIT " . CONFIG_THUMBS_PER_PAGE
	. " OFFSET " . (CONFIG_THUMBS_PER_PAGE * ($page - 1));
	$res = mysqli_query($dbh, $query);
	while ($row = mysqli_fetch_assoc($res)) {
		$results[$row['Id']] = [
			'Class' => $row['Class'],
			'Indexed' => $row['Indexed'],
	];
	}
	return $results;
}
function hooya_bestguess($member)
{
	// Maybe you typed it in first, family name order
	if (strpos($member, ' ')) {
		list($first, $last) = explode(' ', $member);
		if (db_is_member($last . ' ' . $first)) {
			$bestguess = $last . ' ' . $first;
			return $bestguess;
		}
	}
	if ($fullname = is_given_name($member)) return $fullname;

	// EXPENSIVE COMPUTATION ZONE
	// WARNING * WARNING * WARNING
	//
	$members = db_get_all_members();
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
// Japanese name-order is "family_name given_name"
function is_given_name($name)
{
	$dbh = mysqli_connect(CONFIG_MYSQL_HOOYA_HOST,
		CONFIG_MYSQL_HOOYA_USER,
		CONFIG_MYSQL_HOOYA_PASSWORD,
		CONFIG_MYSQL_HOOYA_DATABASE);
	mysqli_set_charset($dbh, 'utf8');
	$name = mysqli_real_escape_string($dbh, $name);
	$query = "SELECT Member FROM `Tags` WHERE Member LIKE '% $name'";
	$res = mysqli_query($dbh, $query);
	$row = mysqli_fetch_assoc($res);
	return $row['Member'];
}
?>
