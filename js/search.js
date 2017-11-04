function enhance_powersearch() {
	var form = document.getElementById('search');
	form.addEventListener("submit", function() {
		var media_type = document.getElementById(
			document.getElementById('media_class').value
		);
		var width, height;

		var inputs = media_type.getElementsByTagName('input');
		for (i = 0; i < inputs.length; i++) {
			// Do not submit empty parameters for our search form
			if (inputs[i].value === '') { inputs[i].disabled = true; continue }
			if (inputs[i].name == 'properties[Width]') width = inputs[i].value;
			if (inputs[i].name == 'properties[Height]') height = inputs[i].value;
		}
		var selects = media_type.getElementsByTagName('select');
		for (i = 0; i < selects.length; i++) {
			if (selects[i].value === '') selects[i].disabled = true;
			// Special handling for "Respect aspect ratio" parameter
			if (selects[i].name === 'properties[Ratio]'
			&& selects[i].value === 'ratio') {
				// Remove the select button
				selects[i].parentNode.removeChild(selects[i]);

				// Calculate the ratio and add it the the form
				// Fast round to the third decimal place in JS
				var ratio = Math.round((width/height)*1000)/1000;
				var ratio_input = document.createElement('input');
				ratio_input.name = 'properties[Ratio]';
				ratio_input.value = ratio;
				media_type.appendChild(ratio_input);

				// Since we only care about a ratio, ignore the exact dimensions
				var inputs = form.getElementsByTagName('input');
				for (j = 0; j < inputs.length; j++) {
					if (inputs[j].name == 'properties[Width]')
						inputs[j].disabled = true;
					if (inputs[j].name == 'properties[Height]')
						inputs[j].disabled = true;
				}
			}
		}

	}, false);
	// Update the media class filter for its initial value
	changeExtAttrs(document.getElementById('media_class').value);
}

function changeExtAttrs(media_class) {
	classes.forEach(function (c) {
		var classdiv = document.getElementById(c);
		classdiv.style.display = 'none';
		toggleinputs(classdiv, false)
	});
	if (!media_class) return;
	var currclass = document.getElementById(media_class);
	currclass.style.display = '';
	toggleinputs(currclass, 'true');
}
function toggleinputs(div, doenable) {
	var inputs = div.getElementsByTagName("input")
	for(i = 0; i < inputs.length; i++) {
		inputs[i].disabled = !doenable;
	};
	var selects = div.getElementsByTagName("select")
	for(i = 0; i < selects.length; i++) {
		selects[i].disabled = !doenable;
	};
}
function remote_suggest(queryfield, suggestfield)
{
	var queryText = queryfield.value;
	var xhr = new XMLHttpRequest();
	xhr.onreadystatechange = function() { if (this.readyState == 4 &&
	this.status == 200) {
		suggestfield.value = this.responseText;
	} }
	var uri = "?q=" + queryText;
	xhr.open('GET', 'hint.php' + uri);
	xhr.send();
}
document.getElementById('query').addEventListener('input', function(e) {
	var suggestfield = document.getElementById('suggest');
	if (this.value.length < 3) {
		suggestfield.value = '';
		return;
	}
	// If the suggestion is correct so-far, do not get another suggestion
	if (suggestfield.value.indexOf(this.value) == -1) {
		remote_suggest(this, suggestfield);
	}
});
// EventListener on input overrides the nice enter-to-submit built in to
// a lot of browsers, so I redo it here
document.getElementById('query').addEventListener('keydown', function(e) {
	// Submit query when enter is pressed
	if (e.which == 13) document.getElementById('search').submit();
	// Complete suggestion if right arrow is pressed
	if (e.which == 39 && document.getElementById('suggest').value != '') {
		document.getElementById('query').value =
		document.getElementById('suggest').value;
	}
});
