<?php
require_once __DIR__."/../lib/mainLib.php";
require_once __DIR__."/../lib/exploitPatch.php";
require_once __DIR__."/../lib/security.php";
require_once __DIR__."/../lib/enums.php";
$sec = new Security();

$person = $sec->loginPlayer();
if(!$person["success"]) exit(Library::returnGeometryDashResponse(CommonError::InvalidRequest));

$type = Escape::number($_POST["type"]);
$usersData = ['data' => []];

switch($type) {
	case 0:
		$isBlocks = false;
		$users = Library::getFriendships($person);
		break;
	case 1:
		$isBlocks = true;
		$users = Library::getBlocks($person);
		break;
	case 2:
		exit(Library::returnGeometryDashResponse(CommonError::InvalidRequest));
}
if(empty($users)) exit(Library::returnGeometryDashResponse(CommonError::NothingFound));

foreach($users AS &$user) {
	$userData = [];
	
	$userData['userName'] = Library::makeClanUsername($user["userName"], $user["clanID"]);
	$userData['userID'] = $user['userID'];
	$userData['iconID'] = $user['icon'];
	$userData['color1'] = $user['color1'];
	$userData['color2'] = $user['color2'];
	$userData['special'] = $user['special'];
	$userData['accountID'] = $user['extID'];
	$userData['iconType'] = $user['iconType'];
	$userData['userCoins'] = $user['userCoins'];
	$userData['color3'] = $user['color3'];
	
	if(!$isBlocks) {
		$userData['isFriendRequestNew'] = $user['person2'] == $user['extID'] ? $user['isNew1'] : $user['isNew2'];
		$userData['messagingState'] = $user['mS'];
	}
	
	$usersData['data'][] = $userData;
}

exit(Library::returnGeometryDashArray($usersData, Keys::User));
?>