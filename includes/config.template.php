<?php
// A place for thumbnails and encoded video
define("CONFIG_TEMPORARY_PATH", "/var/http/a/");
define("CONFIG_TEMPORARY_WEBPATH", "/a/");

// A path for stylsheets and logins
define("CONFIG_COMMON_PATH", "/var/http/hub/common/");
define("CONFIG_COMMON_WEBPATH", "/hub/common/");

// Tag database for hooYa
define("CONFIG_MYSQL_HOOYA_HOST", "localhost");
define("CONFIG_MYSQL_HOOYA_USER", "MYSQL_USER");
define("CONFIG_MYSQL_HOOYA_PASSWORD", "MYSQL_PASSWORD");
define("CONFIG_MYSQL_HOOYA_DATABASE", "MYSQL_DB");

// All pictures, update daily with the cron script
define("CONFIG_DAILY_DUMP_FILE", "/var/a/bigmike-nightly.tar.gz");

// Where you want to keep hooYa files
define("CONFIG_HOOYA_STORAGE_PATH", "/var/media/")
?>
