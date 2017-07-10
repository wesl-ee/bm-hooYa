<!DOCTYPE html>
<?php
include "../includes/config.php";
include CONFIG_COMMON_PATH."includes/core.php";

// Why do you want the admin panel if you can't handle accounts?
if (!CONFIG_REQUIRE_AUTHENTICATION) die;
include CONFIG_COMMON_PATH."includes/auth.php";
include CONFIG_COMMON_PATH."includes/access.php";
include CONFIG_HOOYA_PATH."includes/database.php";
include CONFIG_HOOYA_PATH."includes/admin.php";
// Don't let not-admins in here!
if (!db_isAdmin($_SESSION['userid'])) die;

?>
<HTML>
<head>
	<?php include CONFIG_COMMON_PATH."includes/head.php"; ?>
	<script src="<?php print CONFIG_HOOYA_WEBPATH.'js/f.js'?>"></script>
	<title>bmffd — import</title>
	<script type="text/javascript">
	function togglecheck(checkbox) {
		var method = document.getElementById('method');
		if (checkbox.checked)
			method.style.display = 'block';
		else method.style.display = 'none';
	}
	</script>
</head>
<body>
<div id="container">
<div id="left_frame">
	<div id="logout">
		<?php print_login(); ?>
        </div>
	<img id="mascot" src=<?php echo $_SESSION['mascot']?>>
</div>

<div id="right_frame">
	<h1 style="text-align:center;">admin hub</h1>
	<div class="header">
	<a href=<?php echo CONFIG_COMMON_WEBPATH . "admin/" ?>>« back</a>
	</div>
	<form method="POST" style="display:block;width:70%;margin:auto;">
		<input type="text" name="path" placeholder="file or directory path" style="width:100%;margin-top:10px;"></input>
		<div id="tagform" style="overflow:auto;">
			<input id="space_box" name="tag_space[]" value=""
			onKeyDown="inputFilter(event)" placeholder="space">
			<input id="member_box" name="tag_member[]" value=""
			onKeyDown="inputFilter(event)" placeholder="tag">

		</div>
		<div style="text-align:center;">
			<a onClick="addTagField()">add a tag</a>
		</div><hr/>
		<div style="overflow:hidden;">
			<div style="margin-top:10px;">merge into hooYa storage?</div>
				<input type="hidden" name="merge" value="0"> </input>
				<input type="checkbox" onChange="togglecheck(this);" name="merge" style="float:left">
			</div>
		<div id="method" style="overflow:hidden;display:none;">
			<div style="margin-top:10px;">merge method</div>
			<select name="method" style="display:block;">
				<option name="cp" value="cp">cp (preserve original)</option>
				<option name="mv" value="mv">mv (move original)</option>
			</select>
		</div>
		<div style="overflow:hidden;">
			<input type="submit" value="submit!" style="float:right">
		</div>
		<hr/>
	</form>
	<div style="width:70%;margin:auto;display:block;">
	Log
	<textarea style="width:100%;" rows="10"><?php if (isset($_POST['path'])) {
		// Grab tag {space => member pairs} (e.g. 'character' => 'madoka')
		if (isset($_POST['tag_space'], $_POST['tag_member'])
			&& count($_POST['tag_space']) == count($_POST['tag_space'])) {
			$tag_space = $_POST['tag_space'];
			$tag_member = $_POST['tag_member'];
			for ($i = 0; $i < count($tag_space); $i++) {
				if ($tag_space[$i] === ''|| $tag_member[$i] === '')
					continue;
				$tags[$i]['Space'] = $tag_space[$i];
				$tags[$i]['Member'] = $tag_member[$i];
			}
		}
		// TL NOTE not doing anything w/ $tags yet. . .
		// Either copy or move files from the path to CONFIG_HOOYA_STORAGE_PATH
		if ($_POST['merge']) {
			hooya_mergedir($_POST['path'], $_POST['method'], $tags);
		}
		// Just index files where they are
		else {
			hooya_importdir($_POST['path'], $tags);
		}

	} ?></textarea>
	</div>
</div>
</div>
</body>
</HTML>