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
	
	syslog(LOG_INFO|LOG_DAEMON, "User $id uploaded a file"
	. " from " . $_SERVER['HTTP_X_REAL_IP']);
	return True;
}

// PROPERTY EXTRACTION FUNCTIONS
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
function extract_imgheight($file)
{
	$file = escapeshellarg($file);
	exec("convert $file -print '%h\n' /dev/null", $out);
	$line = $out[0];
	if (preg_match('/(\d+)/', $line, $m)) {
		$height = $m[1];
		return $height;
	}
}
function extract_imgwidth($file)
{
	$file = escapeshellarg($file);
	exec("convert $file -print '%w\n' /dev/null", $out);
	$line = $out[0];
	if (preg_match('/(\d+)/', $line, $m)) {
		$width = $m[1];
		return $width;
	}
}
?>
