<?php
require_once __DIR__."/../lib/mainLib.php";
require_once __DIR__."/../lib/security.php";
require_once __DIR__."/../lib/exploitPatch.php";
require_once __DIR__."/../lib/enums.php";
$sec = new Security();

$person = $sec->loginPlayer();
if(!$person["success"]) exit(CommonError::InvalidRequest);
$accountID = $person['accountID'];

$targetAccountID = Escape::latin_no_spaces($_POST['accountID']);
$targetUserID = Library::getUserID($targetAccountID);
if(!$targetUserID) exit(CommonError::InvalidRequest);

$page = abs(Escape::number($_POST["page"]) ?: 0);
$commentsPage = $page * 10;

$isBlocked = Library::isPersonBlocked($accountID, $targetAccountID);
if($isBlocked) exit(CommonError::InvalidRequest);

$targetUser = Library::getUserByID($targetUserID);
$targetPerson = [
	'accountID' => $targetUser['extID'],
	'userID' => $targetUser['userID'],
	'IP' => $targetUser['IP']
];
$targetUserAppearance = Library::getPersonCommentAppearance($targetPerson);

$accountComments = Library::getAccountComments($person, $targetUserID, $commentsPage);
$echoString = '';
foreach($accountComments['comments'] AS &$accountComment) {
	$extraTextArray = [];
	
	if(!empty($targetUserAppearance['commentsExtraText'])) $extraTextArray[] = Escape::gd($targetUserAppearance['commentsExtraText']);
	if(!empty($accountComment['repliesCount'])) $extraTextArray[] = $accountComment['repliesCount'] > 1 ? $accountComment['repliesCount'].' replies' : $accountComment['repliesCount'].' reply';
	
	$timestamp = Library::makeTime($accountComment['timestamp'], $extraTextArray);
	
	$likes = $accountComment['likes'] - $accountComment['dislikes'];
	$accountComment['comment'] = Escape::url_base64_encode((Escape::translit(trim(Escape::url_base64_decode($accountComment['comment']))) ?: '(Empty post)'));
	
	$echoString .= "2~".$accountComment["comment"]."~3~".$accountComment["userID"]."~4~".$likes."~7~".$accountComment["isSpam"]."~9~".$timestamp."~6~".$accountComment["commentID"]."|";
}
$echoString = rtrim($echoString, "|");
exit($echoString."#".$accountComments['count'].":".$commentsPage.":10");
?>