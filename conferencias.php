<?php
header('content-type: text/html; charset: utf-8');
ini_set('default_charset', 'UTF-8');
include_once("./classes/class.Templates.php");
include_once("./classes/class.Conferencias.php");

session_start();

$tpl = new Template("./template");

$tpl->load_file("template.html", "main");
$tpl->set_var("idLink",0);
$tpl->set_var("anchorName","0");
include_once("contador.php"); //incluye el archivo que cuenta la cantidad de visitas
$tpl->set_var("totalVisitas",$_SESSION['totalVisitasSession']);

include_once("leftcol.php");//dentro de este archivo hace $tpl->load_file("leftcol.html", "menu");

showWebPage();

$tpl->pparse("main",false);


function showWebPage(){
	global $tpl;
	$idConf = $_GET["idConf"];//entonces es que no viene del Menu. Viene del Index
	
	echo $idConf;
	
	$conf = new Conferencias($idConf);
	$nameOfFile = $conf->getFileName();
	$tpl->load_file($nameOfFile, "body");
	
	$tpl->set_var("idConf", $idConf);
	
}
?>
