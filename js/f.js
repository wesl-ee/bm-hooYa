// Meant for input boxes, replace spaces with underscores
function inputFilter(e)
{
	switch (e.keyCode) {
	// Replace spaces with underscores unless that would make
	// two underscores in a row
	case 32:
		if (e.target.value.slice(-1) == '_') {
			a = e.target.value;
			e.target.value = a.substring(0, a.length - 1);
		}
		else {
			e.preventDefault();
			e.target.value += '_';
		}
	}
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
	space.addEventListener('keydown', inputFilter);
	td.appendChild(space);
	tr.appendChild(td);

	td = document.createElement('td');
	member.type='text';
	member.name='tag_member[]';
	member.addEventListener('keydown', inputFilter);
	td.appendChild(member);
	tr.appendChild(td);

	// Ship the inputs with the other tags
	tags.appendChild(tr);

	space.focus();
}
