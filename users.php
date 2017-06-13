<!DOCTYPE html>
<?php include $_SERVER['DOCUMENT_ROOT']."/bmffd/includes/core.php";
if (CONFIG_REQUIRE_AUTHENTICATION)
        include CONFIG_ROOT_PATH."includes/auth.php";
?>
<HTML>
<head>
	<?php include CONFIG_ROOT_PATH."includes/head.php";?>
	<title>bmffd — directory</title>
</head>
<BODY>

<div id="container">
<div id="left_frame">
<img id="mascot" src=<?php echo $_SESSION['mascot'];?>>
</div>
<div id="right_frame">
<h1 style="text-align:center;">the big mike directory</h1>
<div id="header">
<a href='.'>« back</a>
</div>
<div style="width:33%;float:left;font-weight:bold;">username</div>
<div style="width:33%;float:left;font-weight:bold;">tags added</div>
<div style="width:33%;float:left;font-weight:bold;">join date</div>
<div style="width:100%;float:left;">&nbsp</div>
<?php
	$mysql_hostname = CONFIG_DB_SERVER;
	$mysql_username = CONFIG_DB_USERNAME;
	$mysql_password = CONFIG_DB_PASSWORD;
	$mysql_dbname = CONFIG_DB_DATABASE;
	$mysql_table = CONFIG_DB_TABLE;
	$conn = new mysqli(CONFIG_DB_SERVER, CONFIG_DB_USERNAME, CONFIG_DB_PASSWORD, CONFIG_DB_DATABASE);
	$cmd = "SELECT `username`, `signup_date`, `tags_added`, `pref_css`, `domain` FROM `" . CONFIG_DB_TABLE . "` ORDER BY tags_added DESC";
	$result=$conn->query($cmd);
	foreach ($result as $row) {
		print '<div style="width:33%;float:left;"><a href="mailto:'.$row["username"].'@'.$row["domain"].'" >'.$row["username"].'</a></div>';
		print '<div style="width:33%;float:left;">'.$row["tags_added"].'</div>';
		print '<div style="width:33%;float:left;">'.explode(' ', $row["signup_date"])[0].'</div>';
	}
?>
</div>
</div>
</body>
</HTML>