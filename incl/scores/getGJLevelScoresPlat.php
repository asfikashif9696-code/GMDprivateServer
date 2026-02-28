<?php
require_once __DIR__."/../lib/mainLib.php";
require_once __DIR__."/../lib/security.php";
require_once __DIR__."/../lib/exploitPatch.php";
require_once __DIR__."/../lib/XOR.php";
require_once __DIR__."/../lib/enums.php";
$sec = new Security();

$person = $sec->loginPlayer();
if(!$person["success"]) exit(Library::returnGeometryDashResponse(CommonError::InvalidRequest));

$usersData = ['data' => []];
$scores = [];

$levelID = abs(Escape::number($_POST['levelID']) ?: 0);
$type = abs(Escape::number($_POST['type']) ?: 0);
$scores['time'] = abs(Escape::number($_POST["time"]));
$scores['points'] = Escape::number($_POST["points"]);

$attempts = !empty($_POST["s1"]) ? abs(Escape::number($_POST["s1"])) - 8354 : 0;
$clicks = !empty($_POST["s2"]) ? abs(Escape::number($_POST["s2"])) - 3991 : 0;
$progresses = !empty($_POST["s6"]) ? Escape::multiple_ids(XORCipher::cipher(Escape::url_base64_decode($_POST["s6"]), 41274)) : 0;
$coins = !empty($_POST["s9"]) ? abs(Escape::number($_POST["s9"])) - 5819 : 0;
$dailyID = !empty($_POST["s10"]) ? abs(Escape::number($_POST["s10"])) : 0;
$mode = $_POST['mode'] == 1 ? 'points' : 'time';

Library::submitPlatformerLevelScore($levelID, $person, $scores, $attempts, $clicks, $progresses, $coins, $dailyID, $mode);

$levelScores = Library::getPlatformerLevelScores($levelID, $person, $type, $dailyID, $mode);
if(!$levelScores['count']) exit(Library::returnGeometryDashResponse(CommonError::NothingFound));

foreach($levelScores['scores'] AS $key => $user) {
	$userData = [];
	
	$userData['userName'] = Library::makeClanUsername($user["userName"], $user["clanID"]);
	$userData['userID'] = $user['userID'];
	$userData['stars'] = $user[$mode];
	$userData['rank'] = $key + 1;
	$userData['iconID'] = $user['icon'];
	$userData['color1'] = $user['color1'];
	$userData['color2'] = $user['color2'];
	$userData['special'] = $user['special'];
	$userData['accountID'] = $user['extID'];
	$userData['coins'] = $user['scoreCoins'];
	$userData['iconType'] = $user['iconType'];
	$userData['color3'] = $user['color3'];
	$userData['scoreTimestamp'] = Library::makeTime($user["timestamp"]);
	
	$usersData['data'][] = $userData;
}

exit(Library::returnGeometryDashArray($usersData, Keys::User));
?>