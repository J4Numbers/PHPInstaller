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

//if ( file_exists("../config/props.php") )
//	header("Location: ../");

$home_dir = getcwd()."/..";

require_once "$home_dir/function/page_generation.php";
require_once "$home_dir/function/function.php";

$pg = new pageTemplate( "installer.htm", $home_dir );

$menu = array();

$menu["Index"] = "#";

if (!isInstalled($home_dir))
	$menu["Install/Repair"] = "install.php";
else
	$menu["Re-install"] = "reinstall.php";

foreach ( $menu as $name => $link )
	$pg->appendTag("MENU",
		"<a href='./$link' class='menuItem menuLink' >$name</a>");

$body = "<div class='newsarticle_text'>Congratulations! You have installed your copy of these forums. To complete the installation, please rename the install directory to something else to avoid unwary users finding their way in here. After you have done this, feel free to have a look around your new forums and configuration panel. For any further support on the matter, please contact the creator at <a href='mailto:numbers@cynicode.co.uk>' >numbers@cynicode.co.uk</a>.</div>";

$pg->setTag( "LOCATION", ".." );
$pg->setTag( "TITLE", "Installation Complete" );
$pg->setTag( "BODY", "<div class='newsarticle'>$body</div>" );
$pg->setTag( "HEAD", "<img src='../images/forum_logo.png' class='logo' />" );
$pg->setTag( "FOOT", "" );

$pg->showPage();

?>