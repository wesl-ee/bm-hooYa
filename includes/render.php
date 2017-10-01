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
function render_properties($key, $class, $editmode = True)
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
		if ($value['Immutable'] || !$editmode) {
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
function render_tags($key, $editmode = True)
{
	$tags = db_get_tags([$key])[$key];
	print '<table id="tags">';
	foreach ($tags as $tag) {
		print '<tr>'
		. '<th>';
		if ($editmode)
			print '<input name="tag_space[]"'
			. ' value="'.ucwords($tag['Space']).'">';
		else
			print ucwords($tag['Space']);
		print '</th>';
		print '<td>';
		if ($editmode)
			print '<input name="tag_member[]"'
			. ' value="'.ucwords($tag['Member']).'">';
		else
			print ucwords($tag['Member']);
		print '</td></tr>';
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
		echo 'All ';
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
	foreach ($results as $result) $keys[] = $result['Key'];
	$tags = db_get_tags($keys);
	foreach ($results as $result) {
		$key = $result['Key'];
		$class = $result['Class'];
		$fileproperties = db_getproperties($key);
		print "<div id=searchresult>"
		. "<div id=preview><a"
		. " href='view.php?key=".rawurlencode($key) . "'>"
		. "<img"
		. " src='download.php?key=".rawurlencode($key)."&thumb'"
		. " >"
		. "</a></img>"
		. "</div>"
		. "<div id=details>"
		. "<table><tr><th colspan=2>".$class."</th>";
		foreach (DB_FILE_EXTENDED_PROPERTIES[$class] as $property => $value) {
			print "<tr><td>$property</td>";
			print "<td>".$fileproperties[$property]."</td></tr>";
		}
		$taglist = $tags[$key];
		foreach ($taglist as $tag) {
			$space = ucwords($tag['Space']);
			$mem = ucwords($tag['Member']);
			$added = $tag['Added'];
			$author = $tag['Author'];
			if ($space)
			print "<tr><th>$space</th>"
			. "<td>$mem</td></tr>";
			if ($author) {
			$author = get_username($author);
			print "<tr><td>Added By</td>"
			. "<td>$author</td></tr>";
			}
			if ($added)
			print "<tr><td>Timestamp</td>"
			. "<td>$added</td></tr>";
		}
		print "</table></div></div>";
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
		. " >";
		render_title($key);
		print "</span>";
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
	foreach (db_get_tags([$key]) as $pair) {
		print ucwords($pair['Member']) . ' ';
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
function render_bargraph($data, $linkify = NULL)
{
	foreach($data as $label => $value) {
		if ($value > $max) $max = $value;
	}
	print '<div id="bargraph"><dl>';
	foreach($data as $label => $value) {
		$ratio = $value/$max;
		$width = $ratio*100 . "%";
		if (isset($linkify)) {
			$link = str_replace('{?}', urlencode($label), $linkify);
			print "<dt><a href='" . $link
			. "'>$label</a></dt>";
		}
		else {
			print "<dt>$label</dt>";
		}
		print "<dd style='width:$width;'>$value</dd>";
	}
	print '</dl></div>';
}
?>
