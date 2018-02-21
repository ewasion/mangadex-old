<?php 
if ($user->user_id) {
	print js_display_file_select();

	print jquery_post("change_password", 0, "save", "Change password", "Changing", "Your password has been changed.", "settings");
	print jquery_post("reader_settings", 0, "save", "Save", "Saving", "Your reader settings have been saved.", "settings");
	print jquery_post("upload_settings", 0, "save", "Save", "Saving", "Your upload settings have been saved.", "settings");
	print jquery_post("change_profile", 0, "save", "Save", "Saving", "Your profile settings have been saved.", "settings");
	print jquery_post("filter_settings", 0, "save", "Save", "Saving", "Your filter settings have been saved.", "settings");
} 
?>