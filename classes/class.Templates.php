<?php
/*********************************************************************************
 *       Filename: template.php
 *       PHP 4.0 & Templates build 11/30/2001
 *               
 *       Usage:
 *       $tpl = new Template($app_path);
 *       $tpl->load_file($template_filename, "main");
 *       $tpl->set_var("ID", 2);
 *       $tpl->set_var("Value", "Name");
 *       $tpl->parse("DynBlock", false); // true if you want to create a list
 *
 *       $tpl->pparse("main", false); // parse and output block 
 *                 OR
 *       $tpl->parse("main", false); // parse block
 *       $tpl->print_var("main");    // output block 
 *********************************************************************************/

class Template 
{

  var $sTemplate;
  var $DBlocks = array();       // initial data:files and blocks
  var $ParsedBlocks= array();   // resulted data and variables
  var $templates_root;
  var $tempout;

  function Template($templates_root)
  {
    $this->templates_root = $templates_root;
  }

  function load_file($filename, $sName) {
    $nName = "";
    $template_path = $this->templates_root . "/" . $filename;
    if (file_exists($template_path)) {
      $this->DBlocks[$sName] = join('',file($template_path));
      $nName = $this->NextDBlockName($sName);
      while ($nName != "") {
        $this->SetBlock($sName, $nName);
        $nName = $this->NextDBlockName($sName);
      }
    }
  }

  function load_file_section($filename, $targetBlock, $sectionName) {
    $nName = "";
    $template_path = $this->templates_root . "/" . $filename;
    if (file_exists($template_path)) {
       $holeFile = join('',file($template_path));
	   $posStart =  strpos($holeFile, "<!--Begin" . trim($sectionName) . "-->");
	   $posEnd = strpos($holeFile, "<!--End" . trim($sectionName) . "-->") + strlen("<!--End" . trim($sectionName) . "-->");
	   $this->DBlocks[$targetBlock] = substr ($holeFile, $posStart, $posEnd);
       $nName = $this->NextDBlockName($targetBlock);
       while ($nName != "") {
			$this->SetBlock($targetBlock, $nName);
			$nName = $this->NextDBlockName($targetBlock);
       }
    }
  }

  function load_BlockFromVar ($sName, $sVariable)   {
      $this->DBlocks[$sName] = $sVariable;
      $nName = $this->NextDBlockName($sName);
	  while ($nName != "") {
        $this->SetBlock($sName, $nName);
        $nName = $this->NextDBlockName($sName);
	}
  }
  
  function NextDBlockName($sTemplateName)
  {
    $sTemplate = $this->DBlocks[$sTemplateName];
    $BTag = strpos($sTemplate, "<!--Begin");
    if($BTag === false)
    {
      return "";
    }
    else
    {
      $ETag = strpos($sTemplate, "-->", $BTag);
      $sName = substr($sTemplate, $BTag + 9, $ETag - ($BTag + 9));
      if(strpos($sTemplate, "<!--End" . $sName . "-->") > 0)
      {
        return $sName;
      }
      else
      {
        return "";
      }
    }
  }


  function SetBlock($sTplName, $sBlockName)
  {
    if(!isset($this->DBlocks[$sBlockName]))
      $this->DBlocks[$sBlockName] = $this->getBlock($this->DBlocks[$sTplName], $sBlockName);

    $this->DBlocks[$sTplName] = $this->replaceBlock($this->DBlocks[$sTplName], $sBlockName);

    $nName = $this->NextDBlockName($sBlockName);
    while($nName != "")
    {
      $this->SetBlock($sBlockName, $nName);
      $nName = $this->NextDBlockName($sBlockName);
    }
  }

  function getBlock($sTemplate, $sName)
  {
    $alpha = strlen($sName) + 12;

    $BBlock = strpos($sTemplate, "<!--Begin" . $sName . "-->");
    $EBlock = strpos($sTemplate, "<!--End" . $sName . "-->");
    if($BBlock === false || $EBlock === false)
      return "";
    else
      return substr($sTemplate, $BBlock + $alpha, $EBlock - $BBlock - $alpha);
  }


  function replaceBlock($sTemplate, $sName)
  {
    $BBlock = strpos($sTemplate, "<!--Begin" . $sName . "-->");
    $EBlock = strpos($sTemplate, "<!--End" . $sName . "-->");
    if($BBlock === false || $EBlock === false)
      return $sTemplate;
    else
      return substr($sTemplate,0,$BBlock) . "{" . $sName . "}" . substr($sTemplate, $EBlock + strlen("<!--End" . $sName . "-->"));
  }
                                                 
  function GetVar($sName)
  {
    return $this->DBlocks[$sName];
  }

  function getParsedVar($sName) {
    return $this->ParsedBlocks[$sName];
  }

  function getParsedVars() {
    return $this->DBlocks;
  }

  function set_var($sName, $sValue)
  {
    $this->ParsedBlocks[$sName] = $sValue;
  }

  function print_var($sName)
  {
    echo $this->ParsedBlocks[$sName];
  }

  function assign_var($sName)
  {
    $this->tempout.= $this->ParsedBlocks[$sName];
  }


  function parse($sTplName, $bRepeat)
  {
    if(isset($this->DBlocks[$sTplName])) {
      if($bRepeat && isset($this->ParsedBlocks[$sTplName])) 
        $this->ParsedBlocks[$sTplName] = $this->ParsedBlocks[$sTplName] . $this->ProceedTpl($this->DBlocks[$sTplName]);
      else
        $this->ParsedBlocks[$sTplName] = $this->ProceedTpl($this->DBlocks[$sTplName]);
    }
    else
    {
      echo "<br><b>Block with name <u><font color=\"red\">$sTplName</font></u> does't exist</b><br>";
    }
  }


  // This function should be called after parse main page	
  // For examples see on project "New-Nina" page templatesabm.php
  function put_without_parse ($sTplName, $sKeyName, $sValue) {
	$pBlock = $this->ParsedBlocks[$sTplName] ;
	$this->ParsedBlocks[$sTplName] = str_replace("@##_".$sKeyName."_##@", $sValue, $pBlock);
  }


  function pparse($block_name, $is_repeat)
  {
    $this->parse($block_name, $is_repeat);
    echo $this->ParsedBlocks[$block_name];
  }

  function blockVars($sTpl,$beginSymbol,$endSymbol)
  {
    if(strlen($beginSymbol) == 0) $beginSymbol = "{";
    if(strlen($endSymbol) == 0) $endSymbol = "}";
    $beginSymbolLength = strlen($beginSymbol);
    $endTag = 0;
    while (($beginTag = strpos($sTpl,$beginSymbol,$endTag)) !== false) 
    {
      if (($endTag = strpos($sTpl,$endSymbol,$beginTag)) !== false) 
      {
        $vars[] = substr($sTpl, $beginTag + $beginSymbolLength, $endTag - $beginTag - $beginSymbolLength);
      }
    }
    if(isset($vars)) return $vars;
    else return false;
  }

  function ProceedTpl($sTpl)
  {
    $vars = $this->blockVars($sTpl,"{","}");
    if($vars)
    {
      reset($vars);
      while(list($key, $value) = each($vars))
      {
        if(preg_match("/^[\w\_][\w\_]*$/",$value))
          if(isset($this->ParsedBlocks[$value]))
            $sTpl = str_replace("{".$value."}",$this->ParsedBlocks[$value],$sTpl);
          else if(isset($this->DBlocks[$value]))
          {
            $this->parse($value, false);
            $sTpl = str_replace("{".$value."}", $this->ParsedBlocks[$value], $sTpl);
          }
          else
            $sTpl = str_replace("{".$value."}","",$sTpl);
      }
    }
    return $sTpl;
  }


  function PrintAll()
  {
    $res = "<table border=\"1\" width=\"100%\">";
    $res .= "<tr bgcolor=\"#C0C0C0\" align=\"center\"><td>Key</td><td>Value</td></tr>";
    $res .= "<tr bgcolor=\"#FFE0E0\"><td colspan=\"2\" align=\"center\">ParsedBlocks</td></tr>";
    reset($this->ParsedBlocks);
    while(list($key, $value) = each($this->ParsedBlocks))
    {
      $res .= "<tr><td><pre>" . htmlspecialchars($key) . "</pre></td>";
      $res .= "<td><pre>" . htmlspecialchars($value) . "</pre></td></tr>";
    }
    $res .= "<tr bgcolor=\"#E0FFE0\"><td colspan=\"2\" align=\"center\">DBlocks</td></tr>";
    reset($this->DBlocks);
    while(list($key, $value) = each($this->DBlocks))
    {
      $res .= "<tr><td><pre>" . htmlspecialchars($key) . "</pre></td>";
      $res .= "<td><pre>" . htmlspecialchars($value) . "</pre></td></tr>";
    }                                 
    $res .= "</table>";
    return $res;
  }

}

?>