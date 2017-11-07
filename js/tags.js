var n = document.getElementsByClassName('space');
for (var i = 0; i < n.length; i++) {
	n[i].addEventListener('input', function(e) {
		var suggestlist = document.getElementById('namespace-suggest-list');
		if (this.value.length < 1) {
			while (suggestlist.lastChild) {
				suggestlist.removeChild(suggestlist.lastChild);
			}
			return;
		}
		var suggested = suggestlist.childNodes;
		remote_suggestnamespace(this, suggestlist);
	});
}
var n = document.getElementsByClassName('member');
for (var i = 0; i < n.length; i++) {
	n[i].addEventListener('input', function(e) {
		var suggestlist = document.getElementById('member-suggest-list');
		if (this.value.length < 1) {
			while (suggestlist.lastChild) {
				suggestlist.removeChild(suggestlist.lastChild);
			}
			return;
		}
		var suggested = suggestlist.childNodes;
		remote_suggest(this, suggestlist);
	});
}
// Insert an additional space => member pair of input boxes
function addTagField()
{
	var tags = document.getElementById('tags');
	var boxes = tags.querySelectorAll('input');
	for (var i=0; i < boxes.length; i++)
		// Why do you need another box?
		if (!boxes[i].value) {boxes[i].focus(); return;}
		if (i/2 >= maxtags) { alert("Too many tags!"); return; }

	// Generate a new row with two inputs
	var tr = document.createElement('tr');
	var td;
	var space = document.createElement('input');
	var member = document.createElement('input');


	td = document.createElement('td');
	space.type = 'text';
	space.name = 'tag_space[]';
	space.className = 'space';
	space.setAttribute('list', 'namespace-suggest-list');
	space.addEventListener('input', function(e) {
		var suggestlist = document.getElementById('namespace-suggest-list');
		if (this.value.length < 1) {
			while (suggestlist.lastChild) {
				suggestlist.removeChild(suggestlist.lastChild);
			}
			return;
		}
		var suggested = suggestlist.childNodes;
		remote_suggestnamespace(this, suggestlist);
        });
	td.appendChild(space);
	tr.appendChild(td);

	td = document.createElement('td');
	member.type='text';
	member.name='tag_member[]';
	member.className='member';
	member.setAttribute('list', 'member-suggest-list');
	member.addEventListener('input', function(e) {
		var suggestlist = document.getElementById('member-suggest-list');
		if (this.value.length < 1) {
			while (suggestlist.lastChild) {
				suggestlist.removeChild(suggestlist.lastChild);
			}
			return;
		}
		var suggested = suggestlist.childNodes;
		remote_suggest(this, suggestlist);
        });
	td.appendChild(member);
	tr.appendChild(td);

	// Ship the inputs with the other tags
	tags.appendChild(tr);

	space.focus();
}
