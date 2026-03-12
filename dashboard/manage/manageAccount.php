<?php
require_once __DIR__."/../incl/dashboardLib.php";
require_once __DIR__."/../".$dbPath."incl/lib/exploitPatch.php";
require_once __DIR__."/../".$dbPath."incl/lib/enums.php";
require_once __DIR__."/../".$dbPath."incl/lib/mainLib.php";
require_once __DIR__."/../".$dbPath."incl/lib/security.php";
$sec = new Security();

$person = Dashboard::loginDashboardUser();
if(!$person['success']) exit(Dashboard::renderToast("xmark", Dashboard::string("errorLoginRequired"), "error", "account/login", "box"));

if(isset($_POST['accountID'])) {
	$accountID = Escape::number($_POST['accountID']);
	$isPersonThemselves = $accountID == $person['accountID'];
	
	if(!$isPersonThemselves && !Library::checkPermission($person, "dashboardManageAccounts")) exit(Dashboard::renderToast("xmark", Dashboard::string("errorTitle"), "error"));
	
	$account = Library::getAccountByID($accountID);
	if(!$account) exit(Dashboard::renderToast("xmark", Dashboard::string("errorAccountNotFound"), "error"));
	
	if($isPersonThemselves) {
		$lang = strtoupper(Escape::latin($_POST['lang'], 2)) ?: 'EN';
		$enableLoweredMotion = isset($_POST['loweredMotion']) ? 1 : 0;
		
		$_COOKIE['lang'] = $lang;
		setcookie("lang", $lang, 2147483647, "/");
		$_COOKIE['enableLoweredMotion'] = $enableLoweredMotion;
		setcookie("enableLoweredMotion", $enableLoweredMotion, 2147483647, "/");
	}
	
	$newMessagesState = Security::limitValue(0, Escape::number($_POST["messagesPrivacy"]), 2);
	$newFriendRequestsState = $_POST["friendRequests"] ? 1 : 0;
	$newCommentsState = Security::limitValue(0, Escape::number($_POST["commentHistory"]), 2);
	
	$newSocialsYouTube = Escape::text($_POST["youtube"], 30);
	$newSocialsTwitter = Escape::text($_POST["twitter"], 20);
	$newSocialsTwitch = Escape::text($_POST["twitch"], 30);
	$newSocialsInstagram = Escape::text($_POST["instagram"], 30);
	$newSocialsTikTok = Escape::text($_POST["tiktok"], 30);
	$newSocialsDiscord = Escape::text($_POST["discord"], 30);
	$newSocialsCustom = Escape::text($_POST["custom"], 40);
	
	$newTimezone = Escape::text($_POST["timezone"]);
	
	$timezones = Dashboard::getTimezones();
	if(!isset($timezones[$newTimezone])) $newTimezone = $account['timezone'];
	
	if($account['mS'] != $newMessagesState ||
		$account['frS'] != $newFriendRequestsState ||
		$account['сS'] != $newCommentsState ||
		$account['youtubeurl'] != $newSocialsYouTube ||
		$account['twitch'] != $newSocialsTwitter ||
		$account['twitter'] != $newSocialsTwitch ||
		$account['instagram'] != $newSocialsInstagram ||
		$account['tiktok'] != $newSocialsTikTok ||
		$account['discord'] != $newSocialsDiscord ||
		$account['custom'] != $newSocialsCustom
	) Library::updateAccountSettings($person, $accountID, $newMessagesState, $newFriendRequestsState, $newCommentsState, $newSocialsYouTube, $newSocialsTwitter, $newSocialsTwitch, $newSocialsInstagram, $newSocialsTikTok, $newSocialsDiscord, $newSocialsCustom);
	
	if($account['timezone'] != $newTimezone) Library::updateAccountTimezone($accountID, $newTimezone);
	
	exit(Dashboard::renderToast("check", Dashboard::string("successAppliedSettings"), "success", '@', "settings"));
}

exit(Dashboard::renderToast("xmark", Dashboard::string("errorTitle"), "error"));
?>