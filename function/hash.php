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

if (!file_exists("$home_dir/config/props.php"))
	die("No file exists");

require_once "$home_dir/config/props.php";

/**
 * A function to take a plaintext password and return the hashed
 * value of it. Please note... I may need to make something a
 * little stronger than this...
 *
 * @param String $pass : The password that we're going to
 *  hash into something else that is slightly harder to read
 * @param int|bool $d : A variable to tell us whether we're
 *  making a hash now, or one that was in the past
 * @return string : The final hash that we've created from
 *  the variables above
 */
function cyniHash( $pass, $d = false ) {

	$input = unistr_to_ords( $pass );
	$i = 0;
	$final = array();

	foreach ( $input as $started ) {
		$move[$i] = (int) ord( $started );
		$i++;
	}

	$final['time'] = (!$d) ? time() : $d;
	$c = 2;
	$id = DATACONST;

	for ( $l=1; $l<=64; $l++ ) {
		$char = intval( ( ($final['time'] * (($id/10)*3))/ $move[$c] ) * ( $l * (($id/3)*4) ) );
		$char = ( $char * ((5*19*3)/63)*4);
		$newchar = $char % 127;
		if ( $newchar < 0 ) {
			$newchar = $newchar * (-1);
		}
		if ( $newchar <= 32 ) {
			$newchar = $newchar + 33;
		}
		$shah[$l-1] = $newchar;
		$c = ( ( $c * 2 ) / $l ) % strLen( $final['time'] );
		$hca = $shah[$l-1];
		//echo $hca ." : ". chr( $hca ) . "<br />";
	}

	$final['hash'] = ords_to_unistr( $shah );

	return json_encode( $final );
}

function ords_to_unistr($ords, $encoding = 'UTF-8'){
	// Turns an array of ordinal values into a string of unicode characters
	$str = '';
	for($i = 0; $i < sizeof($ords); $i++){
		// Pack this number into a 4-byte string
		// (Or multiple one-byte strings, depending on context.)
		$v = $ords[$i];
		$str .= pack("N",$v);
	}
	$str = mb_convert_encoding($str,$encoding,"UCS-4BE");
	return($str);
}

function unistr_to_ords($str, $encoding = 'UTF-8'){
	// Turns a string of unicode characters into an array of ordinal values,
	// Even if some of those characters are multibyte.
	$str = mb_convert_encoding($str,"UCS-4BE",$encoding);
	$ords = array();

	// Visit each unicode character
	for($i = 0; $i < mb_strlen($str,"UCS-4BE"); $i++){
		// Now we have 4 bytes. Find their total
		// numeric value.
		$s2 = mb_substr($str,$i,1,"UCS-4BE");
		$val = unpack("N",$s2);
		$ords[] = $val[1];
	}
	return($ords);
}