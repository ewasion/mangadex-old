<?php
header('Content-Type: application/rss+xml');

require_once ($_SERVER["DOCUMENT_ROOT"] . "/config.req.php");

require_once (ABSPATH . "/scripts/header.req.php");

session_start();

$categories = new Categories($db);
//$torrents = new Torrents($db, "id", "DESC", $_SESSION["limit"], $_SESSION["offset"], $filter_array);

$filter_array = array();

//filename
if (!empty($_GET["q"])) //not ""
	$filter_array["filename"] = $_GET["q"];

//categories
if (!empty($_GET["cat"])) //not 0
	$filter_array["category"] = $_GET["cat"];

//uploader_id
if (isset($_GET["user"])) 
	$filter_array["uploader"] = $_GET["user"];

//group_id
if (isset($_GET["group"])) 
	$filter_array["group_id"] = $_GET["group"];

//filtering
if (isset($_GET["b"])) 
	$filter_array["batch"] = 1;

//hentai
if (isset($_GET["h"])) {
	if ($_GET["h"] == 1)
		$filter_array["hentai"] = 1;
	elseif ($_GET["h"] == 0)
		$filter_array["hentai"] = 0;
}

//reencode
if (isset($_GET["r"])) 
	$filter_array["reencode"] = 0;

//only trusted/auth
if (isset($_GET["a"])) {
	$filter_array["authorised"] = 1;
	$filter_array["trusted"] = 1;
}

//lang
if (!empty($_GET["lang_id"]))	//not 0
	$filter_array["lang_id"] = $_GET["lang_id"];

//search stuff
if (!empty($filter_array)) {
	$search_string = "WHERE private = 0 AND ";
	
	foreach ($filter_array as $key => $value) {
		$value = mysql_escape_mimic($value);
		$key = mysql_escape_mimic($key);
		switch ($key) {
			case "filename":
				$terms = explode(" ", $value);
				foreach ($terms as $term) 
					$search_string .= "$key LIKE '%$term%' AND ";
				break;
			case "category":
				$search_string .= "$key IN ($value) AND ";
				break;			
			case "lang_id":
				$search_string .= "$key IN ($value) AND ";
				break;	
			case "authorised":
				$search_string .= "($key = $value OR ";
				break;
			case "trusted":
				$search_string .= "$key = $value) AND ";
				break;				
			default:
				$search_string .= "$key = $value AND ";
				break;

				
		}
	}
	
	$search_string = rtrim($search_string, " AND ");
}
else $search_string = "WHERE private = 0";

$torrents = $db->get_results(" SELECT * FROM anidex_files $search_string ORDER BY upload_timestamp DESC LIMIT 100 "); 

visit_log_cumulative($db, $ip, $table = "rss");
visit_log($db, $_SERVER, $_SERVER["HTTP_CF_CONNECTING_IP"], 0, 0, "rss");

?>
<?xml version="1.0" encoding="utf-8" ?>
<rss xmlns:atom="http://www.w3.org/2005/Atom" version="2.0">
<channel>
<title>AniDex Tracker</title>
<link>https://<?= $_SERVER['SERVER_NAME'] ?></link>
<description>Torrent Listing</description>
<atom:link href="https://<?= $_SERVER['SERVER_NAME'] ?>/rss/" rel="self" type="application/rss+xml" />
<language>en</language>
<ttl>15</ttl>

<?php foreach ($torrents as $torrent) { 
$cat = $torrent->category - 1; 

?>
<item>
<category><?= $categories->{$cat}->cat_name ?></category>
<title><?= htmlspecialchars($torrent->filename); ?></title>
<link>https://<?= $_SERVER['SERVER_NAME'] ?>/dl/<?= $torrent->id ?></link>
<description><![CDATA[Category: <?= $categories->{$cat}->cat_name ?> | Labels:<?= display_labels_rss($torrent->batch, $torrent->hentai, $torrent->reencode, 1) ?> | Size: <?= format_filesize($torrent->size) ?> | Download: <a href="https://<?= $_SERVER['SERVER_NAME'] ?>/dl/<?= $torrent->id ?>">Torrent</a> <a href="magnet:?xt=urn:btih:<?= convBase($torrent->info_hash, "0123456789abcdef", "ABCDEFGHIJKLMNOPQRSTUVWXYZ234567") ?>&tr=<?= DEFAULT_ANNOUNCE_URL ?>">Magnet</a><hr /><br />]]></description>
<pubDate><?= gmdate("D, d M Y H:i:s O", $torrent->upload_timestamp) ?></pubDate>
<guid><![CDATA[http://<?= $_SERVER['SERVER_NAME'] ?>/dl/<?= $torrent->id ?>]]></guid>
</item>
<?php } ?>

</channel>
</rss>