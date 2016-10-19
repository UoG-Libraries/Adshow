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
    private $ini;
	private $link;


    /* constructor
    ******************/
    function __construct()
    {
        $this->ini = parse_ini_file('app.ini');
    }


  /* private methods
   ******************/

    private function connect() {
		$this->link = mysqli_connect($this->ini["db_host"],$this->ini["db_user"],$this->ini["db_pwd"],$this->ini["db_name"]);
    }

    private function select_query($query) {
		if (!$this->link) {
			$this->connect();
		}
		$result = mysqli_query($this->link, $query);
		$rows = array();
		while($row = mysqli_fetch_assoc($result)) {
			$rows[] = $row;
		}
		return $rows;
    }

    private function query($query) {
		if (!$this->link) {
			$this->connect();
		}
		return mysqli_query($this->link, $query);
    }
    
    public function prepared_query($query, $values) {
	    if (!$this->link) {
			$this->connect();
		}
		
		if ($statement = mysqli_prepare($this->link, $query)) {
			foreach ($values as $value) {
				mysqli_stmt_bind_param($statement, $value[0], $value[1]);
			}
			
			mysqli_stmt_execute($statement);
			mysqli_stmt_bind_result($statement, $result);
			mysqli_stmt_fetch($statement);
			
			return $result;
		} else {
			return NULL;
		}
    }


  /* public methods
   *****************/

    public function __destruct() {
		if ($this->link != "") {
			mysqli_close($this->link);
		}
    }


    // getter methods

    public function getSummary() {

		$query = "SELECT count(*) FROM department";
		$result = $this->query($query);
		$summary["Department"] = $result->fetch_array()[0];

		$query = "SELECT count(*) FROM user";
		$result = $this->query($query);
		$summary["User"] = $result->fetch_array()[0];

		$query = "SELECT count(*) FROM screen";
		$result = $this->query($query);
		$summary["Screen"] =$result->fetch_array()[0];

		$query = "SELECT count(*) FROM playlist";
		$result = $this->query($query);
		$summary["Playlist"] = $result->fetch_array()[0];

		return $summary;
    }

	public function getDepartments() {
		$query ="SELECT * FROM department";
		$rows = $this->select_query($query);

		return $rows;
	}

	public function getDepartment($id){
		$query = "SELECT * FROM department WHERE department.ID = " . $id;
		$rows = $this->select_query($query);

		return $rows;
	}

    public function getScreensList() {
		$query = "SELECT screen.ID, screen.location, department.department FROM screen, department WHERE screen.departmentIDfk = department.ID";
		$rows = $this->select_query($query);

		return $rows;
    }

	public function getDepartmentsAndOwner () {
		$query ="SELECT department.ID as ID, department, sNumber FROM department LEFT JOIN user ON department.ID = user.departmentIDfk;";
		$rows = $this->select_query($query);

		return $rows;
	}

	public function getPlaylists() {
		$query = "SELECT playlist.ID as ID, playlist.name as name, playlist.active as active, user.sNumber as sNumber FROM playlist, user
	WHERE playlist.createdBy = user.ID";
		$rows = $this->select_query($query);

		return $rows;
    }

    public function getScreen($id){
        $query = "SELECT * FROM adshow.screen WHERE ID = " . $id;
        $rows = $this->select_query($query);
        return $rows;
    }
    
    public function getUser($sNumber) {
	    $query = "SELECT * FROM adshow.user WHERE `sNumber`=?";
	    $result = $this->prepared_query($query, array(
		    "s" => $sNumber
	    ));
	    
	    return $result;
    }


    // setter methods

	public function addScreen($location, $department, $orientation) {
		$query = "INSERT INTO screen SET location = '$location', departmentIDfk = '$department' , orientation = '$orientation'";
		$this->query($query);
	}

	public function deleteScreen($id) {
		$query = "DELETE FROM screen where ID = '$id'";
		$this->query($query);
	}

	public function addDepartment($department,$owner) {
		$query = "INSERT INTO department SET department = '$department'";
		$this->query($query);
		$query = "SELECT ID FROM department WHERE department = '$department'";
		$deptID = $this->query($query);
		$query = "INSERT INTO user SET sNumber = '$owner', owner = 1, departmentIDfk = '$deptID'";
		$this->query($query);
	}

	public function deleteDepartment($id) {
		$this->query("BEGIN");
		$query = "DELETE FROM user WHERE departmentIDfk = '$id'";
		$this->query($query);
		$query = "DELETE FROM screen WHERE departmentIDfk = '$id'";
		$this->query($query);
		$query = "DELETE FROM department WHERE ID = '$id'";
		$this->query($query);
		$this->query("COMMIT");

	}

    public function editScreen($location, $department, $orientation, $id)
    {
        $query = "UPDATE screen SET location = '" . $location . "', departmentIDfk =" . $department . ", orientation = '$orientation' WHERE ID= " . $id;
        $this->query($query);
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
