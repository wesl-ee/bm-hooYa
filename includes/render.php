<?php
function render_properties($key, $class)
{
	$fileproperties = db_get_file_properties($key, $class, DB_FILE_EXTENDED_PROPERTIES[$class]);
	foreach (DB_FILE_EXTENDED_PROPERTIES[$class] as $property => $value) {
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
			print "<input name=properties[id='box'";
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
?>