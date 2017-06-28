<!DOCTYPE html>
<?php include $_SERVER['DOCUMENT_ROOT']."/bmffd/includes/core.php"; ?>
<HTML>
<head>
	<?php include CONFIG_ROOT_PATH."includes/head.php";?>
	<title>bmffd — create an account</title>
</head>
<BODY>

<div id="container">
<div id="left_frame">
<img id="mascot" src=<?php echo $_SESSION['mascot'];?>>
</div>
<div id="right_frame">
<h1 style="text-align:center;">the bath house</h1>
<h3 style="text-align:center;margin-top:-20px;">account creation wizard</h3>
<a href='.'>« back</a>
<form action="acc_create.php" method="post">
<?php if (!CONFIG_OPEN_REGISTRATION) {
	print '<label style="display:block;">one-time registration key</label>';
	print '<input style="display:block;" type="password" id="onsen_otp" name="onsen_otp" maxlength="50" />';
}?>
<label style="display:block;">requested username</label>
<input style="display:block;" type="text" id="onsen_username" name="onsen_username" maxlength="20" />
<label style="display:block;">requested password</label>
<input style="display:block;" type="password" id="onsen_passowrd" name="onsen_password" maxlength="50" />
<input style="display:block;" type="submit" value="Sign me up!"/>
</form>
<?php if (isset($_POST['onsen_username']) && isset($_POST['onsen_password']) && (CONFIG_OPEN_REGISTRATION || isset($_POST['onsen_otp']))) {
	$onsen_username = filter_var($_POST['onsen_username'], FILTER_SANITIZE_STRING);
	$onsen_password = filter_var($_POST['onsen_password'], FILTER_SANITIZE_STRING);
	$fh = fopen("./otp_challenge", "r") or die("Not accepting new accounts right now!");
	$realotp = fgets($fh);
	fclose($fh);
	if (!CONFIG_OPEN_REGISTRATION && $realotp !== hash("sha1", $_POST['onsen_otp'])) {
		print "Incorrect one-time key!";
		lwrite(CONFIG_AUTHLOG_FILE, $_SERVER['REMOTE_IP']." tried to create an account for ".$_POST['onsen_username']);
		return;
	}

	$conn = new mysqli(CONFIG_DB_SERVER, CONFIG_DB_USERNAME, CONFIG_DB_PASSWORD, CONFIG_DB_DATABASE);
	$cmd = "SELECT `username` FROM `" . CONFIG_DB_TABLE . "` WHERE `username`='".$onsen_username."'";
	$result=$conn->query($cmd);
	if ($result->num_rows !== 0)
	{
		print "Username is taken!";
		return;
	}
	$salt = randomHex(16);
	$hash = $onsen_password;
	$hash = crypt($hash, '$6$'.$salt.'$');
	$salted_password = $hash;
	$cmd = "INSERT INTO " . CONFIG_DB_TABLE . " (username, password) VALUES ('" . $_POST['onsen_username']."','".$salted_password."')";
	$conn->query($cmd);
	$fh = fopen("./otp_challenge", "w") or die("Not accepting new accounts right now!");
	$realotp = fwrite($fh, $_POST['onsen_otp']);
	fclose($fh);
	lwrite(CONFIG_AUTHLOG_FILE, $_SERVER['REMOTE_IP']." created an account with the username ".$onsen_username);
	print 'Created your account!';
}
?>
</div>
</div>
</body>
</HTML>
