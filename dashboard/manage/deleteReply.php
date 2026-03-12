<?php
require_once __DIR__."/../incl/dashboardLib.php";
require_once __DIR__."/../".$dbPath."incl/lib/mainLib.php";
require_once __DIR__."/../".$dbPath."incl/lib/security.php";
require_once __DIR__."/../".$dbPath."incl/lib/exploitPatch.php";
require_once __DIR__."/../".$dbPath."incl/lib/enums.php";
$sec = new Security();

$person = Dashboard::loginDashboardUser();
if(!$person['success']) exit(Dashboard::renderToast("xmark", Dashboard::string("errorLoginRequired"), "error", "account/login", "box"));

if(isset($_POST['replyID'])) {
	$replyID = Escape::number($_POST['replyID']);
	if(empty($replyID)) exit(Dashboard::renderToast("xmark", Dashboard::string("errorTitle"), "error"));
	
	$deleteReply = Library::deleteAccountCommentReply($person, $replyID);
	if(!$deleteReply) exit(Dashboard::renderToast("xmark", Dashboard::string("errorCantDeleteReply"), "error"));
	
	exit(Dashboard::renderToast("check", Dashboard::string("successDeletedReply"), "success", '@', "settings"));
}

exit(Dashboard::renderToast("xmark", Dashboard::string("errorTitle"), "error"));
?>