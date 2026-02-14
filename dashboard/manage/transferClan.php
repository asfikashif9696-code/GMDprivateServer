<?php
require_once __DIR__."/../incl/dashboardLib.php";
require_once __DIR__."/../".$dbPath."incl/lib/mainLib.php";
require_once __DIR__."/../".$dbPath."incl/lib/security.php";
require_once __DIR__."/../".$dbPath."incl/lib/exploitPatch.php";
require_once __DIR__."/../".$dbPath."incl/lib/enums.php";
$sec = new Security();

$person = Dashboard::loginDashboardUser();
if(!$person['success']) exit(Dashboard::renderToast("xmark", Dashboard::string("errorLoginRequired"), "error", "account/login", "box"));
$accountID = $person['accountID'];

if(isset($_POST['clanID'])) {
	$clanID = Escape::number($_POST['clanID']);
	$clanOwner = Escape::latin_no_spaces($_POST['clanOwner']);
	
	if(empty($clanID) || empty($clanOwner)) exit(Dashboard::renderToast("xmark", Dashboard::string("errorTitle"), "error"));
	if(!is_numeric($clanOwner)) exit(Dashboard::renderToast("xmark", Dashboard::string("errorTransferToAccountOnly"), "error"));
	
	$clan = Library::getClanByID($clanID);
	if(!$clan) exit(Dashboard::renderToast("xmark", Dashboard::string("errorClanNotFound"), "error"));
	
	$user = Library::getUserByAccountID($clanOwner);
	if(!$user) exit(Dashboard::renderToast("xmark", Dashboard::string("errorUserNotFound"), "error"));
	
	if(($clan['clanOwner'] != $accountID && !Library::checkPermission($person, 'dashboardManageClans'))) exit(Dashboard::renderToast("xmark", Dashboard::string("errorCantTransferClan"), "error"));
	if($user['clanID'] != $clanID) exit(Dashboard::renderToast("xmark", Dashboard::string("errorUserNotInClan"), "error"));
	
	$transferClan = Library::transferClan($person, $clanID, $clanOwner);
	if(!$transferClan) exit(Dashboard::renderToast("xmark", Dashboard::string("errorCantTransferClan"), "error"));
	
	exit(Dashboard::renderToast("check", Dashboard::string("successTransferedClan"), "success", 'clan/'.$clan['clanName'], "list"));
}

exit(Dashboard::renderToast("xmark", Dashboard::string("errorTitle"), "error"));
?>