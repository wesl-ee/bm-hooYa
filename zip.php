<?php
session_start();
/* This file  used to write a temporary file to /srv/http/a
 * which is remarkably bad practice and I still don't sleep as well at night
 * knowing that I did that dirty hack
 *
 * Now this section takes an encoded url parameter dir and zips that
 * directory and sends it to the user to download only using pipes.
 *
 * Cool!
 */

// If you're not properly authenticated then kick the user back to index.html

if (isset($_GET["dir"])) {
	$dirToZip = urldecode("share/".$_GET["dir"]);
}
else {
	die();
}
// Handle requests that try to break out of the /bmffd/share directory
if (strpos(realpath($dirToZip), realpath($_SERVER['DOCUMENT_ROOT']."/bmffd/share")) === false) {
	die();
}
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="'.basename($dirToZip).'.zip"');

// Zip the directory and write that to stdout, then catch it in a pipe
$command = 'zip -r - "'.$dirToZip.'"';
$fp = popen($command, "r");

// Now echo the FIFO pipe to the user
$bufsize = 65535;
$buff = '';
while( !feof($fp) ) {
	$buff = fread($fp, $bufsize);
	echo $buff;
}
pclose($fp);
?>
