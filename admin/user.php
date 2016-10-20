<?php
	/*
	 * author:        lukas bischof
	 *
	 * created:       19.10.2016
	 * last modified: 
	 *
	 * description:   class that represents a user.
	 */
	 
	include_once 'db.php';
	
	class Permission {
		const Editor = 0;
		const Admin = 1;
		const Superadmin = 2;
		
		public static function getStr($permission) {
			switch ($permission) {
				case 0:
					return "Editor";
				break;
				case 1:
					return "Administrator";
				break;
				case 2:
					return "Super-Administrator";
				break;
			}
		}
	}
	
	/**
		@class User
		@brief Represents a user
		@usage Call User::getCurrentUser(). This returns a user singleton. Never use new User(), because it will fail	
	*/
	class User {
		public $name;
		public $fullName;
		public $sNumber;
		public $department;
		public $permission;
		public $db; // For reuse
		
		public static $currentUser;
		
		public static function getUserWithID($id) {
			$user = new User();
			$user->db = new Database();
			$dbUser = $user->db->getUser($id);
			
			if (empty($dbUser)) {
				return NULL;
			} else {
				$dbUser = $dbUser[0];
				$user->sNumber = $dbUser['sNumber'];
				$user->department = $db->getDepartment($user['departmentIDfk']);
			}
			
			return $user;
		}
		
		public static function getCurrentUser() {
    	    if (null === static::$currentUser) {
    	        static::$currentUser = new static();
    	        static::$currentUser->initCurrentUser();
    	    }
    	    
    	    return static::$currentUser;
    	}
    	
    	private function initCurrentUser() {
	    	if (isset($_SESSION['auth']) && $_SESSION['auth'] == "true") {
		    	$this->db = new Database();
		    			    	
		    	$this->sNumber = $_SESSION['sNumber'];
		    	$this->name = $_SESSION['name'];
		    	$this->fullName = $_SESSION['fullname'];
		    	
		    	$userResult = $this->db->getUser($this->sNumber);
		    	$this->department = $this->db->getDepartment($userResult[0]['departmentIDfk'])[0];
		    	$this->permission = $userResult[0]["permission"];  /// @todo Find a better way
	    	}
    	}
    	
    	public function isSuperadmin() {
	    	return $this->permission == Permission::Superadmin;
    	}
    	
    	public function isAdmin() {
	    	return $this->permission == Permission::Admin;
    	}
    	
    	public function isEditor() {
	    	return $this->permission == Permission::Editor;
    	}
    	
    	/// Returns whether the user is some kind of admin (admin or superadmin)
    	public function hasAdminPrivileges() {
	    	return $this->isAdmin() || $this->isSuperadmin();
    	}
		
		public function __construct() {
			// intentionally left blank. Do not change.
		}
		
		public function __clone() {
			// intentionally left blank. Do not change.
		}
		
		public function __wakeup() {
			// intentionally left blank. Do not change.
		}
	}
?>