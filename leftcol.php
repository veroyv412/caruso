<?php
//header('content-type: text/html; charset: utf-8');
//ini_set('default_charset', 'UTF-8');

$tpl->set_var("headerDate",date("d") . " de " . getMonth() . " de " . date("Y"));

$tpl->load_file("menu.html", "menu");


function getMonth(){
	switch (date("F")) {
		case "January":
			$mes = "Enero";
			break;
		case "February":
			$mes = "Febrero";
			break;
		case "March":
			$mes = "Marzo";
			break;
		case "April":
			$mes = "Abril";
			break;
		case "May":
			$mes = "Mayo";
			break;
		case "June":
			$mes = "Junio";
			break;
		case "July":
			$mes = "Julio";
			break;
		case "August":
			$mes = "Agosto";
			break;
		case "September":
			$mes = "Septiembre";
			break;
		case "October":
			$mes = "Octubre";
			break;
		case "November":
			$mes = "Noviembre";
			break;
		case "December":
			$mes = "Diciembre";
			break;
	}

	return $mes;
}

?>
