<?php
include "includes/config.php";
include CONFIG_COMMON_PATH."includes/core.php";

include CONFIG_HOOYA_PATH."includes/database.php";
include CONFIG_HOOYA_PATH."includes/render.php";

if ($_GET['key']) {
	$class = db_getfileinfo($_GET['key'])['class'];
	render_properties($_GET['key'], $class);
	render_tags($_GET['key']);
}
?>