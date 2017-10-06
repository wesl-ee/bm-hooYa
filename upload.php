<!DOCTYPE HTML>
<?php
include "includes/config.php";

include CONFIG_COMMON_PATH."includes/core.php";
include CONFIG_COMMON_PATH."includes/auth.php";
include CONFIG_HOOYA_PATH."includes/upload.php";
include CONFIG_HOOYA_PATH."includes/render.php";
?>
<html>
<head>
	<?php include CONFIG_COMMON_PATH."includes/head.php";
	include CONFIG_HOOYA_PATH."includes/head.php"; ?>
	<title>bigmike — hooYa! upload</title>
</head>
<body>
<div id="container">
<div id="leftframe">
	<nav>
		<?php print_login();?>
	</nav>
	<aside>
		<h1 style="text-align:center;">hooYa!</h1>
		<?php render_min_search(); render_hooya_headers(); ?>
	</aside>
</div>
<?php
$files = uploaded_today($_SESSION['userid']);
$rem = CONFIG_HOOYA_DAILY_UPLOAD_LIMIT - count($files);

if ($rem > 0 && isset($_POST['class'])) {
	$class = $_POST['class'];
	$file = $_FILES['file'];

	$accept_file = True;
	if ($file['size'] > CONFIG_HOOYA_MAX_UPLOAD) {
		$accept_file = False;
		$failmessage = 'File too big';
	}
	if (!$to = DB_MEDIA_CLASSES[$class]['ULPath']) {
		$accept_file = False;
		$failmessage =  'Unrecognized media class';
	}
	if ($accept_file) {
		$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
		$from = $file['tmp_name'];
		$key = md5_file($from);
		if ($to = CONFIG_HOOYA_STORAGE_PATH)
			$basename = $key;
		else
			$basename = $file['name'];
		$to .= '/' . $basename . '.' . $ext;

		move_uploaded_file($from, $to);
		if (simple_import($to, $class, $key)) {
			$href = "view.php?key=$key";
			print "<script>window.location='$href'</script>";
		}
		else $failmessage = "That file is"
		. " <a href='view.php?key=$key'>already indexed!</a>";
	}
}
?>
<div id="rightframe">
	<header><h1>hooYa! Upload</h1></header>
	<?php
	if (isset($failmessage)) print "<p>$failmessage</p>";
	if ($rem > 0) {
		print "You can still upload $rem files today"
		. '<form method=post id=form enctype="multipart/form-data">'
		. '<ol><li>'
		. '<input type=file id=fupload name=file class=fupload>'
		. '<label id=flabel for="fupload">Choose a file</label></li>'
		. '<li><select name=class id=class>'
		. '<option disabled selected>Media Class</option>';
		render_classmenu();
		print '</select></li>'
		. '<li><input id=fsubmit type=submit value=Upload></li>'
		. '</ol></form>'
		. '<header id=filename style="justify-content:center;"></header>'
		. '<main class=single><div style="text-align:center" id=hack>'
		. '<img id=output alt="Preview not available for this file">'
		. '</div></main>';
	}
	else {
		print 'You cannot upload anymore files today';
	}
	?>
</div>
<script>
document.getElementById("fsubmit").style.visibility = 'hidden';
document.getElementById("class").style.visibility = 'hidden';
document.getElementById("hack").style.display = 'none';

document.getElementById("fupload").onchange = function() {
	document.getElementById("class").style.visibility = 'initial';
	var file = document.getElementById("fupload");
	var name = file.value;
	name = name.split('\\').pop();
	document.getElementById('filename').innerHTML = name;
	document.getElementById('hack').style.display = 'initial';
	document.getElementById('flabel').innerHTML = 'Choose a different file';

	if (file.files[0].name.match(/.(jpg|jpeg|png|gif)$/i)) {
		var reader = new FileReader();
		reader.onload = function(){
			var output = document.getElementById('output');
			output.src = reader.result;
		};
		reader.readAsDataURL(file.files[0]);
	}
};
document.getElementById("class").onchange = function() {
	document.getElementById('fsubmit').style.visibility = 'initial';
};
</script>
</body>
</html>
