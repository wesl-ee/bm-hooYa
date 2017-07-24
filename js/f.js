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
	var tagform = document.getElementById('tagform');
	var boxes = tagform.querySelectorAll('input');
	for (var i=0; i < boxes.length; i++)
		// Why do you need another box?
		if (!boxes[i].value) {boxes[i].focus(); return;}
		if (i/2 >= maxtags) { alert("Too many tags!"); return; }
	var space = document.createElement('input');
	var member = document.createElement('input');
	space.type = 'text';
	space.id = 'space_box';
	space.name = 'tag_space[]';
	space.addEventListener('keydown', inputFilter);

	member.type='text';
	member.id = 'member_box';
	member.name='tag_member[]';
	member.addEventListener('keydown', inputFilter);

	document.getElementById('tagform').appendChild(space);
	document.getElementById('tagform').appendChild(member);
	space.focus();
}
