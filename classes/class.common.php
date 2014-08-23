<?php

/*********************************************************************************
 *       Filename: common.php
 *       Generated with CodeCharge 2.0.7
 *       PHP 4.0 & Templates build 11/30/2001
 *********************************************************************************/

error_reporting (E_ALL ^ E_NOTICE);
//include("./class.Templates.php");
//===============================
// Database Connection Definition
//-------------------------------
//Fidely.Net System Connection begin

include_once("db_mysql.inc");

// Database Initialize
define("DATABASE_NAME","");
define("DATABASE_USER","");
define("DATABASE_PASSWORD","");
define("DATABASE_HOST","localhost");


$db = new DB_Sql();
$db->Database     = DATABASE_NAME;
$db->User         = DATABASE_USER;
$db->Password     = DATABASE_PASSWORD;
$db->Host         = DATABASE_HOST;

session_start();

// Fidely.Net System Connection end

//===============================
// Site Initialization
//-------------------------------
// Obtain the path where this site is located on the server
//-------------------------------
$app_path = ".";
//-------------------------------
// Create Header and Footer Path variables
//-------------------------------
$header_filename = "templates/header.lmth";
$footer_filename = "templates/footer.lmth";
//===============================

//===============================
// Common functions
//-------------------------------
// Convert non-standard characters to HTML
//-------------------------------
function tohtml($strValue)
{
  return htmlspecialchars($strValue);
}

//-------------------------------
// Convert value to URL
//-------------------------------
function tourl($strValue)
{
  return urlencode($strValue);
}

//-------------------------------
// Obtain specific URL Parameter from URL string
//-------------------------------
function get_param($param_name)
{
  $param_value = "";
  if(isset($_POST[$param_name]))
    $param_value = $_POST[$param_name];
  else if(isset($_GET[$param_name]))
    $param_value = $_GET[$param_name];

  return $param_value;
}

function get_session($parameter_name)
{
  return isset($_SESSION[$parameter_name]) ? $_SESSION[$parameter_name] : "";
}

function set_session($parameter_name, $parameter_value)
{
  global ${$parameter_name};
  if(session_is_registered($parameter_name)) {
    session_unregister($parameter_name);
	}

  ${$parameter_name} = $parameter_value;
  session_register($parameter_name);
  $_SESSION[$parameter_name] = $parameter_value;
}

function is_number($string_value)
{
  if(is_numeric($string_value) || !strlen($string_value))
    return true;
  else
    return false;
}

//-------------------------------
// Convert value for use with SQL statament
//-------------------------------
function tosql($value, $type)
{
  if(!strlen($value))
    return "NULL";
  else
    if($type == "Number")
      return str_replace (",", ".", doubleval($value));
    else
    {
      if(get_magic_quotes_gpc() == 0)
      {
        $value = str_replace("'","''",$value);
        $value = str_replace("\\","\\\\",$value);
      }
      else
      {
        $value = str_replace("\\'","''",$value);
        $value = str_replace("\\\"","\"",$value);
      }

      return "'" . $value . "'";
    }
}

function strip($value)
{
  if(get_magic_quotes_gpc() == 0)
    return $value;
  else
    return stripslashes($value);
}

function db_fill_array($sql_query)
{
  global $db;
  $db_fill = new DB_Sql();
  $db_fill->Database = $db->Database;
  $db_fill->User     = $db->User;
  $db_fill->Password = $db->Password;
  $db_fill->Host     = $db->Host;

  $db_fill->query($sql_query);
  if ($db_fill->next_record())
  {
    do
    {
      $ar_lookup[$db_fill->f(0)] = $db_fill->f(1);
    } while ($db_fill->next_record());
    return $ar_lookup;
  }
  else
    return false;

}

//-------------------------------
// Deprecated function - use get_db_value($sql)
//-------------------------------
function dlookup($table_name, $field_name, $where_condition)
{
  $sql = "SELECT " . $field_name . " FROM " . $table_name . " WHERE " . $where_condition;
  return get_db_value($sql);
}


//-------------------------------
// Lookup field in the database based on SQL query
//-------------------------------
function get_db_value($sql)
{
  global $db;
  $db_look = new DB_Sql();
  $db_look->Database = $db->Database;
  $db_look->User     = $db->User;
  $db_look->Password = $db->Password;
  $db_look->Host     = $db->Host;

  $db_look->query($sql);
  if($db_look->next_record())
    return $db_look->f(0);
  else
    return "";
}

//-------------------------------
// Obtain Checkbox value depending on field type
//-------------------------------
function get_checkbox_value($value, $checked_value, $unchecked_value, $type)
{
  if(!strlen($value))
    return tosql($unchecked_value, $type);
  else
    return tosql($checked_value, $type);
}

//-------------------------------
// Obtain lookup value from array containing List Of Values
//-------------------------------
function get_lov_value($value, $array)
{
  $return_result = "";

  if(sizeof($array) % 2 != 0)
    $array_length = sizeof($array) - 1;
  else
    $array_length = sizeof($array);
  reset($array);

  for($i = 0; $i < $array_length; $i = $i + 2)
  {
    if($value == $array[$i]) $return_result = $array[$i+1];
  }

  return $return_result;
}

//-------------------------------
// Verify user's security level and redirect to login page if needed
//-------------------------------

function check_security()
{
  $return_page = getenv("REQUEST_URI");
  if($return_page === "") { $return_page = getenv("SCRIPT_NAME") . "?" . getenv("QUERY_STRING"); }
  if(!session_is_registered("UserQ1lm3sFidely"))
  {
    header ("Location: login.php?querystring=" . urlencode(getenv("QUERY_STRING")) . "&ret_page=" . urlencode($return_page));
    exit;
  }
}
//  GlobalFuncs begin
//#######################################################
//***********      CHECK SECURITY FUNC        ***********
//*******************************************************
//PARAMS : $page=filename of page             ***********
//get bit of security for $page from DB       ***********
// CMP BIT with User RIGHTS                   ***********
//$type:type 0 system                         ***********
//           1 grupo                          ***********
//      	 2 negocio    				      ***********
//           3 terminal                       ***********
//           4 virtual group				  ***********
//$redirect  if user not have rights          ***********
//           0 Return False widhout redirect  ***********
//           1 Redirect to Login page         ***********
//#######################################################
function  chksecurity($page,$type=0,$redirect=0){
 $checkAll	 = false;
 $Secure	 = false;
 $obj_kind	 = $type;
 $operator	 = getUser();
 $otherRight = "";
 switch($type){
	case 0://SYSTEM CHECK
		$objectParam=0;
	break;
	case 1:// GROUP CHECK
		$objectParam=get_param("group");
	break;
	case 2:// SHOP CHECK
		$objectParam=get_param("group").";".get_param("shop");
	break;
	case 3:// TERMINAL CHECK
		$objectParam=get_param("group").";".get_param("shop").";".get_param("term");
        //$otherRight="_term";
	break;
	case 99: //CHECK IN ALL OPERATOR'S RIGHTS.
		$checkAll=true;
	break;
 }
 $pageRight =get_db_value("select mask from webpages where name='$page'");
 if(!$checkAll){
	//take Operator's rights.
	$operRights=get_db_value("select rights$otherRight from obj_rights where op_kind=0 and obj_kind=$obj_kind and operator='$operator' and object='$objectParam;'");
	//take page's security
	if (( (abs($pageRight) & abs($operRights)) == abs($pageRight))&& (!($pageRight=="")))
		$Secure=true;
 }//IF ! CHECK ALL
 else{ // -=check all=-
	 $Secure=(get_db_value("select count(*) from obj_rights where operator='$operator' and (rights$otherRight|'$pageRight') = rights$otherRight"  )>0);
	 }//end else check all
if ($Secure&&(strlen($pageRight)))
	return true;
else
    if ($redirect)
  	  header("Location: norights.php");
    else
        return false;


}//endfunc

function get_bit_value($bit,$right)
{
//return(substr($right,$bit+1,1));
return(substr($right,strlen($right)-($bit+1),1));
}

function dec2bin($decimal_code){
 for($half=($decimal_code);$half>=1;$half=(floor($half))/2){
   if(($half%2)!=0){
   $y.=1;
   }
   else{
   $y.=0;
   }
  }
 $calculated_bin=strrev($y);
 return $calculated_bin;
}
function Charge_Counters($group){
//insert into counters table records for group's counters
get_db_value("INSERT INTO counters VALUES('$group', 'agents', '1')");
get_db_value("INSERT INTO counters VALUES('$group', 'countries', '1')");
get_db_value("INSERT INTO counters VALUES('$group', 'groups_cards', '1')");
get_db_value("INSERT INTO counters VALUES('$group', 'obj_rights', '1')");
get_db_value("INSERT INTO counters VALUES('$group', 'operator_groups', '1')");
get_db_value("INSERT INTO counters VALUES('$group', 'opgroups', '1')");
get_db_value("INSERT INTO counters VALUES('$group', 'shop_cards', '1')");
get_db_value("INSERT INTO counters VALUES('$group', 'shop_operatordata', '1')");
get_db_value("INSERT INTO counters VALUES('$group', 'shop_personaldata', '1')");
get_db_value("INSERT INTO counters VALUES('$group', 'shops', '1')");
get_db_value("INSERT INTO counters VALUES('$group', 'shops_vgroups', '1')");
get_db_value("INSERT INTO counters VALUES('$group', 'terminals', '1')");
get_db_value("INSERT INTO counters VALUES('$group', 'transactions', '1')");
get_db_value("INSERT INTO counters VALUES('$group', 'transactions_kind', '1')");
get_db_value("INSERT INTO counters VALUES('$group', 'tree_vgroups', '1')");
get_db_value("INSERT INTO counters VALUES('$group', 'versions', '1')");
get_db_value("INSERT INTO counters VALUES('$group', 'virtual_groups', '1')");
get_db_value("INSERT INTO counters VALUES('$group', 'customers', '1');");
get_db_value("INSERT INTO counters VALUES('$group', 'z_representations_entries', '1');");
get_db_value("INSERT INTO counters VALUES('$group', 'z_fields_by_group', '1');");

}
function get_regiscodeGroup($idGroup)
{
$Groupname=get_db_value("select name from groups where id='$idGroup'");
$BaseString="Q1lm3s";
$strMD5=$Groupname.$idGroup.$BaseString;
$fldcode=md5($strMD5);
return($fldcode);
}

function get_object_value($type,$obj)
{
if($type=="group")
 $obj=substr($obj,0,strpos($obj,";"));
if($type=="shop"){
 $obj=substr($obj,strpos($obj,";")+1);
 $obj=substr($obj,0,strpos($obj,";"));
}
if($type=="terminal")
 {
   $obj=substr($obj,strpos($obj,";")+1);
   $obj=substr($obj,strpos($obj,";")+1,(strlen($obj)-1)-(strpos($obj,";")+1));
 }

 return($obj);
}
function date_format($type,$date)
{
if ($type=="d-m-Y"){
  //supongo entrada yyyy-mm-dd
   $anio=substr($date,0,4);
   $mes=substr($date,5,2);
   $dia=substr($date,8,2);
   $fecha="$dia/$mes/$anio";
}
else
   if($type=="d-m-Y H:i:s"){
  	 //supongo entrada yyyy-mm-dd hh:ii:ss
	 $anio = substr($date,0,4);
	 $mes  = substr($date,5,2);
	 $dia  = substr($date,8,2);
     $hora = substr($date,11,2);
	 $min  = substr($date,14,2);
	 $seg  = substr($date,17,2);
	 $fecha="$dia/$mes/$anio $hora:$min:$seg";
   }else{
    //supoong entrada dd-mm-yyyy
	$dia=substr($date,0,2);
	$mes=substr($date,3,2);
	$anio=substr($date,6,4);
	$fecha="$anio-$mes-$dia";
    }
return ($fecha);
}
function cmp_time($time1,$time2){
//#########################################|
//.::Supose hh:mm:ss				  #####|
//          01234567				  #####|
//.::Returns:	1: $time1 > $time2	  #####|
//				2: $time1 < $time2    #####|
//				0: $time1 = $time2    #####|
//-----------------------------------------|
$h1=intval(substr($time1,0,2));  $m1=intval(substr($time1,3,2)); $s1=intval(substr($time1,6,2));
$h2=intval(substr($time2,0,2));  $m2=intval(substr($time2,3,2)); $s2=intval(substr($time2,6,2));
if(($h1==$h2)&&($m1==$m2)&&($s1==$s2))
	$ret=0;
else
	$ret = ( ($h1>$h2)||( ($h1==$h2)&&($m1>$m2)) || ( ($h1==$h2)&&($m1==$m2)&&($s1>$s2)) ) ? 1 : 2;
return $ret;

}
function CloseDay(){
 //#######################################################
 //===== This func process all transactions that not =====
 //===== was processed yet & check if EXE is running =====
 //===== in this way check if Exe's time to live is  =====
 //===== in time, otherwise send Alert mail          =====
 //#######################################################
global $db;
 //.:: First check if EXE is Running
 $running=get_db_value("Select value from params where name='ExecCloseDay'");
 if(!$running){
   //.:: Set bit start on ExecCloseDay variable
   get_db_value("update params set value='1' where name='ExecCloseDay'");
   //.:: Set date time start on ExecCloseDay variable
   get_db_value("update params set value=now() where name='ExecCloseDay_Date'");
   //.:: Set bit processed = 2 => set all records that I will processes
   get_db_value("update transactions set processed=2 where processed=0");
   $db->begin_transaction();
   $sSQL=" select  t.`group`,"
	    ."	t.shop,"
	    ."	substring(local_time,1,10) 	    as DateClosed,"
	    ."	sum(if(t.kind='2',1,0))	        as CountChargemoney,"
	    ."	sum(if(t.kind='2',t.money,0))	as Chargemoney,"
	    ."	sum(if(t.kind='3',1,0))	        as CountDischargemoney,"
	    ."	sum(if(t.kind='3',t.money,0))	as Dischargemoney, 	"
	    ."	sum(if(t.kind='4',1,0))       	as CountChargepoints,"
	    ."	sum(if(t.kind='4',t.points,0)) 	as Chargepoints,"
	    ."	sum(if(t.kind='5',1,0))        	as CountDischargepoints,"
	    ."	sum(if(t.kind='5',t.points,0)) 	as Dischargepoints,"
	    ."	sum(if(t.kind='1',1,0))			as Creation,	"
	    ."	sum(if(t.kind='6',1,0))			as BlockCard,   "
	    ."	sum(if(t.kind='7',1,0))			as UnblockCard,"
	    ."	sum(if(t.kind='8',1,0))			as ChangeCard,"
	    ."	sum(if(t.kind='10',1,0))		as DPmodify,"
	    ."	sum(if(t.kind='99',1,0))		as DeleteCard"
	    ." from (( transactions t  left join groups g on g.id=t.`group`)	"
	    ."			left join shops s on t.`group`=s.`group` and t.shop=s.id )	"
	    ." where"
	    ."	t.processed=2	"
	    ." and"
	    ." exists(select * from statements_shops ss2 where ss2.group=t.group and ss2.shop=t.shop and substring(ss2.local_time,1,10)=substring(t.local_time,1,10) )"
	    ." group by `group`,shop,substring(local_time,1,10)";
	    $db->query($sSQL);
		$next_record=$db->next_record();
		while($next_record){
			//.:: Group by Local_time(x Day), data that exist in statements_shops, so i have to update it.
			$CountChargepoints		=$db->f("CountChargepoints");
			$Chargepoints			=$db->f("Chargepoints");
			$CountDischargepoints	=$db->f("CountDischargepoints");
			$Dischargepoints	    =$db->f("Dischargepoints");
			$CountChargemoney		=$db->f("CountChargemoney");
			$Chargemoney			=$db->f("Chargemoney");
			$CountDischargemoney	=$db->f("CountDischargemoney");
			$Dischargemoney			=$db->f("Dischargemoney");
			$Creation				=$db->f("Creation");
			$BlockCard				=$db->f("BlockCard");
			$UnblockCard			=$db->f("UnblockCard");
			$ChangeCard				=$db->f("ChangeCard");
			$DPmodify				=$db->f("DPmodify");
			$DeleteCard				=$db->f("DeleteCard");
			$GroupID				=$db->f("group");
			$ShopID					=$db->f("shop");

	        get_db_value(" update statements_shops set "
				 ."       count_points_charged      = count_points_charged   + $CountChargepoints,"
				 ."       points_charged            = points_charged         + $Chargepoints,"
				 ."       count_points_discharged   = count_points_discharged+ $CountDischargepoints,"
				 ."       points_discharged         = points_discharged      + $Dischargepoints,"
				 ."       count_money_charged       = count_money_charged    + $CountChargemoney,"
				 ."       money_charged             = money_charged          + $Chargemoney,"
				 ."       count_money_discharged    = count_money_discharged + $CountDischargemoney,"
				 ."       money_discharged          = money_discharged       + $Dischargemoney,"
				 ."       t_create                  = t_create               + $Creation,"
				 ."       t_block                   = t_block                + $BlockCard,"
				 ."       t_unblock                 = t_unblock              + $UnblockCard,"
				 ."       t_changecard              = t_changecard           + $ChangeCard,"
				 ."       t_dpmodify                = t_dpmodify             + $DPmodify,"
 				 ."       t_delete                  = t_delete               + $DeleteCard"
				 ." where `group`='$GroupID' and shop='$ShopID' and substring(local_time,1,10)='$DateClosed'");
		$next_record=$db->next_record();
		}//end while
	    get_db_value(" insert into statements_shops	("
			    ."		 `group`,"
			    ." 		  shop,"
			    ."        local_time,"
			    ."        count_money_charged,"
			    ."        money_charged,"
			    ."        count_money_discharged,"
			    ."        money_discharged,"
			    ."        count_points_charged,"
			    ."        points_charged,"
			    ."        count_points_discharged,"
			    ."        points_discharged,"
			    ."        t_create,"
			    ."        t_block,"
			    ."        t_unblock,"
			    ."        t_changecard,"
			    ."        t_dpmodify,"
			    ."        t_delete 		)"
			    ." select  t.`group`,"
			    ."	t.shop,"
			    ."	substring(local_time,1,10) 	   as DateClosed,"
			    ."	sum(if(t.kind='2',1,0))	       as CountChargemoney,"
			    ."	sum(if(t.kind='2',t.money,0))  as Chargemoney,"
			    ."	sum(if(t.kind='3',1,0))	       as CountDischargemoney, "
			    ."	sum(if(t.kind='3',t.money,0))  as Dischargemoney, 	"
			    ."	sum(if(t.kind='4',1,0))        as CountChargepoints,"
			    ."	sum(if(t.kind='4',t.points,0)) as Chargepoints,"
			    ."	sum(if(t.kind='5',1,0))        as CountDischargepoints,"
			    ."	sum(if(t.kind='5',t.points,0)) as Dischargepoints,"
			    ."	sum(if(t.kind='1',1,0))		   as Creation,	"
			    ."	sum(if(t.kind='6',1,0))		   as BlockCard,   "
			    ."	sum(if(t.kind='7',1,0))		   as UnblockCard,"
			    ."	sum(if(t.kind='8',1,0))		   as ChangeCard,"
			    ."	sum(if(t.kind='10',1,0))	   as DPmodify,"
			    ."	sum(if(t.kind='99',1,0))	   as DeleteCard"
			    ." from (( transactions t  left join groups g on g.id=t.`group`)"
			    ."			left join shops s on t.`group`=s.`group` and t.shop=s.id )"
			    ." where"
			    ."	t.processed=2"
			    ." and"
			    ."	concat(t.`group`,t.shop,substring(t.local_time,1,10)) not in (select concat(ss.`group`,ss.shop,substring(ss.local_time,1,10)) from statements_shops ss)"
			    ." group by `group`,shop,substring(local_time,1,10)");
		//.:: Set transactions as processed & set ExecCloseDay as "Not Running" (value=0).
		if (!$db->Query_ID)
			$db->rollback_transaction();
		else {
			 $db->commit_transaction();
			 get_db_value("update transactions set processed=1 where processed=2");
			 get_db_value("update params set value='0' where name='ExecCloseDay'");
			}
 }else//.:: Is running
	{
	 $timeRunning=get_db_value("select 	( "
         ."       	  select time_to_sec(timediff(now(),convert(value,datetime))) as value"
         ."       	  from params where name='ExecCloseDay_Date'"
         ."           )"
         ."       	   -"
         ."        	 ("
         ."       	  select time_to_sec(value)"
         ."       	  from params where name='ExecCloseDay_timetolive'"
         ."          ) as result");
	 // .:: If timeruning >0 the time to live was expired.
	 if($timeRunning>0){
		$mail=get_db_value("select value from params where name='ExecEmailnotify'");
  		mail($mail,"Close Day Exe Time Out!!","Please check pcloseday.exe. It has a problem because is runing and its time to live has expired");
	  }
	}//.:: Else Is running
}//.:: END FUNC CLOSE DAY
//###########################################################
// Description: this func export resultset to excel      ####
// Input: SQL    => string query to execute              ####
//       filename=> Name of file that will be generate   ####
// Alert: this func Open window to save or open XLS file ####
//###########################################################
function ExportExcel($sSQL,$Excelname){
	//connect mysql database
	global $db;
	mysql_select_db($db->Database,$db->Link_ID);

	//get result
	$result=mysql_query($sSQL);
	$numoffields=mysql_num_fields($result);

	// now we could construct Excel output
	$fieldstype=array();
	 for($i=0;$i<$numoffields;$i++){
		$fieldstype[]=mysql_field_type($result,$i);
	 }// for($i=0;...) END
	 //initiate a counter for excel "ROW" counter
	 $rowscounter=0;
	 //write fields to excel
	 $table="<table><tr>";
	 for($i=0;$i<$numoffields;$i++){
			$fld=mysql_fetch_field($result,$i);
			$fldname=$fld->name;
			$table.="<td>$fldname</td>";
	 }// for($i=0;...) END
	 $table.="</tr>";
	 $rowscounter++;
	 while($row=mysql_fetch_array($result)){
	 //fetch each Cell($rowscounter,$colscounter) into Excel output stream
		$table.="<tr>";
		for($colscounter=0;$colscounter<$numoffields;$colscounter++){
			//identify field type to descide how to write excel cell
			    $table.="<td>".$row[$colscounter]."</td>";
			}//for($colscounter..) END
			$table.="</tr>";
			$rowscounter++;
		}// while($row=mysql..) END
	    $table.="</table>";
	header ( "Expires: Mon, 1 Apr 1974 05:00:00 GMT");
	header ( "Last-Modified: " . gmdate("D,d M YH:i ") . " GMT" );
	header ( "Pragma: no-cache" );
	header ("Content-type: application/x-msexcel");
	header ("Content-Disposition: attachment; filename=".$Excelname.".xls");
	header ("Content-Description: Fern@ndo TVTGA" );
	print $table;
	exit;
return;
}

function check_date($fecha)
{
  //=====================================================
  // :: First check if date has chars
  //=====================================================
  $flength=strlen($fecha);
  $aNumbers=array("0","1","2","3","4","5","6","7","8","9");
  $i=0;
  $haschar=false;
  while($i<$flength){
	  if((!array_key_exists(substr($fecha,$i,1),$aNumbers))&& !((substr($fecha,$i,1)=="/")&&(($i==2)||($i==5))) )
			$haschar=true;
	  $i++;
  }
  //========================================================
  // :: Second check if date is OK. Ex. No Ok --> 31/02/2005
  //========================================================
  $year=substr($fecha,6);
  $day=substr($fecha,0,2);
  $month=substr($fecha,3,2);

  if( (strlen($year)==4) && (strlen($day)==2) && (strlen($month)==2) )
	   $firstCheck = checkdate(intval($month),intval($day),intval($year));
  else
	  $firstCheck=false;
  $SecondCheck= (substr($fecha,2,1)=="/")&&(substr($fecha,5,1)=="/");
  return($firstCheck&&$SecondCheck&&!$haschar);
}
function array_value_exists($value,$array){
 $res=false;
 while (list ($clave, $val) = each ($array) )
		//"$clave => $val<br>";
		if($val==$value) $res=true;
return $res;
}
function array_set_rala($array){
	$max=$CountRows=$j=0;
	$maxRow=0;
	// SEARCH MAX ROW THAT HAVE COLUMN AND COUNT COLUM.
	while (list ($row, $val) = each ($array) ) {
		$j=count($array[$row]);
		$CountRows++;
		if ($j>=$max){
			$max=$j;
			$maxRow=$row;
		}
	}// while row
   reset($array);
	while (list ($row, $val) = each ($array) )
		for($i=0;$i<$max;$i++)
			if($array[$row][$i]=="")
				$array[$row][$i]=0;

return($array);
}
function upload_GenLicenceMod(){
	// UPOAD MODULE TO GENERATE LICENCES

	if(!extension_loaded('php_fidelylic'))
		//dl('php_fidelylic.dll');
	$module = 'php_fidelylic';
	if (extension_loaded($module))
		  $str = "module loaded";
	else
		$str = "Module $module is not compiled into PHP";

}

function getLanguageTemplate($filename,$lang){
    // keep out ".php"
	$filename=substr($filename,0,strlen($filename)-4);
	switch($lang) {
		case "ENG":
			   $template_filename = "templates/".$filename."_ENG.lmth";
		break;
		case "ITA":
			   $template_filename = "templates/".$filename."_ITA.lmth";
		break;
		case "SPA":
			   $template_filename = "templates/".$filename."_SPA.lmth";
		break;
		default:
 			   $template_filename ="templates/".$filename."_ITA.lmth";
		break;
	}
	return $template_filename;
}
function getLanguage(){
	if (get_param("lang")=="")
	    if (get_session("lang")!="")
			$lang=get_session("lang");
		else
	        $lang="ITA";
    else
		$lang=get_param("lang");

	set_session("lang",$lang);
   return $lang;
}
function getUser(){
 return get_session("UserQ1lm3sFidely");
}
// Format date for human representation - Used generally in Shows Forms. [By Kr|ts0]
// Examples: dateToDDMMYYYY("", tohtml($fldexpiration));
// ---------------------------------------------------------------------------------
function dateToDDMMYYYY ($dateSource, $date) {
	$dateOUT = "";
	$dateSource = strtolower($dateSource);
	// Replace '/' by '-' from original date. This allow me use "sscanf($date,"%d %d %d")"
	$date = str_ireplace("/", "-", $date);
	if ($date != "") {
		if ($dateSource == "yyyymmdd")
			list($year, $month, $day) = sscanf($date,"%d %d %d");
		if ($dateSource == "ddmmyyyy")
			list($day, $month, $year) = sscanf($date,"%d %d %d");
		if ($dateSource == "yyyyddmm")
			list($year, $day, $month) = sscanf($date,"%d %d %d");
		if ($dateSource == "mmyyyydd")
			list($month, $year, $day) = sscanf($date,"%d %d %d");
		if ($dateSource == "") {
			// Autodetect mode [Supported modes: (YYYYMMDD) Or (DDMMYYYY)]
			list($day, $month, $year) = sscanf($date,"%d %d %d");
			if (strlen($year) <= 3) {
				list($year, $month, $day) = sscanf($date,"%d %d %d");
			}
		}
		$dateOUT = sprintf ("%02d/%02d/%04d", abs($day), abs($month), abs($year));
	}
	return ($dateOUT);
}

// ---------------------------------------------------------------------------------
// Format date for MySQL (YYYY-MM-DD) - Used generally in Actions Forms. [By Kr|ts0]
// ---------------------------------------------------------------------------------
function dateToMySQL ($dateSource, $date) {
	$dateOUT = "";
	$dateSource = strtolower($dateSource);
	// Replace '/' by '-' from original date. This allow me use "sscanf($date,"%d %d %d")"
	$date = str_ireplace("/", "-", $date);
	if ($date != "") {
		if ($dateSource == "yyyymmdd")
			list($year, $month, $day) = sscanf($date,"%d %d %d");
		if ($dateSource == "ddmmyyyy")
			list($day, $month, $year) = sscanf($date,"%d %d %d");
		if ($dateSource == "yyyyddmm")
			list($year, $day, $month) = sscanf($date,"%d %d %d");
		if ($dateSource == "mmyyyydd")
			list($month, $year, $day) = sscanf($date,"%d %d %d");
		if ($dateSource == "") {
			// Autodetect mode [Supported modes: (YYYYMMDD) Or (DDMMYYYY)]
			list($day, $month, $year) = sscanf($date,"%d %d %d");
			if (strlen($year) <= 3) {
				list($year, $month, $day) = sscanf($date,"%d %d %d");
			}
		}
		$dateOUT = sprintf ("%04d-%02d-%02d", abs($year), abs($month), abs($day));
	}
	return ($dateOUT);
}
//  GlobalFuncs end
//===============================
?>
