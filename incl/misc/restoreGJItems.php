<?php
require_once __DIR__."/../lib/mainLib.php";
require_once __DIR__."/../lib/security.php";
require_once __DIR__."/../lib/exploitPatch.php";
require_once __DIR__."/../lib/XOR.php";
require_once __DIR__."/../lib/enums.php";
$sec = new Security();

$person = $sec->loginPlayer();
if(!$person["success"]) exit(Library::returnGeometryDashResponse(CommonError::InvalidRequest));

if(!file_exists(__DIR__."/../../data/info/".$person['accountID'])) exit(Library::returnGeometryDashResponse(CommonError::NothingFound));

exit(Library::returnGeometryDashResponse(XORCipher::cipher(Escape::url_base64_decode(file_get_contents(__DIR__."/../../data/info/".$person['accountID'])), 24157)));
?>