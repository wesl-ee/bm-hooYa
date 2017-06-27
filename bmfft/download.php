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
		$file=CONFIG_TEMPORARY_DIRECTORY.'/'.bin2hex(base64_decode($key)).'.jpg';
		if (!file_exists($file))
			exec('ffmpegthumbnailer -i '.escapeshellarg(bmfft_getattr($key, 'path')).' -f -q 10 -s 320 -o '.$file);
		bmfft_xsendfile($file);
		return;
	}
	if ($ftype == 'image' && $mimetype == 'image/gif') {
		$file=CONFIG_TEMPORARY_DIRECTORY.'/'.bin2hex(base64_decode($key)).'.jpg';
		if (!file_exists($file))
			exec('convert '.escapeshellarg(bmfft_getattr($key, 'path')).'[0] -thumbnail "500x500>" '.$file);
		bmfft_xsendfile($file);
		return;
	}
	// Default to JPG thumbnails to save space and time
	if ($ftype == 'image' && $mimetype != 'image/png') {
		$file=CONFIG_TEMPORARY_DIRECTORY.'/'.bin2hex(base64_decode($key)).'.jpg';
		if (!file_exists($file))
			exec('convert '.escapeshellarg(bmfft_getattr($key, 'path')).' -thumbnail "500x500>" '.$file);
		bmfft_xsendfile($file);
		return;
	}
	// PNGs need a PNG thumbnail because otherwise transparencies look funny
	if ($ftype == 'image') {
		$file=CONFIG_TEMPORARY_DIRECTORY.'/'.bin2hex(base64_decode($key)).'.png';
		if (!file_exists($file))
			exec('convert '.escapeshellarg(bmfft_getattr($key, 'path')).' -thumbnail "500x500>" '.$file);
		bmfft_xsendfile($file);
		return;
	}
}
if (isset($_GET['partyhat'])) {
	// Coming soon -- watch imagemagick render party hats onto thumbnails!
}
if ($ftype == 'video') {
	if ($_GET['videotrack'] !== false) {
		$n = $_GET['videotrack'];
	}
	else if ($_GET['subtrack'] !== false) {
		$n = $_GET['subtrack'];
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
