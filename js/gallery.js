function nextImage()
{
	for (i = 0; contents[i] != currPicture; i++);
	if (i == contents.length-1)
		i = 0;
	else
		i++;
	currPicture = contents[i];
	updateImage();
	return;
}

function previousImage()
{
	for (i = 0; contents[i] != currPicture; i++);
	if (i == 0)
		i = contents.length-1;
	else
		i--;
	currPicture = contents[i];
	updateImage();
	return;
}
function zoomImage()
{
	div = document.getElementById("picture");
	div.style.position = "absolute";
	div.style.right = "0";
	div.style.left = "0";
	div.style.height = "auto";
	div.style.zIndex = "999";
	div.style.maxHeight = "initial";
	div.onclick = function() { unzoomImage() };

	div = document.getElementById("galleryFrame");
	div.style.backgroundColor = "black";

	div = document.getElementById("next");
	div.style.display = "none";

	div = document.getElementById("previous");
	div.style.display = "none";

	div = document.getElementById("logout");
	div.style.display = "none";

	document.body.style.backgroundColor = "black";

	return;
}
function unzoomImage()
{
	div = document.getElementById("picture");
	div.style.position = "";
	div.style.top = "";
	div.style.right = "";
	div.style.bottom = "";
	div.style.left = "";
	div.style.height = "";
	div.style.zIndex = "";
	div.style.maxHeight = "100%";
	div.onclick = function() { zoomImage() };

	div = document.getElementById("galleryFrame");
	div.style.backgroundColor = "";

	div = document.getElementById("next");
	div.style.display = "";

	div = document.getElementById("previous");
	div.style.display = "";

	div = document.getElementById("logout");
	div.style.display = "";

	document.body.style.backgroundColor = "";
	return;
}
function updateImage()
{

	document.getElementById("picture").src = "share" + currDir + currPicture;
	document.getElementById("caption").innerHTML = currPicture;
	var newURL = document.URL.split("?")[0] + "?dir=" + encodeURIComponent(currDir + currPicture);
	if (window.history.replaceState) {
		window.history.replaceState(currPicture, currPicture, newURL);
	}
}

// It would be neat if I could define an array of picture objects,
// with descriptions and tags and everything! PHP could initialize
// this using an initialize function or something
function picture()
{
	return;
}
function updateQueryStringParameter(uri, key, value) {
	var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
	var separator = uri.indexOf('?') !== -1 ? "&" : "?";
	if (uri.match(re)) {
		return uri.replace(re, '$1' + key + "=" + value + '$2');
	}
	else {
		return uri + separator + key + "=" + value;
	}
}