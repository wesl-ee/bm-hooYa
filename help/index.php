<!DOCTYPE HTML>
<?php
include "../includes/config.php";

include CONFIG_COMMON_PATH."includes/core.php";
if (CONFIG_REQUIRE_AUTHENTICATION)
        include CONFIG_COMMON_PATH."includes/auth.php";
include "../includes/bmfft_db.php";
?>
<html>
<head>
	<?php include CONFIG_COMMON_PATH."includes/head.php";
	include CONFIG_HOOYA_PATH."includes/head.php"; ?>
	<title>bigmike — hooYa! help</title>
</head>
<body>
<div id="container">
<div id="right_frame" style="width:100%;">
	<div id="title"><h1>searching</h1></div>
	<div id="header" style="width:100%;">
		<div style="width:33%;float:left;"><a href="#" onClick="window.history.back()">back</a></div>
		<div style="width:33%;float:left;">&nbsp</div>
		<div style="width:33%;float:left;text-align:right;"><a href="guidelines.php">guidelines</a></div>
	</div>
	<p>searching is currently exact-match only. You can check out some popular tags <a href="popular.php">here</a>
	while we work on this feature.</p>
	<p>Here are some example searches</p>
	<dl>
		<dt>puella_magi_madoka_magica</dt>
		<dd> - shows all pictures with the pmmm tag</dd>
		<dt>puella_magi_madoka_magica gun</dd>
		<dd> - shows all pictures with either the pmmm or gun tag, sorted by relevance</dd>
		<dt>series:re:zero</dt>
		<dd> - shows all pictures from the series re:zero</dd>
<!--		<dt>+lil_b</dt>
		<dd> - all results are REQUIRED to have the lil_b tag</dd>
		<dt>-mathematics</dt>
		<dd> - all results MUST NOT have the mathematics tag</dd>-->
	</dl>
	<h3>tagspaces and tags</h3>
	<p>what the hell is the difference?</p>
	<p> a tagspace is something like 'series' or 'character' or 'clothing'. Note that the pairs 'series:naruto' and 'character:naruto'
	are completely different searches</p>
	<h3>soft introducton to database internals</h3>
	<p>Internally, every file is hashed and its hash is stored in one SQL table. Then, a seperate table is created for
	each tagspace:member pair like 'series:pmmm'. Finally, A go-between table is created for mapping files->tags. This
	arrangement is ideal because the data is 'normalized', meaning that redundant data is prevented and SQL lookups
	are very fast, since they are keyed primarily by the file's MD5 hash. For deeper reading, check <a>this</a> out</p>
	<!-- tagging quiz here -->
	<p>as always, please read the <a href="guidelines.php">guidelines</a> before tagging, and just enjoy yourself!</p>
</div>
</div>
</body>
</html>
