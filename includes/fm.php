<?php
function fm_listing($class) {
	$dbh = mysqli_connect(CONFIG_MYSQL_HOOYA_HOST,
		CONFIG_MYSQL_HOOYA_USER,
		CONFIG_MYSQL_HOOYA_PASSWORD,
		CONFIG_MYSQL_HOOYA_DATABASE);
	mysqli_set_charset($dbh, 'utf8');
	// Escape all potential user input
	$class = mysqli_real_escape_string($dbh, $class);
	$group = DB_MEDIA_CLASSES[$class]['FMGroup'];
	$exts = DB_FILE_EXTENDED_PROPERTIES[$class];

	$query = "SELECT `Member`,";
	foreach ($exts as $ext => $value) {
		if ($value['Sort'])
			$query .= " `$ext`,";
	}
	$query .= " `Files`.Id FROM"
	. " `Files`, `TagMap`, `Tags`, `$class` WHERE"
	. " Files.Id=FileId AND TagId=Tags.Id AND Files.Class='$class'"
	. " AND `$class`.Id=Files.Id AND Space='$group'"
	. " ORDER BY `Member` ASC";
	foreach ($exts as $ext => $value) {
		if ($value['Sort'])
			$query .= ", `$ext`+0 ASC";
	}
	$res = mysqli_query($dbh, $query);
	while ($row = mysqli_fetch_assoc($res)) {
		unset($text);
		foreach($exts as $ext => $value) {
			$format = $value['Format'];
			if ($format)
				$text .=  str_replace('?', $row[$ext], $format);
		}
		$ret[$row['Member']][] = [
			'Text' => $text,
			'Key' => $row['Id'],
		];
	}
	return $ret;
}
?>
