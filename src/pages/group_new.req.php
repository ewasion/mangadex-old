<?php
$languages = new Languages($db, "lang_name", "ASC");

$group_by_lang = $db->get_results(" SELECT COUNT(*) AS Rows, mangadex_groups.group_lang_id, mangadex_languages.* FROM mangadex_groups,mangadex_languages WHERE mangadex_groups.group_lang_id = mangadex_languages.lang_id GROUP BY mangadex_groups.group_lang_id ORDER BY Rows DESC ");
?>

<ul style="margin-bottom: 20px; " class="nav nav-tabs">
	<li title="All languages" role="presentation"><a href="/groups/0"><?= display_glyphicon("globe", "fas", "All languages", "fa-fw") ?></a></li>
	<?php 
	foreach ($group_by_lang as $lang) 
		print "<li role='presentation' id='$lang->group_lang_id' data-src='$lang->lang_name'><a href='/groups/$lang->group_lang_id'>" . display_lang_flag_v2($lang->lang_name, $lang->lang_flag, array(5, 5, 5, 5)) . "</a></li>"; 
	?>
	<li title="Search groups" role="presentation" class="pull-right"><a href="/group_search"><?= display_glyphicon("search", "fas", "Search groups", "fa-fw") ?></a></li>
	<li title="Add new group" role="presentation" class="active pull-right"><a href="/group_new"><?= display_glyphicon("plus-circle", "fas", "Add new group", "fa-fw") ?></a></li>
</ul>

<div class="panel panel-default">	
	<div class="panel-heading">
		<h3 class="panel-title"><?= display_glyphicon("plus-circle", "fas", "", "fa-fw") ?> Add a group</h3>
	</div>
	<div class="panel-body">
		<form style="margin-top: 15px;" id="group_add_form" method="post" class="form-horizontal">
			<div class="form-group">
				<label for="name" class="col-sm-3 control-label"><?= display_glyphicon("users", "fas", "", "fa-fw") ?> Group name</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" id="group_name" name="group_name" placeholder="Group name" required>
				</div>
			</div>
			<div class="form-group">
				<label for="group_lang_id" class="col-sm-3 control-label"><?= display_glyphicon("globe", "fas", "", "fa-fw") ?> Language</label>
				<div class="col-sm-9">   
					<select required title="Select a language" class="form-control selectpicker" id="group_lang_id" name="group_lang_id">
						<?php 
						foreach ($languages as $language) {
							print "<option value='$language->lang_id'>$language->lang_name</option>"; 
						}
						?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label for="website" class="col-sm-3 control-label"><?= display_glyphicon("external-link-square-alt", "fas", "", "fa-fw") ?> Website</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" id="group_website" name="group_website" placeholder="(include http:// or https://) (Optional)">
				</div>
			</div>
			<div class="form-group">
				<label for="irc" class="col-sm-3 control-label"><?= display_glyphicon("hashtag", "fas", "", "fa-fw") ?> IRC channel</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" id="group_irc_channel" name="group_irc_channel" placeholder="# not required (Optional)">
				</div>
			</div>
			<div class="form-group">
				<label for="irc" class="col-sm-3 control-label"><?= display_glyphicon("hashtag", "fas", "", "fa-fw") ?> IRC server</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" id="group_irc_server" name="group_irc_server" placeholder="irc.rizon.net (Optional)">
				</div>
			</div>
			<div class="form-group">
				<label for="irc" class="col-sm-3 control-label"><?= display_glyphicon("discord", "fab", "", "fa-fw") ?> Discord</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" id="group_discord" name="group_discord" placeholder="Discord link (No need to include https://discord.gg/) (Optional)">
				</div>
			</div>
			<div class="form-group">
				<label for="email" class="col-sm-3 control-label"><?= display_glyphicon("envelope", "fas", "", "fa-fw") ?> Email</label>
				<div class="col-sm-9">
					<input type="email" class="form-control" id="group_email" name="group_email" placeholder="Email (Optional)">
				</div>
			</div>
			<div class="form-group">
				<label for="group_description" class="col-sm-3 control-label">Description</label>
				<div class="col-sm-9">
					<textarea class="form-control" rows="11" id="group_description" name="group_description" placeholder="(Optional)"></textarea>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-offset-3 col-sm-9">
					<button type="submit" class="btn btn-default" id="group_add_button"><?= display_glyphicon("plus-circle", "fas", "", "fa-fw") ?> Add new group</button>
				</div>
			</div>
		</form>	
	</div>
</div>	 

<div class="panel panel-default">	
	<div class="panel-heading">
		<h3 class="panel-title"><?= display_glyphicon("info-circle", "fas", "", "fa-fw") ?> Features</h3>
	</div>
	<div class="panel-body">
		<ul>
			<li>Anyone may add a group.</li>
			<li>If you are the leader of a group, you can be assigned "Group Leader" for your group on MangaDex. </li>
			<li>Group leaders may add members to their group.</li>
			<li>Group leaders are able to manage chapters attributed to their group.</li>
			<li>Group leaders will have the option of stopping non group members from uploading chapters to their group.</li>
		</ul>
	</div>
</div>  