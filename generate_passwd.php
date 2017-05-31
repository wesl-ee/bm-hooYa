<?php include $_SERVER['DOCUMENT_ROOT']."/bmffd/includes/core.php"; ?>
<HTML>
<head>
	<?php include CONFIG_ROOT_PATH."includes/head.php";?>
	<title>bmffd — password change</title>
</head>
<BODY>

<div id="container">
<div id="left_frame">
<img id="mascot" src=<?php echo $_SESSION['mascot'];?>>
</div>
<div id="right_frame">
<h1 style="text-align:center;">the bath house</h1>
<a href='.'>go back »</a>
<form action="generate_passwd.php" method="post">
</br><label>new password</label></br>
<input type="password" id="onsen_new_password" name="onsen_new_password" maxlength="50" /></br>
<input type="submit" value="Calculate my hash"/>
</form>
<p>
Please send me the following hash with your username:
</p>
<textarea style="width:100%;resize:none;">
<?php if (isset($_POST['onsen_new_password'])){
	$salt = randomHex(16);
	$hash = $_POST['onsen_new_password'];
	$hash = crypt($hash, '$6$'.$salt.'$');
	$salted_password = $hash;
	echo $salted_password;
}
?>
</textarea>
</div>
</div>
</body>
</HTML>