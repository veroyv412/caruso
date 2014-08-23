<?php
require ("class.phpmailer.php");
class Mailer {
	var $phpm;
	var $idIMG;										// AutoID de incrustacion de imagenes ;)

	/** Constructor */
	function Mailer () {
		$this->idIMG = 0;							// Cada vez que inserta una imagen lo autoincremento
		$this->phpm = new PHPMailer();
		// Configuraciones raras del script
		$this->phpm->AltBody = "";							// ????????
	    $this->phpm->WordWrap = 0;							// En esta longitud se corta el mensaje ???
	    $this->phpm->SMTPDebug = false;						// ????????
		$this->phpm->PluginDir = "";						// ????????
		//$this->phpm->SetLanguage("es", "language/");		// es:spanish, en:english
		// Metodo o servicio a utilizar para mandar el email
		$this->phpm->IsSMTP();								// Mailer = "smtp" using SMTP
		//$this->phpm->IsMail();							// Mailer = "mail" using PHP mail() function
		//$this->phpm->IsSendmail();						// Mailer = "sendmail" using SendMail program
		//$this->phpm->IsQmail();							// Mailer = "sendmail" using the qmail MTA
		//$this->phpm->Sendmail = "/usr/sbin/sendmail";		// PATH Al programa sendMail
		}

	// Configura parametros de los emails enviados
	function setUpMailer ($hostname, $host, $port, $helo, $auth, $usr, $psw, $tout, $keepA) {
	    $this->phpm->Hostname = $hostname;				// Mail server
	    $this->phpm->Host = $host;		     			// Host que es el servidor de emails
		$this->phpm->Port = $port;						// Puerto
	    $this->phpm->Helo = $helo;						// El default es el HostName (Se hace un Helo Hostname)
	    $this->phpm->SMTPAuth = $auth;					// Si se pone true se usa Username & Password
		$this->phpm->Username = $usr;					// Solo si "SMTPAuth = true"
		$this->phpm->Password = $psw;					// Solo si "SMTPAuth = true"
	    $this->phpm->Timeout = $tout;					// Server Timeout
	    $this->phpm->SMTPKeepAlive = $keepA;			// Evita la desconexion automatica
	}

	// Agrega un texto al body
	function ponerEnCuerpo ($texto) {
		$this->phpm->Body .= $texto;
	}

	// Pega una imagen en el cuerpo del mensaje
	function incrustarImagen ($imgPath, $imgComm, $tipo) {
		$this->idIMG++;
		$this->phpm->Body .= "<img alt=\"" . $imgComm . "\" src=\"cid:imgID" . $this->idIMG . "\">";
		$this->phpm->AddEmbeddedImage($imgPath, "imgID" . $this->idIMG, $imgPath, "base64", $tipo);
	}

	function getHTMLImage($imgPath, $imgComm, $tipo){
		$this->idIMG++;
		$this->phpm->AddEmbeddedImage($imgPath, "imgID" . $this->idIMG, $imgComm, "base64", $tipo);
//		return "<img alt=\"" . $imgComm . "\" src=\"cid:imgID" . $this->idIMG . "\">";
		return "src=\"cid:imgID" . $this->idIMG;
	}

	// Agrega un adjunto
	function agregarAdjunto ($filePath,$attName=null) {
		$this->phpm->AddAttachment($filePath, $filePath);
	}

	// Agrega un string "$string" como un archivo adjunto de nombre "$AsfileName"
	function agregarStrComoAdjunto ($string, $AsfileName) {
		$this->phpm->AddStringAttachment($string, $AsfileName);
	}

	// Preparo todo para un nuevo email
	function clearALL () {
		// Borro lo que este seteado dentro de la clase (UTIL PARA REUTILIZAR LA INSTANCIA !!!)
		$this->phpm->ClearAddresses();
		$this->phpm->ClearCCs();
		$this->phpm->ClearBCCs();
		$this->phpm->ClearReplyTos();
		$this->phpm->ClearAllRecipients();
		$this->phpm->ClearAttachments();
		$this->phpm->ClearCustomHeaders();
		$this->phpm->Body = "";
	}

	// Retorna el ultimo error ocurrido
	function getLastError () {
		return ($this->phpm->ErrorInfo);
	}

	// Configura el email a enviar
	function initEmail ($prior, $enco, $chst, $htm, $fromEmail, $fromName, $sender, $confTo, $subject) {
		$this->phpm->Priority = $prior;				// 1 = High, 3 = Normal, 5 = low
		$this->phpm->Encoding = $enco;				// "8bit", "7bit", "binary", "base64", and "quoted-printable"
		$this->phpm->CharSet  = $chst;				// Ver porque hay varios tipos
		$this->phpm->IsHTML(($htm == "html"));		// Si pongo FALSE pasa a ser "text/plain"
		$this->phpm->From = $fromEmail;				// em@il From
		$this->phpm->FromName = $fromName;			// Nombre del From
		$this->phpm->Sender = $sender;				// Email real del sender
		$this->phpm->ConfirmReadingTo  = $confTo;	// Direccion de email de confirmacion de lectura
	    $this->phpm->Subject = $subject;			// Asunto
	}

	// Agrega direcciones Reply-TO al email
	function AddReplyTo($email, $name) {
		$this->phpm->AddReplyTo($email, $name);
	}

	// Agrega direcciones TO al email
	function AddTo($email, $name) {
		$this->phpm->AddAddress($email, $name);
	}

	// Agrega direcciones CC al email
	function AddCC($email, $name) {
		$this->phpm->AddCC($email, $name);
	}

	// Agrega direcciones BCC al email
	function AddBCC($email, $name) {
		$this->phpm->AddBCC($email, $name);
	}

	// Envia un email y retorna un booleano
	function sendEmail() {
		return ($this->phpm->Send());
	}

} // fin de clase
?>
