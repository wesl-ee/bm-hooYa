function remote_suggest(queryfield, suggestlist)
{
	var queryText = queryfield.value;
	var xhr = new XMLHttpRequest();
	xhr.onreadystatechange = function() { if (this.readyState == 4 &&
	this.status == 200) {
		var suggests = JSON.parse(this.responseText);
		if (!suggests) return;
		var suggestedtext = [];
		var suggested = suggestlist.children;
		for (i = 0; i < suggested.length; i++) {
			if (suggests.indexOf(suggested[i].value) == -1)
				suggested[i--].remove();
			else
				suggestedtext.push(suggested[i].value);
		}
		suggests.forEach(function(suggest) {
			// Do not add the same suggestion twice!
			if (suggestedtext.indexOf(suggest) != -1) return;
			var option = document.createElement('option');
			option.value = suggest;
			suggestlist.appendChild(option);
		});
	} }
	var uri = "?q=" + queryText;
	xhr.open('GET', 'hint.php' + uri);
	xhr.send();
}
