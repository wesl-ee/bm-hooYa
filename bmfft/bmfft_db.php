<?php
include_once '../includes/config.php';
# AJAX handler goes here because I haven't set aside a file to do that yet
if (isset($_GET['key']) && isset($_GET['tags'])) {
	foreach (bmfft_getattr($_GET['key'], 'tags') as $key => $value) {
		$tags[] = $key;
	}
	print json_encode($tags);
	return;
}

# Passed an MD5sum, return all its tags
# This function is convenient
function bmfft_gettags($key)
{
	return bmfft_getattr($key, 'namespaces')['tags'];
}
function bmfft_getnamespaces($key)
{
	$namespaces = bmfft_getattr($key, 'namespaces');
	unset($namespaces['tags']);
	return $namespaces;
}
function bmfft_setnamespaces($key, $keys, $values)
{
	if (count($keys) !== count($values)) die;
	for ($i=0; $i < count($keys); $i++) {
		if ($keys[$i] && $values[$i])
			$namespaces[$keys[$i]][$values[$i]] = 1;
	}
	bmfft_setattr($key, 'namespaces', $namespaces);
}
# Passed an MD5sum and some tags in an array, create the namespace hash
# So if you're passed ('ass') this function constructs ('ass' => 1)
# and sets that as the $namespace component of the value in the key->value db
# This makes searching tags and namespaces for a given file super-fast!
function bmfft_settags($key, $tags)
{
	foreach ($tags as $tag) {
		$hash_tags[$tag] = 1;
	}
	$new_namespaces = bmfft_getattr($key, 'namespaces');
	$new_namespaces['tags'] = $hash_tags;
	bmfft_setattr($key, 'namespaces', $new_namespaces);
}
# Just a neat function for fun, spits out $count hashes
# from a random part of the database
function bmfft_getrandom($count)
{
	$dbh = dba_open(CONFIG_TAG_DB, 'rd', 'gdbm');
	$skip = rand(0, bmfft_info(){'files'});
	if (!dba_firstkey($dbh)) {
		print '<h3>wow no more untagged files!</h3>';
	}
	while (--$skip-$count) {
		dba_nextkey($dbh);
	}

	while (--$count) {
		if (!($key = dba_nextkey($dbh))) {
			break;
		}
		if (!is_null(json_decode(dba_fetch($key, $dbh), true)['tags'])) {
			++$count;
			continue;
		}
		$hashes[] = $key;
	}
	dba_close($dbh);
	return $hashes;
}
# Returns the value associated with an attribute that's
# part of a given item in the database
function bmfft_getattr($key, $attr)
{
	$dbh = dba_open(CONFIG_TAG_DB, 'rd', 'gdbm');
	$value = dba_fetch($key, $dbh);
	$value = json_decode($value, true);
	dba_close($dbh);
	return $value[$attr];
}
# Sets an attribute in the database given its MD5sum, the attribute
# name, and w/e data we want to associate with that attribute
function bmfft_setattr($key, $attr, $data)
{
	$dbh = dba_open(CONFIG_TAG_DB, 'wd', 'gdbm');
	$value = dba_fetch($key, $dbh);
	$value = json_decode($value, true);
	$value[$attr] = $data;
	$value = json_encode($value);
	dba_replace($key, $value, $dbh);
	dba_sync($dbh);
	dba_close($dbh);
}
# Simple function to get the name of an item. Currently just
# returns the filename as it was picked up by the Perl script
# on initialization
function bmfft_name($key)
{
	return basename(bmfft_getattr($key, 'path'));
}
# Returns some info about the database (this will later be stored
# inside the DB so it needn't be calculated everytime)
function bmfft_info()
{
	// Not necessary every time but here for now
//	bmfft_meta_update();
	$dbh = dba_open(CONFIG_META_DB, 'rd', 'gdbm');
	$size= dba_fetch('size', $dbh);
	$files = dba_fetch('files', $dbh);
	$untagged = dba_fetch('untagged', $dbh);
	$namespaces = dba_fetch('namespaces', $dbh);
	dba_close($dbh);
	return array (
		'size' => $size,
		'files' => $files,
		'untagged' => $untagged,
		'namespaces' => $namespaces,
	);
}
function bmfft_namespaceheat()
{
	$dbh = dba_open(CONFIG_TAG_DB, 'rd', 'gdbm');
	$key = dba_firstkey($dbh);
	while ($key !== false) {
		$value = dba_fetch($key, $dbh);
		$value = json_decode($value, true);
		foreach($value['namespaces'] as $type => $value) {
			if ($value) foreach ($value as $single => $a)
				$heat[$type][$single]++;
		}
		$key = dba_nextkey($dbh);
	}
	dba_close($dbh);
	return $heat;
}
# Returns an unsorted association of tags->tag_counts
function bmfft_tagheat()
{
	$dbh = dba_open(CONFIG_TAG_DB, 'rd', 'gdbm');
	$key = dba_firstkey($dbh);
	while ($key !== false) {
		$value = dba_fetch($key, $dbh);
		$value = json_decode($value, true);
		foreach($value['tags'] as $key => $value) $heat[$key]++;
		$key = dba_nextkey($dbh);
	}
	dba_close($dbh);
	return $heat;
}
function bmfft_meta_update()
{
	$dbh = dba_open(CONFIG_TAG_DB, 'rd', 'gdbm');
	$key = dba_firstkey($dbh);
	$size = 0;
	for ($i = 0; $key; $i++) {
		$size += bmfft_getattr($key, 'size');
		if (!bmfft_gettags($key)) {
			$untagged++;
		}
		foreach (bmfft_getnamespaces($key) as $namespace) {
			$namespaces[$namespace] = 1;
		}
		$key = dba_nextkey($dbh);
	}
	dba_close($dbh);
	$dbh = dba_open(CONFIG_META_DB, 'nd', 'gdbm');
	dba_replace('size', $size, $dbh);
	dba_replace('files', $i, $dbh);
	dba_replace('untagged', $untagged, $dbh);
	dba_replace('namespaces', $namespaces, $dbh);
	dba_close($dbh);
}
function bmfft_meta_getattr($key, $attr)
{
	$dbh = dba_open(CONFIG_META_DB, 'rd', 'gdbm');
	$value = dba_fetch($key, $dbh);
	$value = json_decode($value, true);
	dba_close($dbh);
	return $value[$attr];
}
# Returns an unsorted association of tags->tag_counts
function bmfft_allkeys()
{
	$dbh = dba_open(CONFIG_TAG_DB, 'rd', 'gdbm');
	$key = dba_firstkey($dbh);
	while ($key !== false) {
		$result[] = $key;
		$key = dba_nextkey($dbh);
	}
	dba_close($dbh);
	return $result;
}
function bmfft_getfiletype($key)
{
	return explode('/', bmfft_getattr($key, 'mimetype'))[0];
}
?>
