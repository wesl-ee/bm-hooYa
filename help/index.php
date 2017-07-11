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
	<?php include CONFIG_COMMON_PATH."includes/head.php"; ?>
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
	<p>currently if you enter a bundle of search terms, each word is parsed out and the database is searched for that
	word. The most relevant results are shown first, less relevant results shown on the following pages. Not all the pictures
	may have all your tags, so use some of these operators to narrow your search</p>
	<dl>
		<dt>+lil_b</dt>
		<dd> - all results are REQUIRED to have the lil_b tag</dd>
		<dt>-mathematics</dt>
		<dd> - all results MUST NOT have the mathematics tag</dd>
	</dl>
	<h3>namespaces and tags</h3>
	<p>what the hell is the difference?</p>
	<p>a namespace is like a tag, but for a certain characteristic. For instance, your favorite anime may be have indexed as
	series:yuyushiki and be tagged as something like slice_of_life. It's not too hard to differentiate between a tag and a namespace
	attribute, but if you have any hesitations just ask away in the IRC or imageboard!</p>
	<h3>soft introducton to database internals</h3>
	<p>internally, a tag is stored with all the other namespaces, just under the special 'tags' namespace. It's complex, but storing
	things like this makes searching a lot faster, as only one calculation per search term per picture is necessary; the whole array
	doesn't need to be walked, because I abuse PHP and Perl's associative hashing; exciting! For deeper reading, check <a>this</a> out</p>
	<!-- tagging quiz here -->
	<p>as always, please read the <a href="guidelines.php">guidelines</a> before tagging, and just enjoy yourself!</p>
</div>
</div>
</body>
</html>
