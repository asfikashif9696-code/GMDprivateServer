<?php
require_once __DIR__."/../lib/mainLib.php";
require_once __DIR__."/../lib/security.php";
require_once __DIR__."/../lib/commands.php";
require_once __DIR__."/../lib/exploitPatch.php";
require_once __DIR__."/../lib/enums.php";
$sec = new Security();

$person = $sec->loginPlayer();
if(!$person["success"]) exit(Library::returnGeometryDashResponse(CommonError::InvalidRequest));

$itemID = Escape::number($_POST['itemID']) ?: abs(Escape::number($_POST['levelID']) ?: 0);
$type = Escape::number($_POST['type']) ?: 1;
$isLike = Escape::number($_POST['like']);

if(!$itemID) exit(Library::returnGeometryDashResponse(CommonError::InvalidRequest));

$rateItem = Library::rateItem($person, $itemID, $type, $isLike);
if(!$rateItem) exit(Library::returnGeometryDashResponse(CommonError::InvalidRequest));

exit(Library::returnGeometryDashResponse(CommonError::Success));
?>