<?php
require '../config/dashboard.php';
require '../config/misc.php';
require_once '../incl/lib/exploitPatch.php';

if(!$enableGDPSSwitcherMOTD) {
	http_response_code(404);
	exit();
}

$gdpsSwitcherConfiguration = [
	'version' => 1,
	
	'motd' => sprintf($gdpsSwitcherMOTD, $gdps, Escape::latin_no_spaces($accentColor)),
	'icon' => $gdpsSwitcherIcon
];

exit(json_encode($gdpsSwitcherConfiguration));
?>