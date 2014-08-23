<?
//header('content-type: text/html; charset: utf-8');
//ini_set('default_charset', 'UTF-8');
include_once("./classes/class.Templates.php");

session_start();

$total = 0;

if ($_SESSION['totalVisitasSession'] == ""){
	// Archivo en donde se acumular� el numero de visitas
	$archivo = "numero.dat";
	// Abrimos el archivo para solamente leerlo (r de read)
	$abre = fopen($archivo, "r");
	// Leemos el contenido del archivo
	$total = fread($abre, filesize($archivo));
	// Cerramos la conexi�n al archivo
	fclose($abre);
	// Abrimos nuevamente el archivo
	$abre = fopen($archivo, "w");
	// Sumamos 1 nueva visita
	$total = $total + 1;
	// Y reemplazamos por la nueva cantidad de visitas 
	$grabar = fwrite($abre, $total);
	// Cerramos la conexi�n al archivo
	fclose($abre);

	$_SESSION['totalVisitasSession'] = $total; 
}
?>

