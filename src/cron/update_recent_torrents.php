<?php
require_once ("/home/www/anidex.info/config.req.php"); //must be like this

require_once (ABSPATH . "/scripts/header.req.php");

$scraper = new httptscraper(SCRAPE_TIMEOUT, SCRAPE_MAXREAD);

$torrents = $db->get_results(" SELECT id, info_hash, completed FROM anidex_files ORDER BY upload_timestamp DESC LIMIT 1000"); 

$i = 0;
foreach ($torrents as $torrent) {
	
	$scrape_array = scrape_torrent($scraper, array("$torrent->info_hash"));
	
	if ($scrape_array["c"] >= $torrent->completed) 
		update_stats($db, $scrape_array["s"], $scrape_array["l"], $scrape_array["c"], $torrent->id);
	
	$i++;
}

update_cron_logs($db, "Update Recent Torrents", "Updated $i torrents.");


?>