<?php
function render_file($key, $ftype)
{
	switch($ftype) {
	case 'image':
		print '<main id="single">'
		// Wrap the img in a div to preserve the aspect ratio
		. '<div id="hack">'
		. '<img src="download.php?key='.rawurlencode($key).'"'
		. ' onClick="window.open(this.src)">'
		. '</img>'
		. '</div>'
		. '</main>'
		. '<footer style="text-align:center;">'
		. "<span>$key</span>"
		. '</footer>';
		break;
	case 'video':
		print '<main class="thumbs">';
		foreach (range(0, 100, 100/5) as $percent) {
			print '<img src="download.php?key='.rawurlencode($key).''
			. '&preview&percent=' . $percent . '"'
			. ' onClick="window.open(this.src)">'
			. '</img>';
		}
		print '</main>'
		. '<footer><a href="download.php?key=' . rawurlencode($key) . '">'
		. 'Download this file!'
		. '</a></footer>';
		break;
	}
}
function render_properties($key, $class)
{

	if (!isset(DB_FILE_EXTENDED_PROPERTIES[$class]))
		return;
	if (!($fileproperties = db_getproperties($key)))
		print "Query failed. . . check that table `$class` has the rows: "
		. join(array_keys(DB_FILE_EXTENDED_PROPERTIES[$class]), ', ');
	else { print '<table id="properties">';
	foreach (DB_FILE_EXTENDED_PROPERTIES[$class] as $property => $value) {
		print '<tr>';
		print '<td>'
		. $property
		. '</td>';

		print '<td>';
		if ($value['Immutable']) {
			print $fileproperties[$property];
		}
		else {
			print "<input name='properties[$property]' id='box'";
			if ($value['Type'])
				print " type='" . $value['Type'] . "'";
			print " value='" . $fileproperties[$property] . "'>";
		}
                print '</td></tr>';
        }}
	print '</table>';
}
function render_tags($key)
{
	$tags = db_get_tags($key);
	print '<table id="tags">';
	foreach ($tags as $tag) {
		print '<tr>'
		. '<td><input name="tag_space[]"'
		. ' value="'.$tag['Space'].'"'
		. ' onKeyDown="inputFilter(event)"'
		. '></td>';
		print '<td><input name="tag_member[]"'
		. ' value="'.$tag['Member'].'"'
		. ' onKeyDown="inputFilter(event)"'
		. '></td></tr>';
	}
	print '</table>';
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
	foreach ($keys as $key) {
		print '<img'
		. ' onClick="window.location.href=\'view.php?key='.rawurlencode($key).'\'"'
		. ' src="download.php?key='.rawurlencode($key).'&thumb"'
		. ' title="'.''.'">'
		. '&nbsp</img>';
	}
}
function render_pagenav($currpage, $totalpages, $q)
{
	print '<div>';
	if ($currpage > 0)
		print "<a href='?".http_build_query($query)."&page=".($currpage-1)."'><</a> ";
	print '<form method="GET" style="text-align:center;display:inline;">'
	. '<input style="text-align:center;width:50px;"'
	. ' name="page" type="text" Value=' . $currpage . '>';
	render_hidden_inputs($q);
	if ($currpage < $totalpages)
		print " <a href='?".http_build_query($query)."&page=".($currpage+1)."'>></a>";
	print '</div>';
}
function render_hidden_inputs($array, $path = NULL) {
	foreach ($array as $k => $v) {
		if (!is_array($v)) {
			// leaf node
			if ($path)
				$fullpath = $path.'['.$k.']';
			else
				$fullpath = $k;
			print "<input type='hidden' name='$fullpath' value='$v'>";
		}
		else {
			// directory node
			render_hidden_inputs($v, $path.$k);
		}
	}
}
?>