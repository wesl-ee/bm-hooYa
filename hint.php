<?php
include "includes/config.php";

include CONFIG_COMMON_PATH."includes/core.php";
include CONFIG_HOOYA_PATH."includes/database.php";

if (isset($_GET['q'])) {
	print json_encode(db_gethints($_GET['q']));
}
if (isset($_GET['n'])) {
	print json_encode(db_getnamespacehints($_GET['n']));
}
?>
