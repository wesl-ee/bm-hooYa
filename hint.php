<?php
include "includes/config.php";

include CONFIG_COMMON_PATH."includes/core.php";
include CONFIG_HOOYA_PATH."includes/database.php";

if (isset($_GET['q'])) {
	print db_gethint($_GET['q']);
}
?>
