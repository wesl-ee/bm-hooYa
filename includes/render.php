<?php
function render_properties($key, $class)
{
        $properties = db_get_file_properties($key, $class, DB_FILE_EXTENDED_PROPERTIES[$class]);
        foreach ($properties as $property => $value) {
		print '<div style="overflow:auto;">';
		print '<div style="float:left;width:50%;text-align:center;">'
		. $property
		. '</div>';
		if (DB_FILE_EXTENDED_PROPERTIES[$class][$property]['Immutable']) {
	                print '<div style="float:left;width:50%;text-align:center;">'
	                . $value
	                . '</div>';
		}
		else {
			print '<input id="box"'
			. "value='$value'>";
		}
                print '</div>';
        }
}
function render_classmenu($class = NULL)
{
	print "<select name='class'>"
	. "<option style='display:none;'> </option>";
	foreach(DB_MEDIA_CLASSES as $c) {
		print "<option value='$c'";
		if ($c == $class) print " selected";
		print ">$c</option>";
	}
	print "</select>";
}