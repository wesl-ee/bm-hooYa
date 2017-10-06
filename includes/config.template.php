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
define("CONFIG_DAILY_DUMP_FILE", "/var/http/a/bigmike-nightly.tar");

// For every search
define("CONFIG_THUMBS_PER_PAGE", 20);

// Maximum tags for each file
define("CONFIG_HOOYA_MAX_TAGS", 7);

// Where you want to keep hooYa files
define("CONFIG_HOOYA_STORAGE_PATH", "/var/media/")

// hooYa upload limit
define("CONFIG_HOOYA_DAILY_UPLOAD_LIMIT", 15);

define('DB_MEDIA_CLASSES', [
	'single_image' => [
		'ULPath' => CONFIG_HOOYA_STORAGE_PATH,
	],
	'video' => [
	],
	'anime' => [
		'FMGroup' => 'series',
		'Restricted' => 1,
		'ULPath' => CONFIG_HOOYA_STORAGE_PATH,
	],
	'television' => [
		'FMGroup' => 'series',
		'Restricted' => 1,
		'ULPath' => CONFIG_HOOYA_STORAGE_PATH,
	],
	'movie' => [
		'FMGroup' => 'series',
		'Restricted' => 1,
		'ULPath' => CONFIG_HOOYA_STORAGE_PATH,
	]
]);
define('DB_FILE_PROPERTIES', ['Size', 'Path', 'Mimetype']);
define('DB_FILE_EXTENDED_PROPERTIES',
[
	'single_image' => [
		'Width' => ['Type' => 'Number', 'Immutable' => 1],
		'Height' => ['Type' => 'Number', 'Immutable' => 1],
	],
	'video' => [
		'Width' => ['Type' => 'Number', 'Immutable' => 1],
		'Height' => ['Type' => 'Number', 'Immutable' => 1],
	],
	'anime' => [
		'Season' => [
			'Type' => 'Number',
			'Format' => 'Season ? ',
			'Sort' => 1,
		],
		'Episode' => [
			'Format' => 'Episode ?',
			'Sort' => 1,
		],
	],
	'television' => [
		'Season' => [
			'Type' => 'Number',
			'Format' => 'Season ? ',
			'Sort' => 1,
		],
		'Episode' => [
			'Format' => 'Episode ?',
			'Sort' => 1,
		],
	],
	'movie' => [
		'Year' => ['Type' => 'Number', 'Format' => '(?)'],
	],
]);
?>
