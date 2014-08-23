<?php

function dbConnection(){
	$host        = 'localhost';
	$dataBase    = 'globalfit';
	$user        = 'root';
	$pass        = 'toor';
	$persistency = false;
	$db          = new MySQL($host, $dataBase, $user, $pass, $persistency);
	return $db;

}

?>
