<?php
require __DIR__."/../../config/misc.php";
require_once __DIR__."/../lib/mainLib.php";
require_once __DIR__."/../lib/security.php";
require_once __DIR__."/../lib/exploitPatch.php";
require_once __DIR__."/../lib/enums.php";
$sec = new Security();

$person = $sec->loginPlayer();
if(!$person["success"]) exit(Library::returnGeometryDashResponse(CommonError::InvalidRequest));
$userID = $person['userID'];

$udid = Escape::base64($_POST['udid']) ?: '';

$stars = $demons = $coins = $userCoins = $moons = $diamonds = $creatorPoints = 0;
$usersData = ['data' => []];
$runSakujesJoke = date("d.m", time()) == "01.04" && $sakujes;

$type = Escape::latin($_POST["type"]);
$stat = Escape::number($_POST["stat"]) ?: 0;
$count = $_POST["count"] ? abs(Escape::number($_POST["count"])) : 50;

$leaderboard = Library::getLeaderboard($person, $type, $count, $stat);
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
	
	$stars += $userData['stars'];
	$demons += $userData['demons'];
	$coins += $userData['coins'];
	$userCoins += $userData['userCoins'];
	$moons += $userData['moons'];
	$diamonds += $userData['diamonds'];
	$creatorPoints += $userData['creatorPoints'];
}

if($moderatorsListInGlobal && $type == 'relative') {
	$userData = [];
	
	$userData['userName'] = "---Moderators---";
	$userData['userID'] = 0;
	$userData['stars'] = $stars;
	$userData['demons'] = $demons;
	$userData['rank'] = 0;
	$userData['udid'] = 0;
	$userData['creatorPoints'] = $creatorPoints;
	$userData['iconID'] = 0;
	$userData['color1'] = 0;
	$userData['color2'] = 0;
	$userData['shipID'] = 0;
	$userData['coins'] = $coins;
	$userData['special'] = 0;
	$userData['accountID'] = 0;
	$userData['userCoins'] = $userCoins;
	$userData['iconType'] = 0;
	$userData['diamonds'] = $diamonds;
	$userData['color3'] = 0;
	$userData['moons'] = $moons;
	
	array_unshift($usersData['data'], $userData);
}

exit(Library::returnGeometryDashArray($usersData, Keys::User));
?>