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
 * Class user
 *
 * This is a class to represent a user of the system and
 * includes all (or at least most of) their data so that
 * we don't have to keep grabbing stuff from the database
 * and so I don't have to memorise database fields, only
 * the methods within this class... mostly.
 */
class user {

	private $userId;
	private $username;
	private $userMail;
	private $userPrimaryGroup;
	private $timeRegistered;
	private $userTimezone;
	private $userColor;
	private $userAvatar;
	private $userRank;
	private $userRankColor;
	private $auxGroupsList;
	private $userSig;
	private $userBio;

	/**
	 * This constructor allows us to give it the id of a user and
	 * for it to come back with all their data which it then feeds
	 * into all the fields above this method.
	 *
	 * @param int $userId The ID of the user that's being put into
	 *  the instance of this class
	 * @param String $home_dir The installation directory of the
	 *  forums so we can grab files as needed
	 */
	public function __construct( $userId, $home_dir ) {

		require_once($home_dir."/classes/group.php");
		require_once($home_dir."/function/database.php");
		require_once($home_dir."/function/function.php");

		$database = new database($home_dir);

		$rawUser = $database->getCompleteUserInfoFromId($userId);

		$this->userId = $userId;
		$this->username = $rawUser[2]['username'];
		$this->userMail = $rawUser[2]['user_email'];
		$this->userPrimaryGroup = new group($rawUser[2]['primary_group_id'],$this->userId,$home_dir);
		$this->timeRegistered = $rawUser[2]['time_reg'];
		$this->userTimezone = $rawUser[2]['user_timezone'];
		$this->userColor = $rawUser[2]['user_color'];
		$this->userAvatar = $rawUser[2]['user_avatar'];
		$this->userRank = $rawUser[1]['rank_name'];
		$this->userRankColor = $rawUser[1]['rank_color'];

		$auxGroups = array();
		foreach ($rawUser[0] as $group)
			array_push($auxGroups, new group($group['group_id'], $this->userId, $home_dir));

		$this->auxGroupsList = $auxGroups;
		$this->userSig = $rawUser[3]['user_sig'];
		$this->userBio = $rawUser[3]['user_bio'];

	}

	/**
	 * @return int Returns the ID of the person
	 */
	public function getId(){
		return $this->userId;
	}

	/**
	 * @return String Returns the username of the person
	 */
	public function getUsername() {
		return $this->username;
	}

	/**
	 * @return String Returns the email of the person
	 */
	public function getUserMail() {
		return $this->userMail;
	}

	/**
	 * @return group Returns the primary group of the person
	 */
	public function getPrimaryGroup() {
		return $this->userPrimaryGroup;
	}

	/**
	 * @return int Returns the UNIX time that the user joined this
	 *  forum
	 */
	public function getTimeRegistered() {
		return $this->timeRegistered;
	}

	/**
	 * @return int Returns what timezone value the person is living in
	 */
	public function getTimezone(){
		return $this->userTimezone;
	}

	/**
	 * @return String Returns the hexadecimal value for the color of
	 *  the person's systems
	 */
	public function getCurrentColor(){
		return $this->userColor;
	}

	/**
	 * @return String Returns the string representation of the avatar
	 *  of the person
	 */
	public function getAvatar(){
		return $this->userAvatar;
	}

	/**
	 * @return String Returns the name of the rank that the person belongs
	 *  to
	 */
	public function getRank(){
		return $this->userRank;
	}

	/**
	 * @return String Returns the hexadecimal color of the rank in the
	 *  method above for the same person
	 */
	public function getRankColor(){
		return $this->userRankColor;
	}

	/**
	 * @return array Returns an array of all the groups that the person is
	 *  joined to, including the primary group
	 */
	public function getAuxGroups(){
		return $this->auxGroupsList;
	}

	/**
	 * @return String Returns the signature thing of the person and the text
	 *  within
	 */
	public function getSignature(){
		return $this->userSig;
	}

	/**
	 * @return String Returns the biography of the person if it is set
	 */
	public function getBiography(){
		return $this->userBio;
	}

	/**
	 * Destroy the object... or sit here and do nothing.
	 * Not quite decided yet.
	 */
	public function __destruct() {}

}