<?php

if (!file_exists("../config/props.php"))
	die("no file found");

if (!file_exists("../sql/startup.sql"))
	die("no sql found");

require_once("../config/props.php");
require_once("../function/database.php");

$sql = file_get_contents( "../sql/startup.sql" );

$database = new database(getcwd()."/..");

if ( $database == false )
	die("sql error");

try {

	$database->executeSimpleStatement($sql);
	die("successful");

} catch (PDOException $ex) {

	die("sql error");

}