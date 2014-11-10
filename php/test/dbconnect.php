<?php 

class dbConnect
{


var $dbHost='localhost';
var $dbUserName='so';
var $dbPassword='Ballada1g23';
var $dbName='solaspor_cargocloud';
var $conn;
//var $strQuary;

function ConnectDB()
{
	$this->conn=mysql_connect($this->dbHost,$this->dbUserName,$this->dbPassword,false,65536) or die("Cannot Connect");
	mysql_select_db($this->dbName,$this->conn) or die("cannot find the DB.".mysql_error());
}

function CloseDB()
{
	mysql_close($this->conn);
	
}




}


?>
