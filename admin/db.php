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
        $this->ini = parse_ini_file('../app.ini');
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
        $query = "SELECT screen.ID, screen.location, screen.departmentIDfk AS departmentID, department.department, playlist.name AS playlistName FROM (screen, department) LEFT JOIN playlist ON playlist.ID = screen.playlistIDfk WHERE screen.departmentIDfk = department.ID ORDER BY department.department ASC, screen.location ASC";
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
        $query = "SELECT playlist.ID AS ID, playlist.name AS name, playlist.active AS active, playlist.global AS global, department.department AS department, playlist.screenOrientation AS screenOrientation FROM playlist, department
	WHERE playlist.departmentIDfk = department.ID AND playlist.ID = " . $id;
        $rows = $this->select_query($query);

        return $rows;
    }

    public function getPlaylists()
    {
        $query = "SELECT playlist.ID AS ID, playlist.name AS name, playlist.active AS active, playlist.global AS global, department.department AS department, playlist.screenOrientation AS screenOrientation FROM playlist, department
	WHERE playlist.departmentIDfk = department.ID ORDER BY department ASC, playlist.global DESC, playlist.active DESC ,playlist.name ASC ";
        $rows = $this->select_query($query);

        return $rows;
    }

    public function getPlaylistsByDeptID($deptId)
    {
        $query = "SELECT playlist.ID AS ID, playlist.name AS name, playlist.active AS active, playlist.global AS global, department.department AS department, playlist.screenOrientation AS screenOrientation FROM playlist, department
	WHERE playlist.departmentIDfk = department.ID AND department.ID = $deptId  ORDER BY department ASC, playlist.global DESC, playlist.active DESC ,playlist.name ASC";
        $rows = $this->select_query($query);

        return $rows;
    }

    public function getActivePlaylistsByDeptID($deptId, $orientation)
    {
        $query = "SELECT
  playlist.ID                AS ID,
  playlist.name              AS name,
  playlist.active            AS active,
  playlist.global            AS global,
  department.department      AS department,
  playlist.screenOrientation AS screenOrientation
FROM playlist, department
WHERE playlist.departmentIDfk = department.ID AND
      department.ID = $deptId AND
      playlist.active = 1 AND
      playlist.global = 0 AND
      playlist.screenOrientation = $orientation
ORDER BY department ASC, playlist.global DESC, playlist.active DESC, playlist.name ASC;";
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

        $query = "SELECT * FROM adshow.user WHERE `sNumber`='$sNumber'";
        $result = $this->select_query($query);

        return $result;
    }

    public function getUsers()
    {
        $query = "SELECT * FROM adshow.user";
        return $this->select_query($query);
    }

    public function userExists($sNumber)
    {
        $query = "SELECT count(1) as count FROM user WHERE sNumber='$sNumber'";
        $ret = $this->select_query($query);
        $count = $ret[0]['count'];

        return $count == '1';
    }

    public function getUsersWithDeptName($filterByDeptID = null)
    {
        $addition = '';
        if (is_numeric($filterByDeptID)) {
            $addition = " AND user.departmentIDfk=$filterByDeptID";
        }

        $query = "SELECT user.ID, user.sNumber, user.firstname, user.lastname, user.owner, department.department, user.permission FROM user JOIN department WHERE user.departmentIDfk=department.ID$addition ORDER BY user.permission DESC, department.department ASC, user.firstname ASC ";
        return $this->select_query($query);
    }

    public function getPlaylistForScreen($id)
    {
        $query = "SELECT * FROM playlist WHERE ID=(SELECT playlistIDfk FROM screen WHERE ID=$id)";
        return $this->select_query($query);
    }

    public function getGlobalPlaylist()
    {
        $query = 'SELECT * FROM playlist WHERE `global`=1';
        return $this->select_query($query);
    }
    
    public function getRemovedSlidesForPlaylist($playlistID, $timestamps)
    {
        $query = "SELECT `ID`, `active`, `title`, `text`, `playtime`, `templateName`, `playlistID`, `imageURL`, DATE_FORMAT(`timestamp`, '%Y-%m-%dT%H:%i:%s.000') as \"timestamp\", `markdownEnabled` as \"mdEnabled\" FROM slide WHERE `playlistID`=$playlistID";
        $result = $this->select_query($query);
		$return = array();
		
		$currentSlideIDs = array();
        if (!empty($result)) {
	        foreach ($result as $slide) {
		        $currentSlideIDs[] = $slide['ID'];
	        }
	        
	        foreach ($timestamps as $slideId => $timestamp) {
		        if (!in_array($slideId, $currentSlideIDs)) {
			        $return[] = array('ID' => $slideId, 'timestamp' => $timestamp, 'templateName' => '_delete');
		        }
	        }
	    }
	    
	    return $return;
	}

    public function getChangedSlidesForPlaylist($playlistID, $timestamps)
    {
        $query = "SELECT `ID`, `active`, `title`, `text`, `playtime`, `templateName`, `playlistID`, `imageURL`, DATE_FORMAT(`timestamp`, '%Y-%m-%dT%H:%i:%s.000') as \"timestamp\", `markdownEnabled` as \"mdEnabled\" FROM slide WHERE `playlistID`=$playlistID";
        $result = $this->select_query($query);
        $return = array();

        if (!empty($result)) {
            foreach ($result as $slide) {
                if (array_key_exists($slide['ID'], $timestamps)) {
                    $timestamp = strtotime($timestamps[$slide['ID']]);
                    $slideTimestamp = strtotime($slide['timestamp']);

                    if ($slideTimestamp > $timestamp && $slide['active']) {
                        $return[] = $slide;
                    }
                } else {
                    if ($slide['active']) {
                        $return[] = $slide;
                    }
                }
            }
        } else {
            return array();
        }

        return $return;

        /*$return = array();
        foreach ($timestamps as $slideID => $timestamp) {
            $query = "SELECT `ID`, `active`, `title`, `text`, `playtime`, `templateName`, `playlistID`, `changed`, `imageURL`, DATE_FORMAT(`timestamp`, '%Y-%m-%dT%H:%i:%s.000') as \"timestamp\" FROM slide WHERE `timestamp`>'$timestamp' AND `playlistID`=$playlistID AND `ID`=$slideID";
            $result = $this->select_query($query);
            if (!empty($result)) {
                $return[] = $result[0];
            }
        }
        
        return $return;*/
    }

    public function getSlidesFromPlaylist($playlistID)
    {
        $query = "SELECT `ID`, `active`, `title`, `text`, `playtime`, `templateName`, `playlistID`, `markdownEnabled`, `imageURL`, DATE_FORMAT(`timestamp`, '%Y-%m-%dT%H:%i:%s.000') as \"timestamp\", `markdownEnabled` as \"mdEnabled\" FROM slide WHERE playlistID = $playlistID";
        return $this->select_query($query);
    }

    public function getSlide($id)
    {
        $query = "SELECT `ID`, `active`, `title`, `text`, `playtime`, `templateName`, `playlistID`, `markdownEnabled`, `imageURL`, DATE_FORMAT(`timestamp`, '%Y-%m-%dT%H:%i:%s.000') as \"timestamp\", `markdownEnabled` as \"mdEnabled\" FROM slide WHERE id=$id";
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

    public function addPlaylist($name, $active, $departmentID, $screenOrientation)
    {
        $query = "INSERT INTO playlist(ID, name, active, global, departmentIDfk, screenOrientation) VALUE (NULL, '$name', $active , 0, $departmentID, $screenOrientation)";
        $this->query($query);
        return mysqli_insert_id($this->link);
    }

    public function addUser($sNumber, $isOwner, $deptIDfk, $permission, $firstname, $lastname)
    {
        $query = "INSERT INTO user (sNumber, owner, departmentIDfk, permission, firstname, lastname) VALUES ('$sNumber', $isOwner, $deptIDfk, $permission, '$firstname', '$lastname')";
        return $this->query($query) === TRUE;
    }

    public function addSlide($playlistID, $title, $text, $showTime, $imageURL, $templateName, $active, $markdownEnabled)
    {
        $showTime = $showTime == "" ? 5 : $showTime;
        $imageURL = str_replace(' ', '', $imageURL) == '' ? NULL : $imageURL;

        if (!$this->link) {
            $this->connect();
        }
        $text = mysqli_real_escape_string($this->link, $text);

        if ($imageURL == NULL) {
            $query = "INSERT INTO slide VALUE (NULL,$active,'$title','$text',$showTime,'$templateName' ,$playlistID, NULL, NULL, $markdownEnabled)";
        } else {
            $query = "INSERT INTO slide VALUE (NULL,$active,'$title','$text',$showTime,'$templateName' ,$playlistID,'$imageURL',NULL, $markdownEnabled)";
        }
        $this->query($query);
        $this->cleanUpImageFolder();
    }

    function cleanUpImageFolder()
    {
        $query = "SELECT imageURL FROM slide";
        $imageURLSource = $this->select_query($query);

        $imageURLs = array();
        foreach ($imageURLSource as $imageSource) {
            $imageURLs[] = $imageSource["imageURL"];
        }

        $baseDir = "../upload_files/";
        $dh = opendir($baseDir);
        $images = array();
        while (false !== ($filename = readdir($dh))) {
            if ($filename != '.' && $filename != '..' && (strpos($filename, 'jpg') !== false || strpos($filename, 'png') !== false)) {
                $images[] = $filename;
            }
        }

        foreach ($images as $image) {
            if (!in_array($image, $imageURLs)) {
                unlink($baseDir . $image);
            }
        }
    }

    public function addDepartment($department/*, $owner*/)
    {
        $query = "INSERT INTO department SET department = '$department'";
        $this->query($query);
        $query = "SELECT ID FROM department WHERE department = '$department'";
        $deptID = $this->select_query($query);

        if ($deptID === FALSE || sizeof($deptID) == 0) {
            return -1;
        } else {
            $deptID = $deptID[0]['ID'];
        }

        /*$query = "INSERT INTO user SET sNumber = '$owner', owner = 1, departmentIDfk = '$deptID'";
        $this->query($query);*/

        return $deptID;
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

    public function deleteDepartment($id)
    {
        $this->query("BEGIN");
        $query = "DELETE FROM user WHERE departmentIDfk = '$id'";
        $this->query($query);
        $query = "DELETE FROM screen WHERE departmentIDfk = '$id'";
        $this->query($query);
        $query = "DELETE FROM department WHERE ID = '$id'";
        $this->query($query);
        return $this->query("COMMIT");
    }

    public function deleteSlide($slideId)
    {
        $query = "DELETE FROM slide WHERE ID = " . $slideId;
        $this->query($query);
        $this->cleanUpImageFolder();
    }

    public function editDepartment($id, $name)
    {
        $query = "UPDATE department SET department='$name' WHERE ID=$id";
        return $this->query($query);
    }

    public function editScreen($location, $department, $orientation, $id)
    {
        $query = "UPDATE screen SET location = '" . $location . "', departmentIDfk =" . $department . ", orientation = '$orientation' WHERE ID= " . $id;
        $this->query($query);
    }

    public function editPlaylist($id, $name, $active, $global, $screenOrientation)
    {
        if ($global) {
            $query = "UPDATE playlist SET global=0 WHERE screenOrientation = $screenOrientation";
            $this->query($query);
            $query = "UPDATE screen SET playlistIDfk = NULL WHERE orientation = $screenOrientation AND playlistIDfk = $id";
            $this->query($query);
        }

        $query = "UPDATE playlist SET name = '$name', active = $active, global=$global, screenOrientation = $screenOrientation WHERE ID = $id";
        $this->query($query);
    }

    public function editSlide($id, $title, $text, $showTime, $imageURL, $templateName, $active, $markdownEnabled)
    {
        $showTime = $showTime == "" ? 5 : $showTime;
        $imageURL = str_replace(' ', '', $imageURL) == "" ? NULL : $imageURL;

        if (!$this->link) {
            $this->connect();
        }
        $text = mysqli_real_escape_string($this->link, $text);

        $query = "UPDATE slide SET active = $active, title ='$title', text ='$text', playtime = $showTime,imageURL='$imageURL', templateName= '$templateName',markdownEnabled=$markdownEnabled, timestamp=NULL WHERE id = " . $id;
        $this->query($query);
        $this->cleanUpImageFolder();
    }

    public function setPlaylistForScreen($screenID, $playlistID)
    {
        $query = "UPDATE screen SET playlistIDfk = $playlistID WHERE ID = $screenID";
        $this->query($query);
    }

    public function deletePlaylist($ID)
    {
        $this->query("BEGIN");
        $query = "UPDATE screen SET playlistIDfk = NULL WHERE playlistIDfk = $ID";
        $this->query($query);
        $query = "DELETE FROM slide WHERE playlistID = $ID";
        $this->query($query);
        $query = "DELETE FROM playlist WHERE ID = $ID";
        $this->query($query);
        return $this->query("COMMIT");
    }

    public function setActiveStatusOfPlaylist($id, $active)
    {
        $query = "UPDATE playlist SET active = $active WHERE ID = $id";
        $this->query($query);
    }

    public function setActiveStatusOfSlide($id, $active)
    {
        $query = "UPDATE slide SET active = $active WHERE ID = $id";
        $this->query($query);
    }
}
