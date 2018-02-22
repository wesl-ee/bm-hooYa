<?php
include "includes/config.php";

include CONFIG_COMMON_PATH."includes/core.php";
include CONFIG_HOOYA_PATH."includes/database.php";

if (isset($_POST['q'])) {
	print json_encode(db_gethints($_POST['q']));
}
if (isset($_POST['n'])) {
	print json_encode(db_getnamespacehints($_POST['n']));
}
?>
