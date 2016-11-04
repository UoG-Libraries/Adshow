<?php
/*
 * author:        lukas bischof
 *
 * created:       19.10.2016
 * last modified:
 *
 * description:   class vm represents a user.
 */

include_once 'db.php';
/** @noinspection PhpIncludeInspection */
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/ldap.php';

class Permission
{
    const Editor = 0;
    const Admin = 1;
    const Superadmin = 2;

    // Don't add any constants, unless it's a new permission

    public static function getStr($permission)
    {
        switch ($permission) {
            case 0:
                return 'Editor';
                break;
            case 1:
                return 'Administrator';
                break;
            case 2:
                return 'Super-Administrator';
                break;
            default:
                return null;
        }
    }
}

/**
 * @class User
 * @brief Represents a user
 * @usage Call User::getCurrentUser(). This returns a user singleton. Never use new User(), because it will fail
 */
class User
{
    public static $currentUser;
    public $ID;
    public $name;
    public $fullName;
    public $sNumber;
    public $department;
    public $permission;
        public $isOwner; // For reuse
public $db;
        public $canEditGlobalPlaylists; // Tracking changes for MySQL Query
private $changes = array();

    public function __construct()
    {
        // intentionally left blank. Do not change.
    }

    public static function getNameOfUserWithSNumber($sNumb)
    {
        return (new Ldap())->getName(strtolower($sNumb));
    }

    public static function getUserWithSNumber($sNumb)
    {
        $user = new User();
        $user->db = new Database();
        $dbUser = $user->db->getUser(strtolower($sNumb));

        if (empty($dbUser)) {
            return NULL;
        } else {
            $dbUser = $dbUser[0];
            $user->sNumber = $sNumb;
            $user->department = $user->db->getDepartment($dbUser['departmentIDfk'])[0];
            $user->permission = $dbUser['permission'];
            $user->isOwner = $dbUser['owner'] == 1;
            $user->fullName = User::formatName($dbUser['firstname'], $dbUser['lastname']);
            $user->name = $dbUser['firstname'];
            $user->ID = $dbUser['ID'];
            $user->canEditGlobalPlaylists = $dbUser['global'];
        }

        return $user;
    }

    private static function formatName($firstname, $lastname)
    {
        return "$firstname " . ucfirst(strtolower($lastname));
    }

    public static function getCurrentUser()
    {
        if (null === static::$currentUser) {
            static::$currentUser = new static();
            static::$currentUser->initCurrentUser();
        }

        return static::$currentUser;
    }

    public static function userExists($sNumber)
    {
        return (new Database())->userExists($sNumber);
    }

    public function updatePermission($newPermission)
    {
        if (!is_numeric($newPermission) || $newPermission < 0 || $newPermission > 2) {
            throw new Exception('Invalid permission');
        } else if ($newPermission == $this->permission) {
            return;
        }

        $this->permission = $newPermission;
        array_push($this->changes, 'permission');
    }

    public function updateDepartment($newDepartmentIDfk)
    {
        if (!is_numeric($newDepartmentIDfk)) {
            throw new Exception('Invalid department ID');
        } else if ($this->department['ID'] == $newDepartmentIDfk) {
            return;
        }

        $department = $this->db->getDepartment($newDepartmentIDfk)[0];
        $this->department = $department;
        array_push($this->changes, 'department');
    }

    public function updateIsOwner($newIsOwner)
    {
        if (!is_bool($newIsOwner)) {
            throw new Exception('Invalid isOwner');
        }

        $this->isOwner = $newIsOwner;
        array_push($this->changes, 'owner');
    }

    public function updateCanEditGlobalPlaylists($newGlobalPermission)
    {
        if (!is_bool($newGlobalPermission)) {
            throw new Exception('Invalid "newGlobalPermission" parameter');
        }

        $this->canEditGlobalPlaylists = $newGlobalPermission;
        array_push($this->changes, 'global');
    }

    public function commitChanges()
    {
        if (empty($this->changes)) {
            return null;
        }

        $valueString = '';
        $i = 0;
        foreach ($this->changes as $change) {
            switch ($change) {
                case 'permission':
                    $valueString .= 'permission=' . $this->permission;
                    break;
                case 'owner':
                    $valueString .= 'owner=' . ($this->isOwner ? 1 : 0);
                    break;
                case 'department':
                    $valueString .= 'departmentIDfk=' . $this->department['ID'];
                    break;
                case 'global':
                    $valueString .= 'global=' . ($this->canEditGlobalPlaylists ? 1 : 0);
                    break;
                default:
                    return null;
            }

            if ($i++ < sizeof($this->changes) - 1) {
                $valueString .= ',';
            }
        }

        $query = "UPDATE user SET $valueString WHERE id=" . $this->ID;
        return $this->db->execSQL($query) === TRUE;
    }

    public function deleteUser()
    {
        return $this->db->deleteUser($this->sNumber) === TRUE;
    }

    public function isEditor()
    {
        return $this->permission == Permission::Editor;
    }

    public function isGlobal()
    {
        return true; //TODO check if user is global
    }

    public function getDepartmentID()
    {
        return $this->department['ID'];
    }

    public function hasAdminPrivileges()
    {
        return $this->isAdmin() || $this->isSuperadmin();
    }

    public function isAdmin()
    {
        return $this->permission == Permission::Admin;
    }

    /// Returns whether the user is some kind of admin (admin or superadmin)

    public function isSuperadmin()
    {
        return $this->permission == Permission::Superadmin;
    }

    public function __clone()
    {
        // intentionally left blank. Do not change.
    }

    public function __wakeup()
    {
        // intentionally left blank. Do not change.
    }

    private function initCurrentUser()
    {
        if (isset($_SESSION['auth']) && $_SESSION['auth'] == 'true') {
            $this->db = new Database();

            $this->sNumber = $_SESSION['sNumber'];
            $this->name = $_SESSION['name'];
            $this->fullName = $_SESSION['fullname'];

            $userResult = $this->db->getUser($this->sNumber);
            $this->department = $this->db->getDepartment($userResult[0]['departmentIDfk'])[0];
            $this->permission = $userResult[0]['permission'];  /// @todo Find a better way
            $this->isOwner = $userResult[0]['owner'] == 1;
            $this->ID = $userResult[0]['ID'];
            $this->canEditGlobalPlaylists = $userResult[0]['global'];
        }
    }
}