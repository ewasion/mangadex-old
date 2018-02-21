<?php
/* display_lang_flag($lang_id, $border_radius_array)
 * display_cat_image($cat_id, $border_radius_array)
 * display_auth($auth)
 * display_ban_user($user_level_id, $target_user_level_id) 
 * display_unban_user($user_level_id, $target_user_level_id) 
 * display_edit_group($user_level_id, $user_id, $group_leader_id)
 * display_delete_torrent($user_level_id, $torrent_user_id, $user_id, $group_leader_id)
 * display_edit_torrent($user_level_id, $torrent_user_id, $user_id, $group_leader_id)
 * display_labels($batch, $hentai, $raw, $reencode, $none)
 * display_glyphicon($name, $set = "glyphicon", $title = "")
 * display_stacked_glyphicons($bg, $overlay)
 * display_group_members_list($group_members_array)
 * display_user_groups_list($user_groups_array)
 */
 
function jquery_post($name, $id, $button_glyph, $button_text, $button_text_alt, $success_msg, $location) {
	return "
	$('#{$name}_form').submit(function(event) {

		var formData = new FormData($(this)[0]);
		
		var success_msg = \"<div class='alert alert-success text-center' role='alert'><strong>Success:</strong> $success_msg</div>\";
		
		$('#{$name}_button').html(\"" . display_glyphicon("spinner", "fas", "", "fa-pulse fa-fw") . " $button_text_alt...\").attr('disabled', true);
		
		$.ajax({
			url: '/ajax/actions.ajax.php?function=$name&id=$id',
			type: 'POST',
			data: formData,
			success: function (data) {
				if (!data) {
					$('#message_container').html(success_msg).show().delay(" . FADE_DURATION . ").fadeOut();
					location.href = '/$location';
				}
				else {
					$('#message_container').html(data).show().delay(" . FADE_DURATION . ").fadeOut();
					$('#{$name}_button').html(\"" . display_glyphicon($button_glyph, "fas", "", "fa-fw") . " $button_text\").attr('disabled', false);
				}
			},
			cache: false,
			contentType: false,
			processData: false
		});

		event.preventDefault();
	});	";
}
 
function js_display_file_select() {
	return "
	$('.btn-file :file').on('fileselect', function(event, numFiles, label) {
		var input = $(this).parents('.input-group').find(':text');
		log = numFiles > 1 ? numFiles + ' files selected' : label;
		
		if(input.length)
			input.val(log);
		else 
			if(log) alert(log);
	});";
}
 
function display_genres($genre_array) {
	$text = "";
	
	global $genres; //obj 
	foreach ($genre_array as $genre) {
		$text .= "<span class='label label-default'><a class='genre' href='/?page=search&genres=$genre'>" . $genres->{$genre - 1}->genre_name . "</a></span> ";
	}
	
	return $text;
}
 
function display_sort($page, $search, $s, $o, $type, $sort_type) {
	
	
	switch ($page) {
		case "group":
			if ($search['group_id']) $search_group_id = "&id={$search['group_id']}";
			if ($search['filename']) $search_filename = "&q={$search['filename']}";
				
			if ($s != $type) 
				return "<a href='/?page=group$search_group_id$search_filename&s=$type&o=desc'>" . display_glyphicon("sort", "fas", "Sort Desc") . "</a>";
			elseif ($s == $type && $o == "desc")
				return "<a href='/?page=group$search_group_id$search_filename&s=$type&o=asc'>" . display_glyphicon("sort-$sort_type-up", "fas", "Sort Asc") . "</a>";
			else
				return "<a href='/?page=group$search_group_id$search_filename&s=$type&o=desc'>" . display_glyphicon("sort-$sort_type-down", "fas", "Sort Desc") . "</a>";
			
			break;
		
		case "user":
			if ($search['uploader']) $search_uploader = "&id={$search['uploader']}";
			if ($search['filename']) $search_filename = "&q={$search['filename']}";
			
			if ($s != $type) 
				return "<a href='/?page=user$search_uploader$search_filename&s=$type&o=desc'>" . display_glyphicon("sort", "fas", "Sort Desc") . "</a>";
			elseif ($s == $type && $o == "desc")
				return "<a href='/?page=user$search_uploader$search_filename&s=$type&o=asc'>" . display_glyphicon("sort-$sort_type-up", "fas", "Sort Asc") . "</a>";
			else
				return "<a href='/?page=user$search_uploader$search_filename&s=$type&o=desc'>" . display_glyphicon("sort-$sort_type-down", "fas", "Sort Desc") . "</a>";
			
			break;
			
		case "search":
			if ($search['group_id']) $search_group_id = "&group_id={$search['group_id']}";
			if ($search['lang_id']) $search_lang_id = "&lang_id={$search['lang_id']}";
			if ($search['filename']) $search_filename = "&q={$search['filename']}";
			if ($search['category']) $search_category = "&id={$search['category']}";
			if (isset($search['reencode']) && $search['reencode'] == 0) $search_reencode = "&r=1";
			if ($search['batch']) $search_batch = "&b=1";
			if ($search['authorised']) $search_authorised = "&a=1";
				
			if ($s != $type) 
				return "<a href='/?page=search$search_category$search_lang_id$search_group_id$search_filename$search_reencode$search_batch$search_authorised&s=$type&o=desc'>" . display_glyphicon("sort", "fas", "Sort Desc") . "</a>";
			elseif ($s == $type && $o == "desc")
				return "<a href='/?page=search$search_category$search_lang_id$search_group_id$search_filename$search_reencode$search_batch$search_authorised&s=$type&o=asc'>" . display_glyphicon("sort-$sort_type-up", "fas", "Sort Asc") . "</a>";
			else
				return "<a href='/?page=search$search_category$search_lang_id$search_group_id$search_filename$search_reencode$search_batch$search_authorised&s=$type&o=desc'>" . display_glyphicon("sort-$sort_type-down", "fas", "Sort Desc") . "</a>";
			
			break;
			
		default:
			if (isset($search['category'])) $search_category = "&id={$search['category']}";
			
			if ($s != $type) 
				return "<a href='/?q={$search['filename']}$search_category&s=$type&o=desc'>" . display_glyphicon("sort", "fas", "Sort Desc") . "</a>";
			elseif ($s == $type && $o == "desc")
				return "<a href='/?q={$search['filename']}$search_category&s=$type&o=asc'>" . display_glyphicon("sort-$sort_type-up", "fas", "Sort Asc") . "</a>";
			else
				return "<a href='/?q={$search['filename']}$search_category&s=$type&o=desc'>" . display_glyphicon("sort-$sort_type-down", "fas", "Sort Desc") . "</a>";
			
			break;
	}
	
	
}		
						
function display_active($get, $page) {
	if ($get == $page) return "active";
}

function display_alert($type, $strong, $text) {
	return "<div class='alert alert-$type text-center' role='alert'><strong>$strong:</strong> $text</div>";
}

function display_lang_flag_v2($lang_name, $lang_flag, $border_radius_array) {
	return "<img style='display: inline-block; vertical-align: middle; border-radius: $border_radius_array[0]px $border_radius_array[1]px $border_radius_array[2]px $border_radius_array[3]px;' src='/images/flags/$lang_flag.png' alt='$lang_name' title='$lang_name' />";
}

function display_lang_flag($lang_id, $border_radius_array) {
	global $languages; //obj 
	
	$lang_id--; //obj key starts at 0!
	$lang_flag = $languages->{$lang_id}->lang_flag;
	$lang_name = $languages->{$lang_id}->lang_name;
	
	return "<img style='display: inline-block; vertical-align: middle; border-radius: $border_radius_array[0]px $border_radius_array[1]px $border_radius_array[2]px $border_radius_array[3]px;' src='/images/flags/$lang_flag.png' alt='$lang_name' title='$lang_name' />";
}

function display_flag($private, $authorised, $trusted) {
	if ($private)
		return display_glyphicon("user-secret", "fas", "Hidden", "fa-fw");
	elseif ($authorised) 
		return display_glyphicon("check", "fas", "Authorised", "fa-fw text-success");
	elseif ($trusted) 
		return display_glyphicon("smile", "far", "Trusted", "fa-fw text-success");
	else 
		return "";
	
}

function display_send_message($user_id, $uploader_id, $username) {
	if ($user_id != $uploader_id) 
		return "<a style='float: right;' class='btn btn-default' role='button' href='/send_message/$username'>" . display_glyphicon("envelope", "fas", "Send message", "fa-fw") . " <span class='span-1280'>Send message</span></a>";
}

function display_ban_user($user_level_id, $target_user_level_id) {
	if ($user_level_id >= 15 && $target_user_level_id < 15 && $target_user_level_id > 0) 
		return "<a style='float: right;' class='btn btn-danger' id='ban_button' role='button' href='#'>" . display_glyphicon("lock", "fas", "Ban", "fa-fw") . " <span class='span-1280'>Ban</span></a>";
}

function display_unban_user($user_level_id, $target_user_level_id) {
	if ($user_level_id >= 15 && !$target_user_level_id) 
		return "<a style='float: right;' class='btn btn-danger' id='unban_button' role='button' href='#'>" . display_glyphicon("unlock", "fas", "Unban", "fa-fw") . " <span class='span-1280'>Unban</span></a>";
}

function display_auth_rescan($user_level_id, $user_id, $group_leader_id) {
	if ($user_level_id >= 15 || $group_leader_id == $user_id) 
		return "<button class='btn btn-success' id='scan_button'>" . display_glyphicon("sync", "fas", "Auth rescan", "fa-fw") . " <span class='span-1280'>Auth rescan</span></button>";
}

function display_edit_manga($user_level_id, $locked) { 
	if ($user_level_id >= 3) 
		return "<button " . (($locked && $user_level_id < 10) ? "disabled='disabled' title='Editing has been locked to mods only.'" : "") . "class='btn btn-info' id='edit_button'>" . display_glyphicon("pencil-alt", "fas", "Edit", "fa-fw") . " <span class='span-1280'>Edit</span></button>";
}

function display_lock_manga($user_level_id, $locked) { 
	if ($user_level_id >= 10) {
		if ($locked)
			return "<button class='btn btn-warning' id='unlock_button'>" . display_glyphicon("lock-open", "fas", "Unlock", "fa-fw") . " <span class='span-1280'>Unlock</span></button>";
		else 
			return "<button class='btn btn-warning' id='lock_button'>" . display_glyphicon("lock", "fas", "Lock", "fa-fw") . " <span class='span-1280'>Lock</span></button>";
	}	
}

function display_delete_manga($user_level_id) {
	if ($user_level_id >= 15) 
		return "<button class='btn btn-danger pull-right' id='delete_button'>" . display_glyphicon("trash", "fas", "Delete", "fa-fw") . " <span class='span-1280'>Delete</span></button>";
}

function display_edit_group($user_level_id, $user_id, $group_leader_id) {
	if ($user_level_id >= 10 || $group_leader_id == $user_id) 
		return "<button class='btn btn-info' id='edit_button'>" . display_glyphicon("pencil-alt", "fas", "Edit", "fa-fw") . " <span class='span-1280'>Edit</span></button>";
}

function display_delete_group($user_level_id) {
	if ($user_level_id >= 15) 
		return "<button class='btn btn-danger pull-right' id='delete_button'>" . display_glyphicon("trash", "fas", "Delete", "fa-fw") . " <span class='span-1280'>Delete</span></button>";
}

function display_edit_group_members($user_level_id, $user_id, $group_leader_id) {
	if ($user_level_id >= 15 || $group_leader_id == $user_id) 
		return "<button class='btn btn-info' id='edit_members_button'>" . display_glyphicon("pencil-alt", "fas", "Edit members", "fa-fw") . " <span class='span-1280'>Edit members</span></button>";
}

function display_new_thread($user_level_id) {
	if ($user_level_id >= 3) 
		return "<button class='btn btn-default new_thread_button'>" . display_glyphicon("edit", "fas", "New thread", "fa-fw") . " <span class='span-1280'>New thread</span></button>";
}

function display_post_reply($user_level_id) {
	if ($user_level_id >= 3) 
		return "<button class='btn btn-default post_reply_button'>" . display_glyphicon("edit", "fas", "New thread", "fa-fw") . " <span class='span-1280'>Post reply</span></button>";
}

function display_delete_comment($user_level_id, $comment_user_id, $user_id, $comment_id) {
	if ($user_level_id >= 10 || $comment_user_id == $user_id)
		return "<button title='Delete comment' id='$comment_id' type='button' class='delete_comment_button btn btn-danger'>" . display_glyphicon("trash", "fas", "", "fa-fw") . "</button>";
}

function display_labels($hentai) {
	if ($hentai) 
		return "<span class='label label-danger'>H</span>";
}

function display_labels_rss($batch, $hentai, $reencode, $none) {
	$label = "";
	
	if (!$batch && !$hentai && !$reencode && $none) $label .= " None";
	else {
		if ($batch) $label .= " Batch";
		if ($hentai) $label .= " Hentai";
		if ($reencode) $label .= " Remake";
	}
	return $label;
}

function display_glyphicon($name, $set = "glyphicon", $title = "", $class = "") {
	return "<span class='$set fa-$name $class' aria-hidden='true' title='$title'></span>";
}

function display_stacked_glyphicons($bg, $overlay) {
	return "<span class='fa-stack fa-lg'><span class='fa fa-$bg fa-stack-2x'></span><span class='fa fa-$overlay fa-stack-1x fa-inverse'></span></span>";
}


function display_group_members_list($group_members_array) {
	$text = "";
	foreach ($group_members_array as $user_id => $username) {
		$text .= display_glyphicon("user", "fas", "", "fa-fw") . " <a class='uploader' id='$user_id' data-src='$username' href='/user/$user_id'>$username</a> ";
	}
	return $text;
}

function display_delete_group_members_list($group_members_array) {
	$text = "";
	foreach ($group_members_array as $user_id => $username) {
		$text .= display_glyphicon("user", "fas", "", "fa-fw") . " <a class='uploader' id='$user_id' href='/user/$user_id'>$username</a> <a href='#' class='group_delete_member' id='$user_id'>" . display_glyphicon("trash", "fas", "", "fa-fw") . "</a>";
	}
	return $text;
}

function display_user_groups_list($user_groups_array) {
	if ($user_groups_array) {
		$text = "";
		foreach ($user_groups_array as $group_id => $group_name) 
			$text .= display_glyphicon("users", "fas", "", "fa-fw") . " <a class='group' id='$group_id' data-src='$group_name' href='/group/$group_id'>$group_name</a> ";
	}
	else 
		$text = "None";
	
	return $text;
}

function display_like_button($user_id, $ip, $array_of_user_id_ip) {
	if (($user_id && !in_array($user_id, $array_of_user_id_ip["user_id"])) || (!$user_id && !in_array($ip, $array_of_user_id_ip["ip"]))) //(user_id > 0 and user_id not in array) or (user_id = 0 and ip not in array)
		$text = "<button class='btn btn-success' id='like_button'>" . display_glyphicon("thumbs-up", "far", "Like", "fa-fw") . " <span class='span-1280'>Like</span></button>";
	else $text = "<button class='btn btn-danger' id='unlike_button'>" . display_glyphicon("thumbs-down", "far", "Unlike", "fa-fw") . " <span class='span-1280'>Unlike</span></button>";
	
	return $text;
}

function display_follow_button($user_id, $array_of_user_ids) {
	if ($user_id) {
		if (!in_array($user_id, $array_of_user_ids))
			$text = "<button class='btn btn-success' id='follow_button'>" . display_glyphicon("bookmark", "fas", "Follow", "fa-fw") . " <span class='span-1280'>Follow</span></button>";
		else 
			$text = "<button class='btn btn-danger' id='unfollow_button'>" . display_glyphicon("bookmark", "fas", "Unfollow", "fa-fw") . " <span class='span-1280'>Unfollow</span></button>";	
	}
	else {
		$text = "<button class='btn btn-success' disabled title='You need to log in to use this function.'>" . display_glyphicon("bookmark", "fas", "Follow", "fa-fw") . " <span class='span-1280'>Follow</span></button>";
	}
	
	return $text;
}

function display_upload_button($user_id) {
	if ($user_id) {
		$text = "<button class='btn btn-default' id='upload_button'>" . display_glyphicon("upload", "fas", "Upload", "fa-fw") . " <span class='span-1280'>Upload chapter</span></button>";	
	}
	else {
		$text = "<button class='btn btn-default' id='upload_button' disabled title='You need to log in to use this function.'>" . display_glyphicon("upload", "fas", "Upload", "fa-fw") . " <span class='span-1280'>Upload chapter</span></button>";	
	}
	
	return $text;
}
?>