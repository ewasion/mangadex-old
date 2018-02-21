<?php
/* mysql_escape_mimic($inp)
 * rand_string($length)
 * get_time_ago($string)
 * number_format_mod($number)
 * format_filesize($bytes)
 * reArrayFiles(&$file_post)
 * scrape_torrent($scraper, $external, $announce_url, $info_hash_array)
 * decode_torrent($torrent, $torrent_hash)
 * calc_total_transfer($completed, $size)
 * update_stats($db, $s, $l, $c, $id)
 */
 
/*************************************
 * General functions
 *************************************/
function remove_padding($str) {
	if (ltrim($str, '0') != '') {
		return ltrim($str, '0');
	} else {
		return $str;
	}
}

function strpos_recursive($haystack, $needle, $offset = 0, &$results = array()) {               
    $offset = strpos($haystack, $needle, $offset);
    if($offset === false) {
        return $results;           
    } else {
        $results[] = $offset;
        return strpos_recursive($haystack, $needle, ($offset + 1), $results);
    }
}

function validate_image($file) {
	$arr = explode(".", $file["name"]);
	$ext = strtolower(end($arr));
	$validate_extention = in_array($ext, ALLOWED_IMG_EXT);
	$validate_file_size = ($file["size"] <= MAX_IMAGE_FILESIZE); //check file size
	$validate_mime = in_array(mime_content_type($file["tmp_name"]), ALLOWED_MIME_TYPES); 
	$get_image_size = getimagesize($file["tmp_name"]);
	
	if ($_FILES["file"]["error"]) 
		return display_alert("danger", "Failed", "Error Code ({$file['error']})."); 
	elseif (!$validate_file_size) 
		return display_alert("danger", "Failed", "File size exceeds 1 MB.");
	elseif (!$validate_extention) 
		return display_alert("danger", "Failed", "A .$ext file, not an image."); 
	elseif (!$validate_mime) 
		return display_alert("danger", "Failed", "Image failed validation.");
	elseif (!$get_image_size) 
		return display_alert("danger", "Failed", "Image cannot be processed.");
	else 
		return "";
}	


function validate_md5($hash) {
	if (preg_match("/^[a-f0-9]{32}$/i", $hash))
		return $hash;
	else 
		die("Error: Possible SQL injection.");
}
 
function get_ext($filename, $type) {
	$value = explode(".", $filename);
	if ($type) 
		return strtolower(end($value));
	else 
		return current($value);
}
 
function isJson($string) {
	json_decode($string);
	return (json_last_error() == JSON_ERROR_NONE);
}

function mysql_escape_mimic($inp) {
    if(is_array($inp))
        return array_map(__METHOD__, $inp);

    if(!empty($inp) && is_string($inp)) {
        return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $inp);
    }
    return $inp;
} 

function sanitise_id($id) {
	if (preg_match("/^-?\d+$/", $id))
		return $id;
	else 
		die("Error: Possible SQL injection.");
}	

function send_email($to, $subject, $body) {
	require_once (ABSPATH . "/scripts/phpmailer/PHPMailerAutoload.php");
	
	$mail = new PHPMailer;
	$mail->SMTPDebug = false; //Enable SMTP debugging.                          
	$mail->isSMTP(); //Set PHPMailer to use SMTP.
	$mail->Host = SMTP_HOST; //Set SMTP host name     
	$mail->SMTPAuth = true; //Set this to true if SMTP host requires authentication to send email                  
	$mail->Username = SMTP_USER; //Provide username and password             
	$mail->Password = SMTP_PASSWORD;                           
	$mail->SMTPSecure = "tls"; //If SMTP requires TLS encryption then set it
	$mail->Port = SMTP_PORT; //Set TCP port to connect to                     
	$mail->From = SMTP_USER;
	$mail->FromName = TITLE; //From: sdbx.moe
	$mail->addBCC(SMTP_BCC); //bcc: holo@doki.co
	$mail->addReplyTo(SMTP_USER); //reply-to: anidex.moe@gmail.com

	$mail->addAddress($to);
	$mail->Subject = $subject;
	$mail->Body = $body;

	$mail->send();
}

function rand_string($length) {
	$chars = "abcdefghkmnpqrstuvwxyzABCDEFGHKMNPQRSTUVWXYZ23456789";
	return substr(str_shuffle($chars), 0, $length);
}

function get_time_ago($ptime) {
    $etime = abs(time() - $ptime);
	
	if (!$ptime)
		return "Never";
    elseif ($etime < 1) 
        return "Now";

	$ago = ($ptime < time()) ? " ago" : "";
	$in = ($ptime > time()) ? "in " : "";
		
    $a = array( 365 * 24 * 60 * 60  =>  'year',
                 30 * 24 * 60 * 60  =>  'month',
                      24 * 60 * 60  =>  'day',
                           60 * 60  =>  'hour',
                                60  =>  'minute',
                                 1  =>  'second'
                );
    $a_plural = array( 'year'   => 'years',
                       'month'  => 'months',
                       'day'    => 'days',
                       'hour'   => 'hours',
                       'minute' => 'minutes',
                       'second' => 'seconds'
                );

    foreach ($a as $secs => $str)
    {
        $d = $etime / $secs;
        if ($d >= 1)
        {
            $r = round($d);
            return $in . $r . ' ' . ($r > 1 ? $a_plural[$str] : $str) . $ago;
        }
    }
}

function number_format_mod($number) {
	if ($number >= 100)
		return number_format($number, 0);
	else 
		return number_format($number, 1);
}

function format_filesize($bytes) {
	if ($bytes >= 1099511627776000)
		return number_format_mod($bytes / 1024 / 1024 / 1024 / 1024 / 1024) . " PB";
	elseif ($bytes >= 1073741824000)
		return number_format_mod($bytes / 1024 / 1024 / 1024 / 1024) . " TB";
	elseif ($bytes >= 1048576000)
		return number_format_mod($bytes / 1024 / 1024 / 1024) . " GB";
	elseif ($bytes >= 1024000)
		return number_format_mod($bytes / 1024 / 1024) . " MB";
	elseif ($bytes >= 1000)
		return number_format_mod($bytes / 1024) . " KB";
	elseif ($bytes >= 1)
		return $bytes . " B";
	else
		return "0 B";
}

function reArrayFiles(&$file_post) {
    $file_ary = array();
    $file_count = count($file_post['name']);
    $file_keys = array_keys($file_post);

    for ($i = 0; $i < $file_count; $i++) {
        foreach ($file_keys as $key) {
            $file_ary[$i][$key] = $file_post[$key][$i];
        }
    }
	
    return $file_ary;
}

function strpos_arr($haystack, $needle) {
    if (!is_array($needle)) 
		$needle = array($needle);
    foreach($needle as $what) {
        if (($pos = strpos($haystack, $what)) !== false) 
			return $pos;
    }
	
    return false;
}

function get_data($url) {
	$ch = curl_init();
	$timeout = 5;
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}

function download_file($url, $filepath) {
	set_time_limit(0);

	$file = fopen("$filepath", "w+");

	$curl = curl_init($url);

	// Update as of PHP 5.4 array() can be written []
	curl_setopt_array($curl, [
		CURLOPT_URL            => $url,
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_FILE           => $file,
		CURLOPT_TIMEOUT        => 50,
		CURLOPT_USERAGENT      => 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)'
	]);
	
	curl_exec($curl);
	curl_close($curl);
}

function httpPost($url, $data) {
    $curl = curl_init($url);
	$headers = array(
		'Content-type: application/json',
		'X-Auth-Key: xxx',
		'X-Auth-Email: xxx@gmail.com',
	);
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $r = curl_exec($curl);
    curl_close($curl);
	return $r;
}

function rrmdir($dir) { //recursive delete folder
	if (is_dir($dir)) {
		$objects = scandir($dir); 
		foreach ($objects as $object) { 
			if ($object != "." && $object != "..") { 
				if (is_dir($dir."/".$object))
					rrmdir($dir."/".$object);
				else
					unlink($dir."/".$object); 
			} 
		}
		rmdir($dir); 
	} 
}



/*************************************
 * MangaDex functions
 *************************************/
 
function generate_thumbnail($file, $large) {
	
	if (file_exists ($file)) {
		$thumbFile = preg_replace('/\\.[^.\\s]{3,4}$/', '', $file);

		if ($large) {
			$thumbFile .= '.large.jpg';
		}
		else {
			$thumbFile .= '.thumb.jpg';
		}

		// Setting the resize parameters
		list($width, $height) = getimagesize($file); 

		$modwidth = 100; 
		if ($large) {
		  $modwidth = 300;
		}
		$modheight = $modwidth / $width * $height; 
		 
		// Creating the Canvas 
		$tn= imagecreatetruecolor($modwidth, $modheight); 
		$value = explode(".", $file);
		$ext = strtolower(end($value));

		switch ($ext) {
			case "jpg":
			case "jpeg":
				$image = ImageCreateFromJPEG($file); 
			break;
			
			case "png":
				$image = ImageCreateFromPNG($file); 
			break;
			
			case "gif":
				$image = ImageCreateFromGIF($file); 
			break;
			
			default:
				exit;
			break;
			
		}
		
		if ($image) {
			// Resizing our image to fit the canvas 
			imagecopyresampled($tn, $image, 0, 0, 0, 0, $modwidth, $modheight, $width, $height); 
			 
			// Save to file
			imagejpeg($tn, $thumbFile, 95);

			//Free memory
			imagedestroy($tn);		
		}
		
	}
}
 
 /*************************************
 * Update database
 *************************************/
 
function update_cron_logs($db, $type, $result) {
	$timestamp = time();
	$db->query(" INSERT INTO mangadex_logs_cron (id, timestamp, type, result) 
		VALUES (NULL, $timestamp, '$type', '$result'); ");
}

function visit_log_cumulative($db, $ip, $table = "visit") {
	$timestamp = time();

	$db->query(" INSERT INTO mangadex_logs (log_ip, log_visit, log_dl, log_rss, log_timestamp) VALUES ('$ip', 0, 0, 0, $timestamp)
		ON DUPLICATE KEY UPDATE log_$table = log_$table + 1; ");	
}

function visit_log($db, $server, $ip, $user_id, $hentai_toggle = 0, $table = "visits") {
	$timestamp = time();
	$server['QUERY_STRING'] = htmlentities(mysql_escape_mimic($server['QUERY_STRING']), ENT_QUOTES);
	$server['HTTP_REFERER'] = (isset($server['HTTP_REFERER'])) ? htmlentities(mysql_escape_mimic($server['HTTP_REFERER']), ENT_QUOTES) : "";
	$server['HTTP_USER_AGENT'] = (isset($server['HTTP_USER_AGENT'])) ? htmlentities(mysql_escape_mimic($server['HTTP_USER_AGENT']), ENT_QUOTES) : "";
	
	$db->query(" INSERT INTO mangadex_logs_$table (visit_id, visit_ip, visit_user_id, visit_user_agent, visit_referrer, visit_timestamp, visit_page, visit_h_toggle) 
		VALUES (NULL, '$ip', $user_id, '', '{$server['HTTP_REFERER']}', $timestamp, '{$server['QUERY_STRING']}', $hentai_toggle); ");
		
	//$db->query(" UPDATE mangadex_logs_$table SET visit_user_agent = '' WHERE visit_timestamp < ($timestamp - 604800); ");
}

function update_views($db, $type, $id, $ip, $user_id) {
	$timestamp = time();
	switch ($type) {
		case "manga":
			$type_id = 1;
			break;
		case "chapter":
			$type_id = 2;
			break;
		case "group":
			$type_id = 3;
			break;
		case "user":
			$type_id = 4;
			break;
		default:
			exit();
	}
	
	$last_view = $db->get_var(" SELECT timestamp FROM mangadex_views_cumulative WHERE ip_type_id LIKE '$ip" . "_$type_id" . "_$id' LIMIT 1; ");
	
	if ($timestamp - $last_view > 3600) {
		$db->query(" INSERT INTO mangadex_views_cumulative (ip_type_id, user_id, timestamp, count) VALUES ('$ip" . "_$type_id" . "_$id', $user_id, $timestamp, 0)
		ON DUPLICATE KEY UPDATE count = count + 1, timestamp = $timestamp; ");	
		
		$db->query(" UPDATE mangadex_{$type}s SET {$type}_views = {$type}_views + 1 WHERE {$type}_id = $id LIMIT 1; ");
	}
	
	$db->query(" DELETE FROM mangadex_views_cumulative WHERE timestamp < ($timestamp - 3600); ");
}
?>