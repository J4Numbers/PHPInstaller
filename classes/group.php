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
 * Class group
 *
 * A class to represent a group that is represented as a row in
 * a table of SQL values.
 */
class group {

	private $groupId;
	private $groupName;
	private $groupColor;
	private $groupInfo;
	private $joinedOn;
	private $statusId;

	/**
	 * Construct the object by taking the id and feeding it into the
	 * database so it gives us what we want. Then assign all the vars
	 * as they should be assigned.
	 *
	 * @param int $groupId The ID of the group we're searching for
	 * @param int $userId the ID of the user that is in this group
	 * @param String $home_dir The installation directory of the forums
	 */
	public function __construct( $groupId, $userId, $home_dir ) {

		require_once($home_dir."/function/database.php");

		$rawGroup = (new database($home_dir))->getInfoAboutJoinedGroupWithIds($groupId, $userId);

		$this->groupId = $groupId;
		$this->groupName = $rawGroup['group_name'];
		$this->groupColor = $rawGroup['group_color'];
		$this->groupInfo = $rawGroup['group_info'];
		$this->joinedOn = $rawGroup['joined_on'];
		$this->statusId = $rawGroup['status_id'];

	}

	/**
	 * @return int Return the id of the group we're in currently
	 */
	public function getId(){
		return $this->groupId;
	}

	/**
	 * @return String Return the name of the group that this is
	 */
	public function getName(){
		return $this->groupName;
	}

	/**
	 * @return String Return the Hex for the color this group has
	 *  decided to wear
	 */
	public function getColor(){
		return $this->groupColor;
	}

	/**
	 * @return String Return the information that has been provided
	 *  about this group
	 */
	public function getInfo(){
		return $this->groupInfo;
	}

}