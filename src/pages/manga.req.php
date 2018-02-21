<?php
$_GET['manga_id'] = $_GET['id'];
$id = $_GET['manga_id'] ?? 1;
$id = sanitise_id($id);

$manga = new Manga($db, $id);



if ($manga->exists) {	
	$user_id_follows_array = $manga->get_follows_user_id($db);

	update_views($db, "manga", $manga->manga_id, $ip, $user->user_id);

	$genres = new Genres($db);
	
	$missing_chapters = $manga->get_missing_chapters($db, $user->language);
	
	//BBCode
	require_once (ABSPATH . "/scripts/JBBCode/Parser.php");
	$parser = new JBBCode\Parser();
	$parser->addCodeDefinitionSet(new JBBCode\DefaultCodeDefinitionSet());
	$parser->parse($manga->manga_description);
	
	?>
	<?php if ((!$manga->manga_mal_id || !$manga->manga_mu_id) && $user->user_id) { ?>
	<div class="alert alert-success text-center" role="alert">Please take a moment and fill in the corresponding MAL/MU links if you have time!</div>
	<?php } ?>
	
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?= display_glyphicon("book", "fas", "", "fa-fw") ?> <?= $manga->manga_name ?> <?= display_lang_flag_v2($manga->lang_name, $manga->lang_flag, array(5, 5, 5, 5)) ?> <?= display_labels($manga->manga_hentai) ?>
			<a target="_blank" href="/rss/manga/<?= $id ?>"><span style="margin-top: -5px; margin-right: -5px;" class="fa fa-rss fa-2x pull-right"></span></a></h3>
		</div>
			
		<div class="row edit">
			<div class="col-sm-3"><?= $manga->logo; ?></div>
			<div class="col-sm-9">
				<table class="table table-condensed">
					<tr style="border-top: 0;">
						<th width="105px">Alt name(s):</th>
						<td><?php
							$alt_names_array = $manga->get_manga_alt_names($db);
							$alt_name_string = "";
							
							foreach ($alt_names_array["alt_name"] as $key => $alt_name) {
								$alt_name_string .= "$alt_name, ";
							}
							
							$alt_name_string = rtrim($alt_name_string, ", ");
							
							print $alt_name_string;
						?></td>
					</tr>
					<tr>
						<th>Author:</th>
						<td><a href="/?page=search&author=<?= $manga->manga_author ?>" title="Other manga by this author"><?= $manga->manga_author ?></a></td>
					</tr>
					<tr>
						<th>Artist:</th>
						<td><a href="/?page=search&artist=<?= $manga->manga_artist ?>" title="Other manga by this artist"><?= $manga->manga_artist ?></a></td>
					</tr>
					<tr>
						<th>Genres:</th>
						<td><?= display_genres($manga->get_manga_genres($db)) ?></td>
					</tr>
					<tr>
						<th>Rating:</th>
						<td><?= $manga->manga_rating ?></td>
					</tr>
					<tr>
						<th>Status:</th>
						<td><?= $status_array[$manga->manga_status_id] ?></td>
					</tr>
					<tr>
						<th>Stats:</th>
						<td><span class="text-info"><?= display_glyphicon("eye", "fas", "Views", "fa-fw") ?> <?= $manga->manga_views ?></span>
							<span class="text-success"><?= display_glyphicon("bookmark", "fas", "Follows", "fa-fw") ?> <?= $manga->manga_follows ?></span>
							<span><?= display_glyphicon("file", "far", "Total chapters", "fa-fw") ?> <?= $manga->get_total_chapters($db, $user->default_lang_ids) ?></span></td>
					</tr>
					<tr>
						<th>Description:</th>
						<td><?= nl2br($parser->getAsHtml()); ?></td>
					</tr>
					<tr>
						<th>Links:</th>
						<td>
							<?php if ($manga->manga_mal_id >= 0) { ?>
							<a rel="noopener noreferrer" target="_blank" class="btn btn-default <?= (!$manga->manga_mal_id) ? "disabled" : "" ?>" href="https://myanimelist.net/manga/<?= $manga->manga_mal_id ?>" role="button"><?= display_glyphicon("external-link-alt", "fas", "Website") ?> MAL</a> 
							<?php } if ($manga->manga_mu_id >= 0) { ?>
							<a rel="noopener noreferrer" target="_blank" class="btn btn-default <?= (!$manga->manga_mu_id) ? "disabled" : "" ?>" href="https://www.mangaupdates.com/series.html?id=<?= $manga->manga_mu_id ?>" role="button"><?= display_glyphicon("external-link-alt", "fas", "Website") ?> MU</a>
							<?php } ?>
						</td>
					</tr>
					<tr>
						<th>Actions:</th>
						<td>
							<?= display_upload_button($user->user_id) ?>
							<?= display_follow_button($user->user_id, $user_id_follows_array) ?>
							<?= display_edit_manga($user->level_id, $manga->manga_locked) ?>
							<?= display_lock_manga($user->level_id, $manga->manga_locked) ?>
							<?= display_delete_manga($user->level_id) ?>
						</td>
					</tr>
				</table>
			</div>
		</div>

		<?php if ($user->level_id >= 3) { ?>
		<form class="edit display-none" id="manga_edit_form" method="post" enctype="multipart/form-data">
			<table class="table table-condensed ">
				<tr>
					<th width="150px">Name:</th>
					<td><input type="text" class="form-control" id="manga_name" name="manga_name" value="<?= $manga->manga_name ?>" required></td>
				</tr>
				<tr>
					<th>Alt name(s):</th>
					<td>To be coded.</td>
				</tr>
				<tr>
					<th>Author:</th>
					<td><input type="text" class="form-control" id="manga_author" name="manga_author" value="<?= $manga->manga_author ?>"></td>
				</tr>
				<tr>
					<th>Artist:</th>
					<td><input type="text" class="form-control" id="manga_artist" name="manga_artist" value="<?= $manga->manga_artist ?>"></td>
				</tr>
				<tr>
					<th width="100px">Original language:</th>
					<td>
						<select class="form-control selectpicker" id="manga_lang_id" name="manga_lang_id">
							<?php 
							foreach ($orig_lang_array as $key => $language) {
								$selected = ($key == $manga->manga_lang_id) ? "selected" : "";
								print "<option $selected value='$key'>$language</option>"; 
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<th>Status:</th>
					<td>
						<select class="form-control selectpicker" id="manga_status_id" name="manga_status_id">
							<?php 
							foreach ($status_array as $key => $status) {
								$selected = ($key == $manga->manga_status_id) ? "selected" : "";
								print "<option $selected value='$key'>$status</option>"; 
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<th>Genres:</th>
					<td>
						<div class="row">
							<?php for ($j = 1; $j <= 4; $j++) { ?>
							<div class="col-xs-3">
								<?php for ($i = ($j - 1) * 10; ($i < ($j * 10) && $i < 40); $i++) { ?>
									<div class="checkbox">
										<label>
											<input type="checkbox" name="manga_genres[]" value="<?= $genres->{$i}->genre_id ?>" <?= in_array($genres->{$i}->genre_id, $manga->get_manga_genres($db)) ? "checked" : "" ?>>
											<span class="label label-default"><?= $genres->{$i}->genre_name ?></span>
										</label>
									</div>
								<?php } ?>
							</div>
							<?php } ?>
						</div>
					</td>
				</tr>
				<tr>
					<th>Hentai:</th>
					<td>
						<div class="checkbox">  
							<label><input type="checkbox" name="manga_hentai" id="manga_hentai" value="1" <?= ($manga->manga_hentai) ? "checked" : "" ?>></label>
						</div>
					</td>
				</tr>
				<tr>
					<th>Description:</th>
					<td><textarea class="form-control" rows="11" id="manga_description" name="manga_description" placeholder="Optional"><?= $manga->manga_description ?></textarea></td>
				</tr>
				<tr>
					<th>MyAnimeList ID:</th>
					<td><input type="number" class="form-control" id="manga_mal_id" name="manga_mal_id" value="<?= $manga->manga_mal_id ?>"></td>
				</tr>
				<tr>
					<th>MangaUpdates ID:</th>
					<td><input type="number" class="form-control" id="manga_mu_id" name="manga_mu_id" value="<?= $manga->manga_mu_id ?>"></td>
				</tr>
				<tr>
					<th>Image:</th>
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
					<th>Actions:</th>
					<td>
						<button type="submit" class="btn btn-success" id="manga_edit_button"><?= display_glyphicon("pencil-alt", "fas", "", "fa-fw") ?> Save</button>
						<button class="btn btn-warning" id="cancel_edit_button"><?= display_glyphicon("times", "fas", "", "fa-fw") ?> Cancel</button>
					</td>
				</tr>
			</table>
		</form>
		<?php } ?>
	</div>
		
	<!-- Nav tabs -->
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#chapters" aria-controls="chapters" role="tab" data-toggle="tab"><?= display_glyphicon("file", "far", "", "fa-fw") ?> Chapters</a></li>
		<?php if ($missing_chapters) { ?><li role="presentation"><a href="#summary" aria-controls="summary" role="tab" data-toggle="tab"><?= display_glyphicon("info-circle", "fas", "", "fa-fw") ?> Missing chapters</a></li> <?php } ?>
		<li role="presentation"><a href="#comments" aria-controls="comments" role="tab" data-toggle="tab"><?= display_glyphicon("comments", "far", "", "fa-fw") ?> Comments <?= $manga->manga_comments ?></a></li>
		<?php if ($user->level_id >= 15) { ?>
		<li role="presentation"><a href="#admin" aria-controls="admin" role="tab" data-toggle="tab"><?= display_glyphicon("edit", "fas", "", "fa-fw") ?> Admin</a></li>
		<?php } ?>
	</ul>

	<!-- Tab panes -->
	<div class="tab-content">
		<div role="tabpanel" class="tab-pane fade in active" id="chapters">
			<?php 
			//multi_lang
			if ($user->default_lang_ids)
				$search["multi_lang_id"] = $user->default_lang_ids;
			
			$search["chapter_deleted"] = 0;
			
			$order = "(CASE volume WHEN '' THEN 1 END) DESC, abs(volume) DESC, abs(chapter) DESC, group_id ASC ";
			
			require_once(ABSPATH . "/pages/chapters.req.php"); 
			
			?>
		</div>
		
		<?php if ($missing_chapters) { ?>
		<div role="tabpanel" class="tab-pane fade" id="summary">
			Missing chapters: <?= $missing_chapters ?>
		</div>
		<?php } ?>
		
		
		<div role="tabpanel" class="tab-pane fade" id="comments">
			<?php
				if ($manga->manga_comments) {
					$comments = new Comments($db, $id, 1);
					?>
					<table class="table table-condensed table-striped table-hover">
					<?php
					foreach ($comments as $comment) {
						
						$comment_user = new User($db, $comment->user_id, "user_id");
						$parser->parse($comment->comment_text);
						?>
					<tr class="comment">
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
			
			<?php if ($user->user_id) { ?>
			<form style="margin: 0 20px;" id="manga_comment_form" class="form-horizontal">
				<div class="form-group">
					<textarea required id="comment" name="comment" class="comment-box form-control" rows="3" placeholder="Post a comment"></textarea>
				</div>
				<div class="form-group text-center">
					<button type="submit" class="btn btn-default" id="manga_comment_button">Comment</button>
				</div>
			</form>	
			<?php } else { ?>
			<div class="alert alert-warning text-center" role="alert">You must be logged in to comment. </div>
			<?php } ?>
							
		</div>
		
		<?php if ($user->level_id >= 10) { ?>
		<div role="tabpanel" class="tab-pane fade" id="admin">
			<form style="margin: 15px auto;" class="form-horizontal" method="post" id="admin_edit_manga_form">
				<div class="form-group">
					<label for="group_name" class="col-sm-4 control-label">Merge this INTO:</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="old_id" name="old_id" />
					</div>
				</div>			
				<div class="form-group">
					<div class="col-sm-offset-4 col-sm-8">
						<button type="submit" class="btn btn-default" id="admin_edit_manga_button"><?= display_glyphicon("edit", "fas", "", "fa-fw") ?> Save</button>
					</div>
				</div>
			</form>
		</div>
		<?php } ?>	
		
		<?php if ($user->level_id >= 15) { ?>
		<div role="tabpanel" class="tab-pane fade" id="admin">
			<form style="margin: 15px auto;" class="form-horizontal" method="post" id="admin_edit_manga_form">
				<div class="form-group">
					<label for="group_name" class="col-sm-4 control-label">Merge this INTO:</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="old_id" name="old_id" />
					</div>
				</div>			
				<div class="form-group">
					<div class="col-sm-offset-4 col-sm-8">
						<button type="submit" class="btn btn-default" id="admin_edit_manga_button"><?= display_glyphicon("edit", "fas", "", "fa-fw") ?> Save</button>
					</div>
				</div>
			</form>
		</div>
		<?php } ?>		
	</div>

<?php 
}
else {
	http_response_code(404);	
	print "<div class='alert alert-danger text-center' role='alert'>" . display_glyphicon("exclamation-triangle", "fa") . " <strong>Warning:</strong> Manga #$id does not exist.</div>";
}
?>