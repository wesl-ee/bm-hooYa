<!DOCTYPE html>
<HTML>
<?php
include "../includes/core.php";

?>
<head>
	<?php include CONFIG_ROOT_PATH."includes/head.php" ?>
	<title>bmffd — message pile</title>
</head>
<body>
<div id="container">
<div id="left_frame">
	<div id="logout">
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
	<img id="mascot" src=<?php echo $_SESSION['mascot'];?>>
</div>
<div id="right_frame">
<div id="title">
	<h1>Message pile</h1>
</div>
<div id="title">
	<h3>a place to report bugs, and bitch</h3>
</div>
<div id="header" style="margin-right:20px;">
	<div style="width:33%;float:left;"><a href="../">« back</a></div>
	<div style="width:33%;float:left;text-align:center;">&nbsp;</div>
	<div style="width:33%;float:left;text-align:right;"><a href="add.php">add a message</a></div>
</div>
<table style="width:100%;table-layout:fixed;">
	<tr>
	<th style="text-align:left;width:20%;">Resolved</th>
	<th style="text-align:left;">Timestamp</th>
	<th style="text-align:left;">Head</th>
	</tr>
	<tr>
	<td>&nbsp</td>
	<td>&nbsp</td>
	<td>&nbsp</td>
	</tr>
<?php
	$conn = new mysqli(CONFIG_DB_SERVER, CONFIG_DB_USERNAME, CONFIG_DB_PASSWORD, CONFIG_DB_DATABASE);
	if ($conn->connect_error) {
		die();
	}
	$cmd = "SELECT id, timestamp, head, resolved FROM message_pile ORDER BY timestamp DESC";
	$result = $conn->query($cmd);
	while ($row = $result->fetch_assoc()) {
		print('<tr>');
		if ($row['resolved'] == 'n')
			print('<td><span style="color:red">✓</span></td>');
		else
			print('<td><span style="color:green">◍</span></td>');
		print('<td>'.$row['timestamp'].'</td>');
		print('<td style=""><a href="inspect.php?id='.$row['id'].'">'.$row['head'].'</a></td>');
		print('</tr>');
	}
?>
</table>
</div>
</div>
</body>
</HTML>
