<?php
if (!isset($_SERVER["HTTP_X_REQUESTED_WITH"]) || $_SERVER["HTTP_X_REQUESTED_WITH"] != "XMLHttpRequest") {
    die("Hacking attempt... Go away.");
}

require_once ($_SERVER["DOCUMENT_ROOT"] . "/config.req.php");

require_once (ABSPATH . "/scripts/header.req.php");

$timestamp = time();
$ip = $_SERVER['HTTP_CF_CONNECTING_IP'];

session_start();
$_SESSION["token"] = $_SESSION["token"] ?? "";
$token = ($_COOKIE['mangadex']) ?? $_SESSION["token"];
$user = new User($db, $token, "token");

$details = $details ?? "";
$error = "";

switch ($_GET["function"]) {
	/*
	// filter functions
	*/
	case "hentai_toggle":
		$mode = $_GET["mode"];
		
		if ($mode == 1) {
			setcookie("mangadex_h_toggle", $mode, $timestamp + (86400 * 3650), "/"); // 86400 = 1 day
			$details = "Everything displayed.";
		}
		elseif ($mode == 2) {
			setcookie("mangadex_h_toggle", $mode, $timestamp + (86400 * 3650), "/"); // 86400 = 1 day
			$details = "Only hentai displayed.";
		}
		elseif ($mode == 0) {
			setcookie("mangadex_h_toggle", "", $timestamp - 3600, "/");
			$details = "Hentai hidden.";
		}
		
		print display_alert("success", "Success", $details); 
		
		$result = 1;
		break;

	/*
	// user functions
	*/
	case "logout":
		setcookie("mangadex", "", $timestamp - 3600, "/");
		setcookie("mangadex_h_toggle", "", $timestamp - 3600, "/");
		
		$new_token = md5($token . $timestamp);
		
		$db->query(" UPDATE mangadex_users SET token = '$new_token' WHERE user_id = $user->user_id LIMIT 1; ");
		
		session_unset(); //remove all session variables
		session_destroy(); //destroy the session
		
		print display_alert("success", "Success", "You have logged out."); 
		
		$result = 1;
		break;
		
	case "login":
		// username and password sent from form
		// To protect MySQL injection (more detail about MySQL injection)
		$username = mysql_escape_mimic($_POST["login_username"]);
		$password = mysql_escape_mimic($_POST["login_password"]);
		
		$count = $db->get_var("SELECT count(*) FROM mangadex_users WHERE username = '$username' "); //check username exists
		
		if ($count) {
			$hash = $db->get_var("SELECT password FROM mangadex_users WHERE username = '$username' "); //select the hash
			
			if (password_verify($password, $hash)) { //verify the hash
				$token = $db->get_var("SELECT token FROM mangadex_users WHERE username = '$username' ") ;
				$user = new User($db, $token, "token"); //logs
				
				$_SESSION["token"] = $token;
				
				if (isset($_POST["remember_me"]))
					setcookie("mangadex", $token, $timestamp + (86400 * 365), "/"); // 86400 = 1 day
			}
			else {
				$details = "Incorrect password.";
				print display_alert("danger", "Failed", $details);  //wrong password
			}
		}
		else {
			$details = "Incorrect username.";
			print display_alert("danger", "Failed", $details); //wrong username
		}
		
		$result = ($details) ? 0 : 1;
		break;
		
	case "signup":		
		$username = mysql_escape_mimic($_POST["reg_username"]);
		$pass1 = mysql_escape_mimic($_POST["reg_pass1"]);
		$pass2 = mysql_escape_mimic($_POST["reg_pass2"]);	
		$email1 = mysql_escape_mimic($_POST["reg_email1"]);
		$email2 = mysql_escape_mimic($_POST["reg_email2"]);	
		if (isset($_POST["g-recaptcha-response"])) 
			$captcha = $_POST['g-recaptcha-response'];
		
		$password_hash = password_hash($pass1, PASSWORD_DEFAULT);
		$token = md5($password_hash);
		$activation_key = md5($token);
		$password_smf = md5($password_hash);

		//existing username / validate username
		$count_user = $db->get_var("SELECT count(*) FROM mangadex_users WHERE username = '$username' ");
		$username_validate = preg_match("/^[a-zA-Z0-9_-]+$/", $username);
		$username_test = (!$count_user && $username_validate); //return TRUE
		
		//existing email
		$count_email = $db->get_var("SELECT count(*) FROM mangadex_users WHERE email = '$email1' ");	
		
		//banned emails
		$banned_email = strpos($email1, "slipry.net");
		
		//pass1=pass2 and email1=email2
		$password_test = ($pass1 == $pass2 && strlen($pass1) >= 8); //return TRUE
		$email_test = ($email1 == $email2); //return TRUE
		
		//validate captcha
		if ($captcha) 
			$captcha_validate = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=xxx&response=$captcha&remoteip=$ip"), true);
		
		$sign_up_test = ($username_test && $count_email == 0 && $banned_email === FALSE && $password_test && $email_test && $captcha_validate["success"]);
		
		if ($sign_up_test) {
			
			$db->query("INSERT INTO mangadex_users (user_id, username, password, token, level_id, email, language, display_lang_id, default_lang_ids, style, joined_timestamp, last_seen_timestamp, avatar, cip, time_offset, activation_key, activated, user_website, user_description, upload_group_id, upload_lang_id, read_announcement, user_views, user_uploads, hentai_mode, swipe_direction, swipe_sensitivity, reader_mode, reader_click) 
				VALUES (NULL, '$username', '$password_hash', '$token', 2, '$email1', 1, 1, '', 1, $timestamp, $timestamp, '', '$ip', '0', '$activation_key', 0, '', '', 0, 0, 0, 0, 0, 0, 1, 100, 0, 1);" );

			$to = $email1;
			$subject = "MangaDex: Account Creation - $username";
			$body = "Thank you for creating an account on MangaDex. \n\nUsername: $username \nPassword: ******** \n\nActivation code: $activation_key";

			send_email($to, $subject, $body); 
			
			$user = new User($db, $token, "token"); //logs
		}
		else {
			if ($count_user) 
				$details = "Choose a unique username.";
			elseif (!$username_validate) 
				$details = "Choose a valid username. Only a-z, A-Z, 0-9, underscore and hyphen allowed."; 
			elseif ($count_email == 1) 
				$details = "Choose a unique email."; 
			elseif ($banned_email !== FALSE) 
				$details = "Banned email."; 
			elseif ($pass1 !== $pass2) 
				$details = "Your passwords do not match."; 
			elseif (strlen($pass1) < 8) 
				$details = "Your password is too short."; 
			elseif (!$email_test) 
				$details = "Your emails do not match."; 
			elseif (!$captcha_validate["success"]) 
				$details = "You failed the captcha."; 
			
			print display_alert("danger", "Failed", $details);
		}	
		
		$result = ($details) ? 0 : 1;
		break;
		
	case "reset_email":	
		$email = mysql_escape_mimic($_POST["reset_email"]);
		if (isset($_POST["g-recaptcha-response"])) 
			$captcha = $_POST['g-recaptcha-response'];

		$count = $db->get_var("SELECT count(*) FROM mangadex_users WHERE email = '$email' ");
		
		//validate captcha
		if ($captcha) 
			$captcha_validate = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=xxx&response=$captcha&remoteip=$ip"), true);

		if ($count && $captcha_validate["success"]) {
			$user = $db->get_row("SELECT user_id, username FROM mangadex_users WHERE email = '$email' ");
			
			$activation_key = md5(rand_string(8));
			
			$to = $email;
			$subject = "MangaDex: Reset Password Request - $user->username";
			$body = "You have requested a reset code for MangaDex. \n\nUsername: $user->username \n\nReset code: $activation_key \n\nPlease visit https://mangadex.com/reset_confirm/$activation_key to continue with your password reset. ";
			
			send_email($to, $subject, $body); 
			
			$db->query(" UPDATE mangadex_users SET activation_key = '$activation_key' WHERE user_id = $user->user_id ");
			
			$user = new User($db, $token, "token"); //logs
		}
		else {
			if (!$captcha_validate["success"]) 
				$details = "You failed the captcha. Error code: {$captcha_validate['error-codes']}"; 
			else 
				$details = "Incorrect email address: $email";
			
			print display_alert("danger", "Failed", $details);
		}
		
		$result = ($details) ? 0 : 1;
		break;

	case "reset":	
		$reset_code = mysql_escape_mimic($_POST["reset_code"]);
		if (isset($_POST["g-recaptcha-response"])) 
			$captcha = $_POST['g-recaptcha-response'];

		$count = $db->get_var("SELECT count(*) FROM mangadex_users WHERE activation_key = '$reset_code' ");
		
		//validate captcha
		if ($captcha) 
			$captcha_validate = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=xxx&response=$captcha&remoteip=$ip"), true);

		if ($count && $captcha_validate["success"]) {
			$user = $db->get_row("SELECT user_id, username, email FROM mangadex_users WHERE activation_key = '$reset_code' ");
			
			$new_password = rand_string(8);
			$password_hash = password_hash($new_password, PASSWORD_DEFAULT);
			$token = md5($password_hash);
			$activation_key = md5($token);
			
			$to = $user->email;
			$subject = "MangaDex: Reset Password - $user->username";
			$body = "You have successfully reset your password for MangaDex. \n\nUsername: $user->username \nPassword: $new_password \n\nPlease change this password after you log on. ";
			
			send_email($to, $subject, $body); 
			
			$db->query(" UPDATE mangadex_users SET password = '$password_hash', activation_key = '$activation_key', token = '$token' WHERE user_id = $user->user_id ");
			
			$user = new User($db, $token, "token"); //logs
		}
		else {
			if (!$captcha_validate["success"]) 
				$details = "You failed the captcha. Error code: {$captcha_validate['error-codes']}"; 
			else 
				$details = "Incorrect reset code: $reset_code";
			
			print display_alert("danger", "Failed", $details);
		}
		
		$result = ($details) ? 0 : 1;
		break;
		
	case "activate":
		$activation_code = mysql_escape_mimic($_POST["activation_code"]);
		
		if ($activation_code == $user->activation_key) {
			$db->query(" UPDATE mangadex_users SET level_id = 3, activated = 1 WHERE user_id = $user->user_id LIMIT 1 "); //update activated
			
			$to = $user->email;
			$subject = "MangaDex: Successful Activation - $user->username";
			$body = "You have successfully activated your account for MangaDex. ";
			
			//send_email($to, $subject, $body); 
		}
		else {
			$details = "Incorrect activation code.";
			print display_alert("danger", "Failed", $details); // wrong code
		}
		
		$result = ($details) ? 0 : 1;
		break;
	
	case "resend_activation_code":
		$to = $user->email;
		$subject = "MangaDex: Resend Activation Code - $user->username";
		$body = "Here's your activation code. \n\nUsername: $user->username \n\nActivation code: $user->activation_key ";

		send_email($to, $subject, $body); 
		
		print display_alert("success", "Success", "Your activation code has been resent to $user->email."); 
		
		$result = 1;
		break;

	case "ban_user":
		$id = sanitise_id($_GET["id"]);
		
		$target_user = new User($db, $id, "user_id");
		
		if ($user->level_id >= 15 && $target_user->level_id < 15 && $target_user->level_id > 0) {
			$db->query(" UPDATE mangadex_users SET level_id = 0 WHERE user_id = $id LIMIT 1 "); 
			
			$details = $id;
			print display_alert("success", "Success", "$target_user->username has been banned."); //success
		}
		else {
			$details = "You can't ban $target_user->username.";
			print display_alert("danger", "Failed", $details); //fail
		}
		
		$result = (!is_numeric($details)) ? 0 : 1;
		break;

	case "unban_user":
		$id = mysql_escape_mimic($_GET["id"]);
		
		$target_user = new User($db, $id, "user_id");
		
		if ($user->level_id >= 15 && !$target_user->level_id) {
			$db->query(" UPDATE mangadex_users SET level_id = 3 WHERE user_id = $id LIMIT 1 "); 
			
			$details = $id;
			print display_alert("success", "Success", "$target_user->username has been unbanned."); //success
		}
		else {
			$details = "You can't unban $target_user->username.";
			print display_alert("danger", "Failed", $details); //fail
		}
		
		$result = (!is_numeric($details)) ? 0 : 1;
		break;	

	/*
	// import functions
	*/
	case "import_json":

		$json = $_POST["json"];
			
		$insert = "";
		$search = '"comic_id":"';
		$string = $json;
		
		$found = strpos_recursive($string, $search);

		if($found) {
			foreach($found as $pos) {
				$start = $pos + 12;
				$end = strpos($json, '"', $start);
				$diff = $end - $start;
				$substr = substr($json, $start, $diff);
				$substr = sanitise_id($substr);
				$insert .= "($user->user_id, $substr),";
				
			}   
		
			$insert = rtrim($insert,",");
			
			$db->query( "INSERT IGNORE INTO mangadex_follow_user_manga (user_id, manga_id) VALUES $insert;" );
				
			$details = 1;
			
		}
		else {
			$details = "Something's wrong with your JSON.";
			print display_alert("danger", "Failed", $details); // fail
		}
		
		$result = (!is_numeric($details)) ? 0 : 1;
		break;
	

	
	/*
	// chapter functions
	*/
	case "chapter_delete":
		$id = sanitise_id($_GET["id"]);
		
		$chapter = new Chapter($db, $id);
		
		if ($chapter->exists) {
			$group = new Group($db, $chapter->group_id);
			
			if ($user->level_id >= 10 || $chapter->user_id == $user->user_id || $group->group_leader_id == $user->user_id) {
				$db->query(" UPDATE mangadex_chapters SET chapter_deleted = 1 WHERE chapter_id = $id LIMIT 1; "); //delete

				$details = $id;
				print display_alert("success", "Success", "Chapter $id has been deleted."); // success
			}
			else {
				$details = "You can't delete Chapter #$id.";
				print display_alert("danger", "Failed", $details); // fail
			}
		}
		else {
			$details = "Chapter #$id does not exist.";
			print display_alert("danger", "Failed", $details); // fail
		}
		
		$result = (!is_numeric($details)) ? 0 : 1;
		break;
		
	case "chapter_edit":
		$id = sanitise_id($_GET["id"]);
		$chapter = new Chapter($db, $id);
		$group = new Group($db, $chapter->group_id);
		
		$manga_id = sanitise_id($_POST["manga_id"]);
		$chapter_name = htmlentities(mysql_escape_mimic($_POST["chapter_name"]));
		$volume_number = remove_padding(htmlentities(mysql_escape_mimic($_POST["volume_number"])));
		$chapter_number = remove_padding(htmlentities(mysql_escape_mimic($_POST["chapter_number"])));
		$group_id = sanitise_id($_POST["group_id"]);
		$group_id_2 = sanitise_id($_POST["group_id_2"]);
		$group_id_3 = sanitise_id($_POST["group_id_3"]);
		$lang_id = sanitise_id($_POST["lang_id"]);
		$old_file = $_POST["old_file"] ?? "";	
		
		$target_group = new Group($db, $group_id);
		$group_members_array = $target_group->get_members($db);
		
		if ($group_id_2) {
			$target_group2 = new Group($db, $group_id_2);
			$group2_members_array = $target_group2->get_members($db);
		}
		else {
			$target_group2 = new stdClass();
			$target_group2->group_control = 0;
		}
		if ($group_id_3) {
			$target_group3 = new Group($db, $group_id_3);
			$group3_members_array = $target_group3->get_members($db);
		}
		else {
			$target_group3 = new stdClass();
			$target_group3->group_control = 0;
		}
		$validate_group_control = (!$group->group_control || $user->user_id == $group->group_leader_id || ($group->group_control && in_array($user->username, $group_members_array)));
		$validate_group2_control = (!$target_group2->group_control || $user->user_id == $target_group2->group_leader_id || ($target_group2->group_control && in_array($user->username, $group2_members_array)));
		$validate_group3_control = (!$target_group3->group_control || $user->user_id == $target_group3->group_leader_id || ($target_group3->group_control && in_array($user->username, $group3_members_array)));
		
		$same_multi_group_validate = ($group_id != $group_id_2 && $group_id != $group_id_3 && ((!$group_id_2 && !$group_id_3) || $group_id_2 != $group_id_3));
		
		if (((($chapter->user_id == $user->user_id || $group->group_leader_id == $user->user_id) && ($validate_group_control && $validate_group2_control && $validate_group3_control)) || $user->level_id >= 10) && $same_multi_group_validate) {
			if ($old_file) {
				$zip = new ZipArchive;
				
				if ($_FILES["file"]) {
					$value = explode(".", $_FILES["file"]["name"]);
					
					$validate_extention = in_array(strtolower(end($value)), $allowed_chapter_ext);
					$validate_file_size = ($_FILES["file"]["size"] <= MAX_CHAPTER_FILESIZE); //check file size
					
					if ($zip->open($_FILES["file"]["tmp_name"])) {
						$chapter_hash = md5($manga_id . $timestamp);
				
						mkdir(ABSPATH . "/data/$chapter_hash");
						
						$zip->extractTo(ABSPATH . "/data/$chapter_hash/");
						$zip->close();
						
						$files = array_diff(scandir(ABSPATH . "/data/$chapter_hash/"), array(".", ".."));
						
						$pages = count($files);
						
						if ($pages == 1 && is_dir(ABSPATH . "/data/$chapter_hash/$files[2]")) { //folder
							rename(ABSPATH . "/data/$chapter_hash/$files[2]", ABSPATH . "/data/$chapter_hash/folder"); //rename the dir
							
							$files = array_diff(scandir(ABSPATH . "/data/$chapter_hash/folder/"), array(".", ".."));
							foreach($files as $value) {
								rename(ABSPATH . "/data/$chapter_hash/folder/$value", ABSPATH . "/data/$chapter_hash/$value"); //move them all
							} 
							
							rmdir(ABSPATH . "/data/$chapter_hash/folder");
							
							$files = array_diff(scandir(ABSPATH . "/data/$chapter_hash/"), array(".", ".."));
						}
						elseif ($pages > 1 && is_dir(ABSPATH . "/data/$chapter_hash/$files[3]")) {
							$error .= display_alert("danger", "Failed", "Your .zip contains multiple folders."); //can't open zip
						}
					}
					else 
						$error .= display_alert("danger", "Failed", "There's something wrong with your .zip file."); //can't open zip
					
					if ($_FILES["file"]["error"]) 
						$error .= display_alert("danger", "Failed", "Missing file? Code: (" . $_FILES["file"]["error"] . ")."); 
					elseif (!$validate_file_size) 
						$error .= display_alert("danger", "Failed", "File size exceeds 100 MB."); //too big
					elseif (!$validate_extention) 
						$error .= display_alert("danger", "Failed", "A .$extension file, not a .zip."); //too big
						
					
				}
			}
		}
		elseif (!$validate_group_control)
			$error .= display_alert("danger", "Failed", "Group 1 have restricted uploads to members only."); //banned
		elseif (!$validate_group2_control) 
			$error .= display_alert("danger", "Failed", "Group 2 have restricted uploads to members only."); //banned
		elseif (!$validate_group3_control) 
			$error .= display_alert("danger", "Failed", "Group 3 have restricted uploads to members only."); //banned
		elseif (!$same_multi_group_validate)
			$error .= display_alert("danger", "Failed", "Identical groups detected."); //banned
		else 
			$error .= display_alert("danger", "Failed", "You can't edit Chapter #$id."); 
		
		if (!$error) {
			if ($old_file) {
				$page_order = "";
				
				natsort($files);
				$arr = array_values($files);
				
				foreach($arr as $key => $value) {
					$key++;
					$arr = explode(".", $value);
					$ext = strtolower(end($arr));
					if (!in_array($ext, $allowed_image_ext))
						unlink(ABSPATH . "/data/$chapter_hash/$value");
					else {
						rename(ABSPATH . "/data/$chapter_hash/$value", ABSPATH . "/data/$chapter_hash/x$key.$ext"); //rename them all numerically
						
						$page_order .= "x$key.$ext,";
					}
				}				
				
				$page_order = rtrim($page_order, ",");
				
				if (!$chapter->server && is_dir(ABSPATH . "/data/$chapter->chapter_hash")) 
					rename(ABSPATH . "/data/$chapter->chapter_hash", ABSPATH . "/delete/$chapter->chapter_hash");				
				
				$db->query(" UPDATE mangadex_chapters SET server = 0, chapter_hash = '$chapter_hash', page_order = '$page_order' WHERE chapter_id = $id LIMIT 1; ");
			}
			
			$db->query(" UPDATE mangadex_chapters SET manga_id = $manga_id, title = '$chapter_name', volume = '$volume_number', chapter = '$chapter_number', group_id = $group_id, group_id_2 = $group_id_2, group_id_3 = $group_id_3, lang_id = $lang_id WHERE chapter_id = $id LIMIT 1; ");
			
			$details = $id;
			
		}
		
		print $error; //returns "" or a message
		
		$result = ($error) ? 0 : 1;
		break;


	case "chapter_report":
		$id = sanitise_id($_GET["id"]);
		$type = sanitise_id($_GET["type"]);	
		$info = htmlentities(mysql_escape_mimic($_GET["info"]));
		
		if ($user->user_id) {
			if (!$type && (!$info || $info == "null")) {
				$details = "Please give more information.";
				print display_alert("danger", "Failed", $details); //fail
			}
			else {
				$db->query(" INSERT IGNORE INTO mangadex_chapter_reports (report_id, report_chapter_id, report_timestamp, report_type, report_info, report_user_id, report_mod_user_id, report_conclusion) 
					VALUES (NULL, $id, $timestamp, $type, '$info', $user->user_id, 0, 0); ");
				
				$details = $id;
				print display_alert("success", "Success", "You have reported Chapter #$id."); //success
			}
		}
		else {
			$details = "You can't report Chapter $id.";
			print display_alert("danger", "Failed", $details); //fail
		}
		
		$result = (!is_numeric($details)) ? 0 : 1;
		break;
		
	case "chapter_upload":
		$zip = new ZipArchive;
		
		$manga_id = sanitise_id($_POST["manga_id"]);
		$chapter_name = htmlentities(mysql_escape_mimic($_POST["chapter_name"]));
		$volume_number = remove_padding(htmlentities(mysql_escape_mimic($_POST["volume_number"])));
		$chapter_number = remove_padding(htmlentities(mysql_escape_mimic($_POST["chapter_number"])));
		$group_id = sanitise_id($_POST["group_id"]);
		$group_id_2 = !empty($_POST["group_id_2"]) ? sanitise_id($_POST["group_id_2"]) : 0;
		$group_id_3 = !empty($_POST["group_id_3"]) ? sanitise_id($_POST["group_id_3"]) : 0;
		$lang_id = sanitise_id($_POST["lang_id"]);
		$group_delay = isset($_POST["group_delay"]) ? 1 : 0;	
		
		$manga = new Manga($db, $manga_id);
		$group = new Group($db, $group_id);
		$group_members_array = $group->get_members($db);
		
		if ($group_id_2) {
			$group2 = new Group($db, $group_id_2);
			$group2_members_array = $group2->get_members($db);
		}
		else {
			$group2 = new stdClass();
			$group2->group_control = 0;
		}
		
		if ($group_id_3) {
			$group3 = new Group($db, $group_id_3);
			$group3_members_array = $group3->get_members($db);
		}
		else {
			$group3 = new stdClass();
			$group3->group_control = 0;
		}
		
		$validate_group_control = (!$group->group_control || $user->user_id == $group->group_leader_id || ($group->group_control && in_array($user->username, $group_members_array)));
		$validate_group2_control = (!$group2->group_control || $user->user_id == $group2->group_leader_id || ($group2->group_control && in_array($user->username, $group2_members_array)));
		$validate_group3_control = (!$group3->group_control || $user->user_id == $group3->group_leader_id || ($group3->group_control && in_array($user->username, $group3_members_array)));
		
		$same_multi_group_validate = ($group_id != $group_id_2 && $group_id != $group_id_3 && ((!$group_id_2 && !$group_id_3) || $group_id_2 != $group_id_3));
		
		$error = "";
		$stop = 1;
		if ($user->user_id && $user->level_id && $manga->exists && (($validate_group_control && $validate_group2_control && $validate_group3_control) || $user->level_id >= 10) && $same_multi_group_validate && $stop) {
			if ($_FILES["file"]) {
				$value = explode(".", $_FILES["file"]["name"]);
				
				$validate_extention = in_array(strtolower(end($value)), $allowed_chapter_ext);
				$validate_file_size = ($_FILES["file"]["size"] <= MAX_CHAPTER_FILESIZE); //check file size
				
				if ($zip->open($_FILES["file"]["tmp_name"])) {
					$chapter_hash = md5($manga_id . $chapter_name . $volume_number . $chapter_number . $timestamp);
			
					mkdir(ABSPATH . "/data/$chapter_hash");
					
					$zip->extractTo(ABSPATH . "/data/$chapter_hash/");
					$zip->close();
					
					$files = array_diff(scandir(ABSPATH . "/data/$chapter_hash/"), array(".", ".."));
					
					$pages = count($files);
					
					if ($pages == 1 && is_dir(ABSPATH . "/data/$chapter_hash/$files[2]")) { //folder
						rename(ABSPATH . "/data/$chapter_hash/$files[2]", ABSPATH . "/data/$chapter_hash/folder"); //rename the dir
						
						$files = array_diff(scandir(ABSPATH . "/data/$chapter_hash/folder/"), array(".", ".."));
						foreach($files as $value) {
							rename(ABSPATH . "/data/$chapter_hash/folder/$value", ABSPATH . "/data/$chapter_hash/$value"); //move them all
						} 
						
						rmdir(ABSPATH . "/data/$chapter_hash/folder");
						
						$files = array_diff(scandir(ABSPATH . "/data/$chapter_hash/"), array(".", ".."));
					}
					elseif ($pages > 1 && is_dir(ABSPATH . "/data/$chapter_hash/$files[3]")) {
						$error .= display_alert("danger", "Failed", "Your .zip contains multiple folders, or you have files starting with an invalid character (!)"); //can't open zip
					}
				}
				else 
					$error .= display_alert("danger", "Failed", "There's something wrong with your .zip file."); //can't open zip
				
				if ($_FILES["file"]["error"]) 
					$error .= display_alert("danger", "Failed", "Missing file? Code: (" . $_FILES["file"]["error"] . ")."); 
				elseif (!$validate_file_size) 
					$error .= display_alert("danger", "Failed", "File size exceeds 100 MB."); //too big
				elseif (!$validate_extention) 
					$error .= display_alert("danger", "Failed", "A .$extension file, not a .zip."); //too big
					
				
			}
			else 
				$error .= display_alert("danger", "Failed", "Missing file."); //missing image
		}
		else {
			if (!$user->user_id)
				$error .= display_alert("danger", "Failed", "Your session has timed out. Please log in again."); //timed_out
			elseif (!$user->level_id) 
				$error .= display_alert("danger", "Failed", "You're banned from uploading!"); //banned
			elseif (!$validate_group_control) 
				$error .= display_alert("danger", "Failed", "Group 1 have restricted uploads to members only."); //banned
			elseif (!$validate_group2_control) 
				$error .= display_alert("danger", "Failed", "Group 2 have restricted uploads to members only."); //banned
			elseif (!$validate_group3_control) 
				$error .= display_alert("danger", "Failed", "Group 3 have restricted uploads to members only."); //banned
			elseif (!$same_multi_group_validate)
				$error .= display_alert("danger", "Failed", "Identical groups detected."); //banned
			elseif (!$stop)
				$error .= display_alert("danger", "Failed", "Stop uploading for a while due to image transfer."); //banned
			else 
				$error .= display_alert("danger", "Failed", "Manga #$manga_id does not exist.");
		}
		
		//if no errors, then upload
		if (!$error) {
			$page_order = "";
			
			natsort($files);
			$arr = array_values($files);
			
			foreach($arr as $key => $value) {
				$key++;
				$arr = explode(".", $value);
				$ext = strtolower(end($arr));
				if (!in_array($ext, $allowed_image_ext))
					unlink(ABSPATH . "/data/$chapter_hash/$value");
				else {
					rename(ABSPATH . "/data/$chapter_hash/$value", ABSPATH . "/data/$chapter_hash/x$key.$ext"); //rename them all numerically
					
					$page_order .= "x$key.$ext,";
				}
			}
			
			$page_order = rtrim($page_order, ",");
			
			$upload_timestamp = ($group_delay) ? $timestamp + $group->group_delay : $timestamp;
			
			$db->query("INSERT INTO mangadex_chapters (chapter_id, chapter_hash, manga_id, volume, chapter, title, upload_timestamp, user_id, chapter_views, lang_id, authorised, group_id, group_id_2, group_id_3, server, page_order, chapter_deleted) 
			VALUES (NULL, '$chapter_hash', $manga_id, '$volume_number', '$chapter_number', '$chapter_name', $upload_timestamp, $user->user_id, 0, $lang_id, 0, $group_id, $group_id_2, $group_id_3, 0, '$page_order', 0); ");
			
			$db->query(" UPDATE mangadex_mangas SET manga_last_updated = $timestamp WHERE manga_id = $manga_id LIMIT 1; ");
			
		}
		
		print $error; //returns "" or a message
		
		$result = ($error) ? 0 : 1;
		break;
	


	/*
	// manga functions
	*/
	case "manga_delete":
		$id = sanitise_id($_GET["id"]);
		
		$manga = new Manga($db, $id);
		
		if ($manga->exists) {
			if ($user->level_id >= 15) {
				$db->query(" DELETE FROM mangadex_mangas WHERE manga_id = $id LIMIT 1; "); //delete from actual table
				
				
				unlink(ABSPATH . "/images/manga/$manga->manga_id.$manga->manga_image");
				unlink(ABSPATH . "/images/manga/$manga->manga_id.thumb.jpg");
				
				$details = $id;
				print display_alert("success", "Success", "Manga #$id has been deleted."); // success
			}
			else {
				$details = "You can't delete Manga #$id.";
				print display_alert("danger", "Failed", $details); // fail
			}
		}
		else {
			$details = "Manga #$id does not exist.";
			print display_alert("danger", "Failed", $details); // fail
		}
		
		$result = (!is_numeric($details)) ? 0 : 1;
		break;
		
	case "manga_edit":
		$id = sanitise_id($_GET["id"]);
		
		$manga = new Manga($db, $id);
		
		if ($manga->exists) {
			$manga_name = htmlentities(mysql_escape_mimic($_POST["manga_name"]));
			//$manga_alt_names = htmlentities(mysql_escape_mimic($_POST["manga_alt_names"]));
			$manga_author = htmlentities(mysql_escape_mimic($_POST["manga_author"]));
			$manga_artist = htmlentities(mysql_escape_mimic($_POST["manga_artist"]));
			$manga_lang_id = sanitise_id($_POST["manga_lang_id"]);
			$manga_status_id = sanitise_id($_POST["manga_status_id"]);
			$manga_hentai = isset($_POST["manga_hentai"]) ? 1 : 0;	
			$manga_description = htmlentities(mysql_escape_mimic($_POST["manga_description"]));	
			$manga_mal_id = sanitise_id($_POST["manga_mal_id"]);
			$manga_mu_id = sanitise_id($_POST["manga_mu_id"]);
			$old_file = htmlentities(mysql_escape_mimic($_POST["old_file"]));	
			
			$error = "";
				
			if ($old_file && $_FILES["file"])
				$error .= validate_image($_FILES["file"]);
			
			if ($user->level_id < 3) 
				$error .= display_alert("danger", "Failed", "You can't edit this title."); 
			
			if ($user->level_id < 10 && $manga->manga_locked)
				$error .= display_alert("danger", "Failed", "Editing has been locked to mods only."); 
			
			if (!$error) {
				$db->query(" UPDATE mangadex_mangas SET manga_name = '$manga_name', manga_author = '$manga_author', manga_artist = '$manga_artist', manga_lang_id = $manga_lang_id, manga_status_id = $manga_status_id, manga_hentai = $manga_hentai, manga_description = '$manga_description', manga_mal_id = $manga_mal_id, manga_mu_id = $manga_mu_id WHERE manga_id = $id LIMIT 1; ");
				
				$db->query(" DELETE FROM mangadex_manga_genres WHERE manga_id = $id ");
				
				foreach ($_POST["manga_genres"] as $genre_id) {
					$genre_id = sanitise_id($genre_id);
					if ($genre_id <= 40)
						$db->query(" INSERT INTO mangadex_manga_genres (manga_id, genre_id) VALUES ($id, $genre_id); ");
				}

				if ($old_file) {
					$arr = (explode(".", $_FILES["file"]["name"]));
					$ext = strtolower(end($arr));
					
					if ($manga->manga_image)
						unlink(ABSPATH . "/images/manga/$manga->manga_id.$manga->manga_image");
					
					move_uploaded_file($_FILES["file"]["tmp_name"], ABSPATH . "/images/manga/$manga->manga_id.$ext");
					
					$db->query(" UPDATE mangadex_mangas SET manga_image = '$ext' WHERE manga_id = $manga->manga_id LIMIT 1; ");
					
					generate_thumbnail(ABSPATH . "/images/manga/$manga->manga_id.$ext", 0);
				}
				
				$details = $id;			
			}
			else {
				$details = $error;
				print $error; //returns "" or a message
			}
		}
		else {
			$details = "Manga #$id does not exist.";
			print display_alert("danger", "Failed", $details); // fail
		}
		
		$result = (!is_numeric($details)) ? 0 : 1;
		break;

	case "manga_add":
		
		$error = "";
		$manga_name = htmlentities(mysql_escape_mimic($_POST["manga_name"]));	
		
		//existing manga
		$count_manga = $db->get_var("SELECT count(*) FROM mangadex_mangas WHERE manga_name = '$manga_name' ");	
		
		if ($user->user_id && $user->level_id >= 3 && !$count_manga) {
			if ($_FILES["file"]) 
				$error .= validate_image($_FILES["file"]);
			else 
				$error .= display_alert("danger", "Failed", "Missing image."); //missing image
		}
		else {
			if (!$user->user_id)
				$error .= display_alert("danger", "Failed", "Your session has timed out. Please log in again."); //timed_out
			elseif ($count_manga)
				$error .= display_alert("danger", "Failed", "This title already exists in the database."); //timed_out
			else
				$error .= display_alert("danger", "Failed", "You can't upload."); //banned
		}
		
		//if no errors, then upload
		if (!$error) {
			
			$manga_alt_names = htmlentities(mysql_escape_mimic($_POST["manga_alt_names"]));	
			$manga_author = htmlentities(mysql_escape_mimic($_POST["manga_author"]));	
			$manga_artist = htmlentities(mysql_escape_mimic($_POST["manga_artist"]));	
			$manga_lang_id = sanitise_id($_POST["manga_lang_id"]);
			$manga_status_id = sanitise_id($_POST["manga_status_id"]);
			$manga_hentai = isset($_POST["manga_hentai"]) ? 1 : 0;	
			$manga_description = htmlentities(mysql_escape_mimic($_POST["manga_description"]));	
			
			$db->query("INSERT INTO mangadex_mangas (manga_id, manga_name, manga_alt_names, manga_author, manga_artist, manga_lang_id, manga_status_id, manga_hentai, manga_description, manga_image, manga_rating, manga_views, manga_follows, manga_last_updated, manga_comments, manga_mal_id, manga_mu_id, manga_locked) VALUES (NULL, '$manga_name', '$manga_alt_names', '$manga_author', '$manga_artist', $manga_lang_id, $manga_status_id, $manga_hentai, '$manga_description', '', 0, 0, 0, 0, 0, 0, 0, 0); ");
			
			$manga_id = $db->get_var("SELECT manga_id FROM mangadex_mangas WHERE manga_name = '$manga_name' ");	
			
			$arr = (explode(".", $_FILES["file"]["name"]));
			$ext = strtolower(end($arr));
			
			move_uploaded_file($_FILES["file"]["tmp_name"], ABSPATH . "/images/manga/$manga_id.$ext");
			
			$db->query(" UPDATE mangadex_mangas SET manga_image = '$ext' WHERE manga_id = $manga_id LIMIT 1; ");
			
			generate_thumbnail(ABSPATH . "/images/manga/$manga_id.$ext", 0);
			
		}
		
		print $error; //returns "" or a message
		
		$result = ($error) ? 0 : 1;
		break;

	case "manga_comment":
		$id = sanitise_id($_GET["id"]);
		$manga = new Manga($db, $id);
		
		$comment = htmlentities(mysql_escape_mimic($_POST["comment"]));	
		
		if ($user->user_id) {
			$db->query(" INSERT INTO mangadex_comments_manga (comment_id, manga_id, user_id, comment_text, comment_timestamp, comment_del) 
				VALUES (NULL, $id, $user->user_id, '$comment', $timestamp, 0); ");
			
			$count = $db->get_var("SELECT count(*) FROM mangadex_comments_manga WHERE manga_id = $id AND comment_del = 0; ");	
			
			$db->query(" UPDATE mangadex_mangas SET manga_comments = $count WHERE manga_id = $id LIMIT 1; ");
			
			$details = $id;
		}
		else {
			$details = "You can't comment on Manga $id.";
			print display_alert("danger", "Failed", $details); //fail
		}
		
		$result = (!is_numeric($details)) ? 0 : 1;
		break;

	case "manga_comment_delete":
		$id = sanitise_id($_GET["id"]);		
		$comment = new Comment($db, $id);
		
		if ($comment->exists) {
			if ($user->level_id >= 10 || $comment->user_id == $user->user_id) {
				$db->query(" UPDATE mangadex_comments_manga SET comment_del = 1 WHERE comment_id = $id LIMIT 1; "); //delete 
				
				$count = $db->get_var("SELECT count(*) FROM mangadex_comments_manga WHERE manga_id = $comment->manga_id AND comment_del = 0; ");	
				
				$db->query(" UPDATE mangadex_mangas SET manga_comments = $count WHERE manga_id = $comment->manga_id LIMIT 1; "); //update comments
				
				$details = $comment->manga_id;
				print display_alert("success", "Success", "Comment #$id has been deleted."); // success
			}
			else {
				$details = "You can't delete Comment #$id.";
				print display_alert("danger", "Failed", $details); // fail
			}
		}
		else {
			$details = "Comment #$id does not exist.";
			print display_alert("danger", "Failed", $details); // fail
		}
		
		$result = (!is_numeric($details)) ? 0 : 1;
		break;

	case "manga_follow":
		$id = sanitise_id($_GET["id"]);
		
		if ($user->user_id) {
			$db->query(" INSERT INTO mangadex_follow_user_manga (user_id, manga_id) VALUES ($user->user_id, $id); ");
			$details = $id;
			print display_alert("success", "Success", "You have followed this manga."); 
			
			$follows = $db->get_var(" SELECT count(*) FROM mangadex_follow_user_manga WHERE manga_id = $id "); 
			$db->query(" UPDATE mangadex_mangas SET manga_follows = $follows WHERE manga_id = $id LIMIT 1; ");
		}
		else {
			$details = "Action failed.";
			print display_alert("danger", "Failed", $details); // fail
		}
		
		$result = (!is_numeric($details)) ? 0 : 1;
		break;

	case "manga_unfollow":
		$id = sanitise_id($_GET["id"]);

		if ($user->user_id) {
			$db->query(" DELETE FROM mangadex_follow_user_manga WHERE user_id = $user->user_id AND manga_id = $id LIMIT 1; ");
			$details = $id;
			print display_alert("warning", "Warning", "You have unfollowed this manga."); 
			
			$follows = $db->get_var(" SELECT count(*) FROM mangadex_follow_user_manga WHERE manga_id = $id "); 
			$db->query(" UPDATE mangadex_mangas SET manga_follows = $follows WHERE manga_id = $id LIMIT 1; ");
		}
		else {
			$details = "Action failed.";
			print display_alert("danger", "Failed", $details); // fail
		}
		
		$result = (!is_numeric($details)) ? 0 : 1;
		break;	

	case "mod_lock_manga":
		$id = sanitise_id($_GET["id"]);
		
		if ($user->level_id >= 10) {
			$db->query(" UPDATE mangadex_mangas SET manga_locked = 1 WHERE manga_id = $id; ");
			
			$details = $id;
			print display_alert("success", "Success", "Manga #$id has been locked.");  //success		
		}
		else {
			$details = "You can't lock manga.";
			print display_alert("danger", "Failed", $details); //fail	
		}
		
		$result = (!is_numeric($details)) ? 0 : 1;
		break;

	case "mod_unlock_manga":
		$id = sanitise_id($_GET["id"]);
		
		if ($user->level_id >= 10) {
			$db->query(" UPDATE mangadex_mangas SET manga_locked = 0 WHERE manga_id = $id; ");
			
			$details = $id;
			print display_alert("success", "Success", "Manga #$id has been unlocked.");  //success		
		}
		else {
			$details = "You can't unlock manga.";
			print display_alert("danger", "Failed", $details); //fail	
		}
		
		$result = (!is_numeric($details)) ? 0 : 1;
		break;
		
	case "admin_edit_manga":
		$id = sanitise_id($_GET["id"]);
		$old_id = sanitise_id($_POST["old_id"]);	
		
		if ($user->level_id >= 15) {
			$db->query(" UPDATE mangadex_chapters SET manga_id = $old_id WHERE manga_id = $id; ");
			$db->query(" UPDATE IGNORE mangadex_follow_user_manga SET manga_id = $old_id WHERE manga_id = $id; ");
			$db->query(" UPDATE mangadex_comments_manga SET manga_id = $old_id WHERE manga_id = $id; ");
			
			$db->query(" DELETE FROM mangadex_mangas WHERE manga_id = $id LIMIT 1 ");
			
			$details = $id;
			print display_alert("success", "Success", "Manga #$id has been edited.");  //success		
		}
		else {
			$details = "You can't edit manga.";
			print display_alert("danger", "Failed", $details); //fail	
		}
		
		$result = (!is_numeric($details)) ? 0 : 1;
		break;
		
	/*
	// group functions
	*/	
	case "group_add":
		
		$group_name = htmlentities(mysql_escape_mimic($_POST["group_name"]));
		$group_lang_id = mysql_escape_mimic($_POST["group_lang_id"]);
		$group_website = htmlentities(mysql_escape_mimic($_POST["group_website"]));
		$group_irc_channel = htmlentities(mysql_escape_mimic($_POST["group_irc_channel"]));
		$group_irc_server = htmlentities(mysql_escape_mimic($_POST["group_irc_server"]));
		$group_discord = htmlentities(mysql_escape_mimic($_POST["group_discord"]));
		$group_email = htmlentities(mysql_escape_mimic($_POST["group_email"]));
		$group_description = htmlentities(mysql_escape_mimic($_POST["group_description"]));
		
		//existing group/tag
		$count_group_name = $db->get_var("SELECT count(*) FROM mangadex_groups WHERE group_name = '$group_name' ");	
		
		if (!$count_group_name) {
			$db->query(" INSERT INTO mangadex_groups (group_id, group_name, group_leader_id, group_website, group_irc_channel, group_irc_server, group_discord, group_email, group_lang_id, group_founded, group_banner, group_comments, group_likes, group_follows, group_views, group_description, group_control, group_delay) VALUES (NULL, '$group_name', 1, '$group_website', '$group_irc_channel', '$group_irc_server', '$group_discord', '$group_email', $group_lang_id, '2018-01-01', '', 0, 0, 0, 0, '$group_description', 0, 0); ");
			
			$group_id = $db->get_var("SELECT group_id FROM mangadex_groups WHERE group_name LIKE '$group_name' ");	
			
			$details = $group_id;
		}
		else {
			$details = "Your chosen group name is not unique.";
			print display_alert("danger", "Failed", $details); //fail
		}
		
		$result = (!is_numeric($details)) ? 0 : 1;
		break;
	
	case "group_add_member":
		$group_id = sanitise_id($_GET["id"]);
		
		$group = new Group($db, $group_id);
		
		$add_member_user_id = sanitise_id($_POST["add_member_user_id"]);
		$check_member = $db->get_var(" SELECT count(*) FROM mangadex_users WHERE user_id = $add_member_user_id "); //does user exist?
		$check_existing_member = $db->get_var(" SELECT count(*) FROM mangadex_link_user_group WHERE user_id = $add_member_user_id AND group_id = $group_id "); //does user exist?

		if ($user->level_id >= 10 || $group->group_leader_id == $user->user_id) {
			if ($check_member && !$check_existing_member && $add_member_user_id > 1) {
				$db->query(" INSERT INTO mangadex_link_user_group (id, user_id, group_id, role) 
					VALUES (NULL, $add_member_user_id, $group_id, 2); ");
				
				$details = $group_id;
				print display_alert("success", "Success", "User $add_member_user_id has been added to group $group_id."); //success
			}
			else {
				$details = "User does not exist or is already in your group.";
				print display_alert("danger", "Failed", $details); //wrong user id
			}
		}
		else {
			$details = "You can't add group members.";
			print display_alert("danger", "Failed", $details); //fail
		}
		
		$result = (!is_numeric($details)) ? 0 : 1;
		break;
		
	case "group_delete_member":
		$delete_user_id = sanitise_id($_GET['user_id']);
		$group_id = sanitise_id($_GET['group_id']);
		
		$group = new Group($db, $group_id);
		
		if ($user->level_id >= 10 || $group->group_leader_id == $user->user_id) {	
			$db->query(" DELETE FROM mangadex_link_user_group WHERE group_id = $group_id AND user_id = $delete_user_id AND role = 2 LIMIT 1; ");
			
			$details = $group_id;
			print display_alert("success", "Success", "User has been deleted."); //success	
		}
		else {
			$details = "You can't delete members.";
			print display_alert("danger", "Failed", $details); //fail
		}
		
		$result = (!is_numeric($details)) ? 0 : 1;
		break;
				
	case "group_edit":
		$id = sanitise_id($_GET["id"]);
		
		$group = new Group($db, $id);
		
		$group_founded = htmlentities(mysql_escape_mimic($_POST["group_founded"]));
		$url_link = htmlentities(mysql_escape_mimic($_POST["url_link"]));
		$irc_channel = htmlentities(mysql_escape_mimic($_POST["irc_channel"]));
		$irc_server = htmlentities(mysql_escape_mimic($_POST["irc_server"]));
		$discord = htmlentities(mysql_escape_mimic($_POST["discord"]));
		$lang_id = sanitise_id($_POST["lang_id"]);
		$group_email = htmlentities(mysql_escape_mimic($_POST["group_email"]));
		$group_description = htmlentities(mysql_escape_mimic($_POST["group_description"]));
		$group_control = isset($_POST["group_control"]) ? 1 : 0;	
		$group_delay = sanitise_id($_POST["group_delay"]);
		$old_file = htmlentities(mysql_escape_mimic($_POST["old_file"]));	
		
		$error = "";
		
		if ($old_file && $_FILES["file"])
			$error .= validate_image($_FILES["file"]);
		
		if (!($user->level_id >= 10 || $group->group_leader_id == $user->user_id)) 
			$error .= display_alert("danger", "Failed", "You can't edit $group->group_name."); 
		
		if (!$error) {
			$db->query(" UPDATE mangadex_groups SET group_founded = '$group_founded', group_website = '$url_link', group_irc_channel = '$irc_channel', group_irc_server = '$irc_server', group_discord = '$discord', group_email = '$group_email', group_lang_id = $lang_id, group_description = '$group_description', group_control = $group_control, group_delay = $group_delay WHERE group_id = $id LIMIT 1; ");

			if ($old_file) {
				$arr = (explode(".", $_FILES["file"]["name"]));
				$ext = strtolower(end($arr));
				
				if ($group->group_banner)
					unlink(ABSPATH . "/images/groups/$group->group_id.$group->group_banner");
				
				move_uploaded_file($_FILES["file"]["tmp_name"], ABSPATH . "/images/groups/$group->group_id.$ext");
				
				$db->query(" UPDATE mangadex_groups SET group_banner = '$ext' WHERE group_id = $group->group_id LIMIT 1; ");
			}
			
			$details = $id;			
		}
		else {
			$details = $error;
			print $error; //returns "" or a message
		}		
		
		$result = ($details) ? 0 : 1;
		break;
	
	case "like_group":
		$id = sanitise_id($_GET["id"]);
		
		$group = new Group($db, $id);
		
		$array_of_user_id_ip = $group->get_likes_user_id_ip_list($db);
		
		if ($user->user_id > 1 && !in_array($user->user_id, $array_of_user_id_ip["user_id"])) {
			$db->query(" INSERT INTO mangadex_group_likes (id, group_id, user_id, ip, timestamp) VALUES (NULL, $id, $user->user_id, '', $timestamp); ");
			print display_alert("success", "Success", "You have liked $group->group_name."); 
		}
		elseif ($user->user_id == 1 && !in_array($ip, $array_of_user_id_ip["ip"])) {
			$db->query(" INSERT INTO mangadex_group_likes (id, group_id, user_id, ip, timestamp) VALUES (NULL, $id, 1, '$ip', $timestamp); ");
			print display_alert("success", "Success", "You have liked $group->group_name. ($ip)"); 
		}
		
		$likes = $db->get_var(" SELECT count(*) FROM mangadex_group_likes WHERE group_id = $id "); 
		$db->query(" UPDATE mangadex_groups SET group_likes = $likes WHERE group_id = $id LIMIT 1; ");
		
		$details = $id;
		$result = 1;
		break;

	case "unlike_group":
		$id = sanitise_id($_GET["id"]);
		
		$group = new Group($db, $id);
		
		$array_of_user_id_ip = $group->get_likes_user_id_ip_list($db);
		
		if ($user->user_id > 1 && in_array($user->user_id, $array_of_user_id_ip["user_id"])) {
			$db->query(" DELETE FROM mangadex_group_likes WHERE group_id = $id AND user_id = $user->user_id LIMIT 1; ");
			print display_alert("warning", "Warning", "You have unliked $group->group_name."); 
		}
		elseif ($user->user_id == 1 && in_array($ip, $array_of_user_id_ip["ip"])) {
			$db->query(" DELETE FROM mangadex_group_likes WHERE group_id = $id AND ip = '$ip' LIMIT 1; ");
			print display_alert("warning", "Warning", "You have unliked $group->group_name. ($ip)"); 
		}
		
		$likes = $db->get_var(" SELECT count(*) FROM mangadex_group_likes WHERE group_id = $id "); 
		$db->query(" UPDATE mangadex_groups SET group_likes = $likes WHERE group_id = $id LIMIT 1; ");
		
		$details = $id;
		$result = 1;
		break;

	case "auth_rescan":
		$id = sanitise_id($_GET['group_id']);
		
		$group = new Group($db, $id);
		
		if ($user->level_id >= 15 || $group->group_leader_id == $user->user_id) {
			$db->query(" UPDATE mangadex_files SET authorised = 0 WHERE group_id = $id; ");
			$db->query(" UPDATE mangadex_files SET authorised = 1 WHERE group_id = $id AND filename LIKE '%$group->group_tag%'; ");
			if ($group->group_tag2) $db->query(" UPDATE mangadex_files SET authorised = 1 WHERE group_id = $id AND filename LIKE '%$group->group_tag2%'; ");
			
			$details = $id;
			print display_alert("success", "Success", "Success."); //success
		}
		else {
			$details = "You do not have permission to rescan.";
			print display_alert("danger", "Failed", $details); //fail
		}
		
		$details = $id;
		$result = 1;
		break;
	
	case "group_comment":
		$id = sanitise_id($_GET["id"]);
		
		$group = new Group($db, $id);
		
		$comment = htmlentities(mysql_escape_mimic($_POST["comment"]));	
		
		if ($user->user_id) {
			$db->query(" INSERT INTO mangadex_comments_groups (comment_id, group_id, user_id, comment_text, comment_timestamp, comment_del) 
				VALUES (NULL, $id, $user->user_id, '$comment', $timestamp, 0); ");
			
			$comments = $db->get_var("SELECT count(*) FROM mangadex_comments_groups WHERE group_id = $id "); //count comments
			
			$db->query(" UPDATE mangadex_groups SET group_comments = $comments WHERE group_id = $id LIMIT 1; ");
			
			$details = $id;
			print display_alert("success", "Success", "You have added a comment to Group $id."); //success
		}
		else {
			$details = "You can't comment on group $id.";
			print display_alert("danger", "Failed", $details); //fail
		}
		
		$result = (!is_numeric($details)) ? 0 : 1;
		break;

	case "group_comment_delete":
		$id = sanitise_id($_GET["id"]);
		
		$comment = new Comment($db, $id, 2);
		
		if ($comment->exists) {
			if ($user->level_id >= 10 || $comment->user_id == $user->user_id) {
				$db->query(" UPDATE mangadex_comments_groups SET comment_del = 1 WHERE comment_id = $id LIMIT 1; "); //delete 
				
				$count = $db->get_var("SELECT count(*) FROM mangadex_comments_groups WHERE group_id = $comment->group_id AND comment_del = 0; ");	
				
				$db->query(" UPDATE mangadex_groups SET group_comments = $count WHERE group_id = $comment->group_id LIMIT 1; "); //update comments
				
				$details = $comment->group_id;
				print display_alert("success", "Success", "Comment $id has been deleted."); // success
			}
			else {
				$details = "You can't delete Comment $id.";
				print display_alert("danger", "Failed", $details); // fail
			}
		}
		else {
			$details = "Comment $id does not exist.";
			print display_alert("danger", "Failed", $details); // fail
		}
		
		$result = (!is_numeric($details)) ? 0 : 1;
		break;		

	case "admin_edit_group":
		$id = sanitise_id($_GET["id"]);
		$group_name = mysql_escape_mimic($_POST["group_name"]);	
		$group_leader_id = mysql_escape_mimic($_POST["group_leader_id"]);	
		
		if ($user->level_id >= 15) {
			$db->query(" UPDATE mangadex_groups SET group_name = '$group_name', group_leader_id = '$group_leader_id' WHERE group_id = $id LIMIT 1 ");
			$db->query(" UPDATE mangadex_users SET level_id = 4 WHERE user_id = $group_leader_id LIMIT 1 ");
			
			if ($group_leader_id > 1) {
				$db->query(" DELETE FROM mangadex_link_user_group WHERE group_id = $id AND role = 3 LIMIT 1; ");
				$db->query(" INSERT INTO mangadex_link_user_group (id, user_id, group_id, role) VALUES (NULL, '$group_leader_id', '$id', '3'); ");
			}
			
			$details = $id;
			print display_alert("success", "Success", "Group $id has been edited.");  //success		
		}
		else {
			$details = "You can't edit groups.";
			print display_alert("danger", "Failed", $details); //fail	
		}
		
		$result = (!is_numeric($details)) ? 0 : 1;
		break;

	case "group_delete":
		$id = sanitise_id($_GET["id"]);
		
		$group = new Group($db, $id);
		
		if ($group->exists) {
			if ($user->level_id >= 15) {
				$db->query(" DELETE FROM mangadex_groups WHERE group_id = $id LIMIT 1; "); //delete from actual table
				
				$details = $id;
				print display_alert("success", "Success", "Group #$id has been deleted."); // success
			}
			else {
				$details = "You can't delete Group #$id.";
				print display_alert("danger", "Failed", $details); // fail
			}
		}
		else {
			$details = "Group #$id does not exist.";
			print display_alert("danger", "Failed", $details); // fail
		}
		
		$result = (!is_numeric($details)) ? 0 : 1;
		break;
		
	/*
	// settings functions
	*/	
	
	case "change_password":
		$old_password = mysql_escape_mimic($_POST["old_password"]);
		$new_password1 = mysql_escape_mimic($_POST["new_password1"]);
		$new_password2 = mysql_escape_mimic($_POST["new_password2"]);	
		
		//$user = $db->get_row("SELECT username, email, password FROM mangadex_users WHERE user_id = $user->user_id "); //select the hash
		
		if (password_verify($old_password, $user->password)) { //verify the hash
			$password_test = ($new_password1 == $new_password2 && strlen($new_password1) >= 8); //return TRUE
			
			$new_hash = password_hash($new_password1, PASSWORD_DEFAULT);
			
			if ($password_test) {
				$db->query(" UPDATE mangadex_users SET password = '$new_hash' WHERE user_id = $user->user_id LIMIT 1 ");

				$to = $user->email;
				$subject = "MangaDex: Change Password - $user->username"; 
				$body = "You have successfully changed your password for MangaDex. \n\nUsername: $user->username \nPassword: ********  ";

				send_email($to, $subject, $body); 
				
				print display_alert("success", "Success", "Your password has been changed."); //success		
			}
			else {
				$details = "Your new password is too short.";
				print display_alert("danger", "Failed", $details); //too short		
			}
		}
		else {
			$details = "Incorrect password.";
			print display_alert("danger", "Failed", $details); //wrong password		
		}
		
		$result = ($details) ? 0 : 1;
		break;
	
	case "upload_settings":
		$lang_id = sanitise_id($_POST["lang_id"]);
		$group_id = sanitise_id($_POST["group_id"]) ?? 0;
		
		if ($user->user_id)
			$db->query(" UPDATE mangadex_users SET upload_group_id = $group_id, upload_lang_id = $lang_id WHERE user_id = $user->user_id LIMIT 1 ");
		else {
			$details = "Your session has timed out. Please log in again.";
			print display_alert("danger", "Failed", $details); //fail
		}
		
		$result = 1;
		break;

	case "reader_settings":
		$swipe_direction = !empty($_POST["swipe_direction"]) ? 1 : 0;
		$reader_click = !empty($_POST["reader_click"]) ? 1 : 0;
		$post_sensitivity = sanitise_id($_POST["swipe_sensitivity"]);
		$reader_mode = sanitise_id($_POST["reader_mode"]) ?? 0;
		
		$swipe_sensitivity = $post_sensitivity * 25 + 25;
		if ($swipe_sensitivity < 25)
			$swipe_sensitivity = 25;
		elseif ($swipe_sensitivity > 150)
			$swipe_sensitivity = 150;		
		
		if ($user->user_id)
			$db->query(" UPDATE mangadex_users SET swipe_direction = $swipe_direction, swipe_sensitivity = $swipe_sensitivity, reader_mode = $reader_mode, reader_click = $reader_click WHERE user_id = $user->user_id LIMIT 1 ");
		else {
			$details = "Your session has timed out. Please log in again.";
			print display_alert("danger", "Failed", $details); //fail
		}
		
		$result = 1;
		break;
		
	case "change_profile":
		$theme_id = sanitise_id($_POST["theme_id"]);
		$lang_id = sanitise_id($_POST["lang_id"]);
		$display_lang_id = sanitise_id($_POST["display_lang_id"]);
		$website = htmlentities(mysql_escape_mimic($_POST["website"]));	
		$old_file = htmlentities(mysql_escape_mimic($_POST["old_file"]));	
		
		$error = "";
				
		if ($old_file && $_FILES["file"])
			$error .= validate_image($_FILES["file"]);
		
		if (!$user->user_id)
			$error .= display_alert("danger", "Failed", "Your session has timed out. Please log in again."); //success	
		
		if (!$error) {
			$db->query(" UPDATE mangadex_users SET style = $theme_id, language = $lang_id, display_lang_id = $display_lang_id, user_website = '$website' WHERE user_id = $user->user_id LIMIT 1 ");
			
			if ($old_file) {
				$arr = (explode(".", $_FILES["file"]["name"]));
				$ext = strtolower(end($arr));
				
				if ($user->avatar)
					unlink(ABSPATH . "/images/avatars/$user->user_id.$user->avatar");
				
				move_uploaded_file($_FILES["file"]["tmp_name"], ABSPATH . "/images/avatars/$user->user_id.$ext");
				
				$db->query(" UPDATE mangadex_users SET avatar = '$ext' WHERE user_id = $user->user_id LIMIT 1; ");
			}
			
			$details = $user->user_id;			
		}
		else {
			$details = $error;
			print $error; //returns "" or a message
		}		
		
		$result = ($details) ? 0 : 1;
		break;

	case "filter_settings":
		$hentai_mode = sanitise_id($_POST["hentai_mode"]);	
		$default_lang_ids = (isset($_POST["default_lang_ids"])) ? implode(",", $_POST["default_lang_ids"]) : "";	
		
		if ($user->user_id)
			$db->query(" UPDATE mangadex_users SET hentai_mode = $hentai_mode, default_lang_ids = '$default_lang_ids' WHERE user_id = $user->user_id LIMIT 1 ");
		else {
			$details = "Your session has timed out. Please log in again.";
			print display_alert("danger", "Failed", $details); //fail
		}
		
		$result = 1;
		break;

	case "admin_edit_user":
		$id = sanitise_id($_GET["id"]);
		$level_id = sanitise_id($_POST["level_id"]);	
		$email = mysql_escape_mimic($_POST["email"]);
		$lang_id = sanitise_id($_POST["lang_id"]);
		$upload_lang_id = sanitise_id($_POST["upload_lang_id"]);
		$upload_group_id = sanitise_id($_POST["upload_group_id"]);
		
		if ($user->level_id >= 15) {
			$db->query(" UPDATE mangadex_users SET level_id = $level_id, email = '$email', language = $lang_id, upload_group_id = $upload_group_id, upload_lang_id = $upload_lang_id WHERE user_id = $id LIMIT 1 ");
						
			$details = $id;
			print display_alert("success", "Success", "User $id has been edited.");  //success		
		}
		else {
			$details = "You can't edit users.";
			print display_alert("danger", "Failed", $details); //fail	
		}
		
		$result = (!is_numeric($details)) ? 0 : 1;
		break;

	/*
	// forum functions
	*/	
	case "start_thread":
		$forum_id = sanitise_id($_GET["id"]);
		$subject = htmlentities(mysql_escape_mimic($_POST["subject"]));	
		$post_text = htmlentities(mysql_escape_mimic($_POST["post_text"]));	
		
		$error = "";
		
		if (!$subject)
			$error .= display_alert("danger", "Failed", "Your subject is empty."); 
		elseif(!$post_text)
			$error .= display_alert("danger", "Failed", "Your post is empty."); 
		elseif (!$user->user_id)
			$error .= display_alert("danger", "Failed", "Your session has timed out. Please log in again."); //success	
		
		if (!$error) {
			$db->query(" INSERT INTO mangadex_forum_threads (thread_id, thread_name, forum_id, user_id, thread_timestamp, thread_posts, thread_views, thread_locked, thread_sticky, thread_deleted, last_post_user_id, last_post_timestamp) VALUES (NULL, '$subject', $forum_id, $user->user_id, $timestamp, 1, 0, 0, 0, 0, $user->user_id, $timestamp); ");
			
			$db->query(" INSERT INTO mangadex_forum_posts (post_id, thread_id, user_id, post_timestamp, post_text, post_deleted) VALUES (NULL, LAST_INSERT_ID(), $user->user_id, $timestamp, '$post_text', 0); ");
			
			$details = $user->user_id;			
		}
		else {
			$details = $error;
			print $error; //returns "" or a message
		}		
		
		$result = ($details) ? 0 : 1;
		break;	

	case "post_reply":
		$thread_id = sanitise_id($_GET["id"]);
		$post_text = htmlentities(mysql_escape_mimic($_POST["post_text"]));	
		
		$error = "";
		
		if(!$post_text)
			$error .= display_alert("danger", "Failed", "Your post is empty."); 
		elseif (!$user->user_id)
			$error .= display_alert("danger", "Failed", "Your session has timed out. Please log in again."); //success	
		
		if (!$error) {
			$db->query(" INSERT INTO mangadex_forum_posts (post_id, thread_id, user_id, post_timestamp, post_text, post_deleted) VALUES (NULL, $thread_id, $user->user_id, $timestamp, '$post_text', 0); ");
			
			$db->query(" UPDATE mangadex_forum_threads SET thread_posts = thread_posts + 1, last_post_user_id = $user->user_id, last_post_timestamp = $timestamp WHERE thread_id = $thread_id LIMIT 1");
			
			$details = $user->user_id;			
		}
		else {
			$details = $error;
			print $error; //returns "" or a message
		}		
		
		$result = ($details) ? 0 : 1;
		break;	
		
	/*
	// message functions
	*/	
	
	case "msg_reply":
		$id = sanitise_id($_GET["id"]);
		
		$reply = htmlentities(mysql_escape_mimic($_POST["reply"]));	
		
		$thread = new PM_Thread($db, $id); 
		
		if ($user->user_id == $thread->sender_id || $user->user_id == $thread->recipient_id) {
			$db->query(" INSERT INTO mangadex_pm_msgs (msg_id, thread_id, user_id, msg_timestamp, msg_text) 
				VALUES (NULL, $id, $user->user_id, $timestamp, '$reply'); ");
			
			if ($thread->sender_id == $user->user_id) 
				$db->query(" UPDATE mangadex_pm_threads SET recipient_read = 0 WHERE thread_id = $id LIMIT 1 ");
			else 
				$db->query(" UPDATE mangadex_pm_threads SET sender_read = 0 WHERE thread_id = $id LIMIT 1 ");
			
			$details = $id;
			print display_alert("success", "Success", "You have added a reply to thread $id."); //success
		}
		else {
			$details = "You can't reply on thread $id.";
			print display_alert("danger", "Failed", $details); //fail
		}
		
		$result = (!is_numeric($details)) ? 0 : 1;
		break;

	case "msg_send":		
		$recipient = htmlentities(mysql_escape_mimic($_POST["recipient"]));	
		$subject = htmlentities(mysql_escape_mimic($_POST["subject"]));	
		$message = htmlentities(mysql_escape_mimic($_POST["message"]));	
		
		$recipient_id = $db->get_var(" SELECT user_id FROM mangadex_users WHERE username LIKE '$recipient' LIMIT 1; "); //does user exist?
		
		if ($recipient_id && $recipient_id != $user->user_id) {
			
			$db->query(" INSERT INTO mangadex_pm_threads (thread_id, thread_subject, sender_id, recipient_id, thread_timestamp, sender_read, recipient_read) 
				VALUES (NULL, '$subject', $user->user_id, $recipient_id, $timestamp, 1, 0); ");
			
			$thread_id = $db->get_var(" SELECT thread_id FROM mangadex_pm_threads WHERE sender_id = $user->user_id ORDER BY thread_timestamp DESC LIMIT 1; "); //get thread_id
			
			$db->query(" INSERT INTO mangadex_pm_msgs (msg_id, thread_id, user_id, msg_timestamp, msg_text) 
				VALUES (NULL, $thread_id, $user->user_id, $timestamp, '$message'); ");
			
			$details = $thread_id;
		}
		else {
			if (!$recipient_id)
				$details = "$recipient does not exist.";
			elseif ($recipient_id == $user->user_id)
				$details = "You can't send yourself a message.";
				
			print display_alert("danger", "Failed", $details); //fail
		}
		
		$result = (!is_numeric($details)) ? 0 : 1;
		break;
		
	/*
	// mod functions
	*/	
	
	case "report_accept":	
		$id = sanitise_id($_GET["id"]);
		
		if ($user->level_id >= 10) 
			$db->query(" UPDATE mangadex_chapter_reports SET report_conclusion = 1, report_mod_user_id = $user->user_id WHERE report_id = $id LIMIT 1 ");
		
		print display_alert("success", "Success", "Report #$id accepted.");  //success		
		
		$details = $id;
		$result = 1;
		break;	

	case "report_reject":	
		$id = sanitise_id($_GET["id"]);
		
		if ($user->level_id >= 10)
			$db->query(" UPDATE mangadex_chapter_reports SET report_conclusion = 2, report_mod_user_id = $user->user_id WHERE report_id = $id LIMIT 1 ");
		
		print display_alert("success", "Success", "Report #$id rejected.");  //success		
		
		$details = $id;
		$result = 1;
		break;	
		
	/*
	// other functions
	*/	

	case "read_announcement":	
		$db->query(" UPDATE mangadex_users SET read_announcement = 1 WHERE user_id = $user->user_id LIMIT 1 ");
		
		$result = 1;
		break;	
		
	default:
		$_GET["function"] = "No action";
		header("location: /");
		break;

}

$db->query("INSERT INTO mangadex_logs_actions (action_id, action_name, action_user_id, action_timestamp, action_ip, action_result, action_details) 
	VALUES (NULL, '{$_GET['function']}', $user->user_id, $timestamp, '$ip', $result, '$details'); ");
?>