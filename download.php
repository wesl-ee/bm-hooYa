<?php
include 'includes/config.php';

include CONFIG_COMMON_PATH.'includes/core.php';
if (CONFIG_REQUIRE_AUTHENTICATION)
	include CONFIG_COMMON_PATH.'includes/auth.php';
include 'includes/video.php';
include 'includes/database.php';

if (!isset($_GET['key']))
	die;
$key = rawurldecode($_GET['key']);

$main_attrs = db_get_main_attrs($key, ['Class', 'Path', 'Mimetype']);
$class = $main_attrs['Class'];
$path = $main_attrs['Path'];
$mimetype = $main_attrs['Mimetype'];

$ftype = explode('/', $mimetype)[0];

// Throw a 404 img if that key is not in the database
if (($file = $path) === false) {
	$file = dirname(__FILE__).'/spoilers/404.jpg';
	bmfft_xsendfile($file);
	return;
}
// Handle thumbnail requests
if (isset($_GET['thumb'])) {
/*	if (bmfft_getattr($key, 'lewd')) {
		$file = dirname(__FILE__).'/spoilers/spoiler1.png';
		bmfft_xsendfile($file);
		return;
	}*/

	// Take a snapshot of the video and use that as a thumbnail
	if ($ftype == 'video') {
		$file = CONFIG_TEMPORARY_PATH.$key.'.jpg';
		if (!file_exists($file))
			exec('ffmpegthumbnailer -i '.escapeshellarg($path).' -f -q 10 -s 320 -o '.$file);
		bmfft_xsendfile($file);
		return;
	}
	if ($ftype == 'image' && $mimetype == 'image/gif') {
		$file = CONFIG_TEMPORARY_PATH.$key.'.jpg';
		if (!file_exists($file))
			exec('convert '.escapeshellarg($path).'[0] -thumbnail "500x500>" '.$file);
		bmfft_xsendfile($file);
		return;
	}
	// Default to JPG thumbnails to save space and time
	if ($ftype == 'image' && $mimetype != 'image/png') {
		$file = CONFIG_TEMPORARY_PATH.$key.'.jpg';
		if (!file_exists($file))
			exec('convert '.escapeshellarg($path).' -thumbnail "500x500>" '.$file);
		bmfft_xsendfile($file);
		return;
	}
	// PNGs need a PNG thumbnail because otherwise transparencies look funny
	if ($ftype == 'image') {
		$file = CONFIG_TEMPORARY_PATH.$key.'.png';
		if (!file_exists($file))
			exec('convert '.escapeshellarg($path).' -thumbnail "500x500>" '.$file);
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
		$out = CONFIG_TEMPORARY_PATH.$key.'_'.$track['index'];
		$file = $path;
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
	header('X-Sendfile: '.$file);
}
?>
