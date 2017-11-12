<?php
function get_usertagcount($id)
{
	$dbh = mysqli_connect(CONFIG_MYSQL_HOOYA_HOST,
		CONFIG_MYSQL_HOOYA_USER,
		CONFIG_MYSQL_HOOYA_PASSWORD,
		CONFIG_MYSQL_HOOYA_DATABASE);
	mysqli_set_charset($dbh, 'utf8');
	$query = "SELECT COUNT(*) AS TagCount FROM TagMap"
	. " WHERE Author=$id GROUP BY Author";
	$res = mysqli_query($dbh, $query);
	return mysqli_fetch_assoc($res)['TagCount'];
}
function get_userfavorites($id, $n)
{
	$dbh = mysqli_connect(CONFIG_MYSQL_HOOYA_HOST,
		CONFIG_MYSQL_HOOYA_USER,
		CONFIG_MYSQL_HOOYA_PASSWORD,
		CONFIG_MYSQL_HOOYA_DATABASE);
	mysqli_set_charset($dbh, 'utf8');
	$query = "SELECT CONCAT(Space, ':', Member) AS Tag,"
	. " COUNT(*) AS Freq FROM TagMap, Tags WHERE TagId=Id"
	. " AND Author=$id GROUP BY TagId ORDER BY Freq DESC LIMIT $n";
	$res = mysqli_query($dbh, $query);
	while ($row = mysqli_fetch_assoc($res)) {
		$ret[$row['Tag']] = $row['Freq'];
	}
	return $ret;
}
function get_recentlytagged($id, $n)
{
	$dbh = mysqli_connect(CONFIG_MYSQL_HOOYA_HOST,
		CONFIG_MYSQL_HOOYA_USER,
		CONFIG_MYSQL_HOOYA_PASSWORD,
		CONFIG_MYSQL_HOOYA_DATABASE);
	mysqli_set_charset($dbh, 'utf8');
	$query = "SELECT FileId,"
	. " Added FROM TagMap WHERE "
	. " Author=$id ORDER BY Added DESC LIMIT $n";
	$res = mysqli_query($dbh, $query);
	while ($row = mysqli_fetch_assoc($res)) {
		$ret[$row['Id']] = $row['Added'];
	}
	return $ret;
}
?>
