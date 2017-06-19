<?php
// Set up variables that will define the page's style depending
// on the pref_css PHP session variable

switch($_SESSION['pref_css']) {
	case "classic":
		$_SESSION['stylesheet']=CONFIG_DOCUMENT_ROOT_PATH."css/style_suckless.css";
		$_SESSION['mascot']=CONFIG_DOCUMENT_ROOT_PATH."img/rei.png";
		$_SESSION['motd']="お帰りなさい";
		break;
	case "gold":
		$_SESSION['stylesheet']=CONFIG_DOCUMENT_ROOT_PATH."css/style_suckless_gold.css";
		$_SESSION['mascot']=CONFIG_DOCUMENT_ROOT_PATH."img/yui.png";
		$_SESSION['motd']="おかえりなさい";
		break;
	case "wu_tang":
		$_SESSION['stylesheet']=CONFIG_DOCUMENT_ROOT_PATH."css/style_suckless_wutang.css";
		$_SESSION['mascot']=CONFIG_DOCUMENT_ROOT_PATH."img/ghost.png";
		$_SESSION['motd']="Protect ya neck";
		break;
	case "red":
		$_SESSION['stylesheet']=CONFIG_DOCUMENT_ROOT_PATH."css/style_suckless_red.css";
		$_SESSION['mascot']=CONFIG_DOCUMENT_ROOT_PATH."img/mao.png";
		$_SESSION['motd']="为人民服务";
		break;
	case "default":
		$_SESSION['stylesheet']=CONFIG_DOCUMENT_ROOT_PATH."css/style_suckless_yys.css";
		$_SESSION['motd']="Welcome home";
		$_SESSION['mascot']="";
		break;
	case "nier":
		$_SESSION['stylesheet']=CONFIG_DOCUMENT_ROOT_PATH."css/style_suckless_nier.css";
		$_SESSION['mascot']=CONFIG_DOCUMENT_ROOT_PATH."img/2B.png";
		$_SESSION['motd']="おかえりなさい";
		break;
	case "none":
	default:
	case "bigmike":
		$_SESSION['stylesheet']=CONFIG_DOCUMENT_ROOT_PATH."css/style_suckless_bigmike.css";
		$_SESSION['mascot']=CONFIG_DOCUMENT_ROOT_PATH."img/mike.png";
		$_SESSION['motd']="おかえりなさい";
		break;
	case "yys":
		$_SESSION['mascot']=CONFIG_DOCUMENT_ROOT_PATH."img/yuzuko.png";
		$_SESSION['stylesheet']=CONFIG_DOCUMENT_ROOT_PATH."css/style_suckless_yys.css";
		$_SESSION['motd']="よぉ";
		break;
	case "none":
	case "worlds":
		$_SESSION['mascot']=CONFIG_DOCUMENT_ROOT_PATH."img/hand.png";
		$_SESSION['stylesheet']=CONFIG_DOCUMENT_ROOT_PATH."css/style_suckless_worlds.css";
		$_SESSION['motd']="we'll see creation come undone";
		break;
}
$_SESSION['last_activity'] = new DateTime();
if (!isset($_SESSION['username']))
	$_SESSION['username'] = 'anon';
?>
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link rel="stylesheet" href=<?php echo $_SESSION['stylesheet']?>>
<link rel="shortcut icon" type="image/x-icon" href="img/favicon.ico"/>
