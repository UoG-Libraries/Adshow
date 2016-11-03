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
				3		Ask for any changes and return the changes, if there are some. Receives all the timestamps, which have to have the following JSON structure:
						{
							<playlist ID>: {
								<slide ID>: <slide timestamp>,
								<...>
							}
						}
						
						Response structure:
						[
							[<Any changes / additions>],
							[<Any deletions>]
						]
						
						If the deletion array contains a playlist with no slides then the client should remove the whole playlist and all its slides
				
			Every call will return a JSON object with the information requested.
			If a call fails, an error will be returned. The error has the following structure:
				{
					"error": < An error title >,
					"desc": < A human readable description of the error. Use it for logging >,
					"code": < An error code to identify the error in code. See the class ErrorCodes for more information. >
				}
	*/
	
	include_once 'db.php';
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
			
			$localPlaylist = null;
			if (!empty($playlist)) {
				if ($playlist[0]['active'] == '1') {
					$localPlaylist = $playlist[0];
				}
			}
			
			if (!empty($globalPlaylist)) {
				if ($globalPlaylist[0]['active'] == '1') {
					$globalPlaylist = $globalPlaylist[0];
				} else {
					$globalPlaylist = NULL;
				}
			}
			
			$deletions = array();
			$changes = array();
			
			// ******* CHANGES / ADDITIONS *******
			
			/// **** LOCAL CHANGES ****
			if ($localPlaylist) {
				if (!array_key_exists($localPlaylist['ID'], $timestamps)) {
					// screen playlist changed
					
					$localChanges = $db->getSlidesFromPlaylist($localPlaylist['ID']);
					$localPlaylist['slides'] = $localChanges;
					
					array_push($changes, $localPlaylist);
				} else {
					$localChanges = $db->getChangedSlidesForPlaylist($localPlaylist['ID'], $timestamps[$localPlaylist['ID']]);
					
					if ($localChanges && !empty($localChanges)) {
						$localPlaylist['slides'] = $localChanges;
						array_push($changes, $localPlaylist);
					} 
				}
			}
			
			
			/// **** GLOBAL CHANGES ****
			if ($globalPlaylist) {
				if (!array_key_exists($globalPlaylist['ID'], $timestamps)) {
					// screen playlist changed
					
					$globalChanges = $db->getSlidesFromPlaylist($globalPlaylist['ID']);
					$globalPlaylist['slides'] = $globalChanges;
					
					array_push($changes, $globalPlaylist);
				} else {
					$globalChanges = $db->getChangedSlidesForPlaylist($globalPlaylist['ID'], $timestamps[$globalPlaylist['ID']]);
					
					if ($globalChanges && !empty($globalChanges)) {
						$globalPlaylist['slides'] = $globalChanges;
						array_push($changes, $globalPlaylist);
					}
				}
			}
			
			
			
			/// ******* DELETIONS *******
			$localPlaylistHasChanged = $localPlaylist && !array_key_exists($localPlaylist['ID'], $timestamps);
			$globalPlaylistHasChanged = $globalPlaylist && !array_key_exists($globalPlaylist['ID'], $timestamps);
			
			if (sizeof($timestamps) == 2) {
				// *** REMOVE CHANGED PLAYLISTS ***
				if ($localPlaylistHasChanged && $globalPlaylistHasChanged) {
					// both playlist have changed => remove the old ones on the client side
					
					$keys = array_keys($timestamps);
					array_push($deletions, array('ID' => $keys[0]));
					array_push($deletions, array('ID' => $keys[1]));
				} else if ($localPlaylistHasChanged) {
					// The global playlist is still the same => remove the old local playlist
					
					array_push($deletions, array('ID' => array_keys($timestamps)[0])); // Index 0 = local playlist
				} else if ($globalPlaylistHasChanged) {
					// The local playlist hasn't changed => remove just the old global playlist
					
					array_push($deletions, array('ID' => array_keys($timestamps)[1])); // Index 1 = global playlist
				} // else: No changes have been made so there's nothing to delete
				
				// *** REMOVE REMOVED PLAYLISTS ***
				if ($localPlaylist && !$globalPlaylist) {
					// The client still has both playlists, but the global playlist has been removed
					
					array_push($deletions, array('ID' => array_keys($timestamps)[1]));
				} else if (!$localPlaylist && $globalPlaylist) {
					// The client still has both playlists, but the local playlist has been removed
					
					array_push($deletions, array('ID' => array_keys($timestamps)[0]));
				} else if (!$localPlaylist && !$globalPlaylist) {
					// The client still has both playlists, but both have been removed
					
					array_push($deletions, array('ID' => array_keys($timestamps)[0]));
					array_push($deletions, array('ID' => array_keys($timestamps)[1]));
				}
			} else if (sizeof($timestamps) == 1) {
				if ($localPlaylistHasChanged && !$globalPlaylistHasChanged) {
					// There's currently just a local playlist and it has changed => remove the old one
					
					if (!$globalPlaylist || array_keys($timestamps)[0] != $globalPlaylist['ID']) {
						array_push($deletions, array('ID' => array_keys($timestamps)[0]));
					}
				} else if ($globalPlaylistHasChanged && !$localPlaylistHasChanged) {
					// There's currently just a global playlist and it has changed => remove the old one
					
					if (!$localPlaylist || array_keys($timestamps)[0] != $localPlaylist['ID']) {
						array_push($deletions, array('ID' => array_keys($timestamps)[0]));
					}
				} else if ($globalPlaylistHasChanged && $localPlaylistHasChanged) {
					// Two new playlists have been added => remove the single one the client has
					
					array_push($deletions, array('ID' => array_keys($timestamps)[0]));
				}
			}
			
			if ($localPlaylist && !$localPlaylistHasChanged) {
				// There's still the same local playlist on the server and client => check for deleted slides
				
				$slideDeletions = $db->getRemovedSlidesForPlaylist($localPlaylist['ID'], $timestamps[$localPlaylist['ID']]);
				if (!empty($slideDeletions)) {
					$playlist = array('ID' => $localPlaylist['ID'], 'slides' => $slideDeletions);
					array_push($deletions, $playlist);
				}
			} 
			if ($globalPlaylist && !$globalPlaylistHasChanged) {
				$slideDeletions = $db->getRemovedSlidesForPlaylist($globalPlaylist['ID'], $timestamps[$globalPlaylist['ID']]);
				if (!empty($slideDeletions)) {
					$playlist = array('ID' => $globalPlaylist['ID'], 'slides' => $slideDeletions);
					array_push($deletions, $playlist);
				}
			}
			
			
			/*if ($localPlaylist) {
				if (!array_key_exists($localPlaylist['ID'], $timestamps)) {
					// Current local playlist has changed => remove old one
					
					$globalPlaylistHasChanged = $globalPlaylist && !array_key_exists($globalPlaylist['ID'], $timestamps);
					
					if (sizeof($timestamps) == 2) {
						if ($globalPlaylistHasChanged) {
							// remove both playlists, because the new ones will be added
							
							$keys = array_keys($timestamps);
							array_push($deletions, array('ID' => $keys[0]));
							array_push($deletions, array('ID' => $keys[1]));
						} else {
							// Just the local playlist has changed
							
							array_push($deletions, array('ID' => array_keys($timestamps)[0])); // Index 0 = local playlist
						}
					}
				}
			}
			
			if ($globalPlaylist) {
				
			}*/
			
			complete(HTTPStatusCode::Ok, array($changes, $deletions));
			break;
		
		default:
			complete(HTTPStatusCode::BadRequest, errObj('Unknown call', 'The call param given doesn\'t exist', ErrorCodes::CALL_DOESNT_EXIST));
			break;
	}
