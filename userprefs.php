<?php
include "includes/core.php";
?>
<head>
	<?php include("includes/head.php") ?>
	<title>bmffd — preferences</title>
</head>
<body>
<div id="container">
<div id="left_frame">
	<div id="logout">
		<?php
		if (isset($_SESSION['username'])) {
			print('<a href="../">home</a></br>');
			print('<a href="../logout.php">logout</a>');
		}
		else {
			print('<a href="'.CONFIG_DOCUMENT_ROOT_PATH.'login.php?ref='.$_SERVER['REQUEST_URI'].'">login</a>');
		}
		?>
	</div>
<img id="mascot" src=<?php echo $mascot;?>>
</div>
<div id="right_frame">
<h1 style="text-align:center;">User preferences</h1>
	<div style="padding-bottom:20px;">
		<a href="index.php">« back</a>
	</div>
	<div style="float:left;width:50%;">
		CSS Style</br></br>

	</div>
	<div style="float:left;width:50%">
		<form action="updatestyle.php" method="post">
		<select name="pref_css" onchange="this.form.submit()">
		<option <?php if ($curr_css=="classic") echo "selected" ?> value="classic">Classic</option>
		<option <?php if ($curr_css=="default") echo "selected" ?> value="default">Default</option>
		<option <?php if ($curr_css=="gold") echo "selected" ?> value="gold">Gold</option>
		<option <?php if ($curr_css=="nier") echo "selected" ?> value="nier">Nier</option>
		<option <?php if ($curr_css=="red") echo "selected" ?> value="red">Red</option>
		<option <?php if ($curr_css=="yys") echo "selected" ?> value="yys">Yuyushiki</option>
		<option <?php if ($curr_css=="wu_tang") echo "selected" ?> value="wu_tang">Wu-tang</option>
		</select>
	</div>
	<div style="float:left;width:100%">
		<a href="change_password.php">Change Password</a>
	</div>
</div>

</div>
</body>
</HTML>