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

if(isset($_POST['reportType'])) {
	$reportType = Security::limitValue(0, Escape::number($_POST['reportType']), 6);
	$extraInfo = Escape::text($_POST['extraInfo'], 255);
	
	switch(true) {
		case isset($_POST['levelID']):
			$reportItem = ReportItem::Level;
			$itemID = Escape::number($_POST['levelID']);
			break;
		case isset($_POST['accountID']):
			$reportItem = ReportItem::Account;
			$itemID = Escape::number($_POST['accountID']);
			break;
		case isset($_POST['commentID']):
			$reportItem = ReportItem::Comment;
			$itemID = Escape::number($_POST['commentID']);
			break;
		case isset($_POST['postID']):
			$reportItem = ReportItem::AccountComment;
			$itemID = Escape::number($_POST['postID']);
			break;
		case isset($_POST['replyID']):
			$reportItem = ReportItem::AccountCommentReply;
			$itemID = Escape::number($_POST['replyID']);
			break;
		case isset($_POST['listID']):
			$reportItem = ReportItem::List;
			$itemID = Escape::number($_POST['listID']);
			break;
		case isset($_POST['songID']):
			$reportItem = ReportItem::Song;
			$itemID = Escape::number($_POST['songID']);
			break;
		case isset($_POST['sfxID']):
			$reportItem = ReportItem::SFX;
			$itemID = Escape::number($_POST['sfxID']);
			break;
		case isset($_POST['clanID']):
			$reportItem = ReportItem::Clan;
			$itemID = Escape::number($_POST['clanID']);
			break;
		default:
			exit(Dashboard::renderToast("xmark", Dashboard::string("errorTitle"), "error"));
	}
	
	$reportItem = Library::reportItem($person, $reportType, $reportItem, $itemID, $extraInfo);
	if(!$reportItem['success']) {
		switch($reportItem['error']) {
			case ReportError::AlreadyReported:
				exit(Dashboard::renderToast("xmark", Dashboard::string("errorAlreadyReported"), "error"));
			case ReportError::NothingFound:
				exit(Dashboard::renderToast("xmark", Dashboard::string("errorContentNotFound"), "error"));
			case ReportError::TooFast:
				exit(Dashboard::renderToast("xmark", Dashboard::string("errorReportTooFast"), "error"));
			default:
				exit(Dashboard::renderToast("xmark", Dashboard::string("errorTitle"), "error"));
		}
	}
	
	exit(Dashboard::renderToast("check", Dashboard::string("successSubmittedReport"), "success"));
}

exit(Dashboard::renderToast("xmark", Dashboard::string("errorReportTypeNotSpecified"), "error"));
?>