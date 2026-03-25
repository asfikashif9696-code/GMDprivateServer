<?php
http_response_code(404);

require_once __DIR__."/../incl/dashboardLib.php";
require_once __DIR__."/../".$dbPath."incl/lib/mainLib.php";
require_once __DIR__."/../".$dbPath."incl/lib/exploitPatch.php";

$sfxID = Escape::number($_GET['sfxID']);

$sfx = Library::getSFXByID($sfxID);
if(!$sfx || $sfx['isDisabled']) exit();
$fileName = $sfx['name'].' ('.$sfxID.')';

http_response_code(200);
header("Cache-Control: max-age=604800");

// If SFX is from this folder, return it directly
if(file_exists(__DIR__.'/'.$sfxID.'.ogg')) exit(Library::returnFileByRange($fileName, __DIR__.'/'.$sfxID.'.ogg'));

// If file doesn't exist, and we have no redirect query, it means SFX doesn't exist in the folder for some reason
if(isset($_GET['dashboard-no-redirect'])) {
	http_response_code(404);
	exit();
}

$parseSFXURL = parse_url($sfx['download']);
$sfxURLQueries = !empty($parseSFXURL['query']) ? explode('&', $parseSFXURL['query']) : [];
$sfxURLQueries[] = 'dashboard-no-redirect';
$sfxURLQueries = implode("&", $sfxURLQueries);
$sfx['download'] = $parseSFXURL['scheme']."://".$parseSFXURL["host"].$parseSFXURL["path"]."?".$sfxURLQueries;

// Otherwise send a redirect to proper link
header("Location: ".$sfx['download']);
?>