<?php
http_response_code(404);

require_once __DIR__."/../incl/dashboardLib.php";
require_once __DIR__."/../".$dbPath."incl/lib/mainLib.php";
require_once __DIR__."/../".$dbPath."incl/lib/exploitPatch.php";

$songID = Escape::number($_GET['songID']);

$song = Library::getSongByID($songID);
if(!$song || $song['isDisabled']) exit();
$fileName = $song['authorName'].' - '.$song['name'].' ('.$songID.')';

http_response_code(200);
header("Cache-Control: max-age=604800");

// If song is from this folder, return it directly
if(file_exists(__DIR__.'/'.$songID.'.mp3')) exit(Library::returnFileByRange($fileName, __DIR__.'/'.$songID.'.mp3'));
if(file_exists(__DIR__.'/'.$songID.'.ogg')) exit(Library::returnFileByRange($fileName, __DIR__.'/'.$songID.'.ogg'));

// If file doesn't exist, and we have no redirect query, it means file uploaded song doesn't exist in the folder for some reason
if(isset($_GET['dashboard-no-redirect'])) {
	http_response_code(404);
	exit();
}

$parseSongURL = parse_url($song['download']);
$songURLQueries = !empty($parseSongURL['query']) ? explode('&', $parseSongURL['query']) : [];
$songURLQueries[] = 'dashboard-no-redirect';
$songURLQueries = implode("&", $songURLQueries);
$song['download'] = $parseSongURL['scheme']."://".$parseSongURL["host"].$parseSongURL["path"]."?".$songURLQueries;

// Otherwise send a redirect to proper link
header("Location: ".$song['download']);
?>