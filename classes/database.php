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

/**
 * Class database
 *
 * A class to control all things databasy with the forums.
 * This includes things that might or might not be related
 * to the database.
 */
class database {

	//s = string
	//i = int
	//d = double
	//b = blob

	private $pdo_base;
	private $prefix;

	public function __construct( $home_dir ) {

		if ( !file_exists("$home_dir/config/props.php"))
			return false;

		require_once( "$home_dir/config/props.php" );

		$dsn = sprintf("mysql:dbname=%s;host=%s:%s;",DATABASE, DATAHOST, DATAPORT);

		try {

			$pdo_base = new PDO( $dsn, DATAUSER, DATAPASS );
			$pdo_base->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

			$this->pdo_base = $pdo_base;
			$this->prefix = DATAPFIX;

		} catch (PDOException $ex) {

			error_log( "Could not connect to database: " . $ex->getMessage() );
			die;

		}

	}

	/**
	 * Return the statement when we've given it the basic string template
	 * to play with.
	 *
	 * @param String $string : The string that we're going to put into
	 *  a PDO statement and the thing we're going to return
	 * @return PDOStatement : Well... it was the $string
	 * @throws PDOException : For when santa thinks we've been naughty
	 */
	private function makePreparedStatement( $string ) {

		try {

			$string = sprintf(str_replace('@','%1$s',$string ),$this->prefix);
			return $this->pdo_base->prepare( $string );

		} catch (PDOException $ex) {

			error_log("Failed to make prepared statement: " . $ex->getMessage());
			throw $ex;

		}

	}

	/**
	 * A function to bind all the values of a MySQLi statement to their actual
	 * values as given... read into that what you will.
	 *
	 * @param PDOStatement $statement : MySQL PDO statement
	 * @param array $arrayOfVars : An array, ordered, of all the values corresponding to the
	 *  $statement from before
	 * @return PDOStatement : Return the statement that has been produced, thanks
	 *  to our wonderful code... or false if it fails
	 * @throws PDOException : If the SQL doesn't like to be bound to anyone
	 */
	private function assignStatement( PDOStatement $statement, array $arrayOfVars ) {

		try {

			foreach ( $arrayOfVars as $key => &$val ) {
				error_log( sprintf("[KEY::%s]",$key) );
				error_log( sprintf("[VAL::%s]",$val) );
				$statement->bindParam( $key, $val );
			}

			return $statement;

		} catch (PDOException $except) {

			error_log( "Error encountered during statement binding: " . $except->getMessage() );
			throw $except;

		}

	}

	/**
	 * Execute a given status and return whatever happens to it
	 *
	 * @param PDOStatement $statement : The PDO statement that we're going to execute,
	 *  be it query, insertion, deletion, update, or fook knows
	 * @return PDOStatement : Return the rows that the statement gets
	 *  that have been returned if it manages to find any... Might blow up
	 * @throws PDOException : If SQL doth protest
	 * @throws Exception if the statement itself is borked
	 */
	private function executeStatement( PDOStatement $statement ) {

		try {

			$statement->execute();
			return $statement;

		} catch (PDOException $ex) {

			error_log( "Failed to execute statement: " . $ex->getMessage() );
			throw $ex;

		} catch (Exception $exe) {
			error_log( "Failed to execute statement: " . $exe->getMessage() );
			error_log( "Error code: " . $statement->errorCode() );
			error_log( "Statement was: " . $statement->queryString );
			throw $exe;
		}

	}

	/**
	 * Get, set and execute a statement in this one method instead of having
	 * the same lines in a dozen other statements, all doing the same thing.
	 *
	 * @param PDOStatement $statement : The statement that we're going to make
	 *  into a star...
	 * @param array $arrayOfVars : The variables that are going to go into this
	 *  statement at some point or another.
	 * @return PDOStatement : The result set that /should/ have been generated
	 *  from the PDO execution
	 * @throws PDOException : If PDO is a poor choice...
	 * @throws Exception if the statement is borked
	 */
	private function executePreparedStatement( PDOStatement $statement, $arrayOfVars ) {

		try {

			$stated = $this->assignStatement( $statement, $arrayOfVars );

			$result = $this->executeStatement( $stated );

			return $result;

		} catch (PDOException $ex) {

			throw $ex;

		} catch (Exception $exe) {

			throw $exe;

		}

	}

	/**
	 * Check whether or not a table already exists with the same name that
	 * we're giving it... how nice of us.
	 *
	 * @param String $tableName : The name of the table we're checking off
	 * @return bool : Of whether the table exists
	 * @throws PDOException : If SQL likes to throw a mardy
	 * @throws Exception if the statement is borked
	 */
	private function checkTableExists( $tableName ) {

		$arrayOfVars = array( ":table" => $this->prefix . $tableName );

		$sql = "SHOW TABLES LIKE :table";

		try {

			$result = $this->executePreparedStatement( $this->makePreparedStatement($sql), $arrayOfVars );
			return $result->rowCount() > 0;

		} catch (PDOException $ex) {

			throw $ex;

		} catch (Exception $exe) {

			throw $exe;

		}

	}

	public function checkUsernameExists( $username ) {

		$arrayOfVars = array( ":user" => strtolower($username) );

		$sql = "SELECT count(*) AS `total` FROM `@users` WHERE (`username`=:user)";

		try {

			$result = $this->executePreparedStatement($this->makePreparedStatement($sql), $arrayOfVars);
			$row = $result->fetch();

			return ( $row['total'] > 0 );

		} catch (PDOException $ex) {

			throw $ex;

		} catch (Exception $exe) {

			throw $exe;

		}

	}

	public function checkUserIdExists($userId) {

		$arrayOfVars = array(":userId" => $userId);

		$sql = "SELECT COUNT(*) AS `total` FROM `@users` WHERE (`user_id`=:userId)";

		try {

			$result = $this->executePreparedStatement($this->makePreparedStatement($sql), $arrayOfVars);
			$row = $result->fetch();

			return ( $row['total'] > 0 );

		} catch (PDOException $ex) {

			throw $ex;

		} catch (Exception $exe) {

			throw $exe;

		}

	}

	public function executeSimpleStatement( $sql ) {

		try {

			$this->executeStatement( $this->makePreparedStatement($sql) );

		} catch (PDOException $ex) {

			throw $ex;

		} catch (Exception $exe) {

			throw $exe;

		}

	}

	public function getUsersInstalled() {

		$sql = "SELECT COUNT(*) AS `total` FROM `@users`";

		try {

			$result = $this->executeStatement($this->makePreparedStatement($sql));
			$row = $result->fetch();

			return $row['total'] > 0;

		} catch (PDOException $ex) {

			throw $ex;

		} catch (Exception $exe) {

			throw $exe;

		}

	}

	public function getInstallStatus() {

		try {
			if ($this->checkTableExists("bbcode" ) &&
				$this->checkTableExists("cat_group_permissions") &&
				$this->checkTableExists("cat_user_permissions") &&
				$this->checkTableExists("categories") &&
				$this->checkTableExists("config") &&
				$this->checkTableExists("forum_group_permissions") &&
				$this->checkTableExists("forum_user_permissions") &&
				$this->checkTableExists("forums") &&
				$this->checkTableExists("group_permissions") &&
				$this->checkTableExists("group_status") &&
				$this->checkTableExists("groups") &&
				$this->checkTableExists("permissions") &&
				$this->checkTableExists("posts") &&
				$this->checkTableExists("private_messages") &&
				$this->checkTableExists("private_messages_group_mailing_list") &&
				$this->checkTableExists("private_messages_user_mailing_list") &&
				$this->checkTableExists("ranks") &&
				$this->checkTableExists("status_permissions") &&
				$this->checkTableExists("thread_group_permissions") &&
				$this->checkTableExists("thread_user_permissions") &&
				$this->checkTableExists("threads") &&
				$this->checkTableExists("user_groups") &&
				$this->checkTableExists("user_meta") &&
				$this->checkTableExists("user_permissions") &&
				$this->checkTableExists("users") )

				return true;

			return false;

		} catch (PDOException $ex) {
			return false;
		}

	}

	public function getPasswordFromUsername( $username ) {

		$arrayOfVars = array( ":userName" => $username );

		$sql = "SELECT `password` FROM `@users` WHERE (`username`=:userName)";

		try {

			$result = $this->executePreparedStatement($this->makePreparedStatement($sql),$arrayOfVars);

			if ($result->rowCount()<1) return false;

			$row = $result->fetch();

			return $row['password'];

		} catch (PDOException $ex) {

			throw $ex;

		} catch (Exception $exe) {

			throw $exe;

		}

	}

	public function getPasswordTimeFromUsername( $username ) {

		$arrayOfVars = array( ":userName" => $username );

		$sql = "SELECT `time_pass_altered` FROM `@users` WHERE (`username`=:userName)";

		try {

			$result =$this->executePreparedStatement($this->makePreparedStatement($sql),$arrayOfVars);

			if ( $result->rowCount() < 1 )
				return false;

			$row = $result->fetch();

			return $row['time_pass_altered'];

		} catch (PDOException $ex) {

			throw $ex;

		} catch (Exception $exe) {

			throw $exe;

		}

	}

	public function getJoinedGroups( $userId ) {

		$arrayOfVars = array( ":userId" => $userId );

		$sql = "SELECT `group_id` FROM `@user_groups` WHERE (`user_id`=:userId)";

		try {

			$result =$this->executePreparedStatement($this->makePreparedStatement($sql),$arrayOfVars);

			return $result->fetchAll();

		} catch (PDOException $ex) {

			throw $ex;

		} catch (Exception $exe) {

			throw $exe;

		}

	}

	public function getInfoAboutJoinedGroupWithIds( $groupId, $userId ) {

		$arrayOfVars = array( ":groupId" => $groupId,
			":userId" => $userId );

		$sql = "SELECT `u_groups`.`group_id`,`group_name`,`group_color`,`group_info`,`joined_on`,`status_id` FROM `@user_groups` AS `u_groups` INNER JOIN `@groups` AS `groups` ON `u_groups`.`group_id`=`groups`.`group_id` WHERE (`u_groups`.`user_id`=:userId AND `u_groups`.`group_id`=:groupId)";

		try {

			$result = $this->executePreparedStatement($this->makePreparedStatement($sql),$arrayOfVars);
			return $result->fetch();

		} catch (PDOException $ex) {
			throw $ex;
		} catch (Exception $exe) {
			throw $exe;
		}

	}

	public function getRankFromRankId( $rankId ) {

		$arrayOfVars = array( ":rankId" => $rankId );

		$sql = "SELECT * FROM `@ranks` WHERE (`rank_id`=:rankId)";

		try {

			$result = $this->executePreparedStatement($this->makePreparedStatement($sql),$arrayOfVars);
			return $result->fetch();

		} catch (PDOException $ex) {
			throw $ex;
		} catch (Exception $exe) {
			throw $exe;
		}

	}

	public function getGroupFromGroupId( $groupId ) {

		$arrayOfVars = array( ":groupId" => $groupId );

		$sql = "SELECT * FROM `@groups` WHERE (`group_id`=:groupId)";

		try {

			$result = $this->executePreparedStatement($this->makePreparedStatement($sql),$arrayOfVars);
			return $result->fetch();

		} catch (PDOException $ex) {
			throw $ex;
		} catch (Exception $exe) {
			throw $exe;
		}

	}

	public function getUserFromUserId( $userId ) {

		$arrayOfVars = array( ":userId" => $userId );

		$sql = "SELECT * FROM `@users` WHERE (`user_id`=:userId)";

		try {

			$result = $this->executePreparedStatement($this->makePreparedStatement($sql),$arrayOfVars);

			if ($result->rowCount() < 1) return false;

			return $result->fetch();

		} catch (PDOException $ex) {

			throw $ex;

		} catch (Exception $exe) {

			throw $exe;

		}

	}

	public function getUserMetaFromId( $userId ) {

		$arrayOfVars = array( ":userId" => $userId );

		$sql = "SELECT * FROM `@user_meta` WHERE (`user_id`=:userId)";

		try {

			$result = $this->executePreparedStatement($this->makePreparedStatement($sql),$arrayOfVars);

			if ( $result->rowCount() < 1 ) return false;

			return $result->fetch();

		} catch (PDOException $ex) {

			throw $ex;

		} catch (Exception $exe) {

			throw $exe;

		}

	}

	public function getCompleteUserInfoFromId($userId) {

		$userArray = array();

		array_push($userArray, $this->getJoinedGroups($userId));
		array_push($userArray, $this->getRankFromRankId($this->getRankFromUserWithId($userId)));
		array_push($userArray, $this->getUserFromUserId($userId));
		array_push($userArray, $this->getUserMetaFromId($userId));

		return $userArray;

	}

	public function getUserInfoFromId($userId) {

		$userArray = array();

		array_push($userArray, $this->getUserFromUserId($userId));
		array_push($userArray, $this->getRankFromUserWithId($userId));
		array_push($userArray, $this->getUserMetaFromId($userId));

		return $userArray;
	}

	public function getCompleteUserInfoFromUsername( $username ) {

		$userId = $this->getUserIdFromUserName($username);

		return $this->getCompleteUserInfoFromId($userId);

	}

	public function getRankFromUserWithId($userId) {

		$arrayOfVars = array( ":userId" => $userId );

		$sql = "SELECT `rank_id` FROM `@users` WHERE (`user_id`=:userId)";

		try {

			$result = $this->executePreparedStatement($this->makePreparedStatement($sql),$arrayOfVars);
			$row = $result->fetch();

			return $row['rank_id'];

		} catch (PDOException $ex) {
			throw $ex;
		} catch (Exception $exe) {
			throw $exe;
		}

	}

	public function getUserIdFromUserName( $userName ) {

		$arrayOfVars = array( ":userName" => strtolower($userName) );

		$sql = "SELECT `user_id` FROM `@users` WHERE (`username_cased`=:userName)";

		try {

			$result = $this->executePreparedStatement($this->makePreparedStatement($sql), $arrayOfVars);

			if ( $result->rowCount() < 1 ) return false;

			$row = $result->fetch();

			return intval($row['user_id']);

		} catch (PDOException $ex) {

			throw $ex;

		} catch (Exception $exe) {

			throw $exe;

		}

	}

	/**
	 * Add a completely new user into our database so that we can do
	 * all the wonderful things that we usually do with them.
	 *
	 * @param String $username : The username of the person we're adding in
	 * @param String $password : This has already been hashed... remember that
	 * @param int $hashTime : This is the time the hash was created; the Exact time
	 * @param String $email : The user's e-mail... to be checked via mail if set
	 * @param int $timezone : The current timezone of the user
	 * @param bool $admin_status : Whether the user is an administrator or not
	 * @return bool
	 * @throws PDOException : For if SQL doesn't like who we're adding
	 * @throws Exception if the statement is borked
	 */
	public function insertNewUser( $username, $password, $hashTime, $email, $timezone, $admin_status=false ) {
		if ($this->checkUsernameExists($username)) {

			$arrayOfVars = array( ":username" => $username,
				":casedUsername" => strtolower($username),
				":userEmail" => $email,
				":userPass" => $password,
				":times1" => time(),
				":times2" => $hashTime,
				":timezone" => $timezone) ;

			$sql = "INSERT INTO `@users` (`username`,`username_cased`,`user_email`,`password`,
					`time_reg`,`time_pass_altered`,`user_timezone`) VALUES (:username,
					:casedUsername,:userEmail,:userPass,:times1,:times2,:timezone)";
			$sql2 = "INSERT INTO `@user_meta` (`user_id`) VALUES (:userId)";
			$sql3 = "INSERT INTO `@user_groups` (`user_id`,`group_id`,`joined_on`,`status_id`)
					VALUES (:userId,'3',:times,'1')";

			try {

				$this->executePreparedStatement($this->makePreparedStatement($sql),$arrayOfVars);

				$lastId = $this->pdo_base->lastInsertId();

				$arrayOfVars2 = array( ":userId" => $lastId );

				$arrayOfVars3 = array( ":userId" => $lastId,
					":times" => time() );

				$this->executePreparedStatement($this->makePreparedStatement($sql2),$arrayOfVars2);
				$this->executePreparedStatement($this->makePreparedStatement($sql3),$arrayOfVars3);

				if ($admin_status)
					$this->insertExistingAdmin($lastId);

			} catch (PDOException $ex) {
				throw $ex;
			} catch (Exception $exe) {
				throw $exe;
			}
		}
	}

	/**
	 * A function to take the id of an existing user and to promote them into the
	 * stardom that is the life of the administrator.
	 *
	 * @param int $userId : The ID of the user that is being put into admin status
	 * @throws PDOException : If SQL doesn't like to do something at the current
	 *  hour in the day
	 * @throws Exception if the statement is borked
	 */
	public function insertExistingAdmin( $userId ) {

		$arrayOfVars1 = array( ":userId"=>$userId,
			":joined"=>time());
		$arrayOfVars2 = array( ":userId"=>$userId);

		$sql1 = "INSERT INTO `@user_groups` (`user_id`,`group_id`,`joined_on`,
				`status_id`) VALUES (:userId,'1',:joined,'3') ON DUPLICATE KEY
				UPDATE `user_id`=`user_id`";
		$sql2 = "UPDATE `@users` SET `primary_group_id`='1' WHERE (`user_id`=:userId)";

		try {

			$this->executePreparedStatement($this->makePreparedStatement($sql1),$arrayOfVars1);
			$this->executePreparedStatement($this->makePreparedStatement($sql2),$arrayOfVars2);

		} catch (PDOException $ex) {
			throw $ex;
		} catch (Exception $exe) {

			throw $exe;

		}

	}

	/**
	 * Destroy the database object in the, slightly suspect, documented
	 * method on the PHP site...
	 */
	public function __destruct() {
		$this->pdo_base = null;
	}

}