<?php
function hooya_search($query)
{
	if (!empty($query['media_class'])) $mediaclass = $query['media_class'];

	// First, tokenikze the search string
	if (!empty($query['query'])) $terms = explode(' ', strtolower($query['query']));
	unset($query['query']);

	$tagspaces = db_get_tagspaces();

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
	}

	$dbh = mysqli_connect(CONFIG_MYSQL_HOOYA_HOST,
		CONFIG_MYSQL_HOOYA_USER,
		CONFIG_MYSQL_HOOYA_PASSWORD,
		CONFIG_MYSQL_HOOYA_DATABASE);
	// TODO Approximate tags which are not exact by levenshtien distance

	$query = "SELECT Files.Id, COUNT(*) AS Relevance FROM Files, TagMap, Tags WHERE "
	. "Files.Id = TagMap.FileId AND TagMap.TagId = Tags.Id ";
	if (isset($mediaclass)) $query .= "AND Files.Class = '$mediaclass' ";
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

	// Display all items on an empty query
	if (empty($terms)) {
		$query = "SELECT Id, COUNT(*) AS Relevance FROM Files ";
		if (isset($mediaclass)) $query .= " WHERE Files.Class = '$mediaclass' ";
	}

	$query .= " GROUP BY Files.Id ORDER BY Relevance DESC";
	$res = mysqli_query($dbh, $query);
	while ($row = mysqli_fetch_assoc($res)) {
		$results[] = $row["Id"];
	}
	return $results;
}
?>
