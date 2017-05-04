<?php
session_start();

// variable declarations

define("CONFIG_ROOT_PATH", "/var/git/bmffd/");

switch($_SESSION['pref_css']) {
	case "classic":
		$curr_css="classic";
		$stylesheet="css/style_suckless.css";
		$mascot="img/rei.png";
		break;
	case "wu_tang":
		$curr_css="wu_tang";
		$stylesheet="css/style_suckless_wutang.css";
		$mascot="img/ghost.png";
		$motd="Daily reminder: Protect ya neck";
		break;
	case "gold":
		$curr_css="gold";
		$stylesheet="css/style_suckless_gold.css";
		$mascot="img/yui.png";
		$motd="ゆゆ式";
		break;
	default:
		$curr_css="default";
		$stylesheet="css/style_suckless.css";
}

// fn definitions
function updateUserStyle($css)
{
	$_SESSION['pref_css'] = $css;
	return True;
}
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
function human_filesize($bytes, $decimals = 2)
{
    $size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
}
function sendAnime($filename)
{
	header("Content-Type:".mime_content_type("$filename"));
	header("Content-Disposition: attachment; filename=\"" . basename("$filename") . "\"");
	header('X-Sendfile:'.realpath($filename));
}
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

?>