<?php

// Yes, this site uses sessions! Please enable cookies!
session_start();

// load the user configuration file
include "includes/config.php";

// Set up variables that will define the page's style depending
// on the pref_css PHP session variable
switch($_SESSION['pref_css']) {
	case "classic":
		$curr_css="classic";
		$stylesheet="css/style_suckless.css";
		$mascot="img/rei.png";
		break;
	case "gold":
		$curr_css="gold";
		$stylesheet="css/style_suckless_gold.css";
		$mascot="img/yui.png";
		$motd="ゆゆ式";
		break;
	case "wu_tang":
		$curr_css="wu_tang";
		$stylesheet="css/style_suckless_wutang.css";
		$mascot="img/ghost.png";
		$motd="Daily reminder: Protect ya neck";
		break;
	default:
		$curr_css="classic";
		$stylesheet="css/style_suckless.css";
		$mascot="img/rei.png";
}

// function definitions

// updates php session variable pref_css
function updateUserStyle($css)
{
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
?>