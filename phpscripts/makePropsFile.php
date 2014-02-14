<?php

/**
 * Copyright 2014 Matthew Ball (CyniCode/M477h3w1012)
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

if ( isset($_POST['host']) && isset($_POST['port']) && isset($_POST['user']) &&
	isset($_POST['pass']) && isset($_POST['name']) && isset($_POST['prefix']) &&
	isset($_POST['domain']) && isset($_POST['instLoc']) && isset($_POST['constant']) ) {

	$file = "../config/props.php";

	if ( file_exists($file) ) die("file exists error");

	$filer = fopen( $file, "w+" );

	if ( $filer === false )
		die("perm error");

	fwrite( $filer, '<?php'.PHP_EOL );
	fwrite( $filer, sprintf('define( "DATAHOST", "%s" );'.PHP_EOL, $_POST['host']) );
	fwrite( $filer, sprintf('define( "DATAPORT", "%d" );'.PHP_EOL, $_POST['port']) );
	fwrite( $filer, sprintf('define( "DATAUSER", "%s" );'.PHP_EOL, $_POST['user']) );
	fwrite( $filer, sprintf('define( "DATAPASS", "%s" );'.PHP_EOL, $_POST['pass']) );
	fwrite( $filer, sprintf('define( "DATABASE", "%s" );'.PHP_EOL, $_POST['name']) );
	fwrite( $filer, sprintf('define( "DATAPFIX", "%s" );'.PHP_EOL, $_POST['prefix']) );
	fwrite( $filer, sprintf('define( "DATACONST", "%d" );'.PHP_EOL.PHP_EOL, $_POST['constant']) );
	fwrite( $filer, sprintf('define( "INSTLOC", "%s" );'.PHP_EOL, $_POST['instLoc']) );
	fwrite( $filer, sprintf('define( "DOMAIN", "%s" );'.PHP_EOL, $_POST['domain']) );

	fclose( $filer );

	die("successful");

}

die("data error");