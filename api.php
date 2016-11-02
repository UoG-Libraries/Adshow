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
				3		Ask for any changes and return the changes, if there are some. Receives all the timestamps.
				
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
		const CustomNoDataAvailable = 528;
		
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
		const NO_DATA = 8;
	}
	
	function errObj($title, $desc, $code) {
		return array(
			'error' => $title,
			'desc' => $desc,
			'code' => $code
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
			if (!$globalPlaylist || empty($globalPlaylist)) {
				$globalPlaylist = null;
			} else {
				$globalPlaylist = $globalPlaylist[0];
				
				if ($globalPlaylist['active'] == '0') {
					$globalPlaylist = NULL;
				}
			}
				
			$playlist = $db->getPlaylistForScreen($screenID);
			if ($playlist != FALSE) {
				$localPlaylist = null;
				if (!empty($playlist)) {
					$localPlaylist = $playlist[0];
					
					if ($localPlaylist['active'] == '0') {
						$localPlaylist = NULL;
					}
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
				if ($playlist === FALSE) {
					complete(HTTPStatusCode::InternalError, errObj('Can\'t retrieve playlist', 'An internal DB error occurred', ErrorCodes::UNEXPECTED_ERROR));
				} else if (empty($playlist)) {
					if ($globalPlaylist) {
						$slides = $db->getSlidesFromPlaylist($globalPlaylist['ID']);
						
						$globalPlaylist['slides'] = $slides;
						complete(HTTPStatusCode::Ok, array($globalPlaylist));
					} else {
						complete(HTTPStatusCode::CustomNoDataAvailable, errObj('There\'s no playlist specified for this screen', 'Please assign an active playlist to this screen', ErrorCodes::NO_DATA));
					}
				}
			}
			
			break;
			
		case 3:
			if (!isset($_GET['screen'])) {
				complete(HTTPStatusCode::BadRequest, errObj('Missing screen param', 'Missing GET param "screen" with the screen ID', ErrorCodes::LACK_OF_ARGUMENTS));
			}
			
			$screenID = $_GET['screen'];
			if (!is_numeric($screenID)) {
				complete(HTTPStatusCode::BadRequest, errObj('Screen param not an ID', 'Screen param must be an ID integer', ErrorCodes::INVALID_ARGUMENT_TYPE));
			} else if ($screenID < 0) {
				complete(HTTPStatusCode::BadRequest, errObj('Screen param not valid', 'The screen ID is malformed', ErrorCodes::INVALID_ARGUMENT_TYPE));
			}
			
			if (!isset($_GET['timestamps'])) {
				complete(HTTPStatusCode::BadRequest, errObj('Missing timestamps param', 'Missing GET param "timestamps" with the current timestamps of the client', ErrorCodes::LACK_OF_ARGUMENTS));
			}
			
			$timestamps = $_GET['timestamps'];
			try {
				$timestamps = json_decode($timestamps, true);
				if ($timestamps === NULL) {
					complete(HTTPStatusCode::BadRequest, errObj('Invalid timestamps param', 'The timestamps param has an invalid JSON encoding', ErrorCodes::INVALID_ARGUMENT_TYPE));
				}
			} catch (Exception $e) {
				complete(HTTPStatusCode::BadRequest, errObj('Invalid timestamps param', 'The timestamps param has an invalid JSON encoding', ErrorCodes::INVALID_ARGUMENT_TYPE));
			}
			
			$globalPlaylist = $db->getGlobalPlaylist();
			$playlist = $db->getPlaylistForScreen($screenID);
			
			if ($playlist != FALSE) {				
				$localPlaylist = null;
				if (!empty($playlist)) {
					if ($playlist[0]['active'] == '1') {
						$localPlaylist = $playlist[0];
					}
				}
				
				if (!empty($globalPlaylist)) {
					if ($globalPlaylist[0]['active'] == '1') {
						$globalPlaylist = $globalPlaylist[0];
					}
				}
				
				$ret = array();
				
				/// **** LOCAL CHANGES ****
				if ($localPlaylist) {
					if (!array_key_exists($localPlaylist['ID'], $timestamps)) {
						// screen playlist changed
						
						$localChanges = $db->getSlidesFromPlaylist($localPlaylist['ID']);
						$localPlaylist['slides'] = $localChanges;
						
						array_push($ret, $localPlaylist);
					} else {
						$localChanges = $db->getChangedSlidesForPlaylist($localPlaylist['ID'], $timestamps[$localPlaylist['ID']]);
						
						if ($localChanges && !empty($localChanges)) {
							$localPlaylist['slides'] = $localChanges;
							array_push($ret, $localPlaylist);
						} 
					}
				}
				
				
				/// **** GLOBAL CHANGES ****
				if ($globalPlaylist) {
					if (!array_key_exists($globalPlaylist['ID'], $timestamps)) {
						// screen playlist changed
						
						$globalChanges = $db->getSlidesFromPlaylist($globalPlaylist['ID']);
						$globalPlaylist['slides'] = $globalChanges;
						
						array_push($ret, $globalPlaylist);
					} else {
						$globalChanges = $db->getChangedSlidesForPlaylist($globalPlaylist['ID'], $timestamps[$globalPlaylist['ID']]);
						
						if ($globalChanges && !empty($globalChanges)) {
							$globalPlaylist['slides'] = $globalChanges;
							array_push($ret, $globalPlaylist);
						}
					}
				}
				
				complete(HTTPStatusCode::Ok, $ret);
			} else {
				complete(HTTPStatusCode::InternalError, errObj('Can\'t get the new playlist/slides', 'The database returned an error', ErrorCodes::UNEXPECTED_ERROR));
			}
			break;
		
		default:
			complete(HTTPStatusCode::BadRequest, errObj('Unknown call', 'The call param given doesn\'t exist', ErrorCodes::CALL_DOESNT_EXIST));
			break;
	}
	
?>
