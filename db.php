<?php
if(!mysql_connect('localhost','root','') || !mysql_select_db('zoomrx'))
{
  echo 'database error!!';
  die(); 
}
?>