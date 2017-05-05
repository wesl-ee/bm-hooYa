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
	<div style="padding-bottom:20px;">
		<a href="filemanager.php">« back</a>
	</div>
	<div style="float:left;width:50%;">
		CSS Style</br>
	</div>
	<div style="margins:auto;float:left;width:50%;">
		<form action="updatestyle.php" method="post">
		<select name="pref_css" onchange="this.form.submit()">
		<option <?php if ($curr_css=="classic") echo "selected" ?> value="classic">Classic</option>
		</select>
	</div>
</div>
<img id="mascot" src=<?php echo $mascot;?>>
</body>
</HTML>