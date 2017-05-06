<?php
// Set up variables that will define the page's style depending
// on the pref_css PHP session variable
switch($_SESSION['pref_css']) {
	case "classic":
		$curr_css="classic";
		$stylesheet="css/style_suckless.css";
		$mascot="/bmffd/img/rei.png";
		break;
	case "gold":
		$curr_css="gold";
		$stylesheet="css/style_suckless_gold.css";
		$mascot="/bmffd/img/yui.png";
		$motd="ゆゆ式";
		break;
	case "wu_tang":
		$curr_css="wu_tang";
		$stylesheet="css/style_suckless_wutang.css";
		$mascot="/bmffd/img/ghost.png";
		$motd="Daily reminder: Protect ya neck";
		break;
	default:
		$curr_css="classic";
		$stylesheet="css/style_suckless.css";
		$mascot="/bmffd/img/rei.png";
}
?>
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link rel="stylesheet" href=<?php echo $stylesheet?>>
<link rel="shortcut icon" type="image/x-icon" href="img/favicon.ico"/>