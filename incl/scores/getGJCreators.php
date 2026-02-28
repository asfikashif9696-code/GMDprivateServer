<?php
require_once __DIR__."/../lib/mainLib.php";
require_once __DIR__."/../lib/security.php";
require_once __DIR__."/../lib/exploitPatch.php";
require_once __DIR__."/../lib/enums.php";
$sec = new Security();

$person = $sec->loginPlayer();
if(!$person["success"]) exit(Library::returnGeometryDashResponse(CommonError::InvalidRequest));

$usersData = ['data' => []];
$runSakujesJoke = date("d.m", time()) == "01.04" && $sakujes;

$leaderboard = Library::getLeaderboard($person, "creators", 0);
$rank = $leaderboard['rank'];

foreach($leaderboard['leaderboard'] AS &$user) {
	if($user['stars'] <= 0 && $type == 'week') break;
	$userData = [];
	
	$rank++;
	$oldExtID = !$user['isRegistered'] && $userID == $user['userID'] ? $udid : $user['extID'];
	
	$userData['userName'] = !$runSakujesJoke ? Library::makeClanUsername($user["userName"], $user["clanID"]) : ($sakujesUsername ?: 'sakujes');
	$userData['userID'] = $user['userID'];
	$userData['stars'] = !$runSakujesJoke ? $user['stars'] : 99999;
	$userData['demons'] = !$runSakujesJoke ? $user['demons'] : 9999;
	$userData['rank'] = $rank;
	$userData['udid'] = $oldExtID;
	$userData['creatorPoints'] = round($user["creatorPoints"], PHP_ROUND_HALF_DOWN);
	$userData['iconID'] = $user['icon'];
	$userData['color1'] = $user['color1'];
	$userData['color2'] = $user['color2'];
	$userData['shipID'] = $user['iconType'] == 1 ? $user["accShip"] : 0;
	$userData['coins'] = !$runSakujesJoke ? $user['coins'] : 999;
	$userData['special'] = $user['special'];
	$userData['accountID'] = $user['extID'];
	$userData['iconType'] = $user['iconType'];
	$userData['userCoins'] = !$runSakujesJoke ? $user['userCoins'] : 999;
	$userData['diamonds'] = !$runSakujesJoke ? $user['diamonds'] : 99999;
	$userData['color3'] = $user['color3'];
	$userData['moons'] = !$runSakujesJoke ? $user['moons'] : 9999;
	
	$usersData['data'][] = $userData;
}

exit(Library::returnGeometryDashArray($usersData, Keys::User));
?>