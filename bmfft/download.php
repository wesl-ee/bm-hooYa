<?php
include '../includes/core.php';
include '../includes/auth.php';
include '../includes/video.php';
include 'bmfft_db.php';

if (!isset($_GET['key']))
	die();
$key = rawurldecode($_GET['key']);
if (!bmfft_exists($key)) die('Could not find that key in the database!');
$ftype = bmfft_getfiletype($key);

// Throw a 404 img if that key is not in the database
if (($file = bmfft_getattr($key, 'path')) === false) {
	$file = dirname(__FILE__).'/spoilers/404.jpg';
	bmfft_xsendfile($file);
	return;
}
// Handle thumbnail requests
if (isset($_GET['thumb'])) {
	$mimetype = bmfft_getattr($key, 'mimetype');
	if (bmfft_getattr($key, 'lewd')) {
		$file = dirname(__FILE__).'/spoilers/spoiler1.png';
		bmfft_xsendfile($file);
		return;
	}

	// Take a snapshot of the video and use that as a thumbnail
	if ($ftype == 'video') {
		$file=$_SERVER['DOCUMENT_ROOT'].CONFIG_TEMPORARY_DIRECTORY.'/'.bin2hex(base64_decode($key)).'.jpg';
		if (!file_exists($file))
			exec('ffmpegthumbnailer -i '.escapeshellarg(bmfft_getattr($key, 'path')).' -f -q 10 -s 320 -o '.$file);
		bmfft_xsendfile($file);
		return;
	}
	if ($ftype == 'image' && $mimetype == 'image/gif') {
		$file=$_SERVER['DOCUMENT_ROOT'].CONFIG_TEMPORARY_DIRECTORY.'/'.bin2hex(base64_decode($key)).'.jpg';
		if (!file_exists($file))
			exec('convert '.escapeshellarg(bmfft_getattr($key, 'path')).'[0] -thumbnail "500x500>" '.$file);
		bmfft_xsendfile($file);
		return;
	}
	// Default to JPG thumbnails to save space and time
	if ($ftype == 'image' && $mimetype != 'image/png') {
		$file=$_SERVER['DOCUMENT_ROOT'].CONFIG_TEMPORARY_DIRECTORY.'/'.bin2hex(base64_decode($key)).'.jpg';
		if (!file_exists($file))
			exec('convert '.escapeshellarg(bmfft_getattr($key, 'path')).' -thumbnail "500x500>" '.$file);
		bmfft_xsendfile($file);
		return;
	}
	// PNGs need a PNG thumbnail because otherwise transparencies look funny
	if ($ftype == 'image') {
		$file=$_SERVER['DOCUMENT_ROOT'].CONFIG_TEMPORARY_DIRECTORY.'/'.bin2hex(base64_decode($key)).'.png'; if (!file_exists($file))
			exec('convert '.escapeshellarg(bmfft_getattr($key, 'path')).' -thumbnail "500x500>" '.$file);
		bmfft_xsendfile($file);
		return;
	}
}
if (isset($_GET['partyhat'])) {
	// Coming soon -- watch imagemagick render party hats onto thumbnails!
}
if ($ftype == 'video') {
	if (isset($_GET['track'])) {
		$track = $_GET['track'];
		$out = $_SERVER['DOCUMENT_ROOT'].'/'.CONFIG_TEMPORARY_DIRECTORY.'/'.$key.'_'.$track['index'];
		$file = bmfft_getattr($key, 'path');
		$info = video_getstreaminfo($file, $track)[$track];
		if ($info['codec_type'] == 'video')
			video_muxvideo($file, $track, $out);
		else {
			video_getstream($file, $track, $out);
		}
		$file = $out;
		bmfft_xsendfile($file);
		return;
	}
}
// Otherwise, just send the file with no special rendering
bmfft_xsendfile($file);

function bmfft_xsendfile($file) {
	header('Content-Type:'.mime_content_type($file));
	header('Content-Disposition: attachment; filename="' . basename($file) . '"');
	header('X-Sendfile: '.$file);
}
?>
