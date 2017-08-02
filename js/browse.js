function showThumbInfo(key)
{
	var xhttp = new XMLHttpRequest();


	var leftframe = document.getElementById('leftframe');
	var aside = document.createElement('aside');
	aside.setAttribute('id', key);

	var header = document.createElement('header');

	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			if (this.responseText) {
				header.innerHTML = this.responseText;
				leftframe.appendChild(aside);
			}
		}
	};
	xhttp.open("GET", "info.php?key=" + key, true);
	xhttp.send();

	aside.appendChild(header);

}
function hideThumbInfo(key)
{
	var leftframe = document.getElementById('leftframe');
	leftframe.removeChild(document.getElementById(key));
}
