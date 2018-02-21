<?php
$user_groups_array = $user->get_groups($db);

$languages = new Languages($db, "lang_name", "ASC");

$lang_id_filter_array = explode(",", $user->default_lang_ids);
?>


<!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist" style="margin-bottom: 15px">
	<li class="active" role="presentation"><a href="#change_profile" aria-controls="messages" role="tab" data-toggle="tab"><?= display_glyphicon("user", "fas", "", "fa-fw") ?> Change profile</a></li>
	<li role="presentation"><a href="#change_password" aria-controls="profile" role="tab" data-toggle="tab"><?= display_glyphicon("key", "fas", "", "fa-fw") ?> Change password</a></li>
	<li role="presentation"><a href="#upload_settings" aria-controls="settings" role="tab" data-toggle="tab"><?= display_glyphicon("upload", "fas", "", "fa-fw") ?> Upload settings</a></li>
	<li role="presentation"><a href="#reader_settings" aria-controls="reader" role="tab" data-toggle="tab"><?= display_glyphicon("book", "fas", "", "fa-fw") ?> Reader settings</a></li>
	<li role="presentation"><a href="#filter_settings" aria-controls="filter" role="tab" data-toggle="tab"><?= display_glyphicon("filter", "fas", "", "fa-fw") ?> Filter settings</a></li>
</ul>

<!-- Tab panes -->
<div class="tab-content">
	<div role="tabpanel" class="tab-pane fade in active" id="change_profile">
		<form class="form-horizontal" method="post" id="change_profile_form" enctype="multipart/form-data">
			<div class="form-group">
				<label for="username" class="col-sm-3 control-label">Username:</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" id="username" name="username" value="<?= $user->username ?>" title="Email anidex.moe@gmail.com to change this." disabled>
				</div>
			</div>
			<div class="form-group">
				<label for="email" class="col-sm-3 control-label">Email:</label>
				<div class="col-sm-9">
					<input type="email" class="form-control" id="email" name="email" value="<?= $user->email ?>" title="Email anidex.moe@gmail.com to change this." disabled>
				</div>
			</div>
			<div class="form-group">
				<label for="language" class="col-sm-3 control-label">Site theme:</label>
				<div class="col-sm-9">
					<select class="form-control selectpicker" id="theme_id" name="theme_id">
						<?php 
						foreach ($themes as $key => $theme) {
							$selected = ($user->style == $key) ? "selected" : "";
							print "<option $selected value='$key'>$theme</option>"; 
						}
						?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label for="website" class="col-sm-3 control-label">Website:</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" id="website" name="website" value="<?= $user->user_website ?>" placeholder="your-website.com (No need to type http://)">
				</div>
			</div>
			<div class="form-group">
				<label for="language" class="col-sm-3 control-label">Profile language:</label>
				<div class="col-sm-9">
					<select class="form-control selectpicker" id="lang_id" name="lang_id" data-size="10">
						<?php 
						foreach ($languages as $language) {
							$selected = ($language->lang_id == $user->language) ? "selected" : "";
							print "<option $selected value='$language->lang_id'>$language->lang_name</option>"; 
						}
						?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label for="display_language" class="col-sm-3 control-label">User interface language:</label>
				<div class="col-sm-9">
					<select class="form-control selectpicker" id="display_lang_id" name="display_lang_id" data-size="10">
						<?php 
						foreach ($languages as $language) {
							$selected = ($language->lang_id == $user->display_lang_id) ? "selected" : "";
							print "<option $selected value='$language->lang_id'>$language->lang_name</option>"; 
						}
						?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label for="avatar" class="col-sm-3 control-label">Avatar:</label>
				<div class="col-sm-9">
					<div class="input-group">
						<input type="text" class="form-control" placeholder="Leave blank if no change to image" readonly name="old_file">
						<span class="input-group-btn">
							<span class="btn btn-default btn-file">
								<?= display_glyphicon("folder-open", "far", "", "fa-fw") ?> <span class="span-1280">Browse</span> <input type="file" name="file" id="file">
							</span>
						</span>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label for="avatar" class="col-sm-3 control-label">Current avatar:</label>
				<div class="col-sm-9">
					<img alt="avatar" width="100px" src="/images/avatars/<?= $user->user_id ?>.<?= $user->avatar ?>?<?= $timestamp ?>" />
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-offset-3 col-sm-9">
					<button type="submit" class="btn btn-default" id="change_profile_button"><?= display_glyphicon("save", "fas", "", "fa-fw") ?> Save</button>
				</div>
			</div>
			
			
		</form>	
	</div>

	<div role="tabpanel" class="tab-pane fade" id="change_password">
		<form class="form-horizontal" method="post" id="change_password_form">
			<div class="form-group">
				<label for="old_password" class="col-sm-3 control-label">Old password:</label>
				<div class="col-sm-9">
					<input type="password" class="form-control" id="old_password" name="old_password" placeholder="Old password">
				</div>
			</div>
			<div class="form-group">
				<label for="new_password1" class="col-sm-3 control-label">New password:</label>
				<div class="col-sm-9">
					<input type="password" class="form-control" id="new_password1" name="new_password1" placeholder="New password">
				</div>
			</div>
			<div class="form-group">
				<label for="new_password2" class="col-sm-3 control-label">New password (again):</label>
				<div class="col-sm-9">
					<input type="password" class="form-control" id="new_password1" name="new_password2" placeholder="New password (again)">
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-offset-3 col-sm-9">
					<button type="submit" class="btn btn-danger" id="change_password_button"><?= display_glyphicon("pencil-alt", "fas", "", "fa-fw") ?> Change password</button>
				</div>
			</div>
		</form>	
	</div>
	
	<div role="tabpanel" class="tab-pane fade" id="upload_settings">
		<form class="form-horizontal" method="post" id="upload_settings_form">
			<div class="form-group">
				<label for="cat_id" class="col-sm-3 control-label">Default language:</label>
				<div class="col-sm-9">
					<select title="Select a language" class="form-control selectpicker" id="lang_id" name="lang_id" data-size="10">
						<?php 
						foreach ($languages as $language) {
							$selected = ($language->lang_id == $user->upload_lang_id) ? "selected" : "";
							print "<option $selected value='$language->lang_id'>$language->lang_name</option>"; 
						}
						?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label for="group_id" class="col-sm-3 control-label">Default upload as:</label>
				<div class="col-sm-9">
					<select class="form-control selectpicker" id="group_id" name="group_id">
						<option value="0">Individual</option>
						<?php 
						foreach ($user_groups_array as $group_id => $group_name) {
							$selected = ($group_id == $user->upload_group_id) ? "selected" : "";
							print "<option $selected value='$group_id'>$group_name</option>"; 
						}
						?>
					</select>
				</div>
			</div>
			
			<div class="form-group">
				<div class="col-sm-offset-3 col-sm-9">
					<button type="submit" class="btn btn-default" id="upload_settings_button"><?= display_glyphicon("save", "fas", "", "fa-fw") ?> Save</button>
				</div>
			</div>
			
			
		</form>	
	</div>

	<div role="tabpanel" class="tab-pane fade" id="reader_settings">
		<form class="form-horizontal" method="post" id="reader_settings_form">
			<div class="form-group">
				<label for="cat_id" class="col-sm-3 control-label">Reader mode:</label>
				<div class="col-sm-9">
					<select class="form-control selectpicker" id="reader_mode" name="reader_mode">
						<option <?= (!$user->reader_mode ? "selected" : "") ?> value="0">Normal</option>
						<option <?= ($user->reader_mode == 1 ? "selected" : "") ?> value="1">Infinite scroll (Doesn't work yet!)</option>
						<option <?= ($user->reader_mode == 2 ? "selected" : "") ?> value="2">Long strip (Load all images)</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label for="cat_id" class="col-sm-3 control-label">Reader click:</label>
				<div class="col-sm-9">
					<select class="form-control selectpicker" id="reader_click" name="reader_click">
						<option <?= ($user->reader_click ? "selected" : "") ?> value="1">Enabled (Click on image to go to next page or chapter)</option>
						<option <?= (!$user->reader_click ? "selected" : "") ?> value="0">Disabled</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label for="cat_id" class="col-sm-3 control-label">Swipe direction:</label>
				<div class="col-sm-9">
					<select class="form-control selectpicker" id="swipe_direction" name="swipe_direction">
						<option <?= ($user->swipe_direction ? "selected" : "") ?> value="1">Normal (Swipe left for next page, right for last page)</option>
						<option <?= (!$user->swipe_direction ? "selected" : "") ?> value="0">Reversed (Swipe right for next page, left for last page)</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label for="group_id" class="col-sm-3 control-label">Swipe sensitivity:</label>
				<div class="col-sm-9">
					<select class="form-control selectpicker" id="swipe_sensitivity" name="swipe_sensitivity">
						<option <?= (($user->swipe_sensitivity - 25) / 25 == 5 ? "selected" : "") ?> value="5">Very high</option>
						<option <?= (($user->swipe_sensitivity - 25) / 25 == 4 ? "selected" : "") ?> value="4">High</option>
						<option <?= (($user->swipe_sensitivity - 25) / 25 == 3 ? "selected" : "") ?> value="3">Normal</option>
						<option <?= (($user->swipe_sensitivity - 25) / 25 == 2 ? "selected" : "") ?> value="2">Low</option>
						<option <?= (($user->swipe_sensitivity - 25) / 25 == 1 ? "selected" : "") ?> value="1">Very low</option>
						<option <?= (($user->swipe_sensitivity - 25) / 25 == 0 ? "selected" : "") ?> value="0">Off</option>
					</select>
				</div>
			</div>
			
			<div class="form-group">
				<div class="col-sm-offset-3 col-sm-9">
					<button type="submit" class="btn btn-default" id="reader_settings_button"><?= display_glyphicon("save", "fas", "", "fa-fw") ?> Save</button>
				</div>
			</div>
			
			
		</form>	
	</div>
	
	<div role="tabpanel" class="tab-pane fade" id="filter_settings">
		<form class="form-horizontal" method="post" id="filter_settings_form">
			<div class="form-group">
				<label for="cat_id" class="col-sm-3 control-label">Hentai:</label>
				<div class="col-sm-9">
					<select class="form-control selectpicker" id="hentai_mode" name="hentai_mode">
						<option <?= ($user->hentai_mode == 0 ? "selected" : "") ?> value="0">Hide hentai toggle</option>
						<option <?= ($user->hentai_mode == 1 ? "selected" : "") ?> value="1">Show hentai toggle</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label for="cat_id" class="col-sm-3 control-label">Default filter languages:</label>
				<div class="col-sm-9">
					<select multiple class="form-control selectpicker show-tick" data-selected-text-format="count > 5" data-size="10" id="default_lang_ids" name="default_lang_ids[]" title="All langs">
						<?php 
						foreach ($languages as $key => $language) {
							$selected = (in_array($language->lang_id, $lang_id_filter_array)) ? "selected" : "";
							print "<option $selected value='$language->lang_id'>$language->lang_name</option>"; 
						}
						?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-offset-3 col-sm-9">
					<button type="submit" class="btn btn-default" id="filter_settings_button"><?= display_glyphicon("save", "fas", "", "fa-fw") ?> Save</button>
				</div>
			</div>
			
			
		</form>	
	</div>	
</div>