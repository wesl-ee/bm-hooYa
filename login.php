<?php
include "includes/core.php";
?>
<HTML>
<head>
	<?php include("./includes/head.php") ?>
	<title>bmffd — login</title>
	<link rel="stylesheet" href=<?php echo $stylesheet?>>
</head>
<body>
<h1 style="text-align:center;">the bath house</h1>
<div id="frame">
<div class="entry">
<p>
<a href="../">« back</a>
</p>
<p>
Please log in~
</p>
<?php
echo('<form action="login_submit.php'.$uri.'" method="post">');
?>
	<p>
	<label for="onsen_username">Username</label>
	<input type="text" id="onsen_username" name="onsen_username" value="" maxlength="20" />
	</p>
	<p>
	<label for="onsen_password">Password</label>
	<input type="password" id="onsen_password" name="onsen_password" maxlength="50" />
	</p>
	<p>
	<input type="submit" value="Login»" />
</form>

</div>
</div>
<img id="mascot" src=<?php echo $mascot;?>>
</body>
</HTML>
