<?php
include "includes/core.php";
?>
<head>
	<?php include("includes/head.php") ?>
</head>
<body>
<div class="logout">
        <a href="/">home</a></br>
        <a href="userprefs.html">user preferences</a>
</div>

<h1 style="text-align:center;">User preferences</h1>
<div class="frame">
	<div class="upperLeft" style="line-height:1.5em;">
		<a href="filemanager.php">« back</a>
	</div>
	<div id="left" style="line-height:2.2em;">
		CSS Style</br>
	</div>
	<div id="right" style="margins:auto;">
		<form action="updatestyle.php" method="post">
		<select name="pref_css" onchange="this.form.submit()">
		<option <?php if ($curr_css=="classic") echo "selected" ?> value="classic">Classic</option>
		<option <?php if ($curr_css=="wu_tang") echo "selected" ?> value="wu_tang">Wu-Tang</option>
		<option <?php if ($curr_css=="gold") echo "selected" ?> value="gold">Golden</option>
		</select>
	</div>
</div>
<img id="mascot" src=<?php echo $mascot;?>>
</body>
</HTML>