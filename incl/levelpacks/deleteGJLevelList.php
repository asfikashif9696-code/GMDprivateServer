<?php
require_once __DIR__."/../lib/mainLib.php";
require_once __DIR__."/../lib/security.php";
require_once __DIR__."/../lib/exploitPatch.php";
require_once __DIR__."/../lib/enums.php";
$sec = new Security();

$person = $sec->loginPlayer();
if(!$person["success"]) exit(Library::returnGeometryDashResponse(CommonError::InvalidRequest));
$accountID = $person['accountID'];

$listID = Escape::number($_POST['listID']);

$list = Library::getListByID($listID);
if(!$list || ($list['accountID'] != $accountID && !Library::checkPermission($person, 'gameDeleteLevel'))) exit(Library::returnGeometryDashResponse(CommonError::InvalidRequest));

Library::deleteList($listID, $person);

exit(Library::returnGeometryDashResponse(CommonError::Success));
?>