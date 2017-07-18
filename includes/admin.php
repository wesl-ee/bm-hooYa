<?php
/* Probably merge this and database.php files later */
function hooya_importdir($path, $method, $tags)
{
	$files = getDirFiles($path);
	$dbh = mysqli_connect(CONFIG_MYSQL_HOOYA_HOST,
		CONFIG_MYSQL_HOOYA_USER,
		CONFIG_MYSQL_HOOYA_PASSWORD,
		CONFIG_MYSQL_HOOYA_DATABASE);
	mysqli_set_charset($dbh, 'utf8');
	foreach ($files as $file) {
		// This is the key we'll use to talk about the file from now on
		$id = hash_file('md5', $file);

		// Destructively move files into storage
		if ($method == 'mv') {
			$newfile = CONFIG_HOOYA_STORAGE_PATH . $id . '.' . $extentsion;
			if (file_exists($newfile)) {
				print "Already indexed: " . basename($file) . " ";
				if (unlink($file))
					print "so deleted copy\n" ;
				else
					print "but could not delete copy\n";
				continue;
			}
			if (!rename($file, $newfile)) {
				print "Failed to rename ". basename($file) . "\n";
				print "*Check permissions on $file and "
				. CONFIG_HOOYA_STORAGE_PATH . "\n";
				$failcount++;
				continue;
			}
			$file = $newfile;
		}
		// Preserve the original file while copying it into storage
		else if ($method == 'cp') {
			$newfile = CONFIG_HOOYA_STORAGE_PATH . '.' . $extension;
			if (file_exists($newfile)) {
				print "Already indexed: " . basename($file) . " ";
				if (unlink($file))
					print "so deleted copy\n" ;
				else
					print "but could not delete copy\n";
				continue;
			}
			if (!copy($file, $newfile)) {
				print "Failed to copy " . basename($file) . "\n"
				. "*Check permissions on " . CONFIG_HOOYA_STORAGE_PATH
				. "\n";
				$failcount++;
				continue;
			}
			$file = $newfile;
		}
		// Do nothing if we are not meant to merge this file into storage
		else {
		}

		// Gather general properties about this file
		$size = filesize($file);
		$mimetype = mime_content_type($file);
		$ftype = explode('/', $mimetype)[0];
		$extension = pathinfo($file)['extension'];

		// Determine the files's class in our storage schema
		switch ($ftype) {
		case 'image':
			$class = 'single_image';
			break;
		case 'video':
			$class = 'video';
		}

		// Formulate a query for our storage
		$query = "INSERT INTO `Files`"
		. " (`Id`, `Path`, `Size`, `Class`, `Mimetype`) VALUES"
		. " ('$id', '$file', '$size', '$class', '$mimetype')"
		. " ON DUPLICATE KEY UPDATE `Path` = '$file'";

		// Execute the query, catch any errors
		if (mysqli_query($dbh, $query) === false) {
			print "Failed to index " . basename($file) . "!\n";
			$failcount++;
			continue;
		}

		// Gather specific properties for each type of file
		switch($class) {
		case 'single_image':
			list($width, $height) = getimagesize($file);
			$query = "INSERT INTO `single_image` "
			. " (`Id`, `Width`, `Height`) VALUES"
			. " ('$id', $width, $height)"
			. " ON DUPLICATE KEY UPDATE `Id` = '$id'";
			break;
		// TODO
		case 'video':
		}

		// Execute the query, catch any errors
		if ($query && mysqli_query($dbh, $query) === false) {
			print "Failed to index " . basename($file) . "!\n";
			$failcount++;
			continue;
		}
		$totalsize += $size;
		$successcount++;
	}
	print "\nREPORT";
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
	}
	print "\nREPORT";
	if ($successcount > 0) print "\nUpdated $successcount files (" . human_filesize($totalsize) . ")";
	if ($failcount > 0) print "\nFailed to update $failcount files";
	mysqli_close($dbh);
}
function hooya_tagdir($dir)
{
}
function hooya_deletekey($key, $rm = false)
{
        $dbh = mysqli_connect(CONFIG_MYSQL_HOOYA_HOST,
                CONFIG_MYSQL_HOOYA_USER,
                CONFIG_MYSQL_HOOYA_PASSWORD,
                CONFIG_MYSQL_HOOYA_DATABASE);
	mysqli_set_charset($dbh, 'utf8');
	$query = "SELECT Path FROM Files WHERE Id='$key'";
	$file = mysqli_fetch_assoc(mysqli_query($dbh, $query))['Path'];
	$size = filesize($file);
	if ($rm) {
		if (unlink($file) === false) {
			print "Failed to delete $file\n";
			$failcount++;
		}
		else print "Deleted $file\n";
	}

	$query = "DELETE FROM Files WHERE Id='$key'";
	if (mysqli_query($dbh, $query) === false) {
		print "Failed to unmap $file\n";
		$failcount++;
	}
	else {
		print "Unmapped $key\n";
		$successcount++;
	}
	print "\n\nREPORT";
	if ($successcount > 0) print "\nUnmapped $successcount files (" . human_filesize($size) . ")";
	if ($failcount > 0) print "\nFailed to unmap $failcount files";
}
function hooya_deletedir($dir, $rm = false)
{
	if (($dir = realpath($dir)) === false) {
		print 'Not a directory. . .';
		return;
	}
        $dbh = mysqli_connect(CONFIG_MYSQL_HOOYA_HOST,
                CONFIG_MYSQL_HOOYA_USER,
                CONFIG_MYSQL_HOOYA_PASSWORD,
                CONFIG_MYSQL_HOOYA_DATABASE);
	mysqli_set_charset($dbh, 'utf8');
	$query = "SELECT Id, Path FROM Files";
	$res = mysqli_query($dbh, $query);
	while ($row = mysqli_fetch_assoc($res)) {
		$file = $row['Path']; $id = $row['Id'];
		if ($rm) {
			if (unlink($file) === false) {
				print "Failed to delete $file\n";
				$failcount++;
				continue;
			}
			print "Deleted $file\n";
		}
		if (strncmp($file, $dir, strlen($dir)) !== 0) continue;
		$query = "DELETE FROM Files WHERE Id='$id'";
		if (mysqli_query($dbh, $query) === false) {
			print "Failed to unmap $file\n";
			$failcount++;
			continue;
		}
		print "Unmapped $file\n";
		$successcount++;
	}
	print "\n\nREPORT";
	if ($successcount > 0) print "\nUnmapped $successcount files (" . human_filesize($totalsize) . ")";
	if ($failcount > 0) print "\nFailed to unmap $failcount files";
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
