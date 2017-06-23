<?php
/*
MicroTXT - A tiny PHP Textboard Software
Copyright (c) 2016 Kevin Froman (https://ChaosWebs.net/)

MIT License
*/

class MyDB extends SQLite3
{
   function __construct()
   {
      $this->open('php/threadList.db');
   }
}
$db = new MyDB();

if(!$db){
   die($db->lastErrorMsg());
}

?>
