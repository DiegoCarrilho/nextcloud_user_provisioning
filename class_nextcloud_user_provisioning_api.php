<?php
class nextcloud_user_provisioning_api{
	
	/**
	 * An existing nextcloud admin account's user name
	 * Will be read from nextcloud_user_provisioning_api_config.inc
	 * @var string
	 */
	var $admin_username;
	
	/**
	 * The (existing) nextcloud admin account's password
	 * Will be read from nextcloud_user_provisioning_api_config.inc
	 * @var string
	 */
	var $admin_password;
	
	/**
	 * The URL of the nextcloud installation (e. g. "https://www.mynextcloud.tld")
	 * Will be read from nextcloud_user_provisioning_api_config.inc
	 * @var string
	 */
	var $base_url;
	
	/**
	 * Debug-Level. If set on true you will get lots of debug information from every function
	 * @var boolean
	 */
	var $debug = false;
	
	function __construct() {
		require_once("nextcloud_user_provisioning_api_config.inc");
		$this->admin_username=$admin_username;
		$this->admin_password = $admin_password;
		$this->base_url = $base_url.'/ocs/v1.php/cloud/';
		if (isset($debug)) {
			$this->debug=$debug;
		}
		if($this->debug===true) {
			echo '<p style="background-color: grey">__construct:<br />
			$this->admin_username='.$admin_username.'<br />
			$this->admin_password = '.$admin_password.'<br />
			$this->base_url = '.$base_url.'/ocs/v1.php/cloud/
		</p>';
		}		
	}
	
	/**
	 * Executes a Curl-Query
	 * @param string $url
	 * @param string $modus GET (Default), POST, DELETE or PUT
	 * @param array $postfields Optional: Array with POST-data
	 * @return mixed
	 */
	function doCurl($url,$modus,$postfields) {
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'OCS-APIRequest:true'
		));
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		if ($modus=="" or $modus=="GET") {
			curl_setopt($ch, CURLOPT_HTTPGET, 1);
		}
		if ($modus=="POST") {
			curl_setopt($ch, CURLOPT_POST, 1);
		}
		if ($modus=="PUT") {
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
		}
		if ($modus=="DELETE") {
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
		}
		if (is_array($postfields) and !empty($postfields)) {
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		}
		curl_setopt($ch, CURLOPT_USERPWD, $this->admin_username.":".$this->admin_password);
		$output = curl_exec($ch);
		if($this->debug===true) {
			echo '<p style="background-color: grey">doCurl:<br />';
			echo '$url: '.$url.'<br/>$modus: '.$modus.'<br/>$postfields:</br>';
			print_r($postfields);
			echo '</br>';
			echo 'output: '.$output;
			echo '</p>';			
		}
		curl_close($ch);
		return $output;
	}
	
	/**
	 * Gets all users of the Nextcloud instance
	 * @return array
	 */
	function getAllUsers() {
		$erg=$this->doCurl($this->base_url.'users?search=',"GET",array());
		$out = $this->xml2array($erg);	
		if ($out['meta']['statuscode']=="100") {				
			if (count($out['data']['users']['element'])==1) {
				return array($out['data']['users']['element']);
			} else {
				return $out['data']['users']['element'];
			}
		} else {
			return array();
		}
	}
	
	/**
	 * Information about a single user
	 * @param string $username
	 * @return array
	 */
	function getUser($username) {
		$erg=$this->doCurl($this->base_url.'users/'.$username,"GET",array());
		$out = $this->xml2array($erg);
		if ($out['meta']['statuscode']=="100") {			
			if (isset($out['data'])) {
				return $out['data'];
			} else {
				return array();
			}
		} else {
			return array();
		}
	}
	
	
	/**
	 * Information about the group memberships of a single user
	 * @param string $username
	 * @return array
	 */
	function getUserGroups($username) {
		$erg=$this->doCurl($this->base_url.'users/'.$username.'/groups',"GET",array());
		$out = $this->xml2array($erg);
		if ($out['meta']['statuscode']=="100") {			
			if (count($out['data']['groups']['element'])==1) {
				return array($out['data']['groups']['element']);
			} else {
				return $out['data']['groups']['element'];
			}
		} else {
			return array();
		}
	}	

	/**
	 * Checks, if a user exists
	 * @param string $username
	 * @return boolean
	 */
	function checkIfUserExists($username) {
		$erg=$this->getUser($username);
		if (!empty($erg)) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Adds a user, if it does not exist before
	 * @param string $username
	 * @param string $password
	 * @return mixed
	 */
	function addUser($username, $password) {
		$erg=$this->doCurl($this->base_url.'users',"POST",array("userid"=>$username,"password"=>$password));
		return $this->checkIfUserExists($username);		
	}	
	
	/**
	 * Adding a list of new users 
	 * @param unknown $array_username_password An Array with the designates username as key and the designates password as value
	 * @return array "number_successful" (int) "number_error" (int) "usernames_successful" (array) and "usernames_error" (array)
	 */
	function addMultipleUsers($array_username_password) {
		$out=array("number_successful"=>0,"number_error"=>0,"usernames_successful"=>array(),"usernames_error"=>array());
		if (!is_array($array_username_password)) {
			return $out;
		}
		foreach ($array_username_password as $username=>$password) {
			if ($this->addUser($username, $password)===true) {
				$out['number_successful']++;
				$out['usernames_successful'][]=$username;
			} else {
				$out['number_error']++;
				$out['usernames_error'][]=$username;
			}
		}
		return $out;
	}
	
	/**
	 * Edit data of an existing user
	 * @param string $username
	 * @param string $key The key of the user's data that you want to change. Possible keys: 
	 * mail
	 * quota
	 * displayname
	 * display (deprecated use displayname instead)
	 * phone
	 * address
	 * website
	 * twitter
	 * password
	 * @param string $new_value The new value for this key
	 * @return boolean
	 */
	function editUser($username, $key, $new_value) {
		$erg=$this->doCurl($this->base_url.'users/'.$username,"PUT",array("key"=>$key,"value"=>$new_value));
		$out = $this->xml2array($erg);
		if ($out['meta']['statuscode']=="100") {
			$userdata=$this->getUser($username);
			if ($userdata[$key]==$value) {
				return true;
			} else {
				return false;
			}			
		}
	}
	
	/**
	 * Disables a user, so that he cannot log in anymore
	 * @param string $username
	 * @return boolean
	 */
	function disableUser($username) {
		$erg=$this->doCurl($this->base_url.'users/'.$username.'/disable',"PUT",array());
		$out = $this->xml2array($erg);
		if ($out['meta']['statuscode']=="100") {
			$userinfo=$this->getUser($username);
			if ($userinfo['enabled']=="false") {
				return true;
			} else {
				return false;	
			}
		} else {
			return false;
		}
	}
	
	/**
	 * Disabling a list of users
	 * @param unknown $array_usernames An Array with the usernames to be disabled
	 * @return array "number_successful" (int) "number_error" (int) "usernames_successful" (array) and "usernames_error" (array)
	 */
	function disableMultipleUsers($array_usernames) {
		$out=array("number_successful"=>0,"number_error"=>0,"usernames_successful"=>array(),"usernames_error"=>array());
		if (!is_array($array_usernames)) {
			return $out;
		}
		foreach ($array_usernames as $nr=>$username) {
			if ($this->disableUser($username)===true) {
				$out['number_successful']++;
				$out['usernames_successful'][]=$username;
			} else {
				$out['number_error']++;
				$out['usernames_error'][]=$username;
			}
		}
		return $out;
	}
	
	/**
	 * Enables a disabled user, so  that the user can login again
	 * @param string $username
	 * @return boolean
	 */
	function enableUser($username) {
		$erg=$this->doCurl($this->base_url.'users/'.$username.'/enable',"PUT",array());
		$out = $this->xml2array($erg);
		if ($out['meta']['statuscode']=="100") {
			$userinfo=$this->getUser($username);
			if ($userinfo['enabled']=="true") {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	/**
	 * Enabling a list of users
	 * @param unknown $array_usernames An Array with the usernames to be enabled
	 * @return array "number_successful" (int) "number_error" (int) "usernames_successful" (array) and "usernames_error" (array)
	 */
	function enableMultipleUsers($array_usernames) {
		$out=array("number_successful"=>0,"number_error"=>0,"usernames_successful"=>array(),"usernames_error"=>array());
		if (!is_array($array_usernames)) {
			return $out;
		}
		foreach ($array_usernames as $nr=>$username) {
			if ($this->enableUser($username)===true) {
				$out['number_successful']++;
				$out['usernames_successful'][]=$username;
			} else {
				$out['number_error']++;
				$out['usernames_error'][]=$username;
			}
		}
		return $out;
	}
	
	/**
	 * Deletes a user
	 * @param string $username
	 * @return boolean
	 */
	function killUser($username) {
		if ($this->checkIfUserExists($username)===false) {
			return true;
		} else {
			$erg=$this->doCurl($this->base_url.'users/'.$username,"DELETE",array());
			$ext=$this->checkIfuserExists($username);
			if ($ext===false) {
				return true;
			} else {
				return false;
			}
		}
	}
	
	/**
	 * Killing a list of users
	 * @param unknown $array_usernames An Array with the usernames to kill
	 * @return array "number_successful" (int) "number_error" (int) "usernames_successful" (array) and "usernames_error" (array)
	 */
	function killMultipleUsers($array_usernames) {
		$out=array("number_successful"=>0,"number_error"=>0,"usernames_successful"=>array(),"usernames_error"=>array());
		if (!is_array($array_usernames)) {
			return $out;
		}
		foreach ($array_usernames as $nr=>$username) {
			if ($this->killUser($username)===true) {
				$out['number_successful']++;
				$out['usernames_successful'][]=$username;
			} else {
				$out['number_error']++;
				$out['usernames_error'][]=$username;
			}
		}
		return $out;
	}
	
	
	/**
	 * Gets all groups of the Nextcloud instance
	 * @return array
	 */
	function getAllGroups() {
		$erg=$this->doCurl($this->base_url.'groups?search=',"GET",array());
		$out = $this->xml2array($erg);
		if ($out['meta']['statuscode']=="100") {			
			if (count($out['data']['groups']['element'])==1) {
				return array($out['data']['groups']['element']);
			} else {
				return $out['data']['groups']['element'];
			}
		} else {
			return array();
		}
	}
	
	/**
	 * Information about a group
	 * @param string $groupname
	 * @return array
	 */
	function getGroup($groupname) {
		$erg=$this->doCurl($this->base_url.'groups?search='.$groupname,"GET",array());
		$out = $this->xml2array($erg);
		if ($out['meta']['statuscode']=="100") {			
			if (count($out['data']['groups']['element'])==1) {
				return array($out['data']['groups']['element']);
			} else {
				if (count($out['data']['groups']['element'])>1) {
					return $out['data']['groups']['element'];
				} else {
					return array();
				}
			}
		} else {
			return array();
		}
	}
	
	/**
	 * Checks, if a a group exists
	 * @param string $groupname
	 * @return boolean
	 */
	function checkIfGroupExists($groupname) {
		$erg=$this->getGroup($groupname);
		if (!empty($erg)) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Informationen about the members of a group
	 * @param string $groupname
	 * @return array
	 */
	function getGroupMembers($groupname) {
		$erg=$this->doCurl($this->base_url.'groups/'.$groupname,"GET",array());
		$out = $this->xml2array($erg);
		if ($out['meta']['statuscode']=="100") {			
			if (count($out['data']['users']['element'])==1) {			
				return array($out['data']['users']['element']);
			} else {
				if (count($out['data']['users']['element'])>1) {
					return $out['data']['users']['element'];
				} else {
					return array();
				}				
			}
		} else {
			return array();
		}
	}
	
	/**
	 * Adds a group if the no group with this name exists
	 * @param string $groupname
	 * @return boolean
	 */
	function addGroup($groupname) {
		$erg=$this->doCurl($this->base_url.'groups',"POST", array("groupid"=>$groupname));			
		return $this->checkIfGroupExists($groupname);		
	}
	
	/**
	 * Deletes a group
	 * @param string $groupname
	 * @return boolean
	 */
	function killGroup($groupname) {
		$erg=$this->doCurl($this->base_url.'groups/'.$groupname,"DELETE",array());
		$ext=$this->checkIfGroupExists($groupname);
		if ($ext===false) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Checks, if a user is a member of a group
	 * @param string $username 
	 * @param string $groupname
	 * @return boolean
	 */
	function checkIfUserIsMemberOfGroup($username, $groupname) {
		$groupmembers=$this->getGroupMembers($groupname);
		if (in_array($username,$groupmembers)) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Adds a user to a group
	 * @param string $username 
	 * @param string $groupname
	 * @return boolean
	 */
	function addUserToGroup($username, $groupname) {
		$erg=$this->doCurl($this->base_url.'users/'.$username.'/groups',"POST",array("groupid"=>$groupname));
		if ($this->checkIfUserIsMemberOfGroup($username, $groupname)===true) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Adding a list of users to groups
	 * @param unknown $array_usernames_and_groupnames An Array with Subarrays containing the username as key and the group are value
	 * Example: array(array("user1"=>"group1"),array("user2"=>"group2"))
	 * @return array "number_successful" (int) "number_error" (int) "usernames_successful" (array with Subarrays containing the username as key and the group are value) and "usernames_error" (array with Subarrays containing the username as key and the group are value)
	 */
	function addMultipleUsersToGroups($array_usernames_and_groupnames){
		$out=array("number_successful"=>0,"number_error"=>0,"usernames_successful"=>array(),"usernames_error"=>array());
		if (!is_array($array_usernames_and_groupnames)) {
			return $out;
		}
		foreach ($array_usernames_and_groupnames as $subarray) {
			$username=key($subarray);
			$groupname=current($subarray);
			if ($this->addUserToGroup($username,$groupname)===true) {
				$out['number_successful']++;
				$out['usernames_successful'][]=$subarray;
			} else {
				$out['number_error']++;
				$out['usernames_error'][]=$subarray;
			}
		}
		return $out;
	}
	
	/**
	 * Converts XML-Code into an array
	 * Code from http://bookofzeus.com/articles/php/convert-simplexml-object-into-php-array/
	 * @param string $xml
	 * @return array
	 */
	function xml2array($xml) {
		return json_decode(json_encode((array) simplexml_load_string($xml)), 1);	
	}
	
} // End of class
?>