<?php
	if (!isset($_GET["name"])) {
		header('HTTP/1.1 404 Not Found');
		die('404 Not Found');
	}
	
	$name = $_GET['name'];
	$isTempl = preg_match('/[a-zA-Z0-9\_\-\.]+-templ/', $name) == 1;
	if (!$isTempl && $name != 'splash-screen') {
		header('HTTP/1.1 400 Bad Request');
		die('400 Bad Request');
	}
	
	$content = file_get_contents("$name/template.html");
	if ($content) {
		if (preg_match_all("/\<img[^\>]*src\=[\"\']([^\"\']+)(?:\"|\')[^\/>]*\/?\>/mi", $content, $matches) !== FALSE) {
			foreach ($matches[1] as $match) {
				$isURL = preg_match("/^(?:http|https):\/\/(?:[a-zA-Z0-9\/\?\.\-])+$/mi", $match);
				if ($isURL === 0) {
					$content = str_replace($match, "templates/$name/$match", $content);
				}
			}
		} else {
			header('HTTP/1.1 500 Internal Server Error');
			die('500 Internal Server Error');
		}
	} else {
		header('HTTP/1.1 400 Bad Request');
		die('400 Bad Request');
	}
	
	echo $content;
?>