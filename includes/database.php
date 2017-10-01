<?php
define('DB_MEDIA_CLASSES', [
	'single_image' => [
	],
	'video' => [
	],
	'anime' => [
		'Default' => 'list',
		'FMGroup' => 'series',
	],
	'movie' => [
		'Default' => 'list',
		'FMGroup' => 'series',
	]
]);
define('DB_FILE_PROPERTIES', ['Size', 'Path', 'Mimetype']);
define('DB_FILE_EXTENDED_PROPERTIES',
[
	'single_image' => [
		'Width' => ['Type' => 'Number', 'Immutable' => 1],
		'Height' => ['Type' => 'Number', 'Immutable' => 1],
	],
	'video' => [
	],
	'anime' => [
		'Season' => [
			'Type' => 'Number',
			'Format' => 'Season ? ',
			'Sort' => 1,
		],
		'Episode' => [
			'Format' => 'Episode ?',
			'Sort' => 1,
		],
	],
	'movie' => [
		'Year' => ['Type' => 'Number', 'Format' => '(?)'],
	],
]);
function db_get_tags($keys)
{
	$dbh = mysqli_connect(CONFIG_MYSQL_HOOYA_HOST,
		CONFIG_MYSQL_HOOYA_USER,
		CONFIG_MYSQL_HOOYA_PASSWORD,
		CONFIG_MYSQL_HOOYA_DATABASE);
	mysqli_set_charset($dbh, 'utf8');
	$query = "SELECT FileId, Tags.Space, Tags.Member, Author, Added FROM"
	. " Files, TagMap, Tags WHERE TagMap.FileId = Files.Id"
	. " AND Tags.Id = TagMap.TagId AND (";
	for ($i = 0; $i < count($keys); $i++) {
		$key = $keys[$i];
		// Escape all potential user input
		$key = mysqli_real_escape_string($dbh, $key);
		// Pull from `Tags` using our $key
		$query.= " Files.Id = '$key' ";
		if ($i+1 < count($keys)) $query .= " OR ";
	}
	$query .= ")";
	$res = mysqli_query($dbh, $query);
	// We allow more than one of the same tag space
	while ($row = mysqli_fetch_assoc($res)) {
		$ret[$row['FileId']][] = [
			'Space' => $row['Space'],
			'Member' => $row['Member'],
			'Author' => $row['Author'],
			'Added' => $row['Added']
		];
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
	foreach ($tags as $tag) {
		// First, take the opportunity to insert new tags into `Tags`
		$space = mysqli_real_escape_string($dbh, $tag["Space"]);
		$member = mysqli_real_escape_string($dbh, $tag["Member"]);
		$query = "INSERT INTO Tags (`Space`, `Member`) "
			. "VALUES ('$space', '$member')";
		mysqli_query($dbh, $query);

		// Next, map each file->tag pair to a row in TagMap
		$_SESSION['userid'] ? $author = $_SESSION['userid'] : $author = 'NULL';
		$query = "INSERT INTO TagMap (`FileId`, `Author`, `TagId`) SELECT"
			. " Files.Id AS FileId, $author AS Author"
			. ", Tags.Id AS TagId FROM Files, Tags"
			. " WHERE Files.Id = '$key' AND Tags.Space ="
			. " '$space' AND Tags.Member = '$member'";
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
	mysqli_close($dbh);
}
function db_getrandom($n)
{
	$dbh = mysqli_connect(CONFIG_MYSQL_HOOYA_HOST,
		CONFIG_MYSQL_HOOYA_USER,
		CONFIG_MYSQL_HOOYA_PASSWORD,
		CONFIG_MYSQL_HOOYA_DATABASE);
	mysqli_set_charset($dbh, 'utf8');
	$query = "SELECT Id, Class, Indexed FROM Files WHERE NOT Id in ("
	. "SELECT FileId AS Id FROM TagMap) ORDER BY RAND()"
	. " LIMIT $n";
	$res = mysqli_query($dbh, $query);
	while ($row = mysqli_fetch_assoc($res))
		$ret[] = [
			'Key' => $row['Id'],
			'Class' => $row['Class'],
			'Indexed' => $row['Indexed'],
		];
	mysqli_close($dbh);
	return $ret;
}
function db_getrecent($n)
{
	$dbh = mysqli_connect(CONFIG_MYSQL_HOOYA_HOST,
		CONFIG_MYSQL_HOOYA_USER,
		CONFIG_MYSQL_HOOYA_PASSWORD,
		CONFIG_MYSQL_HOOYA_DATABASE);
	mysqli_set_charset($dbh, 'utf8');
	$query = "SELECT Id, Class, Indexed FROM Files, TagMap WHERE FileId=Id"
	. " GROUP BY Id ORDER BY Added DESC LIMIT $n";
	$res = mysqli_query($dbh, $query);
	while ($row = mysqli_fetch_assoc($res))
		$ret[] = [
			'Key' => $row['Id'],
			'Class' => $row['Class'],
			'Indexed' => $row['Indexed'],
		];
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
function db_get_all_members()
{
	$dbh = mysqli_connect(CONFIG_MYSQL_HOOYA_HOST,
		CONFIG_MYSQL_HOOYA_USER,
		CONFIG_MYSQL_HOOYA_PASSWORD,
		CONFIG_MYSQL_HOOYA_DATABASE);
	$query = "SELECT Member FROM Tags GROUP BY Member";
        $res = mysqli_query($dbh, $query);
	while ($row = mysqli_fetch_assoc($res)) {
		$ret[$row['Member']] = 1;
	}
	return $ret;
}
function db_is_member($member)
{
	$dbh = mysqli_connect(CONFIG_MYSQL_HOOYA_HOST,
		CONFIG_MYSQL_HOOYA_USER,
		CONFIG_MYSQL_HOOYA_PASSWORD,
		CONFIG_MYSQL_HOOYA_DATABASE);
	mysqli_set_charset($dbh, 'utf8');
	$query = "SELECT 1 FROM Tags WHERE Member='$member'";
	$res = mysqli_query($dbh, $query);
	return mysqli_num_rows($res);
}
function db_is_space($namespace)
{
	$dbh = mysqli_connect(CONFIG_MYSQL_HOOYA_HOST,
		CONFIG_MYSQL_HOOYA_USER,
		CONFIG_MYSQL_HOOYA_PASSWORD,
		CONFIG_MYSQL_HOOYA_DATABASE);
	mysqli_set_charset($dbh, 'utf8');
	$query = "SELECT 1 FROM Tags WHERE Space='$namespace'";
	$res = mysqli_query($dbh, $query);
	return mysqli_num_rows($res);
}
function db_getfileinfo($key)
{
	$dbh = mysqli_connect(CONFIG_MYSQL_HOOYA_HOST,
		CONFIG_MYSQL_HOOYA_USER,
		CONFIG_MYSQL_HOOYA_PASSWORD,
		CONFIG_MYSQL_HOOYA_DATABASE);
	mysqli_set_charset($dbh, 'utf8');
	// Escape all potential user input
	$key = mysqli_real_escape_string($dbh, $key);
	// Construct the SQL query
	$query = "SELECT Path, Class, Size, Mimetype"
	. " FROM Files WHERE Id = '$key'";
	$res = mysqli_query($dbh, $query);
	$row = mysqli_fetch_assoc($res);
	mysqli_close($dbh);
	return $row;
}
function db_setclass($key, $class)
{
	$dbh = mysqli_connect(CONFIG_MYSQL_HOOYA_HOST,
		CONFIG_MYSQL_HOOYA_USER,
		CONFIG_MYSQL_HOOYA_PASSWORD,
		CONFIG_MYSQL_HOOYA_DATABASE);
	mysqli_set_charset($dbh, 'utf8');
	// Escape all potential user input
	$class = mysqli_real_escape_string($dbh, $class);
	$query = 'UPDATE Files SET Class = "' . $class . '"'
	. ' WHERE Id = "' . $key . '"';
	return mysqli_query($dbh, $query);
}
function db_setproperties($key, $props)
{
	$class = db_getfileinfo($key)['Class'];
	$dbh = mysqli_connect(CONFIG_MYSQL_HOOYA_HOST,
		CONFIG_MYSQL_HOOYA_USER,
		CONFIG_MYSQL_HOOYA_PASSWORD,
		CONFIG_MYSQL_HOOYA_DATABASE);
	mysqli_set_charset($dbh, 'utf8');
	// TODO filter immutable properties from array
	// TODO quote non-integers
	foreach ($props as $p => $v) {
		if (DB_FILE_EXTENDED_PROPERTIES[$class][$p]['Type'] != 'Number')
			$props[$p] = "'$v'";
		if (DB_FILE_EXTENDED_PROPERTIES[$class][$p]['Immutable'])
			unset($props[$p]);
	}
	$query = "INSERT INTO $class SET `Id`='$key', "
	. aa_join($props, ', ', '=')
	. " ON DUPLICATE KEY UPDATE "
	. aa_join($props, ', ', '=');
	return mysqli_query($dbh, $query);
}
function db_getproperties($key)
{
	$class = db_getfileinfo($key)['Class'];
	$dbh = mysqli_connect(CONFIG_MYSQL_HOOYA_HOST,
		CONFIG_MYSQL_HOOYA_USER,
		CONFIG_MYSQL_HOOYA_PASSWORD,
		CONFIG_MYSQL_HOOYA_DATABASE);
	mysqli_set_charset($dbh, 'utf8');
	$properties = array_keys(db_get_class_properties($class));

	$query = "SELECT "
	. join(',', $properties)
	. " FROM $class WHERE Id = '$key'";
	$res = mysqli_query($dbh, $query);
	$row = mysqli_fetch_assoc($res);
	mysqli_close($dbh);
	return $row;
}

function aa_join($aa, $seperator, $inbetween)
{
	unset($i);
	foreach($aa as $k => $v) {
		if (!($i != count($aa) && !$i++)) $final .= $seperator;
		$final .= $k . $inbetween . $v;
	}
	return $final;
}
// Might do a SQL query here if we want to allow user-defined properties
function db_get_class_properties($class)
{
	return (DB_FILE_EXTENDED_PROPERTIES[$class]);
}
function db_get_alias($alias)
{
	$dbh = mysqli_connect(CONFIG_MYSQL_HOOYA_HOST,
		CONFIG_MYSQL_HOOYA_USER,
		CONFIG_MYSQL_HOOYA_PASSWORD,
		CONFIG_MYSQL_HOOYA_DATABASE);
	mysqli_set_charset($dbh, 'utf8');
	$query = "SELECT `Space` FROM Alias WHERE Alias='$alias'";
	$res = mysqli_query($dbh, $query);
	return mysqli_fetch_assoc($res)['Space'];
}
function db_update_highscore($userid, $diff)
{
	$conn = new mysqli(CONFIG_DB_SERVER, CONFIG_DB_USERNAME
	,CONFIG_DB_PASSWORD, CONFIG_DB_DATABASE);
	// Keep a high-score count for every logged-in user!
	$cmd = 'UPDATE `users` SET `tags_added` = `tags_added` + '
	. $diff . ' WHERE `id`=' . $userid;
	return $conn->query($cmd);
}
?>
