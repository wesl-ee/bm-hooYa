<?php
function hooya_search($query)
{
	if (!empty($query['media_class'])) $mediaclass = $query['media_class'];

	// First, tokenikze the search string
	if (!empty($query['query'])) $terms = explode(' ', strtolower($query['query']));
	unset($query['query']);

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

	$dbh = mysqli_connect(CONFIG_MYSQL_HOOYA_HOST,
		CONFIG_MYSQL_HOOYA_USER,
		CONFIG_MYSQL_HOOYA_PASSWORD,
		CONFIG_MYSQL_HOOYA_DATABASE);

	// TODO Approximate tags which are not exact by levenshtien distance

	// Next search the database for them and keep an array of keys associated with the
	// number of matching terms
	$query = "SELECT FileId, COUNT(*) AS Matches FROM TagMap WHERE TagId IN ("
	. "SELECT Id AS TagId FROM Tags WHERE ";
	foreach ($terms as $i => $term) {
/*		if ($search_rules[$term] == 'forbid') {
			if ($i > 0) $query .= " OR";
			$query .= " Member != '$term'";
		}*/
//		else {
			if ($i > 0) $query .= " OR";
			$query .= " Member = '$term'";
//		}

	}
	$query .= ") GROUP BY FileId ORDER BY Matches DESC";
/*	if (isset($page, $resultsperpage)) {
		$lower = $page * $resultsperpage;
		$upper = $lower + $resultsperpage;
		$query .= " LIMIT $lower,$upper";
	}*/
	$res = mysqli_query($dbh, $query);
	while ($row = mysqli_fetch_assoc($res)) {
		$results[] = $row["FileId"];
	}
	return $results;
}
?>
