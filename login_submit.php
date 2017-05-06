<?php
include "includes/core.php";
// Authenticates POSTED user and password credentials
function login()
{
	if (isset($_SESSION['user_id'])) return 'You are already logged in!';

	elseif (!isset($_POST['onsen_username'], $_POST['onsen_password'])) return 'Please enter a valid username and password!';

	elseif (strlen($_POST['onsen_username']) > 20 || strlen($_POST['onsen_password']) > 50) return 'Invalid length for username or password~';

	elseif (ctype_alnum($_POST['onsen_username']) != true || ctype_alnum($_POST['onsen_password']) != true) return 'Only alphanumeric characters please~';

	else {
		$onsen_username = filter_var($_POST['onsen_username'], FILTER_SANITIZE_STRING);
		$onsen_password = filter_var($_POST['onsen_password'], FILTER_SANITIZE_STRING);

		$onsen_password = hash("sha512", $onsen_password, FALSE);

		$mysql_hostname = CONFIG_DB_SERVER;
		$mysql_username = CONFIG_DB_USERNAME;
		$mysql_password = CONFIG_DB_PASSWORD;
		$mysql_dbname = CONFIG_DB_DATABASE;

		$conn = new mysqli($mysql_hostname, $mysql_username, $mysql_password, $mysql_dbname);
		if ($conn->connect_error) {
			$out = "I cannot access your account";
			return $out;
		}

		// First check if the account you're trying to log in to is locked
		$cmd = "SELECT `id`, `username`, `password`, `failed_logins`, `last_login`, `locked`, `pref_css` FROM `onsen` WHERE `username`='$onsen_username'";
		$result=$conn->query($cmd);
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
			$out = "I cannot find an account like that!";
			return $out;
		}
		// Handling locked accounts
		if ($sql_locked == "y") {
			$out = "Your account is locked, please try again in a few minutes~";
			return $out;
		}
		// Handling incorrect passwords
		if ($sql_password != $onsen_password) {
			$failed_logins = $row["failed_logins"]+1;
			$out = "You have failed to log in $failed_logins times";
			$cmd = "UPDATE `onsen` SET `failed_logins`='$failed_logins' WHERE `username`='$sql_username'";
			$conn->query($cmd);
			return $out;
		}
		// Set the number of failed logins to 0
		$cmd = "UPDATE `onsen` SET `failed_logins`=0 WHERE `username`='$sql_username'";
		$conn->query($cmd);

		// Set preferred css style
		// Default to the Wu-Tang Style
		$_SESSION['pref_css'] = $sql_pref_css;
		if (empty($sql_pref_css)) {
			$_SESSION['pref_css'] = "original";
		}

		// Set the last_login date to today
		$today = date("Y-n-j");
		$cmd = "UPDATE `onsen` SET `last_login`='$today' WHERE `username`='$sql_username'";
		$conn->query($cmd);

		// Set the User ID, effectively logging the user in
		$_SESSION['user_id'] = $sql_id;
		$_SESSION['username'] = $sql_username;
		$out = "Welcome home $sql_username!</br>You last logged in on ".$sql_last_login;
		if ($sql_failed_logins > 0)
			$out .= "</br>There were ".$sql_failed_logins." failed login attempts since the last successful login";
		return $out;
/*		$_SESSION['user_id'] = 0;
		$_SESSION['username'] = "nakomi";
		$out = "Welcome home nakomi!</br>";
		return $out;*/

	}
}
$out = login();
?>
<HTML>
<head>
	<!-- Japanese text will not show if we don't explicitly use utf-8-->
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<!-- style.css is meant to be a site-wide style-sheet -->
<!--	<link rel="stylesheet" href="/style_suckless.css"/>-->
	<?php include($_SERVER['DOCUMENT_ROOT']."/head.php") ?>
	<link rel="stylesheet" href=<?php echo $stylesheet?>>
</head>
<BODY>
<h1 style="text-align:center;">the bath house</h1>
<div id="frame">
<p>
<?php
	echo($out."</br>");
	if (!isset($_SESSION['user_id'])) {
		echo("<a href='login.php'>go back »</a>");
	}
	else if (isset($_GET['ref'])) {
		echo ('<a href="'.$_GET['ref'].'">continue to the bath house »</a>');
	}
	else {
		echo("<a href='index.php'>continue to the bath house »</a>");
	}
?>
</p>
</div>
<img id="mascot" src=<?php echo $mascot;?>>
</body>
</HTML>
