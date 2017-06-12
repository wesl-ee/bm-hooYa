<?php
define("CONFIG_TAG_DB", "/var/http/bmffd/bmfft/tags.db");
if (isset($_GET['key']) && isset($_GET['tags'])) {
	foreach (bmfft_getattr($_GET['key'], 'tags') as $key => $value)
		$tags[] = $value;
	print json_encode($tags);
	return;
}
function bmfft_searchtag($tag)
{
	$dbh = dba_open(CONFIG_TAG_DB, 'rd', 'gdbm');
	$key = dba_firstkey($dbh);
	while ($key !== false) {
		$value = dba_fetch($key, $dbh);
		$value = json_decode($value, true);
		foreach($value['tags'] as $a)
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
function bmfft_gettags($key)
{
	$dbh = dba_open(CONFIG_TAG_DB, 'rd', 'gdbm');
	$value = $dba_fetch($key, $dbh);
	dba_close($dbh);
	return json_decode($value)['tags'];
}
function bmfft_addtags($key, $newtags)
{
	$dbh = dba_open(CONFIG_TAG_DB, 'rd', 'gdbm');
	$value = dba_fetch($key, $dbh);
	dba_close($dbh);
	$value = json_decode($value, true);
	$tags = $value['tags'];
	foreach ($newtags as $newtag) {
		$tags[$newtag] = 1;
	}
	$value['tags'] = $tags;
	bmfft_setattr($key, 'tags', $tags);
}
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
function bmfft_getattr($key, $attr)
{
	$dbh = dba_open(CONFIG_TAG_DB, 'rd', 'gdbm');
	$value = dba_fetch($key, $dbh);
	$value = json_decode($value, true);
	dba_close($dbh);
	return $value[$attr];
}
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
function bmfft_name($key)
{
	return basename(bmfft_getattr($key, 'path'));
}
function bmfft_exists($key)
{
	$dbh = dba_open(CONFIG_TAG_DB, 'rd', 'gdbm');
	$e = dba_exists($key, $dbh);
	dba_close($dbh);
	return $e;
}
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
?>
