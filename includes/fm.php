<?php
function fm_listing($class) {
	$dbh = mysqli_connect(CONFIG_MYSQL_HOOYA_HOST,
		CONFIG_MYSQL_HOOYA_USER,
		CONFIG_MYSQL_HOOYA_PASSWORD,
		CONFIG_MYSQL_HOOYA_DATABASE);
	mysqli_set_charset($dbh, 'utf8');
	// Escape all potential user input
	$class = mysqli_real_escape_string($dbh, $class);
	$group = DB_MEDIA_CLASSES[$class]['Group'];
	$sort = DB_MEDIA_CLASSES[$class]['Sort'];
	$ext = DB_FILE_EXTENDED_PROPERTIES[$class];
	$format = DB_FILE_EXTENDED_PROPERTIES[$class][$sort]['Format'];

	$query = "SELECT `Member`, `$sort`, `Files`.Id FROM"
	. " `Files`, `TagMap`, `Tags`, `$class` WHERE"
	. " Files.Id=FileId AND TagId=Tags.Id AND Files.Class='$class'"
	. " AND `$class`.Id=Files.Id AND Space='$group'"
	. " ORDER BY `Member` ASC, $sort+0 ASC";
	$res = mysqli_query($dbh, $query);
	while ($row = mysqli_fetch_assoc($res)) {
		if ($format) {
			$text =  str_replace('?', $row[$sort], $format);
		}
		$ret[$row['Member']][] = [
			'Text' => $text,
			'Key' => $row['Id'],
		];
	}
	return $ret;
}
?>
