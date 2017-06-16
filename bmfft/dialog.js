function add_tags(key)
{
	var w = window.open("", "adding tags for file", "resizable,status,width=625,height=400");
	var d = w.document;
	var xhttp = new XMLHttpRequest();
	var existing_tags;
	// Rudimentary AJAX for getting existing tags
	// Eventually we will have a suggestion function which reacts on-input
	// and brings tags as you type, to make it easier on people who tag!
	xhttp.open("GET", "bmfft_db.php?key="+encodeURIComponent(key)+"&tags", true);
        xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
		existing_tags = JSON.parse(this.responseText);
		if (existing_tags) {
			d.getElementById('tags').value=existing_tags.join(' ') + ' ';
			d.getElementById('tags').focus();
			d.getElementById('tags').selectionStart = d.getElementById('tags').selectionEnd = d.getElementById('tags').value.length;
		}
		else {
			d.getElementById('tags').focus();
			d.getElementById('header').innerHTML = '<h1>Wow!</h1>You can be the first one to tag ' + key + '!';
		}
		}
	};
	xhttp.send();

	// I read this style in a 1998 javascript book so please mail me
	// if there is a better way to mark this up~
	d.write('<html>');
	d.write('<head>');
	d.write('</head>');
	d.write('<body>');
	d.write('<div style="width:100%">');
		d.write('<div style="width:33%;float:left;"><a href="#" onClick="window.close()">close</a></div>');
		d.write('<div style="width:33%;float:left;text-align:center;">&nbsp</div>');
		d.write('<div style="width:33%;float:left;text-align:right;"><a href="guidelines.php">tagging guidelines</a></div>');
	d.write('</div>');
	d.write('<div id="header" style="text-align:center;"></div>');
	d.write('<div style="width:100%;text-align:center">');
	d.write('Enter relevant, space-seperated tags<br/>');
	d.write('<form action="view.php?key='+encodeURIComponent(key)+'" method="post">');
	d.write('<input id="tags" style="width:100%;" type="text" name="tags"></input><br/>');
	d.write('<input type="submit" value="行こう！">');
	d.write('</form>');
	d.write('</div>');
	d.write('<div style="width:100%;">');
	d.write('</div>');
	d.write('</body>');
	d.close();
	return true;
}
function view_tags(key)
{
	var w = window.open("", "viewing tags for file", "resizable,status,width=625,height=400");
	var d = w.document;
	var xhttp = new XMLHttpRequest();
	// More rudimentary AJAX communication w/ the site
	xhttp.open("GET", "bmfft_db.php?key="+encodeURIComponent(key)+"&tags", true);
        xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
		tags = JSON.parse(this.responseText);
		if (tags) {
			d.getElementById('content').innerHTML="<h1>tags</h1>"
			for (var i=0; i < tags.length; i++)
				d.getElementById('content').innerHTML+= tags[i]+'<br/>';
		}
		else {
			d.getElementById('content').innerHTML="<h1>Wow!</h1>This file has no tags. . .<br/>maybe you can help tag to tag it by clicking on the picture~";
		}
		}
	};
	xhttp.send();
	// I want some way to include the user's style in this. . . so
	// maybe I will just write another page and fill it in with
	// the AJAX data
	d.write('<html>');
	d.write('<head>');
	d.write('</head>');
	d.write('<body>');
	d.write('<div style="width:100%">');
		d.write('<div style="width:33%;float:left;"><a href="#" onClick="window.close()">close</a></div>');
		d.write('<div style="width:33%;float:left;text-align:center;">&nbsp</div>');
		d.write('<div style="width:33%;float:left;text-align:right;"><a href="guidelines.php">tagging guidelines</a></div>');
	d.write('</div>');
	d.write('<div id="content" style="width:100%;text-align:center">');
	d.write('</div>');
	d.write('</body>');
	d.close();
	return true;
}