<?php
/* Probably merge this and database.php files later */
function hooya_mergedir($path, $method, $tags)
{
	$files = getDirFiles($path);
	$dbh = mysqli_connect(CONFIG_MYSQL_HOOYA_HOST,
		CONFIG_MYSQL_HOOYA_USER,
		CONFIG_MYSQL_HOOYA_PASSWORD,
		CONFIG_MYSQL_HOOYA_DATABASE);
	mysqli_set_charset($dbh, 'utf8');
	foreach ($files as $file) {
		$id = hash_file('md5', $file);
		$size = filesize($file);
		$mimetype = mime_content_type($file);
		$ftype = explode('/', $mimetype)[0];
		$extension = pathinfo($file)['extension'];
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
		if ($method == 'mv') {
			if (!rename($file, CONFIG_HOOYA_STORAGE_PATH . $id . '.' . $extension)) {
				print "Failed to rename ". basename($file) . "\n";
				print "*Check permissions on $file and "
				. CONFIG_HOOYA_STORAGE_PATH . "\n";
				$failcount++;
				continue;
			}
			$file = CONFIG_HOOYA_STORAGE_PATH . $id . '.' . $extension;
		}
		else if ($method == 'cp') {
			if (!copy($file, CONFIG_HOOYA_STORAGE_PATH . $id) . '.' . $extension) {
				print "Failed to copy " . basename($file) . "\n"
				. "*Check permissions on " . CONFIG_HOOYA_STORAGE_PATH
				. "\n";
				$failcount++;
				continue;
			}
			$file = CONFIG_HOOYA_STORAGE_PATH . $id . '.' . $extension;
		}
		$query = "INSERT INTO `Files`"
		. " (`Id`, `Path`, `Size`, `Class`, `Mimetype`) VALUES"
		. " ('$id', '$file', '$size', '$class', '$mimetype')"
		. " ON DUPLICATE KEY UPDATE `Path` = '$file'";
		if (mysqli_query($dbh, $query) === false) {
			print "Failed to index " . basename($file) . "!\n";
			$failcount++;
			continue;
		}
		print "Merged " . basename($file)
		. " (" . human_filesize($size). ") as $class\n";
		$totalsize += $size;
		$successcount++;
	}
	print "\n\nREPORT";
	if ($successcount > 0) print "\nIndexed $successcount files (" . human_filesize($totalsize) . ")";
	if ($failcount > 0) print "\nFailed to index $failcount files";
	mysqli_close($dbh);
}
function hooya_importdir($path, $tags)
{
	$files = getDirFiles($path);
	$dbh = mysqli_connect(CONFIG_MYSQL_HOOYA_HOST,
		CONFIG_MYSQL_HOOYA_USER,
		CONFIG_MYSQL_HOOYA_PASSWORD,
		CONFIG_MYSQL_HOOYA_DATABASE);
	mysqli_set_charset($dbh, 'utf8');
	foreach ($files as $file) {
		$id = hash_file('md5', $file);
		$size = filesize($file);
		$mimetype = mime_content_type($file);
		$ftype = explode('/', $mimetype)[0];
		$extension = pathinfo($file)['extension'];
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
		if (mysqli_query($dbh, $query) === false) {
			print "Failed to index " . basename($file) . "!\n";
			$failcount++;
			continue;
		}
		print "Indexed " . basename($file)
		. " (" . human_filesize($size). ") as $class\n";
		$totalsize += $size;
		$successcount++;
	}
	print "\n\nREPORT";
	if ($successcount > 0) print "\nIndexed $successcount files (" . human_filesize($totalsize) . ")";
	if ($failcount > 0) print "\nFailed to index $failcount files";
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
	mysqli_set_charset($dbh, 'utf8');
	foreach ($files as $file) {
		$id = hash_file('md5', $file);
		$query = "UPDATE `Files` SET"
		. " `Path` = '$file'"
		. " WHERE `Id` = '$id'";
		if (mysqli_query($dbh, $query) === false) {
			$failcount++;
			print "Failed to update " . basename($file) . "\n";
			continue;
		}
		$totalsize += filesize($file);
		$successcount++;
		print "Updated " . basename($file) . "\n";
	}
	print "\n\nREPORT";
	if ($successcount > 0) print "\nIndexed $successcount files (" . human_filesize($totalsize) . ")";
	if ($failcount > 0) print "\nFailed to index $failcount files";
	mysqli_close($dbh);
}
function hooya_tagdir($dir)
{
}
function hooya_deletekey($key, $rm = false)
{
}
function hooya_deletedir($dir, $rm = false)
{
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