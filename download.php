<?php
include 'includes/config.php';

include CONFIG_COMMON_PATH.'includes/core.php';
include CONFIG_HOOYA_PATH.'includes/video.php';
include CONFIG_HOOYA_PATH.'includes/database.php';


if (!isset($_GET['key'])) {
	die;
}

$key = rawurldecode($_GET['key']);

$fileinfo = db_getfileinfo($key);
// Grab some basic details for the file
$class = $fileinfo['Class'];
$path = $fileinfo['Path'];
$mimetype = $fileinfo['Mimetype'];
$ftype = explode('/', $mimetype)[0];

if (!file_exists($path)) {
	$file = CONFIG_HOOYA_PATH . 'img/404.jpg';
	bmfft_xsendfile($file);
	return;
}
if (!logged_in()) {
	if (DB_MEDIA_CLASSES[$class]['Restricted'] &&
	!check_user_identity($_SERVER['HTTP_X_REAL_IP']))
		include CONFIG_COMMON_PATH."includes/auth.php";
}

// Assume that we are sending the raw file until we can disprove that
$file = $path;

// Handle thumbnail requests
if (isset($_GET['thumb'])) {
	// Take a snapshot of the video and use that as a thumbnail
	if ($ftype == 'video') {
		$file = CONFIG_TEMPORARY_PATH . $key . '.jpg';
		if (!file_exists($file)) {
			exec("ffprobe -v error -show_entries format=duration"
			. " -of default=noprint_wrappers=1:nokey=1 '$path'"
			, $output);

			$duration = $output[0];
			system("ffmpeg -y -loglevel panic -hide_banner -ss "
			. ($duration/2) . " -i '$path' -an -vframes 1"
			. " -vf scale=\"250:-1\" $file");
		}
		bmfft_xsendfile($file);
		return;
	}
	// Take the first frame of a .gif and thumbnail it
	if ($ftype == 'image' && $mimetype == 'image/gif') {
		$file = CONFIG_TEMPORARY_PATH . $key . '.jpg';
		if (!file_exists($file))
			exec('convert '.escapeshellarg($path)
			.'[0] -thumbnail "500x500>" '.$file);
		bmfft_xsendfile($file);
		return;
	}
	// Default to JPG thumbnails to save space and time
	if ($ftype == 'image' && $mimetype != 'image/png') {
		$file = CONFIG_TEMPORARY_PATH.$key . '.jpg';
		if (!file_exists($file))
			exec('convert '.escapeshellarg($path)
			.' -thumbnail "500x500>" '.$file);
		bmfft_xsendfile($file);
		return;
	}
	// PNGs need a PNG thumbnail because otherwise transparencies look funny
	if ($ftype == 'image') {
		$file = CONFIG_TEMPORARY_PATH . $key . '.png';
		if (!file_exists($file))
			exec('convert ' . escapeshellarg($path)
			. ' -thumbnail "500x500>" '.$file);
		bmfft_xsendfile($file);
		return;
	}
}
if ($ftype == 'video' && isset($_GET['preview'])) {
	$file = CONFIG_TEMPORARY_PATH . $key . '_preview.png';
	if (!file_exists($file)) {
		exec("ffprobe -v error -show_entries format=duration"
		. " -of default=noprint_wrappers=1:nokey=1 '$path'"
		, $output);

		$duration = $output[0];
		system("ffmpeg -y -loglevel panic -hide_banner -ss "
		. ($duration/2) . " -i '$path' -an -vframes 1"
		. " $file");
	}
	bmfft_xsendfile($file);
	return;
}
// Otherwise, just send the file with no special rendering
bmfft_xsendfile($file);

function bmfft_xsendfile($file) {
	header('Content-Type:'.mime_content_type($file));
	header('Content-Disposition: inline; filename="bigmike-' . basename($file) . '"');
	header('X-Sendfile: '.$file);
}
?>
