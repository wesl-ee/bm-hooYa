<?php
function render_file($key, $ftype)
{
	$key = rawurlencode($key);
	switch($ftype) {
	case 'image':
		print '<main class="single">'
		// Wrap the img in a div to preserve the aspect ratio
		. "<a id=hack href='download.php?key=$key'>"
		. "<img src='download.php?key=$key'>"
		. '</a>'
		. '</main>';
		break;
	case 'video':
		print '<main class=single>'
		. "<a id=hack href='download.php?key=$key'>"
		. "<img src='download.php?key=$key&preview'>"
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
	$fileproperties['property'] = htmlspecialchars(
		$fileproperties['property'], ENT_QUOTES
	);
	print '<table id="properties">';
	foreach (DB_FILE_EXTENDED_PROPERTIES[$class] as $property => $value) {
		print '<tr>'
		. "<td>$property</td>"
		. '<td>';
		if (!logged_in() || $value['Immutable'] || !$editmode) {
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
	$tags = db_get_tags([$key])[$key];
	print '<table id="tags">';
	foreach ($tags as $tag) {
		$space = ucwords(
			htmlspecialchars($tag['Space'], ENT_QUOTES)
		);
		$mem = ucwords(
			htmlspecialchars($tag['Member'], ENT_QUOTES)
		);

		print '<tr><td>';
		if (logged_in())
			print "<input name='tag_space[]'"
			. " value='$space'>";
		else
			print $space;
		print '</td>';
		print '<td>';
		if (logged_in())
			print "<input name='tag_member[]'"
			. " value='$mem'>";
		else
			print $mem;
		print '</td></tr>';
	}
	print '</table>';
}
function render_classmenu($class = NULL)
{
	foreach(DB_MEDIA_CLASSES as $c => $value) {
		if ($value['Restricted'] && !logged_in() ) continue;
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
function render_list($results)
{
	foreach ($results as $key => $result) $keys[] = $key;
	$tags = db_get_tags($keys);
	$tags_by_space = db_get_tags_by_space($keys);
	foreach ($results as $key => $result) {
		$class = $result['Class'];
		$indexed = parse_timestamp($result['Indexed']);
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
		. "<h4>$class</h4>"
		. "<span>Indexed on $indexed</span>";

		print "<dl>";
		foreach (DB_FILE_EXTENDED_PROPERTIES[$class] as $property => $value) {
			print "<div id=tag><dt>$property</dt>";
			print "<dd>".$fileproperties[$property]."</dd></div>";
		}
		$taglist = $tags_by_space[$key];
		foreach ($taglist as $space => $tags) {
			$space = ucwords(
				htmlspecialchars($space, ENT_QUOTES)
			);
			print "<div id=tag>";
			if ($space)
			print "<dt>$space</dt>";

			foreach ($tags as $tag) {
				$mem = ucwords(
					htmlspecialchars($tag['Member'], ENT_QUOTES)
				);
				print "<dd>$mem</dd>";
			}
			print "</div>";
		}
		print "</dl></div></div>";
	}
}
function render_thumbs($results)
{
	foreach ($results as $key => $result) $keys[] = $key;
	$tags = db_get_tags($keys);
	foreach ($results as $key => $result) {
		$class = $result['Class'];
		$indexed = parse_timestamp($result['Indexed']);
		$fileproperties = db_getproperties($key);
		print "<a"
		. " href='view.php?key=".rawurlencode($key) . "'>"
		. "<img"
		. " src='download.php?key=".rawurlencode($key)."&thumb'"
		. " >"
		. "</a></img>"
		. "";
	}
}
function render_titles($results)
{
	foreach ($results as $key => $result) {
		print "<span"
		. " onClick='window.location.href=\"view.php?key="
		. rawurlencode($key)
		. "\"'>";
		render_title($key);
		print "</span>";
	}
}
function render_pagenav($currpage, $totalpages, $q = NULL)
{
	print '<form method="GET">';
	if ($currpage > 1) {
		if ($q) print "<a href='?".http_build_query($q)."&page=".($currpage-1)."'><</a> ";
		else print "<a href='?page=".($currpage-1)."'><</a> ";
	}
	print '<input style="text-align:center;width:50px;"'
	. ' name="page" type="text" Value=' . $currpage . '>';

	if ($q) render_hidden_inputs($q);

	if ($currpage < $totalpages) {
		if ($q) print " <a href='?" .http_build_query($q)."&page=".($currpage+1)."'>></a>";
		else print " <a href='?page=".($currpage+1)."'>></a>";
	}
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
	foreach (db_get_tags([$key])[$key] as $pair) {
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
function render_search()
{
	print "<form id='search' action='" . CONFIG_HOOYA_WEBPATH . "browse.php'>"
	. "<input id='searchbox' type='search'"
	. "name='query' placeholder='search,terms'>"
	. "<div id='params'>"
	. "<section>"
	. "<div><input type='submit' value='いこう！'></input></div>"
	. "</section>"
	. "<section id='filters'>"
	. "<div><select id='media_class'"
	. " name='media_class' onChange='changeExtAttrs(this.value)'>";
	render_classmenu();
	print "</select></div>";
	foreach (DB_MEDIA_CLASSES as $c => $more) {
		if ($more['Restricted'] && !logged_in() ) continue;
		$properties = DB_FILE_EXTENDED_PROPERTIES[$c];
		print "<div id='$c' style='display:none;'>";
		foreach ($properties as $p => $value) {
			print "<div><input";
			if ($value['Type']) {
				print " type='" . $value['Type'] . "'";
			}
			print " name='properties[$p]'"
			. " placeholder=$p"
			. " disabled></div>";
		}
		if ($more['Default'])
			print '<input type="hidden"'
			. ' name=' . $more['Default']
			. ' value=y'
			. '>';
		if ($properties['Width'] && $properties['Height'])
			print '<select name="properties[Ratio]">'
			. '<option value>Exact Dimensions</option>'
			. '<option value=ratio>Respect W:H Ratio</option>'
			. '</select>';
		print '</div>';
	}
	print "</section></div>"
	. "</form>"
	. "<script> var classes = ";
	foreach (DB_MEDIA_CLASSES as $class => $property) {
		if ($property['Restricted'] && !logged_in()) continue;
		$classes[] = $class;
	}
	// Update the media class filter for its initial value
	print json_encode($classes)
	. "</script>";
	print "<script src='" . CONFIG_HOOYA_WEBPATH . "js/search.js'></script>";
}
function render_simple_search()
{
	print "<form id='search' action='" . CONFIG_HOOYA_WEBPATH . "browse.php'>"
	. "<input id='searchbox' type='search'"
	. "name='query' placeholder='search,terms'>"
	. "<div><input type='submit' value='いこう！'></input></div>"
	. "</form>";
}
function render_min_search($q = NULL)
{
	print "<form id='search' action='" . CONFIG_HOOYA_WEBPATH . "browse.php'>"
	. "<input id='searchbox' type='search' value='$q'"
	. " name='query' placeholder='search,terms'>"
	. "</form>";
}
function render_hooya_headers()
{
	$h = CONFIG_HOOYA_WEBPATH;
	print "<footer style='margin:auto;'>"
	. "<a href='$h'>Main</a>"
	. "<a href='$h" . "power.php'>Search</a>"
	. "<a href='$h" . "stats.php?overview'>Metrics</a>"
	. "<a href='$h" . "upload.php'>U/L</a>"
	. "<a href='$h" . "random.php?untagged&list'>Random</a>"
	. "</footer>";
}
?>
