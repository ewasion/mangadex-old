<?php
$_GET['group_id'] = $_GET['id'];
$id = $_GET['group_id'] ?? 1;

$id = sanitise_id($id);

$group_delay_array = array(0 => "None", 3600 => "1 hour", 7200 => "2 hours", 10800 => "3 hours", 14400 => "4 hours", 18000 => "5 hours", 21600 => "6 hours", 43200 => "12 hours", 86400 => "1 day", 172800 => "2 days", 259200 => "3 days", 345600 => "4 days", 432000 => "5 days",518400 => "6 days", 604800 => "1 week");
									
$group = new Group($db, $id);

if ($group->exists) {
	
	update_views($db, "group", $group->group_id, $ip, $user->user_id);

	$languages = new Languages($db, "lang_id", "ASC");

	$group_members_array = $group->get_members($db);
	
	require_once (ABSPATH . "/scripts/JBBCode/Parser.php");
	$parser = new JBBCode\Parser();
	$parser->addCodeDefinitionSet(new JBBCode\DefaultCodeDefinitionSet());
	$parser->parse($group->group_description);
	
	?>


	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?= display_glyphicon("users", "fas", "", "fa-fw") ?> <?= $group->group_name ?> <?= display_lang_flag_v2($group->lang_name, $group->lang_flag, array(5, 5, 5, 5)) ?> <?= $group->likes ?>
			<a target="_blank" href="/rss/group/<?= $id ?>"><span style="margin-top: -5px; margin-right: -5px;" class="fa fa-rss fa-2x pull-right"></span></a></h3>
		</div>
		<?= ($group->group_banner) ? "<img style='width: 100%' src='/images/groups/$group->group_id.$group->group_banner?$timestamp' />" : "" ?>
	</div>
	<div class="row">
		<div class="col-md-6">
			<div class="panel panel-default edit">
				<div class="panel-heading">
					<h3 class="panel-title"><?= display_glyphicon("info-circle", "fas", "", "fa-fw") ?> Group info</h3>
				</div>
				<table class="table table-condensed ">
					<tr>
						<th width="150px">Founded:</th>
						<td><span><?= display_glyphicon("calendar-alt", "fas", "", "fa-fw") ?> <?= $group->group_founded ?></span></td>
					</tr>
					<tr>
						<th>Stats:</th>
						<td><span class="text-info"><?= display_glyphicon("eye", "fas", "", "fa-fw") ?> <?= $group->group_views ?></span></td>
					</tr>
					<tr>
						<th>Links:</th>
						<td><?php if ($group->group_website) { ?>
								<a target="_blank" href="<?= $group->group_website ?>"><?= display_glyphicon("external-link-square-alt", "fas", "Website", "fa-lg fa-fw") ?></a>
							<?php } else { ?>
								<?= display_glyphicon("external-link-square-alt", "fas", "Website", "fa-lg fa-fw") ?>
							<?php } 
							
								if ($group->group_discord) { ?>
							<a target="_blank" href="https://discord.gg/<?= $group->group_discord ?>"><?= display_glyphicon("discord", "fab", "Discord", "fa-lg fa-fw") ?></a>
							<?php } else { ?>
								<?= display_glyphicon("discord", "fab", "Discord", "fa-lg fa-fw") ?>
							<?php }
							
								if ($group->group_irc_channel) { ?>
							<a target="_blank" href="<?= $group->irc_link ?>"><?= display_glyphicon("hashtag", "fas", "IRC", "fa-lg fa-fw") ?></a>
							<?php } else { ?>
								<?= display_glyphicon("hashtag", "fas", "IRC", "fa-lg fa-fw") ?>
							<?php } 
								
								if ($group->group_email) { ?>
							<a target="_blank" href="mailto:<?= $group->group_email ?>"><?= display_glyphicon("envelope", "fas", "Email", "fa-lg fa-fw") ?></a>
							<?php } else { ?>
								<?= display_glyphicon("envelope", "fas", "Email", "fa-lg fa-fw") ?>
							<?php } ?></td>
					</tr>
					<tr>
						<th>Upload restriction:</th>
						<td><?= ($group->group_control ? "<span class='label label-warning'>Group members only</span>" : "<span class='label label-success'>None</span>") ?></td>
					</tr>
					<tr>
						<th>Group delay:</th>
						<td><?= ($group->group_delay ? "<span class='label label-warning'>" . $group_delay_array[$group->group_delay] . "</span>" : "<span class='label label-success'>None</span>") ?></td>
					</tr>
					<tr>
						<th>Actions:</th>
						<td>
							<?php // display_like_button($user->user_id, $ip, $group->get_likes_user_id_ip_list($db)) 
							print "like button temp disabled"; ?>
							<button type="button" class="btn btn-success" id="follow_button" disabled title="Future function"><?= display_glyphicon("bookmark", "fas", "", "fa-fw") ?> Follow</button>
							<?= display_edit_group($user->level_id, $user->user_id, $group->group_leader_id) ?>
							<?= display_delete_group($user->level_id) ?>
						</td>
					</tr>
				</table>
			</div>
			
			<?php if ($user->level_id >= 10 || $group->group_leader_id == $user->user_id) { //only display for relevant people ?>
			<div class="panel panel-default edit display-none">
				<div class="panel-heading">
					<h3 class="panel-title"><?= display_glyphicon("info-circle", "fas", "", "fa-fw") ?> Edit group info</h3>
				</div>
				<form id="group_edit_form" method="post">
					<table class="table table-condensed ">
						<tr>
							<th width="150px">Banner URL:</th>
							<td>
								<div class="input-group">
									<input type="text" class="form-control" placeholder="Leave blank if no change to image" readonly name="old_file">
									<span class="input-group-btn">
										<span class="btn btn-default btn-file">
											<?= display_glyphicon("folder-open", "far", "", "fa-fw") ?> <span class="span-1280">Browse</span> <input type="file" name="file" id="file">
										</span>
									</span>
								</div>
							</td>
						</tr>
						<tr>
							<th>Language:</th>
							<td><select class="form-control selectpicker" id="lang_id" name="lang_id" data-size="10">
									<?php 
									$languages_alpha = new Languages($db, "lang_name", "ASC");
									foreach ($languages_alpha as $key => $language) {
										$key++;
										$selected = ($language->lang_id == $group->group_lang_id) ? "selected" : "";
										print "<option $selected value='$language->lang_id'>$language->lang_name</option>"; 
									}
									?>
								</select></td>
						</tr>
						<tr>
							<th>Founded:</th>
							<td><input type="date" class="form-control" id="group_founded" name="group_founded" value="<?= $group->group_founded ?>"></td>
						</tr>
						<tr>
							<th>Website:</th>
							<td><input type="text" class="form-control" id="url_link" name="url_link" value="<?= $group->group_website ?>" placeholder="http:// or https:// required"></td>
						</tr>
						<tr>
							<th>IRC Channel:</th>
							<td><input type="text" class="form-control" id="irc_channel" name="irc_channel" value="<?= $group->group_irc_channel ?>" placeholder="# not required"></td>
						</tr>
						<tr>
							<th>IRC Server:</th>
							<td><input type="text" class="form-control" id="irc_server" name="irc_server" value="<?= $group->group_irc_server ?>" placeholder="irc.rizon.net"></td>
						</tr>
						<tr>
							<th>Discord:</th>
							<td><input type="text" class="form-control" id="discord" name="discord" value="<?= $group->group_discord ?>" placeholder="No need for https://discord.gg/"></td>
						</tr>
						<tr>
							<th>Contact:</th>
							<td><input type="text" class="form-control" id="group_email" name="group_email" value="<?= $group->group_email ?>" placeholder="x@x.x"></td>
						</tr>
						<tr>
							<th>Upload restriction:</th>
							<td>
								<div class="checkbox">  
									<label><input type="checkbox" name="group_control" id="group_control" value="1" <?= ($group->group_control) ? "checked" : "" ?>></label>
								</div>
							</td>
						</tr>
						<tr>
							<th>Group delay:</th>
							<td><select class="form-control selectpicker" id="group_delay" name="group_delay" data-size="10">
									<?php 
									foreach ($group_delay_array as $seconds => $time) {
										$selected = ($seconds == $group->group_delay) ? "selected" : "";
										print "<option $selected value='$seconds'>$time</option>"; 
									}
									?>
								</select></td>
						</tr>
						<tr>
							<th>Description:</th>
							<td><textarea rows="10" type="text" class="form-control" id="group_description" name="group_description" placeholder="BBCode allowed"><?= $group->group_description ?></textarea></td>
						</tr>
						<tr>
							<th>Actions:</th>
							<td>
								<button type="submit" class="btn btn-success" id="group_edit_button"><?= display_glyphicon("pencil-alt", "fas", "", "fa-fw") ?> Save</button>
								<button class="btn btn-warning" id="cancel_edit_button"><?= display_glyphicon("times", "fas", "", "fa-fw") ?> Cancel</button>
							</td>
						</tr>
					</table>
				</form>
			</div>
			<?php } ?>
			
		</div>
		
		<div class="col-md-6">
			<div class="panel panel-default edit-members">	
				<div class="panel-heading">
					<h3 class="panel-title"><?= display_glyphicon("user", "fas", "", "fa-fw") ?> Member info</h3>
				</div>
				<table class="table table-condensed ">
					<tr>
						<th width="140px">Leader:</th>
						<td><?= display_glyphicon("user", "fas", "", "fa-fw") ?> <?= $group->user_link ?></td>
					</tr>
					
					<tr>
						<th>Members:</th>
						<td><?= display_group_members_list($group_members_array); ?></td>
					</tr>
					<?php if ($user->level_id >= 15 || $group->group_leader_id == $user->user_id) { //only display for relevant people ?>
					<tr>
						<th>Actions:</th>
						<td><span>
							<?= display_edit_group_members($user->level_id, $user->user_id, $group->group_leader_id) ?>
						</span></td>
					</tr>
					<?php } ?>
				</table>
			</div>
			
			<?php if ($user->level_id >= 15 || $group->group_leader_id == $user->user_id) { //only display for relevant people ?>
			<div class="panel panel-default edit-members display-none">	
				<div class="panel-heading">
					<h3 class="panel-title"><?= display_glyphicon("user", "fas", "", "fa-fw") ?> Edit members</h3>
				</div>
				<form id="edit_group_members_form" method="post">
					<table class="table table-condensed ">
						<tr>
							<th>Members:</th>
							<td><?= display_delete_group_members_list($group_members_array); ?></td>
						</tr>
						<tr>
							<th>Add member:</th>
							<td><input type="number" class="form-control" id="add_member_user_id" name="add_member_user_id" placeholder="user_id" required ></td>
						</tr>
						<tr>
							<th>Actions:</th>
							<td>
								<button type="submit" class="btn btn-success" id="save_edit_members_button"><?= display_glyphicon("pencil-alt", "fas", "", "fa-fw") ?> Save</button>
								<button class="btn btn-warning" id="cancel_edit_members_button"><?= display_glyphicon("times", "fas", "", "fa-fw") ?> Cancel</button>
							</td>
						</tr>
					</table>
				</form>
			</div>
			<?php } ?>
			
			<?php if ($group->group_leader_id == 1) { ?>
			<div class="alert alert-info" role="alert"><strong>Note:</strong> This is an internally generated group. The group leader may request to take this over by contacting anidex.moe@gmail.com. Please provide your group ID and user ID (found in URL).</div>
			<?php } ?>
			
		</div>
	</div>
	
	<?php if ($group->group_description) { ?>
	<div class="edit panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?= display_glyphicon("info-circle", "fas", "", "fa-fw") ?> Description</h3>
		</div>
		<div class="panel-body">
			<?= nl2br($parser->getAsHtml()); ?>
		</div>
	</div>
	<?php } ?>
	
	<!-- Nav tabs -->
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#chapters" aria-controls="chapters" role="tab" data-toggle="tab"><?= display_glyphicon("file", "far", "", "fa-fw") ?> Latest releases</a></li>
		<li role="presentation"><a href="#manga" aria-controls="manga" role="tab" data-toggle="tab"><?= display_glyphicon("book", "fas", "", "fa-fw") ?> Manga</a></li>
		<li role="presentation"><a href="#comments" aria-controls="comments" role="tab" data-toggle="tab"><?= display_glyphicon("comments", "far", "", "fa-fw") ?> Comments <?= $group->group_comments ?></a></li>
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
			
			require_once(ABSPATH . "/pages/chapters.req.php"); 
			?>
		</div>
		
		<div role="tabpanel" class="tab-pane fade" id="manga">
			<?php 
			$array_of_manga_ids = $group->get_manga_ids($db);
			
			if ($array_of_manga_ids) 
				require_once(ABSPATH . "/pages/mangas.req.php"); 
			else 
				print "<div style='margin: 10px 0;' class='alert alert-warning text-center' role='alert'>" . display_glyphicon("exclamation-triangle", "fa") . " <strong>Warning:</strong> No manga.</div>";
			?>
		</div>
		
		<div role="tabpanel" class="tab-pane fade" id="comments">
			<?php
				if ($group->group_comments) {
					$comments = new Comments($db, $id, 2);
					?>
					<table class="table table-condensed table-striped table-hover">
					<?php
					foreach ($comments as $comment) {
						
						$comment_user = new User($db, $comment->user_id, "user_id");
						$parser->parse($comment->comment_text);
						?>
					<tr>
						<td width="110px"><img alt="avatar" class="avatar" src="/images/avatars/<?= $comment_user->logo ?>" /></td>
						<td><?= display_glyphicon("user", "fas", "", "fa-fw") ?> <?= $comment_user->user_link ?> <span class="pull-right"><?= display_glyphicon("clock", "far", "", "fa-fw") ?> <?= gmdate("Y-m-d H:i:s \U\T\C", $comment->comment_timestamp) ?></span>
							<hr style="margin: 5px 0; clear: both;">
							<?= nl2br($parser->getAsHtml()) ?>
						</td>
						<td width="60px" class="text-center">
							<button title="Report comment" style="margin-bottom: 5px; " id="<?= $comment->comment_id ?>" type="button" class="report_comment_button btn btn-warning"><?= display_glyphicon("flag", "fas", "", "fa-fw") ?></button>
							<?= display_delete_comment($user->level_id, $comment->user_id, $user->user_id, $comment->comment_id) ?>
						</td>
					</tr>
					<?php } ?>
					</table>
			<?php } ?>
			
			<?php if ($user->user_id > 1) { ?>
			<form style="margin: 0 20px;" id="comment_group_form" name="comment_group_form" class="form-horizontal">
				<div class="form-group">
					<textarea required id="comment" name="comment" class="comment-box form-control" rows="3" placeholder="Post a comment"></textarea>
				</div>
				<div class="form-group text-center">
					<button type="submit" class="btn btn-default">Submit</button>
				</div>
			</form>	
			<?php } else { ?>
			<div class="alert alert-warning text-center" role="alert">You must be logged in to comment. </div>
			<?php } ?>
							
		</div>
		
		<?php if ($user->level_id >= 15) { ?>
		<div role="tabpanel" class="tab-pane fade" id="admin">
			<form style="margin: 15px auto;" class="form-horizontal" method="post" id="admin_edit_group_form">
				<div class="form-group">
					<label for="group_name" class="col-sm-4 control-label">Group name:</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="group_name" name="group_name" value="<?= $group->group_name ?>" />
					</div>
				</div>			
				<div class="form-group">
					<label for="group_leader_id" class="col-sm-4 control-label">Group leader ID:</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="group_leader_id" name="group_leader_id" value="<?= $group->group_leader_id ?>" />
					</div>
				</div>			
				<div class="form-group">
					<div class="col-sm-offset-4 col-sm-8">
						<button type="submit" class="btn btn-default" id="admin_edit_group_button"><?= display_glyphicon("edit", "fas", "", "fa-fw") ?> Save</button>
					</div>
				</div>
			</form>
		</div>
		<?php } ?>
	</div>

<?php 
}
else print "<div class='alert alert-danger text-center' role='alert'>" . display_glyphicon("exclamation-triangle", "fa") . " <strong>Warning:</strong> Group #$id does not exist.</div>";
?>