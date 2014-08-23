<?php
header('content-type: text/html; charset: utf-8');
ini_set('default_charset', 'UTF-8');

include_once("./classes/class.Templates.php");

session_start();
$tpl = new Template("./template");
$tpl->load_file("template.html", "main");

include_once("leftcol.php");//dentro de este archivo hace $tpl->load_file("leftcol.html", "menu");

showWebPage();

$tpl->pparse("main",true);

function showWebPage(){
	global $tpl;

	$idLink = $_GET["idLink"];
	$tpl->set_var("idLink",$idLink);
	$tpl->set_var("anchorName","");
	switch ($idLink) {
		case 0:
			$tpl->load_file("cv_entero.html", "body");
			break;
		case 1://Areas de Practica
			$tpl->load_file("cv_entero1link.html", "body");
			$tpl->set_var("anchorName","#esp_com_exterior");
			break;
		case 2://titular
			$tpl->load_file("cv_entero2link.html", "body");
			$tpl->set_var("anchorName","#esp_act_comercial");
			break;
		case 3://titular
			$tpl->load_file("cv_entero3link.html", "body");
			$tpl->set_var("anchorName","#esp_lab_farmaceutico");
			break;
		case 4://titular
			$tpl->load_file("cv_entero4link.html", "body");
			$tpl->set_var("anchorName","#esp_dcho_bancario");
			break;
		case 5://titular
			$tpl->load_file("cv_entero5link.html", "body");
			$tpl->set_var("anchorName","#esp_mercado_financiero");
			break;
		case 6://titular
			$tpl->load_file("cv_entero6link.html", "body");
			$tpl->set_var("anchorName","#esp_exp_agraria");
			break;
	}
	
}

?>