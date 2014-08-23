<?php
ini_set('default_charset', 'UTF-8');
include_once("./classes/class.Templates.php");

session_start();

$tpl = new Template("./template");

$tpl->load_file("template.html", "main");
$tpl->set_var("idLink",0);
$tpl->set_var("anchorName","0");
$tpl->set_var("totalVisitas",$_SESSION['totalVisitasSession']);

include_once("leftcol.php");//dentro de este archivo hace $tpl->load_file("leftcol.html", "menu");

showWebPage();

$tpl->pparse("main",false);

function showWebPage(){
	global $tpl;

	$idEspecialidad = $_GET["idEspecialidad"];
	switch ($idEspecialidad) {
		case 1://Estudio
			$tpl->load_file("art_especialidad1.html", "body");
			break;
		case 2://Areas de Practica
			$tpl->load_file("art_especialidad2.html", "body");
			break;
		case 3://titular
			$tpl->load_file("art_especialidad3.html", "body");
			break;
	}
	
}
?>
