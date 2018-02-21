<?php
$_GET['user_id'] = $_GET['id'];
$id = $_GET['user_id'] ?? 2;

$id = sanitise_id($id);

$uploader = new User($db, $id, "user_id");

$total_uploaded = $uploader->get_total_chapters_uploaded($db);
$db->query(" UPDATE mangadex_users SET user_uploads = $total_uploaded WHERE user_id = $id LIMIT 1; ");


if ($uploader->exists && $id) {
	
	update_views($db, "user", $uploader->user_id, $ip, $user->user_id);

	$languages = new Languages($db, "lang_id", "ASC"); //dispaly_lang_flag
	//$languages = new Languages($db, "lang_name", "ASC");
	
	$user_groups_array = $uploader->get_groups($db);
	
	?>

	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?= display_glyphicon("user", "fas", "", "fa-fw") ?> <?= $uploader->username ?> <?= display_lang_flag($uploader->language, array(5, 5, 5, 5)) ?>
			<a target="_blank" href="/rss/user/<?= $id ?>"><span style="margin-top: -5px; margin-right: -5px;" class="fa fa-rss fa-2x pull-right"></span></a></h3>
		</div>
		<table class="table table-condensed ">
			<tr>
				<td width="150px" rowspan="5"><img alt="avatar" width="150px" src="/images/avatars/<?= $uploader->logo ?>" /></td>
				<th width="105px">User level:</th>
				<td><?= display_glyphicon("graduation-cap", "fas", "", "fa-fw") ?> <span style="color: #<?= $uploader->level_colour ?>; "><?= $uploader->level_name ?></span></td>
				<td width="1px" rowspan="5">
					<?= display_send_message($user->user_id, $uploader->user_id, $uploader->username) ?>
					<?= display_ban_user($user->level_id, $uploader->level_id) ?>
					<?= display_unban_user($user->level_id, $uploader->level_id) ?>
				</td>
			</tr>
			<tr>
				<th>Joined:</th>
				<td><?= display_glyphicon("calendar", "fas", "", "fa-fw") ?> <?= date("Y-m-d", $uploader->joined_timestamp) ?></td>
			</tr>
			<tr>
				<th>Last online:</th>
				<td><?= display_glyphicon("clock", "far", "", "fa-fw") ?> <?= get_time_ago($uploader->last_seen_timestamp) ?></td>
			</tr>
			<tr>
				<th>Group(s):</th>
				<td><?= display_user_groups_list($user_groups_array) ?></td>
			</tr>
			<tr>
				<th>Stats:</th>
				<td>
					<span class="text-info"><?= display_glyphicon("eye", "fas", "Views", "fa-fw") ?> <?= $uploader->user_views ?></span>
					<span><?= display_glyphicon("file", "far", "Chapters uploaded", "fa-fw") ?> <?= $uploader->user_uploads ?></span>
				</td>
			</tr>		
		</table>
	</div>

	<!-- Nav tabs -->
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#chapters" aria-controls="chapters" role="tab" data-toggle="tab"><?= display_glyphicon("file", "far", "", "fa-fw") ?> Chapters</a></li>
		<?php if ($user->level_id >= 15) { ?>
		<li role="presentation"><a href="#admin" aria-controls="admin" role="tab" data-toggle="tab"><?= display_glyphicon("edit", "fas", "", "fa-fw") ?> Admin</a></li>
		<?php } ?>
	</ul>
	

	<!-- Tab panes -->
	<div class="tab-content">
		<div role="tabpanel" class="tab-pane fade in active" id="chapters">
			<?php 
			$order = "upload_timestamp desc";
			$search["chapter_deleted"] = 0;
			unset($_GET["lang_id"]);
			require_once(ABSPATH . "/pages/chapters.req.php"); 
			?>
		</div>
		
		<?php if ($user->level_id >= 15) { ?>
		<div role="tabpanel" class="tab-pane fade" id="admin">
			<form style="margin: 15px auto;" class="form-horizontal" method="post" id="admin_edit_user_form">
				<div class="form-group">
					<label for="level_id" class="col-sm-4 control-label">User level:</label>
					<div class="col-sm-8">
						<select class="form-control selectpicker" id="level" name="level_id">
						<?php 
						$levels = new User_levels ($db);
						
						foreach ($levels as $key => $level) {
							$key++;
							$selected = ($uploader->level_id == $level->level_id) ? "selected" : "";
							print "<option value='$level->level_id' $selected>$level->level_name</option>"; 
						}
						?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="email" class="col-sm-4 control-label">Email address:</label>
					<div class="col-sm-8">
						<input type="email" class="form-control" id="email" name="email" value="<?= $uploader->email ?>" />
					</div>
				</div>
				<div class="form-group">
					<label for="language" class="col-sm-4 control-label">Language:</label>
					<div class="col-sm-8">
						<select class="form-control selectpicker" id="lang_id" name="lang_id" data-size="10">
							<?php 
							foreach ($languages as $language) {
								$selected = ($language->lang_id == $uploader->language) ? "selected" : "";
								print "<option $selected value='$language->lang_id'>$language->lang_name</option>"; 
							}
							?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="cat_id" class="col-sm-4 control-label">Default upload language:</label>
					<div class="col-sm-8">
						<select class="form-control selectpicker" id="upload_lang_id" name="upload_lang_id" data-size="10">
							<?php 
							foreach ($languages as $language) {
								$selected = ($language->lang_id == $uploader->upload_lang_id) ? "selected" : "";
								print "<option $selected value='$language->lang_id'>$language->lang_name</option>"; 
							}
							?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="group_id" class="col-sm-4 control-label">Default upload as:</label>
					<div class="col-sm-8">
						<select class="form-control selectpicker" id="upload_group_id" name="upload_group_id">
							<option value="0">Individual</option>
							<?php 
							foreach ($user_groups_array as $group_id => $group_name) {
								$selected = ($group_id == $uploader->upload_group_id) ? "selected" : "";
								print "<option $selected value='$group_id'>$group_name</option>"; 
							}
							?>
						</select>
					</div>
				</div>				
				<div class="form-group">
					<div class="col-sm-offset-4 col-sm-8">
						<button type="submit" class="btn btn-default" id="admin_edit_user_button"><?= display_glyphicon("edit", "fas", "", "fa-fw") ?> Save</button>
					</div>
				</div>
			</form>
		</div>
		<?php } ?>
		
	</div>

<?php 
}
else print "<div class='alert alert-danger text-center' role='alert'>" . display_glyphicon("exclamation-triangle", "fas") . " <strong>Warning:</strong> User $id does not exist.</div>";
?>