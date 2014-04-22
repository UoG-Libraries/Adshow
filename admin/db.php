<?php

/*
 * author:        paul griffiths
 *
 * created:       19.12.13
 * last modified: 
 *
 * description:   class to handle database connection and interaction.
 */

class Database {

  /* private members
   ******************/

    private $host     = "localhost";
    private $dbname   = "adshow";

    private $user     = "ad5h0w_edit";
    private $pwd      = "w1n3C0rk";

    private $link     = "";
    

  /* private methods
   ******************/

    private function connect() {
		$this->link = mysql_connect($this->host,$this->user,$this->pwd);
		@mysql_select_db($this->dbname, $this->link);
    }

    private function select_query($query) {
		if (!$this->link) {
			$this->connect();
		}
		$result = mysql_query($query,$this->link);
		while($row = mysql_fetch_assoc($result)) {
			$rows[] = $row;
		}
		return $rows;
    }

    private function query($query) {
		if (!$this->link) {
			$this->connect();
		}
		return mysql_query($query,$this->link);
    }


  /* public methods
   *****************/

    public function __destruct() {
		if ($this->link != "") {
			mysql_close($this->link);
		}
    }


    // getter methods

    public function getSummary() {

		$query = "SELECT count(*) FROM Department";
		$result = $this->query($query);
		$summary["Department"] = mysql_result($result,0);		

		$query = "SELECT count(*) FROM User";
		$result = $this->query($query);
		$summary["User"] = mysql_result($result,0);		

		$query = "SELECT count(*) FROM Screen";
		$result = $this->query($query);
		$summary["Screen"] = mysql_result($result,0);

		$query = "SELECT count(*) FROM Playlist";
		$result = $this->query($query);
		$summary["Playlist"] = mysql_result($result,0);

		return $summary;
    }

	public function getDepartments() {
		$query ="SELECT * FROM Department";
		$rows = $this->select_query($query);

		return $rows;
	}

    public function getScreensList() {
		$query = "SELECT Screen.ID, Screen.location, Department.department FROM Screen, Department WHERE Screen.departmentIDfk = Department.ID";
		$rows = $this->select_query($query);

		return $rows;
    }

	public function getDepartmentsAndOwner () {
		$query ="SELECT Department.ID as ID, department, sNumber FROM Department LEFT JOIN User ON Department.ID = User.departmentIDfk;";
		$rows = $this->select_query($query);

		return $rows;
	}

	public function getPlaylists() {
		$query = "SELECT Playlist.ID as ID, Playlist.name as name, Playlist.active as active, User.sNumber as sNumber FROM Playlist, User
	WHERE Playlist.createdBy = User.ID";
		$rows = $this->select_query($query);

		return $rows;
    }


    // setter methods

	public function addScreen($location, $department) {
		$query = "INSERT INTO Screen SET location = '$location', departmentIDfk = '$department'";
		$this->query($query);
	}

	public function deleteScreen($id) {
		$query = "DELETE FROM Screen where ID = '$id'";
		$this->query($query);
	}

	public function addDepartment($department,$owner) {
		$query = "INSERT INTO Department SET department = '$department'";
		$this->query($query);
		$query = "SELECT ID FROM Department WHERE department = '$department'";
		$deptID = $this->query($query);
		$query = "INSERT INTO User SET sNumber = '$owner', owner = 1, departmentIDfk = '$deptID'";
		$this->query($query);
	}

	public function deleteDepartment($id) {
		$this->query("BEGIN");
		$query = "DELETE FROM User WHERE departmentIDfk = '$id'";
		$this->query($query);
		$query = "DELETE FROM Screen WHERE departmentIDfk = '$id'";
		$this->query($query);
		$query = "DELETE FROM Department WHERE ID = '$id'";
		$this->query($query);
		$this->query("COMMIT");

	}

/*
    public function getFiledQuestions() {
      $query = "SELECT questions.* FROM questions,types WHERE questions.typeIDfk=types.id AND types.name!='New' AND types.name!='Testing' ORDER BY date DESC";
      $rows = $this->select_query($query);
      return $rows;
    }

    public function getTestingQuestions() {
      $query = "SELECT questions.* FROM questions,types WHERE questions.typeIDfk=types.id AND types.name='Testing' ORDER BY date DESC";
      $rows = $this->select_query($query);
      return $rows;
    }

    public function getSubjects() {
      $query = "SELECT * FROM subjects,email WHERE subjects.emailIDfk=email.id ORDER BY subject";
      $rows = $this->select_query($query);
      return $rows;
    }

    public function getTypes() {
      $query = "SELECT * FROM types ORDER BY name";
      $rows = $this->select_query($query);
      foreach ($rows as $row) {
        $types[$row["id"]] = $row["name"];
      }
      return $types;
    }

    public function getTypeSummary() {
      $query = "SELECT types.name, count(question) as number FROM questions,types WHERE questions.typeIDfk=types.id GROUP BY types.name";
      $rows = $this->select_query($query);
      foreach ($rows as $row) {
        $summary[$row["name"]] = $row["number"];
      }
      return $summary;
    }

    public function getEmailList() {
      $query = "SELECT * FROM email";
      $rows = $this->select_query($query);
      return $rows;
    }

    public function getAutocomplete($box,$value) {
      if ($box == 'cat1') {
        $box = 'category1';
      }
      $value .= '%';
      $query = "SELECT name FROM $box WHERE name LIKE '$value'";
      $rows = $this->select_query($query);
      foreach($rows as $entry) {
        $entries[] = $entry["name"];
      }
      return $entries;
      //return $query;
    }

    // setter methods
    public function catagorise($field,$questionID,$value) {
      switch ($field) {
        case 'type' : $query = "UPDATE questions SET typeIDfk = $value WHERE id = $questionID";
                      break;
        case 'cat1' : $query = "UPDATE questions SET cat1IDfk = $value WHERE id = $questionID";
                      break;
        case 'cat2' :
                      break;
      }
      return $this->query($query);
    }
*/

}

?>