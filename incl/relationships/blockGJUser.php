<?php
require_once __DIR__."/../lib/mainLib.php";
require_once __DIR__."/../lib/exploitPatch.php";
require_once __DIR__."/../lib/security.php";
require_once __DIR__."/../lib/enums.php";
$sec = new Security();

$person = $sec->loginPlayer();
if(!$person["success"]) exit(Library::returnGeometryDashResponse(CommonError::InvalidRequest));

$targetAccountID = Escape::latin_no_spaces($_POST['targetAccountID']);

$blockUser = Library::blockUser($person, $targetAccountID);
if(!$blockUser) exit(Library::returnGeometryDashResponse(CommonError::InvalidRequest));

exit(Library::returnGeometryDashResponse(CommonError::Success));
?>