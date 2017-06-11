<?php
include '../includes/core.php';
include '../includes/auth.php';
include 'bmfft_db.php';

if (!isset($_GET['key']))
	die();
$key = rawurldecode($_GET['key']);
$content_type = $_GET['t'];
$file_type = bmfft_getattr($key, 'filetype');

// TODO: Deny access if $file is not in the db
// TODO: Detect file type (e.g. audio, image) using the db

/* How do we differentiate the cases where we want a sound file's cover
 * art vs. it's contents?
 *
 * A: We have the referring page request that, because there is no way that
 * we could know that information just by being passed the file hash. So
 * include a GET parameter 't' which asks us what representation of the file
 * to return
 */
if (($file = bmfft_getattr($key, 'path')) === false)
	$file = dirname(__FILE__).'404.jpg';
// This is the weirdest error but sometimes mime_content_type returns
// null for me even on real files
if (mime_content_type($file) === false)
	$file = dirname(__FILE__).'/404.jpg';

switch($content_type) {
case 'audio':
	$file = dirname(__FILE__).'/404.jpg';
	break;
case 'video':
	$file = dirname(__FILE__).'/404.jpg';
	break;
case 'img':
	if (!isAPicturefile(bmfft_name($key))) {
		$file = dirname(__FILE__).'/404.jpg';
	}
	break;
}
header('Content-Type:'.mime_content_type($file));
header('Content-Disposition: attachment; filename="' . basename($file) . '"');
header('X-Sendfile: '.$file);
?>