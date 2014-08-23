<?php
/***************************************
*			 COMMON				   *
***************************************/

lastUpdate();


function lastUpdate(){
	global $tpl;

	$tpl->set_var("dateUpdate",getParam("lastUpdate"));
}

function getParam($paramName){
	global $db;
	global $tpl;

	$sSQL = "select value from params where param = '$paramName'";
	$db->query($sSQL);

	if ($db->next_record())
		return $db->f("value"); 
	else
		return "ERROR";
}
?>