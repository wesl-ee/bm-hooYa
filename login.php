<?php
include "includes/core.php";

// Was the user refered by some other link?
if (isset($_GET['ref']))
	$uri = "?ref=".urlencode($_GET['ref']);
?>
<HTML>
<head>
	<?php include("./includes/head.php") ?>
	<title>bmffd — login</title>
	<link rel="stylesheet" href=<?php echo $stylesheet?>>
</head>
<body>

<div id="container">
<div id="left_frame">
<img id="mascot" src=<?php echo $mascot;?>>
</div>
<div id="right_frame">
<div id="title">
<h1 style="text-align:center;">the bath house</h1>
</div>
<div class="entry">
<p>
<a href="../">« back</a>
</p>
<p>
Please log in ～
</p>
<?php
echo('<form action="login_submit.php'.$uri.'" method="post">');
?>
	<label for="onsen_username">Username</label></br>
	<input type="text" id="onsen_username" name="onsen_username" value="" maxlength="20" /></br>
	<label for="onsen_password">Password</label></br>
	<input type="password" id="onsen_password" name="onsen_password" maxlength="50" /></br></br>
	<input type="submit" value="Login»" />
</form>
</div>
</div>
</div>

</body>
</HTML>
