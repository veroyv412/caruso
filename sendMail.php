<?php
header('content-type: text/html; charset: utf-8');
ini_set('default_charset', 'UTF-8');

include_once("./classes/class.Templates.php");
include_once("./classes/mailer/class.mailer.php");
include_once("./classes/mailer/class.phpmailer.php");

session_start();
$tpl = new Template("./template");
$tpl->load_file("template.html", "main");
$tpl->load_file("estudio.html", "body");


include_once("leftcol.php");//dentro de este archivo hace $tpl->load_file("leftcol.html", "menu");

showWebPage();

$tpl->pparse("main",true);

function showWebPage(){
	global $tpl;
		
    $name = $_POST["txtName"];
	$from = $_POST["txtEmail"];
	$subject = $_POST["txtSubject"];
	$message = $_POST["txtArea"];
	
	$mail             = new PHPMailer(); // defaults to using php "mail()"

	//$body  = " - "."<p>Ha recibido un mensaje de ".$name." (E-mail: "+ $from +").</p><p>".$subject."</p><p>".$message."</p><p>&nbsp;</p><p>Muchas Gracias. </p>";  
	$body = "<html>".
"<head>".
"<title>Web Mail</title>".
"</head>".
"<body>".
"<table>".
	"<tr>".
		"<td width='19%' height='32' colspan='2'><strong>Mensaje Web desde la pagina... &nbsp;</strong></td>".
	"</tr>".
"</table>".
"<table width='80%' style='font-family:Verdana, Arial, Helvetica, sans-serif; font-size:13px'>".
	"<tr><td align='right' width='19%'><strong>Nombre y Apellido: &nbsp;&nbsp;</strong></td>".
	"<td width='81%'>".$name."</td>".
"</tr>".
"<tr><td align='right'><strong>E-Mail:&nbsp;&nbsp;</strong></td>".
"<td>".$from."</td>".
"</tr>".
"<tr>".
	"<td align='right'><strong>Tema: &nbsp;&nbsp;</strong></td>".
	"<td>".$subject."</td>".
"</tr>".
"<tr>".
	"<td align='right'><strong>Mensaje:&nbsp;&nbsp;</strong></td>".
	"<td>".$message."</td>".
"</tr>".
"</table>".
"</body>".
"</html>";

	$mail->From       = $from;
	$mail->FromName   = $name;
	$mail->Subject    = $subject;
	$mail->AltBody    = ""; // optional, comment out and test
	$mail->MsgHTML($body);
	$mail->AddAddress("horaciocaruso@carusoh.com.ar", "Horacio F. Caruso");//to  whom  I'm sending
	
	if(!$mail->Send()){  
       echo "There has been a mail error sending to " . $from . "<br>";  
	   $tpl->load_file("errorSendingMail.html","body");
	}
	else{
		$tpl->load_file("confirmacionMail.html","body");
	}
     // Clear all addresses and attachments for next loop  
     $mail->ClearAddresses();  
     $mail->ClearAttachments();  
}
?>