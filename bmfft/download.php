<?php
include '../includes/core.php';
include '../includes/auth.php';
include 'bmfft_db.php';

if (!isset($_GET['key']))
	die();
$key = rawurldecode($_GET['key']);
$ftype = bmfft_getfiletype($key);

// Throw a 404 img if that key is not in the database
if (($file = bmfft_getattr($key, 'path')) === false) {
	$file = dirname(__FILE__).'/404.jpg';
	bmfft_xsendfile($file);
	return;
}

// Handle thumbnail requests
if (isset($_GET['thumb'])) {
	if (bmfft_getattr($key, 'lewd')) {
		$file = dirname(__FILE__).'/spoilers/spoiler1.png';
		bmfft_xsendfile($file);
		return;
	}
	// Take a snapshot of the video and use that as a thumbnail
	if ($ftype == 'video') {
		if (!file_exists('cache/'.bin2hex(base64_decode($key)).'.jpg'))
		exec('ffmpeg -ss 00:00:00 -i \''.bmfft_getattr($key, 'path').'\' -vframes 1 -q:v 10 -vf scale=320:-1 cache/'.bin2hex(base64_decode($key)).'.jpg');
		$file='cache/'.bin2hex(base64_decode($key)).'.jpg';
		bmfft_xsendfile($file);
		return;
	}
	// Default to JPG thumbnails to save space and time
	if ($ftype == 'image' && bmfft_getattr($key, 'mimetype') != 'image/png') {
		if (!file_exists('cache/'.bin2hex(base64_decode($key)).'.jpg'))
		exec('ffmpeg -i \''.bmfft_getattr($key, 'path').'\' -vframes 1 -q:v 10 -vf scale=320:-1 cache/'.bin2hex(base64_decode($key)).'.jpg');
		$file='cache/'.bin2hex(base64_decode($key)).'.jpg';
		bmfft_xsendfile($file);
		return;
	}
	// PNGs need a PNG thumbnail because otherwise transparencies look funny
	if ($ftype == 'image' && bmfft_getattr($key, 'mimetype') == 'image/png') {
		if (!file_exists('cache/'.bin2hex(base64_decode($key)).'.png'))
		exec('ffmpeg -i \''.bmfft_getattr($key, 'path').'\' -vframes 1 -q:v 20 -vf scale=320:-1 cache/'.bin2hex(base64_decode($key)).'.png');
		$file='cache/'.bin2hex(base64_decode($key)).'.png';
		bmfft_xsendfile($file);
		return;
	}
}
if (isset($_GET['partyhat'])) {
	// Coming soon -- watch imagemagick render party hats onto thumbnails!
}

// Otherwise, just send the file with no special rendering
bmfft_xsendfile($file);

function bmfft_xsendfile($file) {
	header('Content-Type:'.mime_content_type($file));
	header('Content-Disposition: attachment; filename="' . basename($file) . '"');
	header('X-Sendfile: '.$file);
}
?>
