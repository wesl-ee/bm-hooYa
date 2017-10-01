<!DOCTYPE HTML>
<?php
include "../includes/config.php";

include CONFIG_COMMON_PATH."includes/core.php";
if (CONFIG_REQUIRE_AUTHENTICATION)
	include CONFIG_COMMON_PATH."includes/auth.php";
include CONFIG_HOOYA_PATH."../includes/bmfft_db.php";
?>
<html>
<head>
	<?php include CONFIG_COMMON_PATH."includes/head.php";
	include CONFIG_HOOYA_PATH."includes/head.php"; ?>
	<title>bigmike — hooYa! help</title>
</head>
<body>
<div id="container">
<div id="rightframe">
	<span><a href="#" onClick="window.history.back()">back</a></span>
	<header>
		<h1>guidelines</h1>

	</header>
		<h2>rule of thumb</h2>
		<p>Tag what you see. In other words, only tag the visual and factual elements in the image. For example, images where the full moon is prominently depicted will have a full_moon tag. Ideally you should use an existing tag.
		<p>This means you should not tag what you know about a character. For example remilia_scarlet is supposed to be a vampire, but don't tag every picture depicting her with the vampire tag. Only use the vampire tag if the characteristics are clearly visible.</p>
		<p>Do not use subjective tags (such as sexy, cute, hot, etc). These tags are problematic because they are based on personal opinion, and different opinions can conflict.</p>
		<h2>series: character: and proper tagging format</h2>
		<p>Tag each picture with the general title of the show it is from using the series: tag. Crossovers deserve multiple series tags.</p>
		<p style="text-align:center">series:kill_la_kill</p>
		<p>Tag each picture with each character it includes using the character: tag, one for each character. For japanese characters, this means <span style="font-weight:bold;">lastname_firstname</span></p>
		<p style="text-align:center">character:matoi_ryuko</p>
		<p style="text-align:center">character:kiryuin_satsuki</p>
		<p>If you have any question, check <a href="danbooru.donmai.us">danbooru</a> and do not hesitate to post in the message pile or ask me on IRC or any of the other 20,000 ways we keep in touch!</p>
</div>
</div>
</body>
</html>
