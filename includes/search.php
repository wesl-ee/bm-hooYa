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

	$query = "SELECT Id, COUNT(*) AS Matches FROM Files WHERE Id IN ("
	. "SELECT Files.Id FROM Files, TagMap, Tags WHERE "
	. "Files.Id = TagMap.FileId AND TagMap.TagId = Tags.Id ";
	if (isset($mediaclass)) $query .= "AND Files.Class = '$mediaclass' ";
	$query .= "AND (";
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
	$query .= "))";

	// Display all items on an empty query
	if (empty($terms)) {
		$query = "SELECT Id, COUNT(*) AS Matches FROM Files ";
		if (isset($mediaclass)) $query .= " WHERE Files.Class = '$mediaclass' ";
	}

	$query .= " GROUP BY Id ORDER BY Matches DESC, Id DESC";
	$res = mysqli_query($dbh, $query);
	while ($row = mysqli_fetch_assoc($res)) {
		$results[] = $row["Id"];
	}
	return $results;
}
?>
