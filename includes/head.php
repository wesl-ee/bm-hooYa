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
		$_SESSION['stylesheet']=CONFIG_DOCUMENT_ROOT_PATH."css/style_suckless_default.css";
		$_SESSION['motd']="Welcome home";
		break;
	case "nier":
		$_SESSION['stylesheet']=CONFIG_DOCUMENT_ROOT_PATH."css/style_suckless_nier.css";
		$_SESSION['mascot']=CONFIG_DOCUMENT_ROOT_PATH."img/2B.png";
		$_SESSION['motd']="おかえりなさい";
		break;
	case "yys":
		$_SESSION['mascot']=CONFIG_DOCUMENT_ROOT_PATH."img/yuzuko.png";
		$_SESSION['curr_css']="yys";
		$_SESSION['stylesheet']=CONFIG_DOCUMENT_ROOT_PATH."css/style_suckless_yys.css";		$mascot="";
		$_SESSION['motd']="よぉ";
		break;
	default:
		$_SESSION['stylesheet']=CONFIG_DOCUMENT_ROOT_PATH."css/style_suckless_yys.css";		$mascot="";
		$_SESSION['motd']="よぉ";
		break;
}
$_SESSION['last_activity'] = new DateTime();
?>
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link rel="stylesheet" href=<?php echo $_SESSION['stylesheet']?>>
<link rel="shortcut icon" type="image/x-icon" href="img/favicon.ico"/>