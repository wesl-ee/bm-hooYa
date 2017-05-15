<?php
include "includes/core.php";
// Changes current user's POSTED password
function change_password()
{
	if (!isset($_POST['onsen_curr_password'], $_POST['onsen_new_password'], $_POST['onsen_confirm_password'])) return 'Please enter all fields!';

	if ($_POST['onsen_new_password'] != $_POST['onsen_confirm_password']) return 'New passwords do not match';

	if (strlen($_POST['onsen_new_password']) > 50) return 'Invalid length for new password~';

	if (ctype_alnum($_POST['onsen_new_password']) != true) return 'Only alphanumeric characters please~';

	$onsen_curr_password = filter_var($_POST['onsen_curr_password'], FILTER_SANITIZE_STRING);
	$onsen_new_password = filter_var($_POST['onsen_new_password'], FILTER_SANITIZE_STRING);

	$onsen_username = $_SESSION['username'];
	$onsen_curr_password = hash("sha512", $onsen_curr_password, FALSE);
	$onsen_new_password = hash("sha512", $onsen_new_password, FALSE);

	$conn = new mysqli(CONFIG_DB_SERVER, CONFIG_DB_USERNAME, CONFIG_DB_PASSWORD, CONFIG_DB_DATABASE);
	if ($conn->connect_error) {
		$out = "It looks like the SQL database is offline";
		return $out;
	}

	// First check if the account you're trying to log in to is locked
	$cmd = "SELECT `id`, `username`, `password`, `failed_logins`, `last_login`, `locked`, `pref_css` FROM `" . CONFIG_DB_TABLE . "` WHERE `username`='$onsen_username'";
	$result=$conn->query($cmd);
	if (!$result) {
		$out = "Something went wrong with your request. . . email someone who can fix it";
		$out .= "Query: $cmd";
		return $out;
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
		$out = "I cannot find your account!";
		return $out;
	}
	// Handling locked accounts
	if ($sql_locked == "y") {
		$out = "Your account is locked, please try again in a few minutes~";
		return $out;
	}
	// Handling incorrect passwords
	if ($sql_password != $onsen_curr_password) {
		return "Your password is incorrect!";
	}
	$cmd = "UPDATE `onsen` SET `password`='$onsen_new_password' WHERE `username`='$sql_username'";
	$conn->query($cmd);

	return "Successfully changed password!</br><a href='logout.php'>log back in »</a>";
}
$out = change_password();
?>
<HTML>
<head>
	<?php include("./includes/head.php") ?>
	<title>bmffd — change password</title>
</head>
<BODY>
<h1 style="text-align:center;">the bath house</h1>
<div id="frame">
<a href="change_password.php">« back</a>
<p>
<?php
	echo($out."</br>");
?>
</p>
</div>
<img id="mascot" src=<?php echo $mascot;?>>
</body>
</HTML>
