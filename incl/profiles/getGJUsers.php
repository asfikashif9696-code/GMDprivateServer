<?php
require_once __DIR__."/../lib/mainLib.php";
require_once __DIR__."/../lib/exploitPatch.php";
require_once __DIR__."/../lib/security.php";
require_once __DIR__."/../lib/enums.php";
$sec = new Security();

$person = $sec->loginPlayer();
if(!$person["success"]) exit(Library::returnGeometryDashResponse(CommonError::InvalidRequest));

$str = Escape::text($_POST["str"]);
$page = abs(Escape::number($_POST["page"]));
$pageOffset = $page * 10;
$usersData = ['data' => [], 'pages' => []];

$users = Library::getUsers($str, $pageOffset);
if(!$users['users']) exit(Library::returnGeometryDashResponse(CommonError::NothingFound));

foreach($users['users'] AS &$user) {
	$userData = [];
	
	$userData['userName'] = Library::makeClanUsername($user["userName"], $user["clanID"]);
	$userData['userID'] = $user['userID'];
	$userData['stars'] = $user['stars'];
	$userData['demons'] = $user['demons'];
	$userData['creatorPoints'] = round($user["creatorPoints"], PHP_ROUND_HALF_DOWN);
	$userData['iconID'] = $user['icon'];
	$userData['color1'] = $user['color1'];
	$userData['color2'] = $user['color2'];
	$userData['shipID'] = $user['iconType'] == 1 ? $user["accShip"] : 0;
	$userData['coins'] = $user['coins'];
	$userData['special'] = $user['special'];
	$userData['accountID'] = $user['extID'];
	$userData['iconType'] = $user['iconType'];
	$userData['userCoins'] = $user['userCoins'];
	$userData['diamonds'] = $user['diamonds'];
	$userData['color3'] = $user['color3'];
	$userData['moons'] = $user['moons'];
	
	$usersData['data'][] = $userData;
}

$usersData['pages']['total'] = $users['count'];
$usersData['pages']['offset'] = $pageOffset;
$usersData['pages']['count'] = 10;

exit(Library::returnGeometryDashArray($usersData, Keys::User, ['pages']));
?>