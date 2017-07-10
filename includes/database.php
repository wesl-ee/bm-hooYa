<?php
function db_get_tags($key)
{
	$dbh = mysqli_connect(CONFIG_MYSQL_HOOYA_HOST,
		CONFIG_MYSQL_HOOYA_USER,
		CONFIG_MYSQL_HOOYA_PASSWORD,
		CONFIG_MYSQL_HOOYA_DATABASE);
	mysqli_set_charset($dbh, 'utf8');
	// Escape all potential user input
	$key = mysqli_real_escape_string($dbh, $key);
	// Pull from `Tags` using our $key
	$query = "SELECT Tags.Space, Tags.Member FROM " .
		"Files, TagMap, Tags WHERE Files.Id = '$key' " .
		"AND TagMap.FileId = Files.Id " .
		"AND Tags.Id = TagMap.TagId";
	$res = mysqli_query($dbh, $query);
	while ($row = mysqli_fetch_assoc($res)) {
		$ret[] = ['Space' => $row['Space'], 'Member' => $row['Member']];
	}
	mysqli_close($dbh);
	return $ret;
}
function db_set_tags($key, $tags)
{
	$dbh = mysqli_connect(CONFIG_MYSQL_HOOYA_HOST,
		CONFIG_MYSQL_HOOYA_USER,
		CONFIG_MYSQL_HOOYA_PASSWORD,
		CONFIG_MYSQL_HOOYA_DATABASE);
	mysqli_set_charset($dbh, 'utf8');
	// Escape all potential user input
	$key = mysqli_real_escape_string($dbh, $key);
	$tags = mysqli_real_escape_string($dbh, $tags);
	foreach ($tags as $tag) {
		// First, take the opportunity to insert new tags into `Tags`
		$space = $tag["Space"];
		$member = $tag["Member"];
		$query = "INSERT INTO Tags (`Space`, `Member`) "
			. "VALUES ('$space', '$member')";
		mysqli_query($dbh, $query);

		// Next, map each file->tag pair to a row in TagMap
		$query = "INSERT INTO TagMap (`FileId`, `TagId`) SELECT "
			. "Files.Id AS FileId, Tags.Id AS TagId FROM Files, Tags "
			. "WHERE Files.Id = '$key' AND Tags.Space = "
			. "'$space' AND Tags.Member = '$member'";
		mysqli_query($dbh, $query);
	}

	// Delete any mappings which were not explicitly passed to
	// us in $tags; this indicates the user has removed an association
	$query = "DELETE FROM TagMap WHERE FileId = '$key' AND TagId NOT IN (SELECT "
		. "Tags.Id FROM Files, Tags WHERE Files.Id = '$key' "
		. "AND (";
	foreach ($tags as $tag) {
		$space = $tag["Space"];
		$member = $tag["Member"];
		$query .= "(Tags.Space = '$space' AND Tags.Member = '$member') OR";
	}
	$query = substr($query, 0, -3);
	$query .= "))";
	mysqli_query($dbh, $query);
	// Finally, clean up `Tags`, in the case that all references to the
	// tag have been deleted
	$query = "DELETE FROM Tags WHERE Id NOT IN (SELECT TagId FROM TagMap)";
	mysqli_query($dbh, $query);
	mysqli_close($dbh);
}
function db_set_main_attrs($key, $attrs)
{
	$dbh = mysqli_connect(CONFIG_MYSQL_HOOYA_HOST,
		CONFIG_MYSQL_HOOYA_USER,
		CONFIG_MYSQL_HOOYA_PASSWORD,
		CONFIG_MYSQL_HOOYA_DATABASE);
	mysqli_set_charset($dbh, 'utf8');
	// Escape all potential user input
	$key = mysqli_real_escape_string($dbh, $key);
	// Construct the SQL query
	$query = "UPDATE Files SET";
	foreach ($attrs as $attr => $value) {
		$value = mysqli_real_escape_string($dbh, $value);
		$query .= " $attr = '$value',";
	}
	// Remove trailing comma
	$query = substr($query, 0, -1);
	$query .= " WHERE Id = '$key'";
	mysqli_query($dbh, $query);
	mysqli_close($dbh);
}
function db_get_main_attrs($key, $attrs)
{
	$dbh = mysqli_connect(CONFIG_MYSQL_HOOYA_HOST,
		CONFIG_MYSQL_HOOYA_USER,
		CONFIG_MYSQL_HOOYA_PASSWORD,
		CONFIG_MYSQL_HOOYA_DATABASE);
	mysqli_set_charset($dbh, 'utf8');
	// Escape all potential user input
	$key = mysqli_real_escape_string($dbh, $key);
	// Construct the SQL query
	$query = "SELECT";
	foreach ($attrs as $attr) {
		$attr = mysqli_real_escape_string($dbh, $attr);
		$query .= " $attr,";
	}
	// Remove trailing comma
	$query = substr($query, 0, -1);
	$query .= " FROM Files WHERE Id = '$key'";
	$res = mysqli_query($dbh, $query);
	$row = mysqli_fetch_assoc($res);
	mysqli_close($dbh);
	return $row;
}
function db_getrandom($n)
{
	$dbh = mysqli_connect(CONFIG_MYSQL_HOOYA_HOST,
		CONFIG_MYSQL_HOOYA_USER,
		CONFIG_MYSQL_HOOYA_PASSWORD,
		CONFIG_MYSQL_HOOYA_DATABASE);
	mysqli_set_charset($dbh, 'utf8');
	$query = "SELECT Id FROM Files WHERE NOT Id in ("
	. "SELECT FileId AS Id FROM TagMap) ORDER BY RAND()"
	. " LIMIT " . $n;
	$res = mysqli_query($dbh, $query);
	while ($row = mysqli_fetch_assoc($res))
		$ret[] = $row['Id'];
	mysqli_close($dbh);
	return $ret;
}
function db_tagspace_sort($tag_space)
{
	$dbh = mysqli_connect(CONFIG_MYSQL_HOOYA_HOST,
		CONFIG_MYSQL_HOOYA_USER,
		CONFIG_MYSQL_HOOYA_PASSWORD,
		CONFIG_MYSQL_HOOYA_DATABASE);
	mysqli_set_charset($dbh, 'utf8');
	// Escape all potential user input
	$tag_space = mysqli_real_escape_string($dbh, $tag_space);
	$query = "SELECT Member, COUNT(Member) AS Count FROM TagMap,Tags WHERE TagId = Id"
		. " AND Space = '$tag_space' GROUP BY TagId ORDER BY Count DESC";
	$res = mysqli_query($dbh, $query);
	while ($row = mysqli_fetch_assoc($res)) {
		$ret[$row['Member']] = $row['Count'];
	}
	return $ret;
}
function db_get_tagspaces()
{
	$dbh = mysqli_connect(CONFIG_MYSQL_HOOYA_HOST,
		CONFIG_MYSQL_HOOYA_USER,
		CONFIG_MYSQL_HOOYA_PASSWORD,
		CONFIG_MYSQL_HOOYA_DATABASE);
	mysqli_set_charset($dbh, 'utf8');
	$query = "SELECT Space FROM Tags GROUP BY Space";
	$res = mysqli_query($dbh, $query);
	while ($row = mysqli_fetch_assoc($res)) {
		$ret[] = $row['Space'];
	}
	return $ret;
}
function db_info($req)
{
	$dbh = mysqli_connect(CONFIG_MYSQL_HOOYA_HOST,
		CONFIG_MYSQL_HOOYA_USER,
		CONFIG_MYSQL_HOOYA_PASSWORD,
		CONFIG_MYSQL_HOOYA_DATABASE);
	mysqli_set_charset($dbh, 'utf8');
	if ($req['Files']) {
		$query = "SELECT COUNT(*) FROM Files";
		$res = mysqli_query($dbh, $query);
		$files = mysqli_fetch_assoc($res)['COUNT(*)'];
		$ret['Files'] = $files;
	}
	if ($req['Version']) {
		$version = mysqli_get_server_info($dbh);
		$ret['Version'] = $version;
	}
	if ($req['Size']) {
		$query = "SELECT SUM(Size) FROM Files";
		$res = mysqli_query($dbh, $query);
		$size = mysqli_fetch_assoc($res)['SUM(Size)'];
		$ret['Size'] = $size;
	}
	mysqli_close($dbh);
	return $ret;
}
?>