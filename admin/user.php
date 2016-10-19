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
	
	class User {
		public $name;
		public $fullName;
		public $sNumber;
		public $department;
		public $permission;
		
		public static $currentUser;
		
		public static function getCurrentUser() {
    	    if (null === static::$currentUser) {
    	        static::$currentUser = new static();
    	        static::$currentUser->initCurrentUser();
    	    }
    	    
    	    return static::$currentUser;
    	}
    	
    	private function initCurrentUser() {
	    	if (isset($_SESSION['auth']) && $_SESSION['auth'] == "true") {
		    	$db = new Database();
		    	
		    	$this->sNumber = $_SESSION['sNumber'];
		    	$this->name = $_SESSION['name'];
		    	$this->fullName = $_SESSION['fullname'];
		    	$this->department = $_SESSION['dept'];
		    	$this->permission = $db->getUser($this->sNumber);  /// @todo Find a better way
	    	}
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