<HTML>
<?php
include "../includes/core.php";

$id = $_GET['id'];

$conn = new mysqli(CONFIG_DB_SERVER, CONFIG_DB_USERNAME, CONFIG_DB_PASSWORD, CONFIG_DB_DATABASE);
if ($conn->connect_error) {
	die();
}
$cmd = "SELECT timestamp, head, body, username, resolved FROM message_pile WHERE id=".$id;
$result = $conn->query($cmd);
if ($result->num_rows === 0) {
	print('Could not find such a message');
	return;
}
$row = $result->fetch_assoc();
?>
<head>
	<?php include("../includes/head.php") ?>
	<title>bmffd — message #<?php echo $id?></title>
</head>
<body>
<div id="container">
<div id="left_frame">
	<div id="logout">
		<?php
		if (isset($_SESSION['username'])) {
			print('<a href="'.CONFIG_DOCUMENT_ROOT_PATH.'">home</a></br>');
			print('<a href="'.CONFIG_DOCUMENT_ROOT_PATH.'"logout.php">logout</a>');
		}
		else {
			print('<a href="'.CONFIG_DOCUMENT_ROOT_PATH.'login.php?ref='.$_SERVER['REQUEST_URI'].'">login</a>');
		}
		?>
	</div>
	<img id="mascot" src=<?php echo $mascot;?>>
</div>
<div id="right_frame" style="padding-right:20px;">
<div id="title">
	<h1>Message pile</h1>
</div>
<div id="header">
	<div style="width:33%;float:left;"><a href="./">« back</a></div>
	<div style="width:33%;float:left;text-align:center;"><?php print '<span style="font-weight:bold;">'.$row['head'] .'</span>'?></div>
	<div style="width:33%;float:left;text-align:right;"><?php print $row['username'].'<br/>'.$row['timestamp']?></div>
</div>
<?php
	// Throw some resolution indicator on-screen
	print('<p>'.nl2br($row['body']).'</p>');
?>
</div>
</div>
</div>
</body>
</HTML>
