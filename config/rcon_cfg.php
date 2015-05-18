<?
	/** BASIC CONFIG
	address: IP of your server,
	port : port of your server,
	password: Battleye RCON password ste in beserver.cfg,
	start_line: text that appears at beginning of output lines, for better logview, change only if needed.
	**/
	define("address","192.168.1.129");
	define("port","2302");
	define("password","mypass");
	define("start_line", "RCON : [" . date("y.m.d @ H:i:s") . "] ");

	/**
	To add lines to presets:
	"my_preset_unique_id" => "text to send",
	DO NOT FORGET THE "," except for the last entry.
	**/
	//Preset list//
	$presets = [
    "1h" => "(RESTART PENDING)Server Restart in 1 Hour.",
    "1m" => "(RESTART PENDING LOG OFF NOW!)Server Restart in 1 Minute.",
    "2m" => "(RESTART PENDING LOG OFF NOW!)Server Restart in 2 Minutes.",
    "5m" => "(RESTART PENDING LOG OFF NOW!), Or Risk Map/Inventory Problems, RESTART IN 5 MINUTES.",
    "10m" => "(RESTART PENDING)Server Restart in 10 Minutes.",
    "15m" => "(RESTART PENDING)Server Restart in 15 Minutes.",
    "30m" => "(RESTART PENDING)Server Restart in 30 Minutes."
	];
	//Preset list//
	
	/**
	To add lines to random:
	"text to send",
	DO NOT FORGET THE "," except for the last entry.
	**/
	//Randoms list//
	$randoms = [
	"Random Text 01",
	"Random Text 02",
	"Random Text 03",
	"Random Text 04",
	"Random Text 05",
	"Random Text 06",
	"Random Text 07"
	];
	//Randoms list//
?>