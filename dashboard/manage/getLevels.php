<?php
require_once __DIR__."/../incl/dashboardLib.php";
require __DIR__."/../".$dbPath."config/dashboard.php";
require_once __DIR__."/../".$dbPath."incl/lib/mainLib.php";
require_once __DIR__."/../".$dbPath."incl/lib/exploitPatch.php";

$person = Dashboard::loginDashboardUser();
$levels = [];

if(isset($_GET['search'])) {
	$search = trim(Escape::text($_GET['search']));
	if(empty($search)) exit(json_encode([]));
	
	$filters = ['levelName LIKE "%'.$search.'%"', 'unlisted = 0'];
	
	if(is_numeric($search) && $person['success']) {
		$accountID = $person['accountID'];
		
		$friendsString = Library::getFriendsQueryString($accountID);
		
		$filters = ["levels.levelID = ".$search." AND (
				levels.unlisted != 1 OR
				(levels.unlisted = 1 AND (levels.extID IN (".$friendsString.")))
			)"];
	}
	
	$levelsArray = Library::getLevels($filters, '', '', '', 0, 5);
	if(!$levelsArray['levels']) exit(json_encode([]));
	
	foreach($levelsArray['levels'] AS &$level) {
		$userMetadata = Dashboard::getUserMetadata($level);
		
		$levels[] = [
			'ID' => $level['levelID'],
			'name' => sprintf(Dashboard::string("levelTitlePlain"), htmlspecialchars($level['levelName']), htmlspecialchars($level['userName'])),
			'icon' => '<i class="fa-solid fa-gamepad"></i>',
			'elementAfter' => '<img loading="lazy" src="'.$userMetadata['mainIcon'].'"></img>'
		];
	}
	
	exit(json_encode($levels));
}

exit(json_encode([]));
?>