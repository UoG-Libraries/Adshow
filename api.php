<?php
	/*
		api.php
		AUTHOR: Lukas Bischof
		CREATED ON: 24.10.2016
		
		BRIEF: This file is the central connection point between the client, which displays the slides, and the database.
		HOW TO USE:
			Use the api via a normal Ajax call. Every call must contain a GET parameter, called "call". These call params exist:
				
				PARAM	MEANING
				1		This call returns all the screens available
				2		Load the playlist data for a screen. Send an extra GET argument called "screen" with the ID of the screen.
				
			Every call will return a JSON object with the information requested.
			If a call fails, an error will be returned. The error has the following structure:
				{
					"error": < An error title >,
					"desc": < A human readable description of the error. Use it for logging >,
					"code": < An error code to identify the error in code. See the class ErrorCodes for more information. >
				}
	*/
	
	include_once 'admin/db.php';
	include_once 'httpCodes.php';
	
	class HTTPStatusCode {
		const Ok = 200;
		const BadRequest = 400;
		const MethodNotAllowed = 405;
		const InternalError = 500;
		
		public static function getStr($code) {
			return strtohttpcode($code);
		}
	}
	
	class ErrorCodes {
		const NO_CALL_PARAM = 1;
		const CALL_PARAM_INVALID = 2;
		const CALL_DOESNT_EXIST = 3;
		const CANT_RETREIVE_SCREENS = 4;
		const LACK_OF_ARGUMENTS = 5;
		const INVALID_ARGUMENT_TYPE = 6;
		const UNEXPECTED_ERROR = 7;
	}
	
	function errObj($title, $desc, $code) {
		return array(
			"error" => $title,
			"desc" => $desc,
			"code" => $code
		);
	}
	
	function complete($status, $obj) {
		$msg = "HTTP/1.1 $status " . HTTPStatusCode::getStr($status);
		header($msg);
		die(json_encode($obj));
	}

	if (!isset($_GET['call'])) {
		complete(HTTPStatusCode::BadRequest, errObj('Invalid call', 'The call parameter is missing', ErrorCodes::NO_CALL_PARAM));
	}
	
	$call = $_GET['call'];
	if (!is_numeric($call)) {
		complete(HTTPStatusCode::BadRequest, errObj('Invalid call', 'The call parameter is invalid', ErrorCodes::CALL_PARAM_INVALID));
	}
	
	$db = new Database();
	switch ($call) {
		case 1:
			$list = $db->getScreensList();
			if ($list !== NULL && $list !== FALSE) {
				complete(HTTPStatusCode::Ok, $list);
			} else {
				complete(HTTPStatusCode::InternalError, errObj('Can\'t retrieve screens', 'Due to a database error, the screens list can\' be retrieved', ErrorCodes::CANT_RETREIVE_SCREENS));
			}
			break;
			
		case 2:
			if (!isset($_GET['screen'])) {
				complete(HTTPStatusCode::BadRequest, errObj('Missing screen param', 'Missing GET param "screen" with the screen ID', ErrorCodes::LACK_OF_ARGUMENTS));
			}
			
			$screenID = $_GET['screen'];
			if (!is_numeric($screenID)) {
				complete(HTTPStatusCode::BadRequest, errObj('Screen param not an ID', 'Screen param must be an ID integer', ErrorCodes::INVALID_ARGUMENT_TYPE));
			} else if ($screenID < 0) {
				complete(HTTPStatusCode::BadRequest, errObj('Screen param not valid', 'The screen ID is malformed', ErrorCodes::INVALID_ARGUMENT_TYPE));
			}
			
			$globalPlaylist = $db->getGlobalPlaylist();
			$playlist = $db->getPlaylistForScreen($screenID);
			if ($playlist != FALSE) {
				$localPlaylist = null;
				if (!empty($playlist)) {
					$localPlaylist = $playlist[0];
				}
				
				if (!$globalPlaylist || empty($globalPlaylist)) {
					$globalPlaylist = null;
				} else {
					$globalPlaylist = $globalPlaylist[0];
				}
				
				$ret = array();
				if ($localPlaylist) {
					$slides = $db->getSlidesFromPlaylist($localPlaylist['ID']);
					
					$localPlaylist['slides'] = $slides;
					array_push($ret, $localPlaylist);
				}
				
				if ($globalPlaylist) {
					$slides = $db->getSlidesFromPlaylist($globalPlaylist['ID']);
					
					$globalPlaylist['slides'] = $slides;
					array_push($ret, $globalPlaylist);
				}
				
				complete(HTTPStatusCode::Ok, $ret);
			} else {
				complete(HTTPStatusCode::InternalError, errObj('Can\'t retrieve playlist', 'An internal DB error occurred', ErrorCodes::UNEXPECTED_ERROR));
			}
			
			break;
		
		default:
			complete(HTTPStatusCode::BadRequest, errObj('Unknown call', 'The call param given doesn\'t exist', ErrorCodes::CALL_DOESNT_EXIST));
			break;
	}
	
?>
