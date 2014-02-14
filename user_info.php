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

if ( !file_exists("../config/props.php" ) )
	header("Location: index.php");

require_once "../config/props.php";

$home_dir = getcwd()."/..";

require_once "../function/function.php";
require_once "../function/page_generation.php";

if (!isInstalled($home_dir) || usersExist($home_dir) )
	header("Location: index.php");

$pg = new pageTemplate( "installer.htm", $home_dir );

$menu = array();

$menu["Index"] = "index.php";

$menu["Install"] = "user_info.php";

foreach ( $menu as $name => $link )
	$pg->appendTag("MENU",
		"<a href='./$link' class='menuItem menuLink' >$name</a>");

$body1  = "<div class='newsarticle_header' ><h1>User Information</h1></div>";
$body1 .= "<div class='newsarticle_text'>Congratulations! You have the bare-bones of the forum installed. Now there's just the matter of making <strong>You</strong> a part of it. Please fill in the information below.</div>";

$table = "<table class='table_of_things' >
			<tr>
				<td class='field_name' >Username (Between 4 and 20 characters)</td>
				<td><input type='text' class='input' onblur='checkUsername(\"..\", function(data){})'
					 id='user_reg_user' placeholder='Username' /></td>
				<td id='reg_user_res' ></td>
			</tr>
			<tr>
				<td class='field_name' >Password (Above 5 characters)</td>
				<td><input type='password' class='input' onblur='checkPass1(\"..\", function(data){})'
					id='user_reg_pass1' placeholder='********' /></td>
				<td id='reg_pass1_res' ></td>
			</tr>
			<tr>
				<td class='field_name' >Confirm Password</td>
				<td><input type='password' class='input' onblur='checkPass2(\"..\", function(data){})'
					 id='user_reg_pass2' placeholder='********' /></td>
				<td id='reg_pass2_res' ></td>
			</tr>
			<tr>
				<td class='field_name' >E-mail Address</td>
				<td><input type='text' class='input' onblur='checkEmail(\"..\", function(data){})'
					id='user_reg_email' placeholder='example@abc.com' /></td>
				<td id='reg_email_res' ></td>
			</tr>
			<tr>
				<td class='field_name' >Timezone</td>
				<td><select id='user_reg_time' class='input' onblur='checkTime(\"..\", function(data){})' >
					<option value='N'>Choose</option>
					<option value='-12.0'>(GMT -12:00) Eniwetok, Kwajalein</option>
					<option value='-11.0'>(GMT -11:00) Midway Island, Samoa</option>
					<option value='-10.0'>(GMT -10:00) Hawaii</option>
					<option value='-9.0'>(GMT -9:00) Alaska</option>
					<option value='-8.0'>(GMT -8:00) Pacific Time (US &amp; Canada)</option>
					<option value='-7.0'>(GMT -7:00) Mountain Time (US &amp; Canada)</option>
					<option value='-6.0'>(GMT -6:00) Central Time (US &amp; Canada), Mexico City</option>
					<option value='-5.0'>(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima</option>
					<option value='-4.0'>(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz</option>
					<option value='-3.5'>(GMT -3:30) Newfoundland</option>
					<option value='-3.0'>(GMT -3:00) Brazil, Buenos Aires, Georgetown</option>
					<option value='-2.0'>(GMT -2:00) Mid-Atlantic</option>
					<option value='-1.0'>(GMT -1:00 hour) Azores, Cape Verde Islands</option>
					<option value='0.0'>(GMT) Western Europe Time, London, Lisbon, Casablanca</option>
					<option value='1.0'>(GMT +1:00 hour) Brussels, Copenhagen, Madrid, Paris</option>
					<option value='2.0'>(GMT +2:00) Kaliningrad, South Africa</option>
					<option value='3.0'>(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg</option>
					<option value='3.5'>(GMT +3:30) Tehran</option>
					<option value='4.0'>(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi</option>
					<option value='4.5'>(GMT +4:30) Kabul</option>
					<option value='5.0'>(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent</option>
					<option value='5.5'>(GMT +5:30) Bombay, Calcutta, Madras, New Delhi</option>
					<option value='5.75'>(GMT +5:45) Kathmandu</option>
					<option value='6.0'>(GMT +6:00) Almaty, Dhaka, Colombo</option>
					<option value='7.0'>(GMT +7:00) Bangkok, Hanoi, Jakarta</option>
					<option value='8.0'>(GMT +8:00) Beijing, Perth, Singapore, Hong Kong</option>
					<option value='9.0'>(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk</option>
					<option value='9.5'>(GMT +9:30) Adelaide, Darwin</option>
					<option value='10.0'>(GMT +10:00) Eastern Australia, Guam, Vladivostok</option>
					<option value='11.0'>(GMT +11:00) Magadan, Solomon Islands, New Caledonia</option>
					<option value='12.0'>(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka</option>
				</select></td>
				<td id='reg_time_res' ></td>
			</tr>
			<tr>
				<td class='buttons' id='fin_result' ></td>
				<td class='buttons' >
					<button onclick='submitAdminData(\"..\")' class='button' >Submit</button>
				</td>
			</tr>
		</table>";

$body2  = "<div class='newsarticle_header' ><h1>Information</h1></div>";
$body2 .= "<div class='newsarticle_text'>$table</div>";

$pg->setTag("HEAD", "<img src='../images/forum_logo.png' class='logo' />");
$pg->setTag("LOCATION", "..");
$pg->setTag("TITLE", "Installation");
$pg->setTag("BODY", "<div class='newsarticle'>$body1</div><div class='newsarticle'>$body2</div>");
$pg->setTag("FOOT", "");

$pg->showPage();