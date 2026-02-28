<?php
require_once __DIR__."/../lib/mainLib.php";
require_once __DIR__."/../lib/exploitPatch.php";
require_once __DIR__."/../lib/security.php";
require_once __DIR__."/../lib/enums.php";
$sec = new Security();

$person = $sec->loginPlayer();
if(!$person["success"]) exit(Library::returnGeometryDashResponse(CommonError::InvalidRequest));

$page = abs(Escape::number($_POST['page']) ?: 0);
$getSent = abs(Escape::number($_POST['getSent']) ?: 0);
$pageOffset = $page * 10;
$usersData = ['data' => [], 'pages' => []];

$friendRequests = Library::getFriendRequests($person, $getSent, $pageOffset);

if(empty($friendRequests['requests'])) exit(Library::returnGeometryDashResponse(CommonError::NothingFound));

foreach($friendRequests['requests'] AS &$user) {
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
	$userData['friendRequestID'] = $user['ID'];
	$userData["friendRequestComment"] = Escape::url_base64_encode(Escape::translit(Escape::url_base64_decode($user["comment"])));
	$userData["friendRequestTimestamp"] = Library::makeTime($user["uploadDate"]);
	$userData['isFriendRequestNew'] = $user['person2'] == $user['extID'] ? $user['isNew1'] : $user['isNew2'];
	
	$usersData['data'][] = $userData;
}

$usersData['pages']['total'] = $users['count'];
$usersData['pages']['offset'] = $pageOffset;
$usersData['pages']['count'] = 10;

exit(Library::returnGeometryDashArray($usersData, Keys::User, ['pages']));
?>