<?php

/*
 * author:        paul griffiths
 *
 * created:       19.12.13
 * last modified: 
 *
 * description:   class to handle database connection and interaction.
 */
error_reporting(E_ALL ^ E_NOTICE);

class Database
{

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

    public function __destruct()
    {
        if ($this->link != "") {
            mysqli_close($this->link);
        }
    }

    public function getSummary()
    {
        $query = "SELECT count(*) FROM department";
        $result = $this->query($query);
        $summary["Department"] = $result->fetch_array()[0];

        $query = "SELECT count(*) FROM user";
        $result = $this->query($query);
        $summary["User"] = $result->fetch_array()[0];

        $query = "SELECT count(*) FROM screen";
        $result = $this->query($query);
        $summary["Screen"] = $result->fetch_array()[0];

        $query = "SELECT count(*) FROM playlist";
        $result = $this->query($query);
        $summary["Playlist"] = $result->fetch_array()[0];

        return $summary;
    }

    private function query($query)
    {
        if (!$this->link) {
            $this->connect();
        }
        return mysqli_query($this->link, $query);
    }

    private function connect()
    {
        $this->link = mysqli_connect($this->ini["db_host"], $this->ini["db_user"], $this->ini["db_pwd"], $this->ini["db_name"]);
    }


    /* public methods
     *****************/

    // getter methods

    public function getDepartments()
    {
        $query = "SELECT * FROM department";
        $rows = $this->select_query($query);
        return $rows;
    }

    private function select_query($query)
    {
        if (!$this->link) {
            $this->connect();
        }
        $result = mysqli_query($this->link, $query);
        $rows = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function getDepartment($id)
    {
        $query = "SELECT * FROM department WHERE department.ID = " . $id;
        $rows = $this->select_query($query);

        return $rows;
    }

    public function getScreensList()
    {
        $query = "SELECT screen.ID, screen.location, department.department FROM screen, department WHERE screen.departmentIDfk = department.ID";
        $rows = $this->select_query($query);

        return $rows;
    }

    public function getDepartmentsAndOwner()
    {
        $query = "SELECT department.ID AS ID, department, sNumber FROM department LEFT JOIN user ON department.ID = user.departmentIDfk;";
        $rows = $this->select_query($query);

        return $rows;
    }

    public function getPlaylist($id)
    {
        $query = "SELECT playlist.ID AS ID, playlist.name AS name, playlist.active AS active, department.department AS department FROM playlist, department
	WHERE playlist.departmentIDfk = department.ID AND playlist.ID = " . $id;
        $rows = $this->select_query($query);

        return $rows;
    }

    public function getPlaylists()
    {
        $query = "SELECT playlist.ID AS ID, playlist.name AS name, playlist.active AS active, department.department AS department FROM playlist, department
	WHERE playlist.departmentIDfk = department.ID";
        $rows = $this->select_query($query);

        return $rows;
    }

    public function getScreen($id)
    {
        $query = "SELECT * FROM adshow.screen WHERE ID = " . $id;
        $rows = $this->select_query($query);
        return $rows;
    }

    public function getUser($sNumber)
    {
        if (!preg_match('/^s[0-9]{7}$/', strtolower($sNumber))) {
            throw new Exception("Invalid S-Number");
        }

        $query = "SELECT * FROM adshow.user WHERE `sNumber`=\"$sNumber\"";
        $result = $this->select_query($query);

        return $result;
    }

    public function getUsers()
    {
        $query = "SELECT * FROM adshow.user";
        return $this->select_query($query);
    }

    public function getUsersWithDeptName($filterByDeptID = null)
    {
        $addition = '';
        if (is_numeric($filterByDeptID)) {
            $addition = " AND user.departmentIDfk=$filterByDeptID";
        }

        $query = "SELECT user.ID, user.sNumber, user.firstname, user.lastname, user.owner, department.department, user.permission FROM user JOIN department WHERE user.departmentIDfk=department.ID$addition";
        return $this->select_query($query);
    }
    
    public function getPlaylistForScreen($id) {
	    $query = "SELECT * FROM playlist WHERE ID=(SELECT playlistIDfk FROM screen WHERE ID=$id)";
	    return $this->select_query($query);
    }
    
    public function getGlobalPlaylist() {
	    $query = 'SELECT * FROM playlist WHERE `global`=1';
	    return $this->select_query($query);
    }

    public function getSlidesFromPlaylist($playlistID)
    {
        $query = "SELECT * FROM slide WHERE playlistID = " . $playlistID;
        return $this->select_query($query);
    }

    public function getSlide($id)
    {
        $query = "SELECT * FROM slide WHERE ID = " . $id;
        return $this->select_query($query);
    }

    public function execSQL($sql)
    {
        return $this->query($sql);
    }

    // setter methods

    public function addScreen($location, $department, $orientation)
    {
        $query = "INSERT INTO screen SET location = '$location', departmentIDfk = '$department' , orientation = '$orientation'";
        $this->query($query);
    }

    public function addPlaylist($createdBy, $name, $active)
    {
        $createdBy = 1;
        $query = "INSERT INTO playlist(ID, name, active, createdBy) VALUE (NULL, '" . $name . "', " . $active . " , " . $createdBy . ")";
        $this->query($query);
        print_r($query);
    }

    public function addUser($sNumber, $isOwner, $deptIDfk, $permission, $firstname, $lastname)
    {
        $query = "INSERT INTO user (sNumber, owner, departmentIDfk, permission, firstname, lastname) VALUES ('$sNumber', $isOwner, $deptIDfk, $permission, '$firstname', '$lastname')";
        return $this->query($query) === TRUE;
    }

    public function addSlide($playlistID, $title, $text, $showTime, $templateName)
    {
        $query = "INSERT INTO slide VALUE (NULL,1,'" . $title . "','" . $text . "'," . $showTime . ", '" . $templateName . "' ," . $playlistID . ")";
        $this->query($query);
    }

    public function deleteScreen($id)
    {
        $query = "DELETE FROM screen where ID = '$id'";
        $this->query($query);
    }

    public function deleteUser($sNumb)
    {
        return $this->query("DELETE FROM user WHERE `sNumber`='$sNumb'");
    }

    public function addDepartment($department, $owner)
    {
        $query = "INSERT INTO department SET department = '$department'";
        $this->query($query);
        $query = "SELECT ID FROM department WHERE department = '$department'";
        $deptID = $this->query($query);
        $query = "INSERT INTO user SET sNumber = '$owner', owner = 1, departmentIDfk = '$deptID'";
        $this->query($query);
    }


    public function deleteDepartment($id)
    {
        $this->query("BEGIN");
        $query = "DELETE FROM user WHERE departmentIDfk = '$id'";
        $this->query($query);
        $query = "DELETE FROM screen WHERE departmentIDfk = '$id'";
        $this->query($query);
        $query = "DELETE FROM department WHERE ID = '$id'";
        $this->query($query);
        $this->query("COMMIT");
    }

    public function deleteSlide($slideId)
    {
        $query = "DELETE FROM slide WHERE ID = " . $slideId;
        $this->query($query);
    }

    public function editScreen($location, $department, $orientation, $id)
    {
        $query = "UPDATE screen SET location = '" . $location . "', departmentIDfk =" . $department . ", orientation = '$orientation' WHERE ID= " . $id;
        $this->query($query);
    }

    public function editPlaylist($id, $name, $active)
    {
        $query = "UPDATE playlist SET name = '" . $name . "', active =" . $active . " WHERE ID = " . $id;
        $this->query($query);
    }

    public function editSlide($id, $title, $text, $showTime, $templateName)
    {
        $query = "UPDATE slide SET active = 1, title ='" . $title . "', text ='" . $text . "', playtime = " . $showTime . ", templateName= '" . $templateName . "' WHERE id = " . $id;
        echo $query;
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
