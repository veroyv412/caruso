<?php
// #################################################
// Class name: MySQL
// Author: Edu@rdo
// Version: 1.5.2.1 2005/09/18 16:17:20
// Language: PHP 4
// Copyright: Nexus/Ninatec Technologies 2005
// #################################################
/*
Methods List
------------
function MySQL($sqlserver, $sqluser, $sqlpassword, $database, $persistency = true)
Desc: Constructor
Return: Handle or false

function close()
Desc: Finish connection
Return: True or false

function query($query = "", $transaction = FALSE)
Desc: Make new query
Return: True or false

function numrows($query_id = 0)
Desc: Return number of rows of last query
Return: Integer

function affectedrows()
Desc: Return number of affected rows of last query (INSERT, UPDATE o DELETE)
Return: Integer

function numfields($query_id = 0)
Desc: Return fields number of each row for last query
Return: Integer

function fieldname($offset, $query_id = 0)
Desc: Return name of field
Return: String

function fieldtype($offset, $query_id = 0)
Desc: Return type of field ("int", "real", "string", "blob")
Return: String

function fetchrow($query_id = 0)
Desc: Extract ONE row as hash array
Return: Array

function fetchrowset($query_id = 0)
Desc: Extract ALL rows as hash array
Return: Array

function fetchfield($field, $rownum = -1, $query_id = 0)
Desc: Return value of field
Return: Field

function rowseek($rownum, $query_id = 0)
Desc: Moves internal pointer
Return: True or false

function nextid()
Desc: Get Inserted ID
Return: Integer

function freeresult($query_id = 0)
Desc: Free query result
Return: True or false

function error()
Desc: GetLast error
Return: Hash Array (['message'],['code'])
*/
define("BEGIN_TRANSACTION",1);
define("END_TRANSACTION",2);

class MySQL {
	var $db_connect_id;
	var $query_result;
	var $row = array();
	var $rowset = array();
	var $num_queries = 0;
	var $in_transaction = 0;
	var $row_cur = 0;						// Edu
	var $show_errors = 1;					// Edu
	var $debug 	= false;		//Juan
	var $log	= false; //Juan
	var $logFileName	= "./logs/mysql.log";
	var $logFP;


	function wlog($str){
		if($this->log && is_writable($this->logFileName)){
			$dt	= date("d/m/Y H:i");
			$str = "$dt - $str\r\n";

			fwrite($this->logFP,$str);
			fflush($this->logFP);
		}
		if($this->debug)
		echo "$str<br>";
	}

	function MySQL($sqlserver, $database, $sqluser, $sqlpassword, $persistency = false) {
		$this->persistency = $persistency;
		$this->user = $sqluser;
		$this->password = $sqlpassword;
		$this->server = $sqlserver;
		$this->dbname = $database;

		if($this->log){
			$this->logFP = fopen($this->logFileName,"a+");
			if(!$this->logFP){
				$this->log = false;
			}
		}

		$this->db_connect_id = ($this->persistency) ? mysql_pconnect($this->server, $this->user, $this->password) : mysql_connect($this->server, $this->user, $this->password);
		if ($this->db_connect_id ) {
			if ($database != "") {
				$this->dbname = $database;
				$dbselect = mysql_select_db($this->dbname);
				if (!$dbselect) {
					mysql_close($this->db_connect_id);
					$this->db_connect_id = $dbselect;
				}
			}
			return $this->db_connect_id;
		} else {
			return false;
		}
	}

	function close() {
		if ($this->db_connect_id) {
			if ($this->in_transaction) {
				mysql_query("COMMIT", $this->db_connect_id);
				$this->wlog("END TRANSACTION");
			}
			return mysql_close($this->db_connect_id);
		} else {
			return false;
		}
	}

	function query($query = "", $transaction = FALSE) {
		$this->row_cur = 0;
		unset($this->query_result);

		if ($transaction == BEGIN_TRANSACTION)
		$this->wlog("START TRANSACTION");

		if ($query != "") {
			$this->wlog($query);
			$this->num_queries++;
			if ($transaction == BEGIN_TRANSACTION && !$this->in_transaction) {
				$result = mysql_query("BEGIN", $this->db_connect_id);
				if(mysql_errno())
				$this->wlog("SQL ERROR! ".mysql_error() . "<br>$query");

				if (mysql_errno() && $this->show_errors)
				echo ("<br>SQL ERROR! ".mysql_error() . "<br>$query");
				if (!$result) {
					return false;
				}
				$this->in_transaction = TRUE;
			}
			$this->query_result = mysql_query($query, $this->db_connect_id);
			if(mysql_errno())
			$this->wlog("SQL ERROR! ".mysql_error() . "<br>$query");

			if (mysql_errno() && $this->show_errors)
			echo ("<br>SQL ERROR! ".mysql_error() . "<br>$query");
		} else {
			if ($transaction == END_TRANSACTION && $this->in_transaction) {
				$this->wlog("END TRANSACTION");
				$result = mysql_query("COMMIT", $this->db_connect_id);
				if(mysql_errno())
				$this->wlog("SQL ERROR! ".mysql_error() . "<br>$query");

				if (mysql_errno() && $this->show_errors)
				echo ("<br>SQL ERROR! ".mysql_error() . "<br>$query");
			}
		}
		if ($this->query_result) {
			unset($this->row[$this->query_result]);
			unset($this->rowset[$this->query_result]);
			if ($transaction == END_TRANSACTION && $this->in_transaction) {
				$this->wlog("END TRANSACTION");
				$this->in_transaction = FALSE;
				if (!mysql_query("COMMIT", $this->db_connect_id)) {
					mysql_query("ROLLBACK", $this->db_connect_id);
					$this->wlog("ROLLBACK TRANSACTION");
					if(mysql_errno())
					$this->wlog("SQL ERROR! ".mysql_error() . "<br>$query");
					if (mysql_errno() && $this->show_errors)
					echo ("<br>SQL ERROR! ".mysql_error() . "<br>$query");
					return false;
				}
			}
			return $this->query_result;
		} else {
			if ($this->in_transaction) {
				mysql_query("ROLLBACK", $this->db_connect_id);
				$this->wlog("ROLLBACK TRANSACTION");
				if(mysql_errno())
				$this->wlog("SQL ERROR! ".mysql_error() . "<br>$query");

				if (mysql_errno() && $this->show_errors)
				echo ("<br>SQL ERROR! ".mysql_error() . "<br>$query");
				$this->in_transaction = FALSE;
			}
			return false;
		}

	}

	function numrows($query_id = 0) {
		if (!$query_id) {
			$query_id = $this->query_result;
		}
		return ( $query_id ) ? mysql_num_rows($query_id) : false;
	}

	function affectedrows() {
		return ( $this->db_connect_id ) ? mysql_affected_rows($this->db_connect_id) : false;
	}

	function numfields($query_id = 0) {
		if (!$query_id) {
			$query_id = $this->query_result;
		}
		return ( $query_id ) ? mysql_num_fields($query_id) : false;
	}

	function fieldname($offset, $query_id = 0) {
		if (!$query_id) {
			$query_id = $this->query_result;
		}
		return ( $query_id ) ? mysql_field_name($query_id, $offset) : false;
	}

	function fieldtype($offset, $query_id = 0) {
		if (!$query_id)	{
			$query_id = $this->query_result;
		}
		return ( $query_id ) ? mysql_field_type($query_id, $offset) : false;
	}

	function fetchrow($query_id = 0) {
		if (!$query_id)	{
			$query_id = $this->query_result;
		}
		if ($query_id) {
			$this->row[$query_id] = mysql_fetch_array($query_id, MYSQL_ASSOC);
			return $this->row[$query_id];
		} else {
			return false;
		}
	}

	function fetchrowset($query_id = 0) {
		if (!$query_id) {
			$query_id = $this->query_result;
		}
		if ($query_id) {
			unset($this->rowset[$query_id]);
			unset($this->row[$query_id]);
			while($this->rowset[$query_id] = mysql_fetch_array($query_id, MYSQL_ASSOC))	{
				$result[] = $this->rowset[$query_id];
			}
			return $result;
		} else {
			return false;
		}
	}

	function fetchfield($field, $rownum = -1, $query_id = 0) {
		if (!$query_id) {
			$query_id = $this->query_result;
		}
		if ($query_id) {
			if ($rownum > -1) {
				$result = mysql_result($query_id, $rownum, $field);
			} else {
				if (empty($this->row[$query_id]) && empty($this->rowset[$query_id]) ) {
					if( $this->fetchrow() )	{
						$result = $this->row[$query_id][$field];
					}
				} else {
					if (isset($this->rowset[$query_id]) && $this->rowset[$query_id]) {
						$result = $this->rowset[$query_id][0][$field];
					} else if ( $this->row[$query_id] )	{
						$result = $this->row[$query_id][$field];
					}
				}
			}
			return $result;
		} else {
			return false;
		}
	}

	function rowseek($rownum, $query_id = 0) {
		if (!$query_id) {
			$query_id = $this->query_result;
		}
		return ($query_id) ? mysql_data_seek($query_id, $rownum) : false;
	}

	function nextid() {
		return ($this->db_connect_id) ? mysql_insert_id($this->db_connect_id) : false;
	}

	function freeresult($query_id = 0) {
		if (!$query_id) {
			$query_id = $this->query_result;
		}
		if ($query_id) {
			unset($this->row[$query_id]);
			unset($this->rowset[$query_id]);
			mysql_free_result($query_id);
			return true;
		} else {
			return false;
		}
	}

	function error() {
		$result['message'] = mysql_error($this->db_connect_id);
		$result['code'] = mysql_errno($this->db_connect_id);
		return $result;
	}

	// ####################################################################################
	// These functions are only for compatibility with previous versions of MySQL Connector
	// ####################################################################################
	function f($field) {
		return($this->fetchfield($field));
	}

	function next_record() {
		$vret = ($this->row_cur < $this->numrows());
		if ($vret) {
			$this->fetchrow();
			$this->row_cur++;
		}
		return ($vret);
	}

	function free() {
		$this->freeresult();
	}
	function inserted_id() {
		return ($this->Link_ID) ? mysql_insert_id($this->Link_ID) : false;
	}

}

?>
