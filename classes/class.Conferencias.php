<?php
/*********************************************************************************
 *       Filename: conferencias.php
 *********************************************************************************/

class Conferencias 
{

  var $fileName;
  
  function Conferencias($fName)
  {
    $this->fileName = $fName."_confPublicacion.html";
  }

  
  function getFileName() {
    return $this->fileName;
  }


  function setFileName($fName)
  {
    $this->fileName = $fName;
  }
  
}

?>
