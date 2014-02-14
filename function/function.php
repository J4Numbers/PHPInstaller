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

require_once "$home_dir/function/database.php";
require_once "$home_dir/classes/user.php";

/**
 * Get whether or not the properties file exists and therefore
 * whether the user has achieved basic installation.
 *
 * @param String $home_dir The directory we're going off to get
 *  to the config file if it exists
 * @return bool Whether the config file exists or not
 */
function isInstalled( $home_dir ) {

	$database = new database($home_dir);

	return ( file_exists("$home_dir/config/props.php") && $database->getInstallStatus() );

}

/**
 * Get whether the user has achieved full installation via
 * the number of users that may or may not be installed in
 * the database.
 *
 * @param String $home_dir The directory we're bumping off
 *  to use as a common root
 * @return bool of whether uers have been added yet or not
 */
function usersAdded( $home_dir ) {

	$database = new database($home_dir);

	return $database->getUsersInstalled();

}

/**
 * This is a catch-all thing for asking whether installation
 * has not been started or whether it has been completed full
 * stop.
 *
 * @param String $home_dir The root directory we're bouncing
 *  off from
 * @return bool of whether installation has finished.
 */
function usersExist( $home_dir ) {

	$database = new database($home_dir);

	return ( file_exists("$home_dir/config/props.php") && $database->getUsersInstalled() );

}

/**
 * Gather information on whether a username/userId already exists
 * or not in order to aid in facilitating a registration request.
 *
 * @param bool|int $userId This is not false when a userId has been
 *  provided to search for. The userId has priority over the name
 * @param bool|String $username This is not false when a username
 *  has been provided to search for. This is generally used for the
 *  registration process
 * @param String $home_dir This is used as a root point for files
 * @return bool Of whether this user and their details exists or not.
 *  Obviously, someone with no details whatsoever cannot exactly be
 *  true.
 */
function checkUserExists( $userId=false, $username=false, $home_dir ) {

	$database = new database($home_dir);

	if ($userId != false)
		return $database->checkUserIdExists($userId);

	if ($username != false)
		return $database->checkUsernameExists($username);

	return false;

}

function getUserIdFromUsername($username, $home_dir) {

	$database = new database($home_dir);

	return $database->getUserIdFromUserName($username);

}

/**
 * Return the object that represents the user to the
 * person that is asking. We shall take the ID and a
 * small child as compensation.
 *
 * @param int $userId The ID of the user for the class
 *  we're constructing
 * @param String $home_dir The directory that is of the
 *  root variety
 * @return user The resultant user object taken from the
 *  ID and a dir
 */
function getUserFromId( $userId, $home_dir ) {

	return new user($userId, $home_dir);

}

/**
 * @param $username
 * @param $home_dir
 * @return user
 */
function getUserFromName( $username, $home_dir ) {

	$database = new database($home_dir);

	return getUserFromId( $database->getUserIdFromUserName($username), $home_dir );

}

/**
 * A function to take an array of groups and then
 * see if a specified group is inside them. If it
 * is, we return that group, and if it's not, we
 * return false.
 *
 * @param array $groupArray The array of groups
 * @param int $specGroup The ID of the group we're
 *  looking for
 * @return bool|group depending on whether we find
 *  said group or not
 */
function extractGroupFromArrayWithId( $groupArray, $specGroup ) {

	foreach ($groupArray as $group)
		/**
		 * @var group $group The instance of this group in the
		 * array that we're currently looking at
		 */
		if ($group->getId()==$specGroup)
			return $group;

	return false;

}

/**
 * Conclusively start the session, even if it was not
 * already started.
 */
function checkSessionStarted() {
	if (session_status() == PHP_SESSION_NONE)
		session_start();
}

/**
 * If a user logs out or their session expires, we don't
 * necessarily want to be rid of all their data just yet.
 *
 * @param String $home_dir The directory for which the
 *  forums are installed
 */
function endUserSession($home_dir) {

	require_once "$home_dir/config/props.php";

	checkSessionStarted();
	unset($_SESSION[DOMAIN.'cyniForums']['user']);

}

/**
 * Once a user has passed their login test, we are free
 * to assign them their one-of-a-kind session ID! Please
 * note... this may not be one-of-a-kind and actually needs
 * a hashed Session ID in that case.
 *
 * @param String $username The login username of the person
 *  that needs to be marked as logged in.
 * @param String $home_dir The installation directory of the
 *  forum
 */
function createLoginSession( $username, $home_dir ) {

	require_once "$home_dir/config/props.php";

	checkSessionStarted();

	$database = new database($home_dir);
	$_SESSION[DOMAIN.'cyniForums']['user'] =
		json_encode(array("user_id"=>$database->getUserIdFromUserName($username)));

}

/**
 * Return the user ID associated with the session if they are logged
 * in, otherwise, we are free to return false about their login state.
 *
 * @param String $home_dir The path to the installation directory
 * @return bool|array : False if not logged in and the details of
 *  the user if they actually are logged in
 */
function fetchSession($home_dir) {

	require_once "$home_dir/config/props.php";

	checkSessionStarted();
	return (isset($_SESSION[DOMAIN.'cyniForums']['user'])) ? $_SESSION[DOMAIN.'cyniForums']['user'] : false;

}

function getUserIdFromSession( $home_dir ) {

	if (!fetchSession($home_dir))
		return false;

	$ret = (array) loadSession($home_dir);
	return $ret["user_id"];

}

function getUserFromSession($home_dir) {

	require_once $home_dir."/classes/user.php";

	return new user(getUserIdFromSession($home_dir),$home_dir);

}

/**
 * Get the decoded session contents about the user
 *
 * @param String $home_dir The route to the installation dir
 *  of the forums
 * @return object(std) The object representation of the array
 *  containing the data we need about the user
 */
function loadSession($home_dir) {

	return json_decode(fetchSession($home_dir));

}

function getLoginStatus($home_dir) {

	if ( !fetchSession($home_dir) ) {
		$ret = sprintf("<form method='post' action='%s/login.php' ><table class='login_table' >
					<tr>
						<td class='log_field' >Username</td>
						<td><input type='text' class='log_input' id='user_log_user' name='log_user' /></td>
						<td rowspan='2'></td>
					</tr>
					<tr>
						<td class='log_field' >Password</td>
						<td><input type='password' class='log_input' id='user_log_pass' name='log_pass' /></td>
					</tr>
					<tr><td colspan='2' >New? Why not <a href='%s/registration.php'>Register</a>?
						<input type='submit' name='Submit' /></td></tr></table></form>",INSTLOC,INSTLOC);
	} else {
		$user = getUserFromSession($home_dir);
		$ret = sprintf("<table><tr>Welcome %s</tr></table>", $user->getUsername());
	}

	return $ret;

}

function attemptLogin( $username, $password, $home_dir ) {

	$time = getUserRegTime($username, $home_dir);

	if ( $time == false ) return false;

	require_once "$home_dir/function/hash.php";

	$hash = (array) json_decode(cyniHash($password, $time));

	if ( compareHashedPasswordWithUsername($username, $hash['hash'], $home_dir) ) {

		createLoginSession( $username, $home_dir );
		header( "Location: index.php" );
		return true;

	} else {

		return false;

	}

}

function compareHashedPasswordWithUsername( $userName, $hashed, $home_dir ) {

	$database = new database($home_dir);

	$oriHash = $database->getPasswordFromUsername($userName);

	return ( $hashed == $oriHash );

}

function getUserRegTime( $userName, $home_dir ) {

	$database = new database($home_dir);

	return $database->getPasswordTimeFromUsername($userName);

}