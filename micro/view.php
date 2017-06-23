<?php
/*
MicroTXT - A tiny PHP Textboard Software
Copyright (c) 2016 Kevin Froman (https://ChaosWebs.net/)

MIT License
*/
include "../includes/core.php";
// If you're not properly authenticated then kick the user back to login.php
if (CONFIG_REQUIRE_AUTHENTICATION)
	include "includes/auth.php";
include('php/settings.php');
include('php/csrf.php');

if (! isset($_GET['post']))
{
	header('location: index.php');
	die(0);
}
$id = $_GET['post'];
$id = str_replace('/', '', $id);
$id = str_replace('\\', '', $id);

$id = htmlentities($id);

$postFile = 'posts/' . $id . '.html';

if (! file_exists($postFile))
{
	http_response_code(404);
	header('location: 404.html');
	die(0);
}

$data = file_get_contents($postFile);
// DomDocument is stupid and likes to append tags automatically, & breaks without a doctype.
$data = str_replace('<!DOCTYPE HTML>', '', $data);
$data = str_replace('<body>', '', $data);
$data = str_replace('</body>', '', $data);
$data = str_replace('<html>', '', $data);
$data = str_replace('</html>', '', $data);
?>
<!DOCTYPE HTML>
<html>
<head>
	<meta charset='utf-8'>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
	<title><?php echo $siteTitle . ' - ' . $id;?></title>
	<link rel="icon" type="image/x-icon" href="favicon.png?v=1">
	<?php include CONFIG_ROOT_PATH."includes/head.php"; ?>
	<link rel='stylesheet' href='theme.css'>
</head>
<body>
<div id="container">
	<div id="left_frame">
		<div id="logout" style="border-top:0px;">
		<?php
		if (isset($_SESSION['username'])) {
			print('<a href="'.CONFIG_DOCUMENT_ROOT_PATH.'">home</a></br>');
			print('<a href="'.CONFIG_DOCUMENT_ROOT_PATH.'logout.php">logout</a>');
		}
		else {
			print('<a href="'.CONFIG_DOCUMENT_ROOT_PATH.'login.php?ref='.$_SERVER['REQUEST_URI'].'">login</a>');
		}
	        ?>
		</div>
	</div>
	<div id="right_frame">
	<div style="width:100%;float:left;"><h1 class='center logo'><a href="."><?php echo $siteTitle;?></h1></a></div>
	<?php
	echo $data;
	?>
	<hr/>
	<h2 style="text-align:center;" id='replyTitle'>Reply</h2>
	<form method='post' action='reply.php' id='reply' style="text-align:center;">
			<div style="display:table;width:50%;margin:auto;">
			<div style="display:table-row;">
				<div style="display:table-cell;text-align:right">Name:</div>
				<div style="display:table-cell;text-align:left;padding-left:20px;"><input required type='text' name='name' maxlength='20' value='Anonymous'></div>
			</div>
			<div style="display:table-row;">
				<div style="display:table-cell;text-align:right;">Tripcode:</div>
				<div style="display:table-cell;text-align:left;padding-left:20px;"><input type='password' name='tripcode' maxlength='100' placeholder="optional"></div>
			</div>
			<div style="display:table-row;">
				<div style="display:table-cell;text-align:right;">Reply to post:</div>
				<div style="display:table-cell;text-align:left;padding-left:20px;"><input required name='replyTo' type='text' maxlength='30' id='replyTo'></div>
			</div>
			</div>
		<?php
		/*
		<label><input name='sage' type='checkbox'> Sage (Don't bump)</label>
		<br><br>
		*/?>
		<textarea required name='text' maxlength='100000' cols='50' rows='10'></textarea>
		<br><br>
		<input type='hidden' name='CSRF' value='<?php echo $CSRF;?>'>
		<input type='hidden' name='threadID' value='<?php echo $id;?>'>
		<br>
		<?php
			if ($captcha)
			{
				if (! isset($_SESSION['currentPosts']))
				{
					$_SESSION['currentPosts'] = $postsBeforeCaptcha;
				}
				if ($_SESSION['currentPosts'] >= $postsBeforeCaptcha)
				{
					echo '<img src="php/captcha.php" alt="captcha">';
					echo '<br><br><label>Captcha Text: <input required type="text" name="captcha" maxlength="10"></label><br><br>';
				}
			}
		?>
		<input type='submit' value='Reply'>
	</form>
	</div>
	<script src='view.js'></script>
	<?php
	if ($keepSessionAlive == true){
		echo '<iframe src="keep-alive.php" style="display: none;"></iframe>';
	}
	?>
</div>
</body>
</html>
