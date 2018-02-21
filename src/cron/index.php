<?php
require_once ("/home/www/anidex.info/config.req.php"); //must be like this

require_once (ABSPATH . "/scripts/header.req.php");

switch ($_GET["action"]) {
	case "update_all_torrents":
	
		$scraper = new httptscraper(SCRAPE_TIMEOUT, SCRAPE_MAXREAD);

		$torrents = $db->get_results(" SELECT id, info_hash, completed FROM anidex_files "); 

		$i = 0;
		foreach ($torrents as $torrent) {
			
			$scrape_array = scrape_torrent($scraper, array("$torrent->info_hash"));
			
			if ($scrape_array["c"] >= $torrent->completed) 
				update_stats($db, $scrape_array["s"], $scrape_array["l"], $scrape_array["c"], $torrent->id);
			else 
				update_stats($db, $scrape_array["s"], $scrape_array["l"], $torrent->completed, $torrent->id);
			
			$i++;
		}

		update_cron_logs($db, "Update All Torrents", "Updated $i torrents.");
		
		print "Updated $i torrents.";
		
		break;
		
	case "update_recent_torrents":
		
		$scraper = new httptscraper(SCRAPE_TIMEOUT, SCRAPE_MAXREAD);

		$torrents = $db->get_results(" SELECT id, info_hash, completed FROM anidex_files ORDER BY upload_timestamp DESC LIMIT 1000"); 

		$i = 0;
		foreach ($torrents as $torrent) {
			
			$scrape_array = scrape_torrent($scraper, array("$torrent->info_hash"));
			
			if ($scrape_array["c"] >= $torrent->completed) 
				update_stats($db, $scrape_array["s"], $scrape_array["l"], $scrape_array["c"], $torrent->id);
			else 
				update_stats($db, $scrape_array["s"], $scrape_array["l"], $torrent->completed, $torrent->id);
			
			$i++;
		}

		update_cron_logs($db, "Update Recent Torrents", "Updated $i torrents.");
		
		break;
	
	case "update_xdcc":

		$xdcc = new XDCC($db);
		
		foreach ($xdcc as $key => $xdcc_bot) {
		//foreach ($xdcc as $bot_id) {
			//$xdcc_bot = new XDCC_Bot($db, $bot_id);

			update_xdcc_bot($xdcc_bot);
		}
		
		//merge .js into one file
		$text = "var xdcc = [";
		foreach ($xdcc as $key => $xdcc_bot) {

			$text .= file_get_contents(ABSPATH . "/xdcc/$xdcc_bot->xdcc_id.js");
			
		}	
		$text .= "];";
		
		file_put_contents(ABSPATH . "/xdcc/xdcc.js", $text);	
		
		
		break;
		
	default:
		die();
		break;
}
?>