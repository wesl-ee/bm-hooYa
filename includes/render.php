<?php
function render_file($key, $ftype)
{
	switch($ftype) {
	case 'image':
		print '<main class="single">'
		// Wrap the img in a div to preserve the aspect ratio
		. '<div id="hack">'
		. '<img src="download.php?key='.rawurlencode($key).'"'
		. ' onClick="window.open(this.src)">'
		. '</img>'
		. '</div>'
		. '</main>';
		break;
	case 'video':
		print '<main class="single">'
		. '<div id="hack">'
		. '<img src="download.php?key='.rawurlencode($key).'&preview"'
		. 'onClick="window.open(\'download.php?key='.rawurlencode($key).'\')">'
		. '</img>'
		. '</a>'
		. '</div>'
		. '</main>';
		break;
	}
}
function render_properties($key, $class)
{
	if (!isset(DB_FILE_EXTENDED_PROPERTIES[$class])) {
		syslog(LOG_INFO|LOG_DAEMON, "$class not defined in"
		. " DB_FILE_EXTENDED_PROPERTIES in includes/database.php");
		return;
	}
	$fileproperties = db_getproperties($key);
	print '<table id="properties">';
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
	}
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
	foreach(DB_MEDIA_CLASSES as $c => $value) {
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
function render_thumbnails($results)
{
	foreach ($results as $result) {
		$key = $result['key'];
		print "<a"
		. " href='view.php?key=".rawurlencode($key) . "'>"
		. "<img"
		. " onMouseOver='showThumbInfo(\"$key\")'"
		. " onMouseOut='hideThumbInfo(\"$key\")'"
		. " src='download.php?key=".rawurlencode($key)."&thumb'"
		. " >"
		. "&nbsp</a></img>";
	}
}
function render_titles($results)
{
	foreach ($results as $result) {
		$key = $result['key'];
		print "<span"
		. " onClick='window.location.href=\"view.php?key=".rawurlencode($key)."\"'"
		. " onMouseOver='showThumbInfo(\"$key\")'"
		. " onMouseOut='hideThumbInfo(\"$key\")'"
		. " >"
		. render_title($key)
		. "</span>";
	}
}
function render_pagenav($currpage, $totalpages, $q)
{
	print '<form method="GET">';
	if ($currpage > 1)
		print "<a href='?".http_build_query($q)."&page=".($currpage-1)."'><</a> ";
	print '<input style="text-align:center;width:50px;"'
	. ' name="page" type="text" Value=' . $currpage . '>';

	render_hidden_inputs($q);

	if ($currpage < $totalpages)
		print " <a href='?".http_build_query($q)."&page=".($currpage+1)."'>></a>";
	print '</form>';
}
function render_hidden_inputs($array, $path = NULL)
{
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
function render_title($key)
{
	print '<tr><td>';
	print "<a href=view.php?key=$key>";

	// Print the important part of tags
	foreach (db_get_tags($key) as $pair) {
		print $pair['Member'] . ' ';
	}
	// Output important properties by formatting them according to the
	// formatting specified in includes/database.php
	$class = db_getfileinfo($key)['Class'];
	$properties = db_getproperties($key);
	foreach ($properties as $p => $value) {
		$format = DB_FILE_EXTENDED_PROPERTIES[$class][$p]['Format'];
		if ($format) {
			print str_replace('?', $value, $format);
		}
	}
	print '</a>';
	print '</td></tr>';
}
?>
