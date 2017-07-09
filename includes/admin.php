<?php
/* Probably merge this and database.php files later */
function hooya_mergedir($path, $method)
{
}
function hooya_importdir($path)
{
	$files = getDirFiles($path);
	$dbh = mysqli_connect(CONFIG_MYSQL_HOOYA_HOST,
		CONFIG_MYSQL_HOOYA_USER,
		CONFIG_MYSQL_HOOYA_PASSWORD,
		CONFIG_MYSQL_HOOYA_DATABASE);
	foreach ($files as $file) {
		$id = hash_file('md5', $file);
		$size = filesize($file);
		$mimetype = mime_content_type($file);
		$ftype = explode('/', $mimetype)[0];
		switch ($ftype) {
		case 'image':
			$class = 'single_image';
			break;
		case 'video':
			$class = 'video';
			break;
		case 'audio':
			$class = 'audio';
			break;
		default:
			$class = 'single_image';
		}
		$query = "INSERT INTO `Files`"
		. " (`Id`, `Path`, `Size`, `Class`, `Mimetype`) VALUES"
		. " ('$id', '$file', '$size', '$class', '$mimetype')"
		. " ON DUPLICATE KEY UPDATE `Path` = '$file'";
		if (mysqli_query($dbh, $query) === true) {
			print "Indexed " . basename($file)
			. " (" . human_filesize($size). ") as $class\n";
		}
		else {
			print "Failed to index " . basename($file) . "!\n";
		}
	}
	mysqli_close($dbh);
}
// Takes an MD5 hash of all files in $path. If a file is found
// to be already in the database but under a different `path`
// this function will update that `path`
function hooya_updatedb($path)
{
	$files = getDirFiles($path);
        $dbh = mysqli_connect(CONFIG_MYSQL_HOOYA_HOST,
                CONFIG_MYSQL_HOOYA_USER,
                CONFIG_MYSQL_HOOYA_PASSWORD,
                CONFIG_MYSQL_HOOYA_DATABASE);
	foreach ($files as $file) {
		$id = hash_file('md5', $file);
		$query = "UPDATE `Files` SET"
		. " `Path` = '$file'"
		. " WHERE `Id` = '$id'";
		print $query;die;
		mysqli_query($dbh, $query);
	}
	mysqli_close($dbh);
}
function getDirFiles($dir, &$results = array())
{
	$files = scandir($dir);

	foreach($files as $key => $value){
		$path = realpath($dir.DIRECTORY_SEPARATOR.$value);
		if(!is_dir($path)) {
			$results[] = $path;
		} else if($value != "." && $value != "..") {
			getDirFiles($path, $results);
		}
	}
	return $results;
}
?>