function showThumbInfo(key)
{
	var leftframe = document.getElementById('leftframe');
	var aside = document.createElement('aside');
	aside.setAttribute('id', key);

	var header = document.createElement('header');
	header.innerHTML = key;

	aside.appendChild(header);
	leftframe.appendChild(aside);
}
function hideThumbInfo(key)
{
	var leftframe = document.getElementById('leftframe');
	setTimeout(function(){
		leftframe.removeChild(document.getElementById(key));
	}, 2000);
}
