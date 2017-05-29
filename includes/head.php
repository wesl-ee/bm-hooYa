<?php
// Set up variables that will define the page's style depending
// on the pref_css PHP session variable
switch($_SESSION['pref_css']) {
	case "classic":
		$curr_css="classic";
		$stylesheet=CONFIG_DOCUMENT_ROOT_PATH."css/style_suckless.css";
		$mascot=CONFIG_DOCUMENT_ROOT_PATH."img/rei.png";
		$motd="お帰りなさい";
		break;
	case "gold":
		$curr_css="gold";
		$stylesheet=CONFIG_DOCUMENT_ROOT_PATH."css/style_suckless_gold.css";
		$mascot=CONFIG_DOCUMENT_ROOT_PATH."img/yui.png";
		$motd="おかえりなさい";
		break;
	case "wu_tang":
		$curr_css="wu_tang";
		$stylesheet=CONFIG_DOCUMENT_ROOT_PATH."css/style_suckless_wutang.css";
		$mascot=CONFIG_DOCUMENT_ROOT_PATH."img/ghost.png";
		$motd="Protect ya neck";
		break;
	case "red":
		$curr_css="red";
		$stylesheet=CONFIG_DOCUMENT_ROOT_PATH."css/style_suckless_red.css";
		$mascot=CONFIG_DOCUMENT_ROOT_PATH."img/mao.png";
		$motd="为人民服务";
		break;
	case "nier":
		$curr_css="nier";
		$stylesheet=CONFIG_DOCUMENT_ROOT_PATH."css/style_suckless_nier.css";
		$mascot=CONFIG_DOCUMENT_ROOT_PATH."img/2B.png";
		$motd="おかえりなさい";
		break;
	case "yys":
	default:
		$curr_css="yys";
		$stylesheet=CONFIG_DOCUMENT_ROOT_PATH."css/style_suckless_yys.css";
		$mascot=CONFIG_DOCUMENT_ROOT_PATH."img/yuzuko.png";
		$motd="よぉ";
		break;
	case "default":
//	default:
		$curr_css="default";
		$stylesheet=CONFIG_DOCUMENT_ROOT_PATH."css/style_suckless_default.css";
		$motd="Welcome home";
}
$_SESSION['last_activity'] = new DateTime();
?>
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link rel="stylesheet" href=<?php echo $stylesheet?>>
<link rel="shortcut icon" type="image/x-icon" href="img/favicon.ico"/>