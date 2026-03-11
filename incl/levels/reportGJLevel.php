<?php
require_once __DIR__."/../lib/mainLib.php";
require_once __DIR__."/../lib/exploitPatch.php";
require_once __DIR__."/../lib/ip.php";
require_once __DIR__."/../lib/enums.php";

$person = [
	'accountID' => 0,
	'IP' => IP::getIP()
];

$levelID = Escape::number($_POST['levelID']);

$reportLevel = Library::reportItem($person, ReportType::InappropriateContent, ReportItem::Level, $levelID, "");
if(!$reportLevel) exit(Library::returnGeometryDashResponse(CommonError::InvalidRequest));

exit(Library::returnGeometryDashResponse(CommonError::Success));
?>