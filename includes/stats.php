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
	$query = "SELECT Alias, Space FROM Alias";
	$res = mysqli_query($dbh, $query);
	while ($row = mysqli_fetch_assoc($res)) {
		$ret[$row['Alias']] = $row['Space'];
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
