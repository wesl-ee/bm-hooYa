<?php
function uploaded_today($userid)
{
	$dbh = mysqli_connect(CONFIG_MYSQL_HOOYA_HOST,
		CONFIG_MYSQL_HOOYA_USER,
		CONFIG_MYSQL_HOOYA_PASSWORD,
		CONFIG_MYSQL_HOOYA_DATABASE);
	mysqli_set_charset($dbh, 'utf8');
	$userid = mysqli_real_escape_string($dbh, $userid);
	$query = "SELECT Id FROM Files WHERE `By`=$userid AND"
	. " `Indexed` BETWEEN DATE_SUB(NOW(), INTERVAL 1 Day) AND"
	. " NOW()";
	$res = mysqli_query($dbh, $query);
	while ($row = mysqli_fetch_assoc($res))
		$ul[] = $row['Id'];
	return $ul;
}
function simple_import($file, $class, $id)
{
	$size = filesize($file);
	$mimetype = mime_content_type($file);
	$by = $_SESSION['userid'];
	$dbh = mysqli_connect(CONFIG_MYSQL_HOOYA_HOST,
		CONFIG_MYSQL_HOOYA_USER,
		CONFIG_MYSQL_HOOYA_PASSWORD,
		CONFIG_MYSQL_HOOYA_DATABASE);
	mysqli_set_charset($dbh, 'utf8');
	$safefile = mysqli_real_escape_string($dbh, $file);

	// Index the file
	$query = "INSERT INTO `Files`"
	. " (`Id`, `Path`, `Size`, `Class`, `Mimetype`, `By`) VALUES"
	. " ('$id', '$file', $size, '$class', '$mimetype', $by)";
	mysqli_query($dbh, $query);
	if($error = mysqli_error($dbh)) return False;

	// Create the respective media type entry
	$query = "INSERT INTO `$class`"
	. " (`Id`) VALUES ('$id')";
	mysqli_query($dbh, $query);

	// Extra work for files with a width & height
	if (DB_FILE_EXTENDED_PROPERTIES[$class]['Height']
	&& DB_FILE_EXTENDED_PROPERTIES[$class]['Width']) {
		$file = escapeshellarg($file);
		exec("identify $file", $output);
		preg_match('/(\d+)x(\d+)/', $output[0], $m);
		$width = $m[1]; $height = $m[2];
		$query = "UPDATE `$class` SET"
		. " `Width`=$width, `Height`=$height WHERE `Id`='$id'";
		mysqli_query($dbh, $query);
	}
	return True;
}
?>
