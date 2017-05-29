<?php

// Yes, this site uses sessions! Please enable cookies!
session_start();

// deployment-specific configuration
include(dirname(__FILE__)."/config.php");

//function definitions

// updates the user's current style and stashes that into a SQL table
function updateUserStyle($username, $css)
{
	$conn = new mysqli(CONFIG_DB_SERVER, CONFIG_DB_USERNAME, CONFIG_DB_PASSWORD, CONFIG_DB_DATABASE);
	if ($conn->connect_error) {
		return False;
	}
	$cmd = "UPDATE `" . CONFIG_DB_TABLE . "` SET `pref_css`='$css' WHERE `username`='$username'";
	$conn->query($cmd);
	$_SESSION['pref_css'] = $css;
	return True;
}
// returns the size of a directory in bytes
function dirSize($dir)
{
	$totalSize = 0;
	foreach (new DirectoryIterator($dir) as $file) {
		if ($file->isFile()) {
			$totalSize += $file->getSize();
		}
	}
	return $totalSize;
}
// returns a human-readable file-size
function human_filesize($bytes, $decimals = 2)
{
    $size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
}
// really silly function for sending files in a stream-compatible mode
// this will be integrated with a download.php when you request to download
// a file
function sendAnime($filename)
{
	header("Content-Type:".mime_content_type("$filename"));
	header("Content-Disposition: attachment; filename=\"" . basename("$filename") . "\"");
	header('X-Sendfile:'.realpath($filename));
}
// used for determining elligibility to be downloaded as a zip file
function isAnAudioFile($filename)
{
	$extension = pathinfo($filename)["extension"];
	if ($extension == "mp3") return true;
	if ($extension == "m4a") return true;
	if ($extension == "flac") return true;
	if ($extension == "aiff") return true;
	if ($extension == "m3u") return true;
	if ($extension == "mpg") return true;
	if ($extension == "wav") return true;
	if ($extension == "ogg") return true;
	if ($extension == "wma") return true;
	return false;
}
// was used for determining elligibility to be viewed as a gallery
// I should clean this code up, since I'm not sure this is called anymore
function isAPictureFile($filename)
{
	$extension = pathinfo($filename)["extension"];
	if ($extension == "png") return true;
	if ($extension == "jpg") return true;
	if ($extension == "jpeg") return true;
	if ($extension == "webm") return true;
	if ($extension == "jpe") return true;
	if ($extension == "gif") return true;
	return false;
}
// Passed an array of filenames, tests if all files are music files
// Returns true/false if this folder looks like a folder of music
function testForMusicFiles($files)
{
	$isAMusicFolder = false;
	if (empty($files))
		return false;
	foreach ($files as $item) {
		if (isAnAudioFile($item)) {
			$isAMusicFolder = true;
			break;
		}
	}
	return $isAMusicFolder;
}
// tests if all passed files are pictures
// was used to determine gallery elligibility, probably deprecated
function onlyPictures($files)
{
	if (empty($files))
		return false;
	foreach ($files as $item) {
		if (!isAPictureFile($item)) {
			return false;
		}
	}
	return true;
}
// return an array of all files in a directory
function getFiles($dir)
{
	$array = array();
	$files = scandir("share".$dir);
	foreach($files as $file) {
		if (is_file("share".$dir.$file)) {
			$array[] = $file;
		}
	}
	return $array;
}
// Generates a random hex string, mostly for generating a salt
function randomHex($len) {
	$chars = 'abcdef01234567890';
	for ($i = 0; $i < $len; $i++)
		$hex .= $chars[rand(0, strlen($chars) - 1)];
	return $hex;
}
function lwrite($file, $msg) {
	$fd = fopen($file, "a");
	fwrite($fd, @date('[d/M/Y:H:i:s]').' '.$msg.PHP_EOL);
	fclose($fd);
	return;
}
?>