<?php
function render_properties($key, $class)
{
	if (!isset(DB_FILE_EXTENDED_PROPERTIES[$class]))
		return;
	if (!($fileproperties = db_getproperties($key)))
		print "Query failed. . . check that table `$class` has the rows: "
		. join(array_keys(DB_FILE_EXTENDED_PROPERTIES[$class]), ', ');
	else foreach (DB_FILE_EXTENDED_PROPERTIES[$class] as $property => $value) {
		print '<div style="overflow:auto;">';
		print '<div style="float:left;width:50%;text-align:center;">'
		. $property
		. '</div>';

		if ($value['Immutable']) {
			print '<div style="float:left;width:50%;text-align:center;">'
			. $fileproperties[$property]
			. '</div>';
		}
		else {
			print "<input name='properties[$property]' id='box'";
			if ($value['Type'])
				print " type='" . $value['Type'] . "'";
			print " value='" . $fileproperties[$property] . "'>";
		}
                print '</div>';
        }
}
function render_classmenu($class = NULL)
{
	print "<option style='display:none;'> </option>";
	foreach(DB_MEDIA_CLASSES as $c) {
		print "<option value='$c'";
		if ($c == $class) print " selected";
		print ">$c</option>";
	}
}
function render_prettyquery($query)
{
	// Construct a pretty header on the fly from the given query
	if (isset($query['query']))
		echo $query['query'];
	else
		echo 'all ';
	if (isset($query['media_class']))
		echo ' ' . $query['media_class'];
	if (isset($query['properties'])) {
		echo ' (';
		$i = 0;
		foreach ($query['properties'] as $property => $value) {
			if ($i++) echo " ";
			echo "$property: $value";
		}
		echo ')';
	}
}
function render_thumbnails($keys)
{
	print '<div id="thumbs">';
	foreach ($keys as $key) {
		print '<img'
		. ' onClick="window.location.href=\'view.php?key='.rawurlencode($key).'\'"'
		. ' src="download.php?key='.rawurlencode($key).'&thumb"'
		. ' title="'.''.'">'
		. '&nbsp</img>';
	}
	print '</div>';
}
function render_pagenav($currpage, $totalpages, $q)
{
	if ($currpage > 0)
		print "<a href='?".http_build_query($query)."&page=".($currpage-1)."'><</a> ";
	print '<form method="GET" style="text-align:center;display:inline;">'
	. '<input style="text-align:center;width:50px;"'
	. ' name="page" type="text" Value=' . $currpage . '>';
	render_hidden_inputs($q);
	if ($currpage < $totalpages)
		print " <a href='?".http_build_query($query)."&page=".($currpage+1)."'>></a>";
}
function render_hidden_inputs($array, $path = NULL) {
	foreach ($array as $k => $v) {
		if (!is_array($v)) {
			// leaf node
			if ($path)
				$fullpath = $path.'['.$k.']';
			else
				$fullpath = $k;
#			print "$fullpath = $v<br/>";
			print "<input type='hidden' name='$fullpath' value='$v'>";
		}
		else {
			// directory node
			render_hidden_inputs($v, $path.$k);
		}
	}
}
?>