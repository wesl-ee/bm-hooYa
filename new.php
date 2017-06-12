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
<div style="width:50%;float:left;display:table-cell;"><a href="https://bigmike.sne.jp/bmffd/filemanager.php?dir=%2FAnime%2FPuriPuri+Chii-chan%21%21/">Anime/PuriPuri Chii chan!! (weekly)</a></div>
<div style="width:50%;float:left;display:table-cell;">06.09.2017</div>

<div style="width:50%;float:left;display:table-cell;"><a href="https://bigmike.sne.jp/bmffd/filemanager.php?dir=%2FAnime%2FAria+The+Animation+%5BBD+1080p+FLAC%5D+%5BSallySubs%5D/">Anime/Aria</a></div>
<div style="width:50%;float:left;display:table-cell;">06.09.2017</div>

<div style="width:50%;float:left;display:table-cell;"><a href="https://bigmike.sne.jp/bmffd/filemanager.php?dir=%2FAnime%2FHinako+Note/">Anime/Hinako Note (weekly)</a></div>
<div style="width:50%;float:left;display:table-cell;">06.09.2017</div>

<div style="width:50%;float:left;display:table-cell;"><a href="https://bigmike.sne.jp/bmffd/filemanager.php?dir=%2FVideo+that+isn%27t+Anime%2FYes+Man+%282008%29%2F">Movie/Yes Man (2008)</a></div>
<div style="width:50%;float:left;display:table-cell;">06.09.2017</div>

<div style="width:50%;float:left;display:table-cell;"><a href="https://bigmike.sne.jp/bmffd/filemanager.php?dir=%2FVideo+that+isn%27t+Anime%2FCrank+%282006%29+%5B1080p%5D/">Movie/Crank (2006)</a></div>
<div style="width:50%;float:left;display:table-cell;">05.31.2017</div>
</div>
</div>
</div>
</body>
</html>