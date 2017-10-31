<?php
/*
* Quick tag lookup of a bunch of files
* IN: An array of MD5 sums to look up
* OUT: An array keyed by your INput MD5 sums which looks like
* $MD5 => [
*	[
* 	'Space' => $Namespace1,
* 	'Member' => $Member1,
* 	'Author' => $Author1,
*	'Added' => $Date1
*	],
*	[
* 	'Space' => $Namespace2,
* 	'Member' => $Member2,
* 	'Author' => $Author2,
*	'Added' => $Date2
*	],
*	... and so on
* ];
*
*/
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
/*
* Quick tag lookup for a bunch of files; output organized by namespace
* IN: An array of MD5 sums to look up
* OUT: An array keyed by your INput MD5 sums which looks like
* [
* 	$MD5_1 => [ $Namespace1 => [
*		'Member' => $Member1,
* 		'Author' => $Author1,
*		'Added' => $Date1
*		], $Namespace2 => [
* 		'Member' => $Member2,
* 		'Author' => $Author2,
*		'Added' => $Date2
*		],
* 		... and so on
* 	],
* 	$MD5_2 => [ $Namespace3 => [
* 		'Member' => $Member3,
* 		'Author' => $Author3,
*		'Added' => $Date3
*		], $Namespace4 => [
* 		'Member' => $Member4,
* 		'Author' => $Author4,
*		'Added' => $Date4
*		],
* 		... and so on
*	], ... and so on
* ];
*/
function db_get_tags_by_space($keys)
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
		$ret[$row['FileId']][$row['Space']][] = [
			'Member' => $row['Member'],
			'Author' => $row['Author'],
			'Added' => $row['Added']
		];
	}
	mysqli_close($dbh);
	return $ret;
}
/*
* Replace a file's tags w/ $tags
* IN: A file MD5 $key and an associative array like
* 	[
* 		$namespace1 => $tag1,
* 		$namespace2 => $tag2,
* 		$namespace3 => $tag3,
* 		... and so on
* 	];
*/
function db_set_tags($key, $tags)
{
	$dbh = mysqli_connect(CONFIG_MYSQL_HOOYA_HOST,
		CONFIG_MYSQL_HOOYA_USER,
		CONFIG_MYSQL_HOOYA_PASSWORD,
		CONFIG_MYSQL_HOOYA_DATABASE);
	mysqli_autocommit($dbh, false);

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
	mysqli_commit($dbh);
	mysqli_close($dbh);
}
/*
* Get a random slice of untagged files
* IN: $n number of files to return
* OUT: An array of MD5 hashes which have no associated tags
*/
function db_getuntaggedrandom($n)
{
	$dbh = mysqli_connect(CONFIG_MYSQL_HOOYA_HOST,
		CONFIG_MYSQL_HOOYA_USER,
		CONFIG_MYSQL_HOOYA_PASSWORD,
		CONFIG_MYSQL_HOOYA_DATABASE);
	mysqli_set_charset($dbh, 'utf8');
	$query = "SELECT Id, Class, Indexed FROM Files WHERE NOT Id in ("
	. "SELECT FileId AS Id FROM TagMap) ";
	if (!logged_in()) foreach (DB_MEDIA_CLASSES as $class => $value) {
		if ($value['Restricted']) $query .= " AND `Class`!='$class'";
	}
	$query .= " ORDER BY RAND()"
	. " LIMIT $n";
	$res = mysqli_query($dbh, $query);
	while ($row = mysqli_fetch_assoc($res))
		$ret[$row['Id']] = [
			'Class' => $row['Class'],
			'Indexed' => $row['Indexed'],
		];
	mysqli_close($dbh);
	return $ret;
}
/*
* Get a random slice of all indexed files
* IN: $n number of files to return
* OUT: An array of MD5 hashes
*/
function db_getrandom($n)
{
	$dbh = mysqli_connect(CONFIG_MYSQL_HOOYA_HOST,
		CONFIG_MYSQL_HOOYA_USER,
		CONFIG_MYSQL_HOOYA_PASSWORD,
		CONFIG_MYSQL_HOOYA_DATABASE);
	mysqli_set_charset($dbh, 'utf8');
	$query = "SELECT Id, Class, Indexed FROM Files ";
	if (!logged_in()) foreach (DB_MEDIA_CLASSES as $class => $value) {
		if ($value['Restricted']) $query .= " AND `Class`!='$class'";
	}
	$query .= " ORDER BY RAND()"
	. " LIMIT $n";
	$res = mysqli_query($dbh, $query);
	while ($row = mysqli_fetch_assoc($res))
		$ret[$row['Id']] = [
			'Class' => $row['Class'],
			'Indexed' => $row['Indexed'],
		];
	mysqli_close($dbh);
	return $ret;
}
/*
* Get a page of recently tagged files
* IN: The $page to retrieve
* OUT: An array indexed by MD5 hashes which looks like
* [
* 	$MD5_1 => [
* 		'Class' => $Class1,
* 		'Indexed' => $Date1
* 	],
* 	$MD5_2 => [
* 		'Class' => $Class2,
* 		'Indexed' => $Date2
* 	],
* 	... and so on
* ];
*/
function db_getrecent($page)
{
	$dbh = mysqli_connect(CONFIG_MYSQL_HOOYA_HOST,
		CONFIG_MYSQL_HOOYA_USER,
		CONFIG_MYSQL_HOOYA_PASSWORD,
		CONFIG_MYSQL_HOOYA_DATABASE);
	mysqli_set_charset($dbh, 'utf8');
	$query = "SELECT Id, Class, Indexed FROM Files, TagMap WHERE FileId=Id";
	if (!logged_in()) foreach (DB_MEDIA_CLASSES as $class => $value) {
		if ($value['Restricted']) $query .= " AND `Class`!='$class'";
	}
	$query .= " GROUP BY Id ORDER BY MAX(Added) DESC";

	$res = mysqli_query($dbh, $query);
	$ret['Count'] = mysqli_num_rows($res);
	$query .= " LIMIT " . CONFIG_THUMBS_PER_PAGE
	. " OFFSET " . (CONFIG_THUMBS_PER_PAGE * ($page - 1));
	$res = mysqli_query($dbh, $query);
	while ($row = mysqli_fetch_assoc($res))
		$ret[$row['Id']] = [
			'Class' => $row['Class'],
			'Indexed' => $row['Indexed'],
		];
	mysqli_close($dbh);
	return $ret;
}
/*
* Get the frequency of occurence of all members within a given namespace
* IN: The namespace to analyze
* OUT: An array which looks like
* [
* 	$Member1 => $Frequency_of_Member1,
* 	$Member2 => $Frequency_of_Member2,
* 	... and so on
* ];
*/
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
/*
* Simply returns a list of all members (for search suggestions)
* OUT: An array like
* [
* 	$Member1 => 1,
* 	$Member2 => 1,
* 	$Member3 => 1,
* 	... and so on for every member in the database
* ]
*/
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
/*
* Checks if $member is a member of the tag database
* IN: The $member (e.g. 'yuyushiki') to check
* OUT: True or False
*/
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
/*
* Checks if $namespace is a namespace in the tag database
* IN: The $namespace (e.g. 'series') to check
* OUT: True or False
*/
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
/*
* Returns information about a certain file
* IN: The file $key (MD5 Hash)
* OUT: An array like:
* [
* 	'Path' => $filepath,
* 	'Class' => $mediaclass,
* 	'Size' => $filesize,
* 	'Mimetype' => $mimetype
* ];
*/
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
/*
* Sets the media class of $key to $class
* IN: The file $key to alter and the $class to change to
* OUT: False on error, True otherwise
*/
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
/*
* Sets the properties of $key
* IN: The file $key to manipulate and an array $props like
* [
* 	$property1 => $value1,
* 	$property2 => $value2,
* 	... and so on
* ];
* OUT: False on error, True otherwise
*/
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
/*
* Get the properties of a file $key
* IN: File $key (MD5)
* OUT: An array like
* [
* 	$property1 => $value1,
* 	$property2 => $value2,
* 	... and so on
* ];
*/
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
/*
* I wrote this one time when I was really sleepy and I forgot what it does
* but it's useful for setting file properties somehow
*/
function aa_join($aa, $seperator, $inbetween)
{
	unset($i);
	foreach($aa as $k => $v) {
		if (!($i != count($aa) && !$i++)) $final .= $seperator;
		$final .= $k . $inbetween . $v;
	}
	return $final;
}
function db_get_class_properties($class)
{
	return (DB_FILE_EXTENDED_PROPERTIES[$class]);
}
/*
* Resolves a tag alias
* IN: The $alias to resolve
* OUT: W/E $alias is meant to resolve as (from the `Alias` table)
*/
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
/*
* Update the number of tags a user has added / destroyed
* IN: User ID and the number to add / subtract from the user's score
* OUT: False on error, True otherwise
*/
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
