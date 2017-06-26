<!DOCTYPE html>
<?php
include "includes/core.php";

// Was the user refered by some other link?
if (isset($_GET['ref']))
	$uri = "?ref=".urlencode($_GET['ref']);
?>
<HTML>
<head>
	<?php include CONFIG_ROOT_PATH."includes/head.php"; ?>
	<title>bmffd — login</title>
</head>
<body>

<div id="container">
<div id="left_frame">
<img id="mascot" src=<?php echo $_SESSION['mascot'];?>>
</div>
<div id="right_frame">
<div id="title">
<h1 style="text-align:center;">the bath house</h1>
</div>
<?php
if (isset($_POST['onsen_username'], $_POST['onsen_password'])) {

	if (strlen($_POST['onsen_username']) > 20 || strlen($_POST['onsen_password']) > 50) { print '<script type="text/javascript">setTimeout(function () {window.history.back();}, 2000);</script>';return; }

	$onsen_username = filter_var($_POST['onsen_username'], FILTER_SANITIZE_STRING);
	$onsen_password = filter_var($_POST['onsen_password'], FILTER_SANITIZE_STRING);

	$mysql_hostname = CONFIG_DB_SERVER;
	$mysql_username = CONFIG_DB_USERNAME;
	$mysql_password = CONFIG_DB_PASSWORD;
	$mysql_dbname = CONFIG_DB_DATABASE;
	$mysql_table = CONFIG_DB_TABLE;

	$conn = new mysqli(CONFIG_DB_SERVER, CONFIG_DB_USERNAME, CONFIG_DB_PASSWORD, CONFIG_DB_DATABASE);
	if ($conn->connect_error) {
		lwrite(CONFIG_AUTHLOG_FILE, "Could not connect to SQL database for user ".$onsen_username);
		print "It looks like the SQL database is offline";
		print '<script type="text/javascript">setTimeout(function () {window.history.back();}, 2000);</script>';
		return;
	}

	// First check if the account you're trying to log in to is locked
	$cmd = "SELECT `id`, `username`, `password`, `failed_logins`, `last_login`, `locked`, `pref_css` FROM `" . CONFIG_DB_TABLE . "` WHERE `username`='$onsen_username'";
	$result=$conn->query($cmd);
	if (!$result) {
		lwrite(CONFIG_AUTHLOG_FILE, "Could not query SQL database for user ".$onsen_username);
		print "Something went wrong with your request. . . email someone who can fix it";
		print '<script type="text/javascript">setTimeout(function () {window.history.back();}, 2000);</script>';
		return;
	}
	$row = $result->fetch_assoc();
	$sql_id = $row["id"];
	$sql_username = $row["username"];
	$sql_password = $row["password"];
	$sql_failed_logins = $row["failed_logins"];
	$sql_locked = $row["locked"];
	$sql_last_login = $row["last_login"];
	$sql_pref_css = $row["pref_css"];

	// Handling incorrect usernames
	if ($result->num_rows === 0) {
		lwrite(CONFIG_AUTHLOG_FILE, "Failed log-in attempt for non-existent user ".$onsen_username." from ".$_SERVER['REMOTE_ADDR']);
		print "I cannot find an account like that!";
		print '<script type="text/javascript">setTimeout(function () {window.history.back();}, 2000);</script>';
		return;
	}
	// Handling locked accounts
	if ($sql_locked == "y") {
		lwrite(CONFIG_AUTHLOG_FILE, "Failed log-in attempt for locked user ".$onsen_username." from ".$_SERVER['REMOTE_ADDR']);
		print "Your account is locked, please try again in a few minutes~";
		print '<script type="text/javascript">setTimeout(function () {window.history.back();}, 2000);</script>';
		return;
	}
	// Handling incorrect passwords

	$salt = substr($sql_password, 3-mb_strlen($sql_password), -129);
	if (!password_verify($onsen_password, $sql_password)) {
		lwrite(CONFIG_AUTHLOG_FILE, "Failed log-in attempt for ".$onsen_username." from ".$_SERVER['REMOTE_ADDR']);
		$failed_logins = $row["failed_logins"]+1;
		print "You have failed to log in $failed_logins times";
		$cmd = "UPDATE `onsen` SET `failed_logins`='$failed_logins' WHERE `username`='$sql_username'";
		$conn->query($cmd);
		print '<script type="text/javascript">setTimeout(function () {window.history.back();}, 2000);</script>';
		return;
	}

	lwrite(CONFIG_AUTHLOG_FILE, "Successful login for ".$sql_username." from ". $_SERVER['REMOTE_ADDR']);
	// Set the number of failed logins to 0
	$cmd = "UPDATE `onsen` SET `failed_logins`=0 WHERE `username`='$sql_username'";
	$conn->query($cmd);

	// Set preferred css style
	$_SESSION['pref_css'] = $sql_pref_css;
	reloadUserStyle();

	// Set the last_login date to today
	$today = date("Y-m-d H:i:s");
	$cmd = "UPDATE `" . CONFIG_DB_TABLE . "` SET `last_login`='$today' WHERE `username`='$sql_username'";
	$conn->query($cmd);

	// Set the User ID, effectively logging the user in
	$_SESSION['user_id'] = $sql_id;
	$_SESSION['username'] = $sql_username;
	print "Welcome home $sql_username!</br>You last logged in on $sql_last_login";
	if ($sql_failed_logins > 0)
		print "</br>There were ".$sql_failed_logins." failed login attempts since the last successful login";
	print '<script type="text/javascript">setTimeout(function () {window.location.href = ".";}, 2000);</script>';
	die;	
}
?>
<div id="header">
	<div style="width:50%;float:left;"><a href="../">« back</a></div>
	<div style="width:50%;float:left;text-align:right;"><a href="acc_create.php">create an account</a></div>
</div>
<p>
Please log in ～
</p>
<?php
echo('<form action="login.php'.$uri.'" method="post">');
?>
	<label for="onsen_username">Username</label></br>
	<input type="text" id="onsen_username" name="onsen_username" value="" maxlength="20" /></br>
	<label for="onsen_password">Password</label></br>
	<input type="password" id="onsen_password" name="onsen_password" maxlength="50" /></br></br>
	<input type="submit" value="Login»" />
</form>
</div>
</div>

</body>
</HTML>
