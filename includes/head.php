<?php
$_SESSION['last_activity'] = new DateTime();
if (!isset($_SESSION['username']))
	$_SESSION['username'] = 'anon';
if (!isset($_SESSION['pref_css']))
	updateUserStyle();
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<meta name="robots" content="noindex">
<link rel="stylesheet" href=<?php echo $_SESSION['stylesheet']?>>
<link rel="shortcut icon" type="image/x-icon" href="img/favicon.ico"/>
