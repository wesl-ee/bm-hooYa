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
//	if($error = mysqli_error($dbh)) return False;

	// Create the respective media type entry
	$query = "INSERT INTO `$class`"
	. " (`Id`) VALUES ('$id')";
	mysqli_query($dbh, $query);

	// Special, class-specific properties
	$query = "UPDATE `$class` SET `Id`='$id'";
	foreach (DB_FILE_EXTENDED_PROPERTIES[$class] as $property => $value)
	if ($value['Extractor']) {
		// Call this function to extract the property value
		$extractor = $value['Extractor'];
		$prop_value = call_user_func($extractor, $file);
		$query .= ",`$property`=$prop_value";
	}
	$query .= " WHERE `Id`='$id'";
	if ($extractor) mysqli_query($dbh, $query);
	

	// Extra work for files with a width & height
	$file = escapeshellarg($file);
	if (DB_FILE_EXTENDED_PROPERTIES[$class]['Height']
	&& DB_FILE_EXTENDED_PROPERTIES[$class]['Width']) {

		exec("identify $file", $output);
		preg_match('/(\d+)x(\d+)/', $output[0], $m);
		$width = $m[1]; $height = $m[2];
		$query = "UPDATE `$class` SET"
		. " `Width`=$width, `Height`=$height WHERE `Id`='$id'";
		mysqli_query($dbh, $query);
		unset($output, $m);
	}
	if (DB_FILE_EXTENDED_PROPERTIES[$class]['Dominant Color']) {
		exec("convert $file +dither -colors 1 -define"
		. " histogram:unique-colors=true -format '%c'"
		. " histogram:info:", $output);
		preg_match('/ (#[A-Fa-f0-9]{6}) /', $output[0], $m);
		unset($output);
		$dom_color = $m[1];
		if ($dom_color) {
			$query = "UPDATE `$class` SET"
			. " `Dominant Color`='$dom_color' WHERE `Id`='$id'";
			mysqli_query($dbh, $query);
		}
		unset($output, $m);
	}
	syslog(LOG_INFO|LOG_DAEMON, "User $id uploaded a file"
	. " from " . $_SERVER['HTTP_X_REAL_IP']);
	return True;
}
function extract_colors($file)
{
	$file = escapeshellarg($file);
	exec("convert $file +dither -colors 6 -define histogram:unique-colors=true -format '%c' histogram:info:", $out);
	foreach($out as $line) {
		// For sRGBA, ignore the alpha channel
		if (preg_match('/#([A-Fa-f0-9]{6})[A-Fa-f0-9]{2}/', $line, $m)) {
			$colors[] = "#" . $m[1];
		}
		// For RGB, capture the whole string
		elseif (preg_match('/(#[A-Fa-f0-9]{6})/', $line, $m)) {
			$colors[] = $m[1];
		}
		# No more than 6 colors for each picture
		if (@colors >= 6) { last; }
	}
	return "'" . json_encode($colors) . "'";
}
?>
