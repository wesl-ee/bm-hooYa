<?php

# AJAX handler goes here because I haven't set aside a file to do that yet
if (isset($_GET['key']) && isset($_GET['tags'])) {
	foreach (bmfft_getattr($_GET['key'], 'tags') as $key => $value) {
		$tags[] = $key;
	}
	print json_encode($tags);
	return;
}

# Passed a tag, search the database for all MD5sums associated
# with that tag
function bmfft_searchtag($tag)
{
	$dbh = dba_open(CONFIG_TAG_DB, 'rd', 'gdbm');
	$key = dba_firstkey($dbh);
	while ($key !== false) {
		$value = dba_fetch($key, $dbh);
		$value = json_decode($value, true);
		foreach($value['tags'] as $a => $b)
		{
			if ($a == $tag) {
				$results[] = $key;
			}
		}
		$key = dba_nextkey($dbh);
	}
	dba_close($dbh);
	return $results;
}
# Passed an MD5sum, return all its tags
# This function is just pretty convenient and can be
# removed someday
function bmfft_gettags($key)
{
	$dbh = dba_open(CONFIG_TAG_DB, 'rd', 'gdbm');
	$value = $dba_fetch($key, $dbh);
	dba_close($dbh);
	return json_decode($value)['tags'];
}
# Passed an MD5sum and some tags in an array, create the tagging hash
# So if you're passed ('ass') this function constructs ('ass' => 1)
# and sets that as the tag component of the value in the key->value db
# This makes searching tags for a given file super-fast!
function bmfft_settags($key, $tags)
{
	foreach ($tags as $tag) {
		$hash_tags[$tag] = 1;
	}
	bmfft_setattr($key, 'tags', $hash_tags);
}
# Just a neat function for fun, spits out $count hashes
# from a random part of the database
function bmfft_getrandom($count)
{
	$dbh = dba_open(CONFIG_TAG_DB, 'rd', 'gdbm');
	$skip = rand(0, bmfft_info(){'files'});
	dba_firstkey($dbh);
	while (--$skip-$count) {
		dba_nextkey($dbh);
	}

	while (--$count) {
		$hashes[] = dba_nextkey($dbh);
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
# inside the DB so it needn't be calculated everytime
function bmfft_info()
{
	$dbh = dba_open(CONFIG_TAG_DB, 'rd', 'gdbm');
	$key = dba_firstkey($dbh);
	$size = 0;
	for ($i = 0; $key; $i++) {
		$size += bmfft_getattr($key, 'size');
		$key = dba_nextkey($dbh);
	}
	dba_close($dbh);
	return array (
		'size' => $size,
		'files' => $i,
	);
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
	return $heat;
}
?>