<?php
require_once __DIR__."/../lib/mainLib.php";
require_once __DIR__."/../lib/exploitPatch.php";
require_once __DIR__."/../lib/security.php";
require_once __DIR__."/../lib/enums.php";
$sec = new Security();

$person = $sec->loginPlayer();
if(!$person["success"]) exit(Library::returnGeometryDashResponse(CommonError::InvalidRequest));

$toAccountID = Escape::latin_no_spaces($_POST["toAccountID"]);
$comment = trim(Escape::url_base64_decode(Escape::base64($_POST["comment"])));

if(Security::checkFilterViolation($person, $comment, 3)) exit(Library::returnGeometryDashResponse(CommonError::InvalidRequest));

$comment = Escape::url_base64_encode($comment);

$canSendFriendRequest = Library::canSendFriendRequest($person, $toAccountID);
if(!$canSendFriendRequest) exit(Library::returnGeometryDashResponse(CommonError::InvalidRequest));

Library::sendFriendRequest($person, $toAccountID, $comment);

exit(Library::returnGeometryDashResponse(CommonError::Success));
?>