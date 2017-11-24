<?php
function stats_alltags()
{
	$dbh = mysqli_connect(CONFIG_MYSQL_HOOYA_HOST,
		CONFIG_MYSQL_HOOYA_USER,
		CONFIG_MYSQL_HOOYA_PASSWORD,
		CONFIG_MYSQL_HOOYA_DATABASE);
	mysqli_set_charset($dbh, 'utf8');
	$query = "SELECT Space, Member FROM Tags";
	$res = mysqli_query($dbh, $query);
	
	while ($row = mysqli_fetch_assoc($res))
		$ret[] = [$row['Space'] => $row['Member']];
	return $ret;
}
function stats_tag_freq()
{
	$dbh = mysqli_connect(CONFIG_MYSQL_HOOYA_HOST,
		CONFIG_MYSQL_HOOYA_USER,
		CONFIG_MYSQL_HOOYA_PASSWORD,
		CONFIG_MYSQL_HOOYA_DATABASE);
	mysqli_set_charset($dbh, 'utf8');
	$query = "SELECT CONCAT(Space,':',Member) AS Tag, COUNT(*) As Freq FROM TagMap, Tags"
	. " WHERE TagId=Id GROUP BY CONCAT(Space,Member) ORDER BY COUNT(*) DESC";
	$res = mysqli_query($dbh, $query);
	while ($row = mysqli_fetch_assoc($res)) {
		$ret[$row['Tag']] = $row['Freq'];
	}
	return $ret;
}
function stats_tag_activity($tag)
{
	$dbh = mysqli_connect(CONFIG_MYSQL_HOOYA_HOST,
		CONFIG_MYSQL_HOOYA_USER,
		CONFIG_MYSQL_HOOYA_PASSWORD,
		CONFIG_MYSQL_HOOYA_DATABASE);
	mysqli_set_charset($dbh, 'utf8');
	$query = "SELECT Added FROM TagMap, Tags"
	. " WHERE TagId=Id AND CONCAT(Space,':',Member)='$tag'"
	. " AND Added >= DATE_SUB(NOW(), INTERVAL 1 YEAR) ORDER BY Added ASC";
	$res = mysqli_query($dbh, $query);
	while ($row = mysqli_fetch_assoc($res)) {
		// Extract the month from a YYYY-MM-DD format
		$month = (int)explode('-', $row['Added'])[1];
		$ret[$month]++;
	}
	return $ret;
}
function stats_tag_class_freq($tag)
{
	$dbh = mysqli_connect(CONFIG_MYSQL_HOOYA_HOST,
		CONFIG_MYSQL_HOOYA_USER,
		CONFIG_MYSQL_HOOYA_PASSWORD,
		CONFIG_MYSQL_HOOYA_DATABASE);
	mysqli_set_charset($dbh, 'utf8');
	$query = "SELECT Class, COUNT(*) AS Freq FROM Files, TagMap, Tags"
	. " WHERE Files.Id=FileId AND TagId=Tags.Id"
	. " AND CONCAT(Space,':',Member)='$tag' GROUP BY Class";
	$res = mysqli_query($dbh, $query);
	while ($row = mysqli_fetch_assoc($res)) {
		$ret[$row['Class']] = $row['Freq'];
	}
	return $ret;
}
function stats_allaliases()
{
	$dbh = mysqli_connect(CONFIG_MYSQL_HOOYA_HOST,
		CONFIG_MYSQL_HOOYA_USER,
		CONFIG_MYSQL_HOOYA_PASSWORD,
		CONFIG_MYSQL_HOOYA_DATABASE);
	mysqli_set_charset($dbh, 'utf8');
	$query = "SELECT Alias, Space FROM Alias ORDER BY Space ASC";
	$res = mysqli_query($dbh, $query);
	while ($row = mysqli_fetch_assoc($res)) {
		$ret[$row['Alias']] = $row['Space'];
	}
	return $ret;
}
function stats_getassoc($tag)
{
	$dbh = mysqli_connect(CONFIG_MYSQL_HOOYA_HOST,
		CONFIG_MYSQL_HOOYA_USER,
		CONFIG_MYSQL_HOOYA_PASSWORD,
		CONFIG_MYSQL_HOOYA_DATABASE);
	mysqli_set_charset($dbh, 'utf8');
	$tag = mysqli_real_escape_string($dbh, $tag);
	$query = "SELECT CONCAT(Space, ':', Member) AS Tag, COUNT(*) AS Freq"
	. " FROM Tags, TagMap WHERE TagId=Tags.Id AND FileId IN"
	. " (SELECT FileId FROM TagMap, Tags WHERE TagId=Tags.Id"
	. " AND CONCAT(Space,':',Member)='$tag')"
	. " AND CONCAT(Space,':',Member)!='$tag' GROUP By Tag"
	. " ORDER BY Freq DESC LIMIT 5";
	$res = mysqli_query($dbh, $query);
	while ($row = mysqli_fetch_assoc($res)) {
		$ret[$row['Tag']] = $row['Freq'];
	}
	return $ret;
}
function stats_getcolors($tag)
{
	$dbh = mysqli_connect(CONFIG_MYSQL_HOOYA_HOST,
		CONFIG_MYSQL_HOOYA_USER,
		CONFIG_MYSQL_HOOYA_PASSWORD,
		CONFIG_MYSQL_HOOYA_DATABASE);
	mysqli_set_charset($dbh, 'utf8');
	$tag = mysqli_real_escape_string($dbh, $tag);
	$query = "SELECT Colors FROM Tags, TagMap";
	foreach (DB_FILE_EXTENDED_PROPERTIES as $class => $a) {
		if ($a['Colors']) $query .= ", $class";
	}
	$query .= " WHERE TagId=Tags.Id";
	foreach (DB_FILE_EXTENDED_PROPERTIES as $class => $a) {
		if ($a['Colors']) $query .= " AND `$class`.id=FileId";
	}
	$query .= " AND CONCAT(Space,':',Member)='$tag'";
	$res = mysqli_query($dbh, $query);
	while($colors = mysqli_fetch_assoc($res)['Colors']) {
		$colors = json_decode($colors);
		// Deconstruct the hex-coded color, ignoring the #
		foreach ($colors as $color) {
			// Restrict the colorspace to 128 colors for
			// a more simple analysis
			$colorspace = 128;
			$colorestrict = (256 / pow($colorspace, 1/3));
			// Filter out whites, grays and blacks
			$boringfilter = 70 * 70;
			$red = hexdec(substr($color, 1, 2));
			$green = hexdec(substr($color, 3, 2));
			$blue = hexdec(substr($color, 5, 2));

			// Calculate the distance to a gray tone
			if (abs(pow($red, 2) - pow($green, 2)) < $boringfilter
			&& abs(pow($red, 2) - pow($blue, 2)) < $boringfilter) continue;

			$red = dechex(round($red / $colorestrict) * $colorestrict);
			$green = dechex(round($green / $colorestrict) * $colorestrict);
			$blue = dechex(round($blue / $colorestrict) * $colorestrict);
			$modcolor = '#'
			. str_pad($red, 2, '0', STR_PAD_LEFT)
			. str_pad($green, 2, '0', STR_PAD_LEFT)
			. str_pad($blue, 2, '0', STR_PAD_LEFT);

			$palette[$modcolor]++;
		}
	}
	arsort($palette);
	return array_slice($palette, 0, 10, true);
}
function stats_untagged_count()
{
	$dbh = mysqli_connect(CONFIG_MYSQL_HOOYA_HOST,
		CONFIG_MYSQL_HOOYA_USER,
		CONFIG_MYSQL_HOOYA_PASSWORD,
		CONFIG_MYSQL_HOOYA_DATABASE);
	mysqli_set_charset($dbh, 'utf8');
	// The number of untagged files in every class
	$query = "SELECT COUNT(*) AS Freq, Class FROM Files WHERE NOT Id in ("
	. "SELECT FileId AS Id FROM TagMap) GROUP BY Class";
	$res = mysqli_query($dbh, $query);
	while ($row = mysqli_fetch_assoc($res))
		$untagged[$row['Class']] = $row['Freq'];
	arsort($untagged);
	mysqli_close($dbh);
	return $untagged;
}
function stats_total_count()
{
	$dbh = mysqli_connect(CONFIG_MYSQL_HOOYA_HOST,
		CONFIG_MYSQL_HOOYA_USER,
		CONFIG_MYSQL_HOOYA_PASSWORD,
		CONFIG_MYSQL_HOOYA_DATABASE);
	mysqli_set_charset($dbh, 'utf8');
	// The number of indexed files in every class
	$query = "SELECT COUNT(*) AS Freq, Class FROM Files GROUP BY Class";
	$res = mysqli_query($dbh, $query);
	while ($row = mysqli_fetch_assoc($res))
		$total[$row['Class']] = $row['Freq'];
	arsort($total);
	mysqli_close($dbh);
	return $total;
}
function stats_upload_activity()
{
	$dbh = mysqli_connect(CONFIG_MYSQL_HOOYA_HOST,
		CONFIG_MYSQL_HOOYA_USER,
		CONFIG_MYSQL_HOOYA_PASSWORD,
		CONFIG_MYSQL_HOOYA_DATABASE);
	mysqli_set_charset($dbh, 'utf8');
	$query = "SELECT Indexed FROM Files"
	. " WHERE Indexed >= DATE_SUB(NOW(), INTERVAL 1 YEAR)"
	. " ORDER BY Indexed ASC";
	$res = mysqli_query($dbh, $query);
	while ($row = mysqli_fetch_assoc($res)) {
		// Extract the month from a YYYY-MM-DD format
		$month = (int)explode('-', $row['Indexed'])[1];
		$ret[$month]++;
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
