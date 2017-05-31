<?php
include "../includes/core.php";
// If you're not properly authenticated then kick the user back to login.php
if (CONFIG_REQUIRE_AUTHENTICATION)
	include CONFIG_ROOT_PATH."includes/auth.php";

if (isset($_POST['head']) && isset($_POST['body'])) {
	if (isset($_SESSION['username']))
		$username = $_SESSION['username'];
	else
		$username = "anonymous";
	$head = filter_var($_POST['head'], FILTER_SANITIZE_STRING);
	$body = filter_var($_POST['body'], FILTER_SANITIZE_STRING);
	// Move this into message_stack.php sometime
	$conn = new mysqli(CONFIG_DB_SERVER, CONFIG_DB_USERNAME, CONFIG_DB_PASSWORD, CONFIG_DB_DATABASE);
	if ($conn->connect_error) {
		die();
	}
	$cmd = "INSERT INTO message_pile (username, head, body) VALUES (\"".$username."\",\"".$head."\",\"".$body."\")";
	lwrite(CONFIG_ACCESSLOG_FILE, $username." put a message in the pile from ".$_SERVER['REMOTE_ADDR']);
	$conn->query($cmd);
	header('Location: .');
}
?>
<HTML>
<head>
	<?php include CONFIG_ROOT_PATH."includes/head.php"; ?>
	<title>bmffd — add to pile</title>

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
	<img id="mascot" src=<?php echo $_SESSION['mascot'];?>>
</div>
<div id="right_frame">
<div id="title">
	<h1>Message stack</h1>
</div>
<div style="padding-bottom:20px;">
	<a href="./">« back</a>
</div>
<form action="add.php" method="post" id="add">
	<label for="head">Subject</label></br>
	<input style="width:40%;" name="head" type="text" maxlength="40"></input>
	<input style="width:10%;" type="submit" value="Submit"></input>
</form>
Body</br>
<textarea id="textarea" style="width:50%;white-space:pre-wrap;" form="add" name="body" maxlength="1000"></textarea>
</div>
</div>
<script>
	var textarea = document.getElementById("textarea");
	var heightLimit = 50;
	textarea.oninput = function() {
	textarea.style.height = "";
	textarea.style.height = textarea.scrollHeight + "px";
	};
</script>
</body>
</HTML>
