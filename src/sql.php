<?php
header('Content-Type: text/html; charset=utf-8');

require_once ($_SERVER["DOCUMENT_ROOT"] . "/config.req.php");

require_once (ABSPATH . "/scripts/header.req.php");




$dir    = '/home/www/mangadex.com/data/';
$files = array_diff(scandir($dir), array('..', '.'));

foreach ($files as $file) {
	
	$chapter_id = $db->get_var(" SELECT chapter_id FROM mangadex_chapters WHERE chapter_hash LIKE '$file'  "); 
	if (!$chapter_id) {
		print $file . " - $chapter_id<br />";
		
		//$db->query(" UPDATE mangadex_chapters SET server = 1 WHERE chapter_id = $chapter_id LIMIT 1; "); 
		
		//rename("/home/www/mangadex.com/data/$file", "/home/www/mangadex.com/delete/$file"); 
		
	}
}


/*
$results = $db->get_results(" SELECT * FROM mangadex_import GROUP BY user_id  "); 

foreach ($results as $i => $row) {
	$insert = "";
	$search = '"comic_id":"';
	$string = $row->json;
	
	$found = strpos_recursive($string, $search);

	if($found) {
		foreach($found as $pos) {
			$start = $pos + 12;
			$end = strpos($row->json, '"', $start);
			$diff = $end - $start;
			$substr = substr($row->json, $start, $diff);
			$substr = sanitise_id($substr);
			$insert .= "($row->user_id, $substr),";
			
		}   
	} 
	
	$insert = rtrim($insert,",");
	
	$db->query( "INSERT IGNORE INTO mangadex_follow_user_manga (user_id, manga_id) VALUES $insert;" );
	$db->query( "DELETE FROM mangadex_import WHERE user_id = $row->user_id" );
}
	*/
/*
	$follows_array = json_decode($row->json);
	
	if (isJSON($row->json)) {
		print $row->user_id . " ";
			
		foreach ($follows_array as $manga) {				
			$count = $db->get_var(" SELECT count(*) FROM mangadex_follow_user_manga WHERE user_id = $row->user_id AND manga_id = $manga->comic_id ");
			if (!$count) {
				$db->query(" INSERT INTO mangadex_follow_user_manga (id, user_id, manga_id) VALUES (NULL, $row->user_id, $manga->comic_id); ");
				
				//$follows = $db->get_var(" SELECT count(*) FROM mangadex_follow_user_manga WHERE manga_id = $manga->comic_id "); 
				//$db->query(" UPDATE mangadex_mangas SET manga_follows = $follows WHERE manga_id = $id LIMIT 1; ");
			}
		}
	
		
	}
	else print "[not JSon $row->user_id]";
*/
 

/*
$url = ABSPATH . '/relational-data.json'; // path to your JSON file
$data = file_get_contents($url); // put the contents of the file into a variable
$array = json_decode($data); // decode the JSON feed

foreach ($array as $manga) { 
	$db->query(" UPDATE mangadex_mangas SET manga_image = '$manga->filename' WHERE manga_id = $manga->id LIMIT 1; ");
	
}
*/

?>