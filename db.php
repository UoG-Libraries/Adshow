<?php

/*
 * author:        lukas bischof
 *
 * created:       03.11.16
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

    public function getScreensList()
    {
        $query = "SELECT screen.ID, screen.location, screen.departmentIDfk AS departmentID, department.department, playlist.name AS playlistName FROM (screen, department) LEFT JOIN playlist ON playlist.ID = screen.playlistIDfk WHERE screen.departmentIDfk = department.ID ORDER BY department.department ASC, screen.location ASC";
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

    private function connect()
    {
        $this->link = mysqli_connect($this->ini["db_host"], $this->ini["db_user_ro"], $this->ini["db_pwd_ro"], $this->ini["db_name"]);
    }


    /* public methods
     *****************/

    // getter methods

    public function getGlobalPlaylistForScreen($screenID)
    {
        $query = "SELECT * FROM playlist WHERE `global`=1 AND screenOrientation=(SELECT orientation FROM screen WHERE ID=$screenID)";
        return $this->select_query($query);
    }

    public function getPlaylistForScreen($id)
    {
        $query = "SELECT * FROM playlist WHERE ID=(SELECT playlistIDfk FROM screen WHERE ID=$id)";
        return $this->select_query($query);
    }

    public function getSlidesFromPlaylist($playlistID)
    {
        $query = "SELECT `ID`, `active`, `title`, `text`, `playtime`, `templateName`, `playlistID`, `markdownEnabled`, `imageURL`, DATE_FORMAT(`timestamp`, '%Y-%m-%dT%H:%i:%s.000') as \"timestamp\", `markdownEnabled` as \"mdEnabled\" FROM slide WHERE playlistID = $playlistID";
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

    public function getPlaylist($id)
    {
        $query = "SELECT playlist.ID AS ID, playlist.name AS name, playlist.active AS active, playlist.global AS global, department.department AS department, playlist.screenOrientation AS screenOrientation FROM playlist, department
	WHERE playlist.departmentIDfk = department.ID AND playlist.ID = " . $id;
        $rows = $this->select_query($query);

        return $rows;
    }

    public function getScreen($id)
    {
        $query = "SELECT * FROM adshow.screen WHERE ID = " . $id;
        $rows = $this->select_query($query);
        return $rows;
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

    private function query($query)
    {
        if (!$this->link) {
            $this->connect();
        }
        return mysqli_query($this->link, $query);
    }
}

?>
