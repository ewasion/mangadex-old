<?php
require_once ("/home/www/mangadex.com/config.req.php"); //must be like this

require_once (ABSPATH . "/scripts/header.req.php");

$timestamp = time();
$url = "https://api.cloudflare.com/client/v4/user/firewall/access_rules/rules";

$results = $db->get_results(" SELECT * FROM mangadex_logs WHERE log_visit > 400");
foreach ($results as $visitor) {

	$data = array(
		"mode" => "block",
		"configuration" => array("target" => "ip", "value" => "$visitor->log_ip"),
		"notes" => "$visitor->log_visit");
		
	print httpPost($url, $data);
	
}

$db->query(" TRUNCATE TABLE mangadex_logs ");

?>