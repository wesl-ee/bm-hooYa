/*
MicroTXT - A tiny PHP Textboard Software
Copyright (c) 2016 Kevin Froman (https://ChaosWebs.net/)

MIT License
*/
function replyTitle()
{
	document.getElementById('replyTitle').scrollIntoView();
}
	document.getElementById('replyTo').value = document.getElementById('OP').innerHTML;

	function clickItem(item)
	{
		if (item == "OP")
		{
			document.getElementById('replyTo').value = document.getElementById('OP').innerHTML;
			replyTitle();
		}
		else
		{
			document.getElementById('replyTo').value = item;
			replyTitle();
		}
	}
