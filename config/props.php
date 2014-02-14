<?php

//This is for the field type that the definitions are going
// to be checked for. Change at your own risk.
define( "TEXT", "(\w)*" );
define( "DATATEXT", "[a-zA-Z0-9\$_]*" );
define( "LINK", "https?:\/\/([a-zA-Z0-9\.\/_]+[.]{1}[a-z]{2,5}\/?)|localhost\/?" );
define( "LOCATION", "(\/\w*)*" );
define( "INT", "[0-9]*" );

//Put extra items in here for everything you want to
// define.
$definers = array(

	"Database Host" => LINK,
	"Database Port" => INT,
	"Database User" => DATATEXT,
	"Database Pass" => TEXT,
	"Database Name" => DATATEXT,

	"Domain" => LINK,
	"Install Location" => LOCATION,

	"Place to Load the Config File" => LOCATION

);
