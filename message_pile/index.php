<?php
/*
MicroTXT - A tiny PHP Textboard Software
Copyright (c) 2016 Kevin Froman (https://ChaosWebs.net/)

MIT License
*/
include "../includes/core.php";
// If you're not properly authenticated then kick the user back to login.php
if (CONFIG_REQUIRE_AUTHENTICATION)
	include "includes/auth.php";
include('php/settings.php');
include('php/csrf.php');
include('php/sqlite.php');
?>
<!DOCTYPE HTML>
<html>
<head>
	<meta charset='utf-8'>
	<title><?php echo $siteTitle; ?></title>
	<?php include CONFIG_ROOT_PATH."includes/head.php"; ?>
	<link rel='stylesheet' href='theme.css'>
</head>
<body>
<div id="container">
	<div id="left_frame">
		<div id="logout" style="border-top:0px;">
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
	<div style="width:100%;float:left;"><h1 class='center logo'><?php echo $siteTitle;?></h1></div>
	<div id='postList'>
		<h3>Threads:</h3><br>
		<table><tr><th>Title</th><th>Author</th></tr>
		<?php
		function startsWith($haystack, $needle){
	     $length = strlen($needle);
	     return (substr($haystack, 0, $length) === $needle);
		 }

		$max = 0; // Largest thread number in database
		$threadDisplayCount = 1;
		$countReached = false;
		$lastID = 1;
		if (! isset($_GET['range'])){
			$requestRange = 0;
		}
		else{
			$requestRange = $_GET['range'];
		}

		$requestRange = $db->escapeString($requestRange);

	 $sql =<<<EOF
		 SELECT MAX(ID) from threads;
EOF;
$ret = $db->query($sql);

if ($ret == false){
	echo '<p style="color: red;">No posts!</p>';
}

   while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
		 $max = $row['MAX(ID)'];
	 }

	 // Get all threads within the specified range

	 $sql =<<<EOF
	   SELECT * FROM Threads ORDER BY ROWID DESC LIMIT $threadListLimit OFFSET $requestRange;
EOF;
	$ret = $db->query($sql);
	while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
		if (startsWith($row['TITLE'], '.') == false){
			echo '<tr><td><a href="view.php?post=' . $row['TITLE'] . '">' . $row['TITLE'] . '</a></td><td>' . $row['AUTHOR'] . '</td></tr>';
			$lastID = $row['ID'];
		}
	}
	echo '</table>';

	if ($requestRange > 0){
			echo '<a href="index.php?range=' . ($requestRange - $threadListLimit) . '"><button>Back</button></a>';
	}
	if (intval($lastID) != 1){
		echo '<br><br><a href="index.php?range=' . ($requestRange + $threadListLimit) . '"><button>Next</button></a>';
	}

	$db->close();
?>
	</div>
	<?php
	$error = '';
	if ($motd)
	{
		echo '<div class="motd">' . file_get_contents('motd.txt') . '</div>';
	}
	if (isset($_SESSION['mtPostError'])){
		if ($_SESSION['mtPostError']){
			if (isset($_SESSION['mtPostErrorTxt'])){
				$error = htmlentities($_SESSION['mtPostErrorTxt']);
			}
			echo '<div style="color: red; text-align: center; margin-bottom: 1em;">There was an error publishing your post: ' . $error . '</div>';
			$_SESSION['mtPostError'] = false;
		}
	}
	?>
	<div class='postForm'>
		<form method='post' action='submit.php'>
			<div style="display:table;width:50%;margin:auto;">
			<div style="display:table-row;">
				<div style="display:table-cell;width:50%;text-align:right;">Title:</div>
				<div style="display:table-cell;width:50%;text-align:left;padding-left:20px;"><input required type='text' name='title' maxlength='20'></div>
			</div>
			<div style="display:table-row;">
				<div style="display:table-cell;width:50%;text-align:right;">Name:</div>
				<div style="display:table-cell;width:50%;text-align:left;padding-left:20px;"><?php echo $_SESSION['username']?></div>
			</div>
			<div style="display:table-row;">
				<div style="display:table-cell;width:50%;text-align:right;">Tripcode:</div>
				<div style="display:table-cell;width:50%;text-align:left;padding-left:20px;"><input type='password' name='tripcode' maxlength='20' placeholder="optional"></div>
			</div>
			</div>
			<textarea required name='text' maxlength='100000' rows='10' style="width:50%;"></textarea>
			<input type='hidden' name='CSRF' value='<?php echo $CSRF;?>'>
			<br>
			<?php
			if ($captcha) {
				if (! isset($_SESSION['currentPosts']))
				{
					$_SESSION['currentPosts'] = $postsBeforeCaptcha;
				}
				if ($_SESSION['currentPosts'] >= $postsBeforeCaptcha)
				{
					echo '<img src="php/captcha.php" alt="captcha">';
					echo '<br><br><label>Captcha Text: <input required type="text" name="captcha" maxlength="10"></label><br><br>';
				}
			}
			?>
			<input type='submit' value='Post' class='submitPostButton'>
		</form>
	</div>
<?php
if ($keepSessionAlive == true){
	echo '<iframe src="keep-alive.php" style="display: none;"></iframe>';
}
?>
</div>
</body>
</html>
