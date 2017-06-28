<?php
// Require users to authenticate to access files
define("CONFIG_REQUIRE_AUTHENTICATION", false);
// Open the account registration page to everyone instead of using one-time
// keys
define("CONFIG_OPEN_REGISTRATION", false);

// MySQL server config options (more to come)
define("CONFIG_DB_SERVER", "127.0.0.1");
define("CONFIG_DB_USERNAME", "DB_USERNAME");
define("CONFIG_DB_PASSWORD", "DB_PASSWORD");
define("CONFIG_DB_DATABASE", "DB_DATABASE");
define("CONFIG_DB_TABLE", "DB_TABLE");

// Root for serving things for bmffd
define("CONFIG_ROOT_PATH","/var/http/bmffd/");
// Relative to http DOCUMENT_HEAD
define("CONFIG_DOCUMENT_ROOT_PATH", "/bmffd/");
// A place for thumbnails and encoded video, relative to DOCUMENT_HEAD
define("CONFIG_TEMPORARY_DIRECTORY", "/a/");

// Logs
define("CONFIG_AUTHLOG_FILE", "/path/to/auth.log");
define("CONFIG_ACCESSLOG_FILE", "/path/to/access.log");

// Tag databases for bmfft
define("CONFIG_TAG_DB", "/path/to/tags.db");
define("CONFIG_META_DB", "/path/to/meta.db");
?>