// Navigates to the next file in the contents array, setting currFile as
// it exits
function nextFile()
{
	/* OPTIMIZATION: store index of currFile in an index so I don't 
	   need to loop through contents */
	for (i = 0; contents[i] != currFile; i++);
	if (i == contents.length-1)
		i = 0;
	else
		i++;
	currFile = contents[i];
	update();
	var newURL = document.URL.split("?")[0] + "?dir=" + encodeURI(currDir + currFile);
	if (window.history.replaceState) {
		window.history.replaceState(currFile, currFile, newURL);
	}
	return;
}
// Navigates to the previous file in the contents array, setting currFile as
// it exits
function previousFile()
{
	for (i = 0; contents[i] != currFile; i++);
	if (i == 0)
		i = contents.length-1;
	else
		i--;
	currFile = contents[i];
	update();
	var newURL = document.URL.split("?")[0] + "?dir=" + encodeURI(currDir + currFile);
	if (window.history.replaceState) {
		window.history.replaceState(currFile, currFile, newURL);
	}
	return;
}
// Currently returns the first picture it finds in the contents array
function guessAlbumArt()
{
	for (i = 0; i < contents.length && getMimetype(contents[i]) != "image"; i++);
	if (i < contents.length)
		return "share" + currDir + contents[i];
	return;
}
// triggered by onClick of a picture in the gallery
function zoomImage(element)
{
	document.removeEventListener('keyup', doc_hotkeys);
	div = document.getElementById(element);
	div.style.position = "absolute";
	div.style.right = "0";
	div.style.left = "0";
	div.style.height = "auto";
	div.style.zIndex = "999";
	div.style.maxHeight = "initial";
	div.style.cursor = "zoom-out";
	div.onclick = function() { unzoomImage("picture") };

	div = document.getElementById("right_frame");
	div.style.backgroundColor = "inherit";
	div.style.color = "inherit";

	div = document.getElementById("next");
	div.style.display = "none";

	div = document.getElementById("previous");
	div.style.display = "none";

	div = document.getElementById("logout");
	div.style.display = "none";

	div = document.getElementById("title");
	div.style.color = "inherit";

	return;
}
// triggered by onClick of a zoomed-in picture in the gallery
function unzoomImage(element)
{
	document.addEventListener('keyup', doc_hotkeys, false);
	div = document.getElementById(element);
	div.style.position = "";
	div.style.top = "";
	div.style.right = "";
	div.style.bottom = "";
	div.style.left = "";
	div.style.height = "";
	div.style.zIndex = "";
	div.style.maxHeight = "100%";
	div.style.cursor = "zoom-in";
	div.onclick = function() { zoomImage("picture") };

	div = document.getElementById("right_frame");
	div.style.backgroundColor = "";
	div.style.color = "";

	div = document.getElementById("next");
	div.style.display = "";

	div = document.getElementById("previous");
	div.style.display = "";

	div = document.getElementById("logout");
	div.style.display = "";

	return;
}
// create an image in the "content" div and fill it out
function updateImage()
{
	document.getElementById("content").innerHTML = '<img id="picture"></img>';
	document.getElementById("picture").src = "download.php?dir=" + currDir + currFile;
	document.getElementById("picture").onclick = function() { zoomImage("picture") };
	document.getElementById("picture").style.cursor = "zoom-in";
	return;
}
// create a video in the "content" div and fill it out
function updateVideo()
{
	document.getElementById("content").innerHTML = '<video id="video" controls loop preload="auto"></video>';
	var source = document.createElement('source');
	source.setAttribute('src', 'download.php?dir=' + currDir + currFile)
	video.appendChild(source);
//	document.getElementById("video").setAttribute('src', "download.php?dir=" + currDir + currFile);
	document.getElementById("video").style.cursor = "pointer";
	return;
}
// create a video in the "content" div and fill it out
function updateAudio()
{
	document.getElementById("content").innerHTML = '<div id="songinfo"><div style="float:left;width:50%;" id="info">&nbsp</div><img style="float:left;width:50%" id="albumart"></img><audio controls id="audio"></audio>'
	document.getElementById("songinfo").style.height = "90%";
	var art = guessAlbumArt();
	if (art)
		document.getElementById("albumart").src = art;
	document.getElementById("audio").src = "download.php?dir=" + currDir + currFile;
	document.getElementById("audio").style.display = "block";
	document.getElementById("audio").style.width = "100%";
	document.getElementById("audio").style.height = "10%";
	document.getElementById("audio").onended = function() {
		nextFile();
		document.getElementById("audio").autoplay = true;
	};
	document.getElementById("albumart").style.cursor = "initial";
	return;
}
// create a link in the "content" div and fill it out
function updateLink()
{
	document.getElementById("content").innerHTML = '<a id="dlink">download this file!</br>⬇</a>';
	document.getElementById("dlink").href = "download.php?dir=" + encodeURIComponent(currDir + currFile) + "&PHPSESSID=" + getCookie("PHPSESSID");
	document.getElementById("dlink").style.top = "50%";
	document.getElementById("dlink").style.position = "relative";
	document.getElementById("dlink").style.transform = "translateY(-50%)";
	return;
}
function getCookie(cname) {
	var name = cname + "=";
	var ca = document.cookie.split(';');
	for(var i=0; i<ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1);
		if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
	}
	return "";
}
// returns the trailing piece of a filename past the last "."
function getExtension(filename)
{
	return filename.slice((filename.lastIndexOf(".") - 1 >>> 0) + 2);
}
// simple file detector that returns the first part of a mimetype
// this provides information on how to display content
function getMimetype(filename)
{
	switch (getExtension(filename)) {
		case "jpg":
		case "png":
		case "gif":
		case "jpeg":
			return "image";
		case "mp4":
		case "webm":
		case "avi":
		case "wmv":
		case "mpg":
			return "video";
		case "mp3":
		case "flac":
		case "m4a":
		case "wav":
			return "audio";
		default:
			return "unsupported";
	}
}

// It would be neat if I could define an array of picture objects,
// with descriptions and tags and everything! PHP could initialize
// this using an initialize function or something
function picture()
{
	return;
}
// makes the decision about how to display content
function update() {
	document.getElementById("caption").innerHTML = currFile;
	switch(getMimetype(currFile)) {
		case "image":
			updateImage();
			break;
		case "video":
			updateVideo();
			break;
		case "audio":
			updateAudio();
			break;
		default:
			updateLink();
			break;
	}
	return;
}
function doc_hotkeys(e) {
	switch(e.keyCode) {
		case 39:
		case 72:
			nextFile();
			break;
		case 37:
		case 76:
			previousFile();
			break;
		case 32:
			// toggle zoom coming soon
	}
}

document.addEventListener('keyup', doc_hotkeys, false);

window.onload = update;