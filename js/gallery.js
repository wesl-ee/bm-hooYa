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
	alert("zoom");
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