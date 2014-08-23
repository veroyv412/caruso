<?php
header('content-type: text/html; charset: utf-8');
ini_set('default_charset', 'UTF-8');
include_once("./classes/class.Templates.php");

session_start();

$tpl = new Template("./template");

$tpl->load_file("template.html", "main");
$tpl->load_file("reprodMP3_Auto.html", "reproductor");
$tpl->set_var("idLink",0);
$tpl->set_var("anchorName","0");
include_once("contador.php"); //incluye el archivo que cuenta la cantidad de visitas
$tpl->set_var("totalVisitas",$_SESSION['totalVisitasSession']);

include_once("leftcol.php");//dentro de este archivo hace $tpl->load_file("leftcol.html", "menu");

showWebPage();

$tpl->pparse("main",false);


function showWebPage(){
	global $tpl;
	$idMenu = $_GET["idMenu"];//entonces es que no viene del Menu. Viene del Index
	if ($idMenu == ""){
		$idMenu = 1;	
	}
	
	switch ($idMenu) {
		case 1://Estudio
			$tpl->load_file("estudio.html", "body");
			break;
		case 2://Areas de Practica
			$tpl->load_file("areasDePractica.html", "body");
			break;
		case 3://titular
			$tpl->load_file("titular.html", "body");
			break;
		case 4://aranceles
			$tpl->load_file("aranceles.html", "body");
			break;
		case 5://contacto
			$tpl->load_file("contacto.html", "body");
			break;
		case 6://conferencias
			$tpl->load_file("conferencias.html", "body");
			break;
	}
	$tpl->set_var("idMenu", $idMenu);
	
}
?>
