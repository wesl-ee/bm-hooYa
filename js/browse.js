function showThumbInfo(key)
{
	var key = document.getElementById(key);
	key.style.display = '';
}
function hideThumbInfo(key)
{
	var key = document.getElementById(key);
	// Without the timeout, cuts look too sharp
	// maybe do a css fade here
	setTimeout(function() {
		key.style.display = 'none';
	}, 50);
}
