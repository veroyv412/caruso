<?php
header('content-type: text/html; charset: utf-8');
ini_set('default_charset', 'UTF-8');

include_once("./classes/class.Templates.php");

session_start();
$tpl = new Template("./template");

include_once("contador.php"); //incluye el archivo que cuenta la cantidad de visitas
include_once("leftcol.php");//dentro de este archivo hace $tpl->load_file("leftcol.html", "menu");
//le inserto en $tpl->set_var("totalVisitas", $total);

$tpl->load_file("template.html", "main");
$tpl->load_file("estudio.html", "body");
$tpl->load_file("reprodMP3_Auto.html", "reproductor");
$tpl->set_var("idLink",0);
$tpl->set_var("anchorName","0");
$tpl->set_var("totalVisitas",$_SESSION['totalVisitasSession']);

$tpl->pparse("main",false);
?>
