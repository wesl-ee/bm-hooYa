<!DOCTYPE html>
<HTML>
<?php
include "includes/core.php";

?>
<head>
	<?php include CONFIG_ROOT_PATH."includes/head.php" ?>
	<title>bmffd — new</title>
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
	<h1>Recent Curations</h1>
</div>
<div class="header" style="padding-bottom:20px;">
	<a href=".">« back</a>
</div>
<div style="display:table;">
<div style="width:50%;float:left;display:table-cell;">Nothing added recently!</div>
<div style="width:50%;float:left;display:table-cell;">&nbsp</div>

</div>
</div>
</div>
</body>
</html>