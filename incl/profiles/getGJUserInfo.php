<?php
require_once __DIR__."/../lib/mainLib.php";
require_once __DIR__."/../lib/security.php";
require_once __DIR__."/../lib/exploitPatch.php";
require_once __DIR__."/../lib/enums.php";
$sec = new Security();

$person = $sec->loginPlayer();
if(!$person["success"]) exit(Library::returnGeometryDashResponse(CommonError::InvalidRequest));
$accountID = $person['accountID'];

$targetAccountID = Escape::latin_no_spaces($_POST['targetAccountID']);
$targetUserID = Library::getUserID($targetAccountID);
if(!$targetUserID) exit(Library::returnGeometryDashResponse(CommonError::InvalidRequest));

$isBlocked = Library::isPersonBlocked($accountID, $targetAccountID);
if($isBlocked) exit(Library::returnGeometryDashResponse(CommonError::InvalidRequest));

$user = Library::getUserByID($targetUserID);
$account = Library::getAccountByID($targetAccountID);

$targetPerson = [
	'accountID' => $targetAccountID,
	'userID' => $targetUserID,
	'IP' => $user['IP']
];
$userData = [];

$userAppearance = Library::getPersonCommentAppearance($targetPerson);
$checkBan = Library::getPersonBan($targetPerson, Ban::Leaderboards);
$rank = !$checkBan ? Library::getUserRank("stars", $user['stars'], $user['stars'], $user['userName']) : 0;

$userData['userName'] = Library::makeClanUsername($user['extID']);
$userData['userID'] = $user['userID'];
$userData['stars'] = $user['stars'];
$userData['demons'] = $user['demons'];
$userData['creatorPoints'] = round($user["creatorPoints"], PHP_ROUND_HALF_DOWN);
$userData['color1'] = $user['color1'];
$userData['color2'] = $user['color2'];
$userData['coins'] = $user['coins'];
$userData['accountID'] = $user['extID'];
$userData['userCoins'] = $user['userCoins'];
$userData['messagingState'] = $account['mS'];
$userData['friendRequetsState'] = $account['frS'];
$userData['youtube'] = $account['youtubeurl'];
$userData['accIcon'] = $user['accIcon'];
$userData['accShip'] = $user['accShip'];
$userData['accBall'] = $user['accBall'];
$userData['accBird'] = $user['accBird'];
$userData['accDart'] = $user['accDart'];
$userData['accRobot'] = $user['accRobot'];
$userData['accStreak'] = $user['accStreak'];
$userData['accGlow'] = $user['accGlow'];
$userData['isRegistered'] = $user['isRegistered'];
$userData['globalRank'] = $rank;
$userData['accSpider'] = $user['accSpider'];
$userData['twitter'] = $account['twitter'];
$userData['twitch'] = $account['twitch'];
$userData['diamonds'] = $user['diamonds'];
$userData['accExplosion'] = $user['accExplosion'];
$userData['modBadgeLevel'] = $userAppearance['modBadgeLevel'];
$userData['commentHistoryState'] = $account['cS'];
$userData['color3'] = $user['color3'];
$userData['moons'] = $user['moons'];
$userData['accSwing'] = $user['accSwing'];
$userData['accJetpack'] = $user['accJetpack'];
$userData['demonsInfo'] = $user['dinfo'];
$userData['classicLevelsInfo'] = $user['sinfo'];
$userData['platformerLevelsInfo'] = $user['pinfo'];
$userData['discord'] = $account['discord'];
$userData['instagram'] = $account['instagram'];
$userData['tiktok'] = $account['tiktok'];
$userData['custom'] = $account['custom'];

if($accountID == $targetAccountID) {
	$accountFriendshipsStatsCount = Library::getAccountFriendshipsStatsCount($person);
	
	$userData['newMessagesCount'] = $accountFriendshipsStatsCount['newMessagesCount'];
	$userData['newFriendRequestsCount'] = $accountFriendshipsStatsCount['newFriendRequestsCount'];
	$userData['newFriendsCount'] = $accountFriendshipsStatsCount['newFriendsCount'];
} else {
	$accountFriendshipInfo = Library::getAccountFriendshipInfo($person, $targetAccountID);
	
	$userData['friendshipState'] = $accountFriendshipInfo['friendshipState'];
	if(!empty($accountFriendshipInfo['friendRequest'])) {
		$userData['friendRequestID'] = $accountFriendshipInfo['friendRequest']['ID'];
		$userData['friendRequestComment'] = $accountFriendshipInfo['friendRequest']['comment'];
		$userData['friendRequestTimestamp'] = $accountFriendshipInfo['friendRequest']['timestamp'];
	}
}

exit(Library::returnGeometryDashData($userData, Keys::User));
?>