<?php
$id = $_GET['id'] ?? 1;
$p = $_GET['p'] ?? 1;
$m = $_GET['m'] ?? 1;

$id = sanitise_id($id);
$p = sanitise_id($p);

$chapter = new Chapter($db, $id);

if ($chapter->exists && !$chapter->chapter_deleted) {
	$group = new Group($db, $chapter->group_id);

	$other_chapters = $chapter->get_other_chapters($db);
	$other_groups = $chapter->get_other_groups($db);
	
	$manga = new Manga($db, $chapter->manga_id);
	if (in_array(36, $manga->get_manga_genres($db)))
		$user->reader_mode = 3;
	
	$current_key = array_search($chapter->chapter_id, $other_chapters["id"]);
	$next_key = $current_key + 1;
	$prev_key = $current_key - 1;
	$next_id = $other_chapters["id"][$next_key] ?? 0;
	$prev_id = $other_chapters["id"][$prev_key] ?? 0;
	$prev_pages = $chapter->get_pages_of_prev_chapter($db, $prev_id);

	$arr = explode(",", $chapter->page_order);
	$page_array = array_combine(range(1, count($arr)), array_values($arr));
	$pages = count($page_array);

	$server = ($chapter->server) ? "https://s{$chapter->server}.mangadex.com/" : "/data/"; 


	?>
	

	
	
	<div class="row" style="margin-bottom: 20px;">
		<div class="toggle col-sm-2">
			<?php if ($user->user_id) { ?>
			<div class="btn-group">
				<button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?= display_glyphicon("flag", "fas", "", "fa-fw") ?> <span class="caret"></span></button>
				<ul class="dropdown-menu">
					<li><a class="report_button" id="1" href="#">All images broken</a></li>
					<li><a class="report_button" id="2" href="#">Some images broken</a></li>
					<li><a class="report_button" id="3" href="#">Watermarked images</a></li>
					<li><a class="report_button" id="4" href="#">Naming rules broken</a></li>
					<li><a class="report_button" id="5" href="#">Incorrect group</a></li>
					<li><a class="report_button" id="6" href="#">Group policy evasion</a></li>
					<li><a class="report_button" id="7" href="#">Official release/Raw</a></li>
					<li><a class="report_button" id="0" href="#">Other (Please specify)</a></li>
				</ul>
			</div>
			<?php }
			
			if ($user->user_id == $chapter->user_id || $user->level_id >= 10 || $group->group_leader_id == $user->user_id) { ?>
				<button title="Edit" class="btn btn-success" id="edit_button"><?= display_glyphicon("pencil-alt", "fas", "", "fa-fw") ?></button>
				<button title="Delete" class="btn btn-danger" id="delete_button"><?= display_glyphicon("trash", "fas", "", "fa-fw") ?></button>
			<?php }  ?>
			
		</div>			
		
		<div class="toggle col-sm-2">
			<?= display_glyphicon("book", "fas", "Title") ?> <a href="/manga/<?= $chapter->manga_id ?>"><?= $chapter->manga_name ?></a>
		</div>
		
		<div class="toggle col-sm-2">
			<select class="form-control selectpicker" id="jump_chapter" name="jump_chapter" data-size="10">
				<?php
				foreach ($other_chapters["name"] as $key => $name) {
					$selected = ($id == $key) ? "selected" : "";
					print "<option $selected value='$key'>$name</option>";
				}
				?>
			</select>
		</div>
		
		<div class="toggle col-sm-2">
			<select class="form-control selectpicker" id="jump_group" name="jump_group" data-size="10">
				<?php
				foreach ($other_groups["id"] as $key => $name) {
					$selected = ($key == $chapter->group_id) ? "selected" : "";
					print "<option data-content=\"" . display_glyphicon("users", "fas", "Group") . " $name " . display_lang_flag_v2($chapter->lang_name, $chapter->lang_flag, array(5, 5, 5, 5)) . "\" $selected value='{$other_groups['chapter_id'][$key]}'>$name</option>";
				}
				?>
				
			</select>
			
		</div>
		
		<div class="toggle col-sm-2">
			<?php if (!$user->reader_mode) { ?>
			<select class="form-control selectpicker" id="jump_page" name="jump_page" data-size="10">
				<?php 
				for ($i = 1; $i <= $pages; $i++) {
					$selected = ($p == $i) ? "selected" : "";
					print "<option $selected value='$i'>Page $i</option>"; 
				}
				?>
			</select>
			<?php } ?>
		</div>
		
		<div class="col-sm-2 text-right">
			<button class="btn btn-default toggle" id="minimise" title="Hide navbar"><?= display_glyphicon("window-minimize", "fas", "Minimise", "fa-fw") ?></button>
		</div>
		
	</div>
	
	<?php
	if ($chapter->upload_timestamp < $timestamp || ($user->user_id == $chapter->user_id || $user->level_id >= 10 || $group->group_leader_id == $user->user_id)) {
		
		update_views($db, "chapter", $chapter->chapter_id, $ip, $user->user_id);
		
		if ($user->user_id) 
			$db->query(" INSERT IGNORE INTO mangadex_chapter_views (chapter_id, user_id) VALUES ($chapter->chapter_id, $user->user_id); ");

		switch ($user->reader_mode) {
			case 0:
			
				?>
				<img id="current_page" class="edit reader" src="<?= $server ?><?= $chapter->chapter_hash ?>/<?= $page_array[$p] ?>" alt="image" data-page="<?= $p ?>" />
				<?php
				break;

			//case 1: //inf scroll
				
			
				//break;
				
			case 2: //long-strip
				foreach ($page_array as $key => $x) {
					?>
					
					<img class="edit long-strip <?= ($user->reader_click) ? "click" : "" ?>" src="<?= "$server$chapter->chapter_hash/$x" ?>" alt="image <?= $key ?>" />
					
					<?php
				}
				
				break;
				
			case 3: //webtoon
				foreach ($page_array as $key => $x) {
					?>
					
					<img class="edit webtoon <?= ($user->reader_click) ? "click" : "" ?>" src="<?= "$server$chapter->chapter_hash/$x" ?>" alt="image <?= $key ?>" />
					
					<?php
				}
				
				break;
				
			default:
				?>
				<img id="current_page" class="edit reader" src="<?= $server ?><?= $chapter->chapter_hash ?>/<?= $page_array[$p] ?>" alt="image" data-page="<?= $p ?>" />
				<?php
				break;
		}
	}
	else 
		print "<div class='alert alert-warning text-center' role='alert'>" . display_glyphicon("exclamation-triangle", "fa") . " Due to the group's delay policy, this chapter will be available " . get_time_ago($chapter->upload_timestamp) . ".</div>";
	?>
	
	<div class="row <?= ($user->reader_mode) ? "" : "display-none toggle" ?>" style="margin-top: 20px;">
		<div class="col-xs-2">
			<button <?= ($prev_id) ? "" : "disabled" ?> class="btn btn-sm btn-default" id="prev_chapter_alt" title="Go to previous chapter"><?= display_glyphicon("angle-double-left", "fas", "Last chapter", "fa-fw") ?></button>
		</div>
		
		<div class="col-xs-2">
			<button <?= ($user->reader_mode) ? "disabled" : "" ?> class="btn btn-sm btn-default" id="prev_page_alt" title="Go to previous page"><?= display_glyphicon("angle-left", "fas", "Last chapter", "fa-fw") ?></button>
		</div>
		
		<div class="col-xs-4 text-center">
			<button class="btn btn-sm btn-default" id="maximise" title="Display navbar"><?= display_glyphicon("window-maximize", "fas", "Maximise", "fa-fw") ?></button>
		</div>
			
		<div class="col-xs-2 text-right">
			<button <?= ($user->reader_mode) ? "disabled" : "" ?> class="btn btn-sm btn-default" id="next_page_alt" title="Go to next page"><?= display_glyphicon("angle-right", "fas", "Last chapter", "fa-fw") ?></button>
		</div>
		
		<div class="col-xs-2 text-right">
			<button <?= ($next_id) ? "" : "disabled" ?> class="btn btn-sm btn-default" id="next_chapter_alt" title="Go to next chapter"><?= display_glyphicon("angle-double-right", "fas", "Next chapter", "fa-fw") ?></button>
		</div>		
	</div>
	
	<?php if ($user->user_id == $chapter->user_id || $user->level_id >= 10 || $group->group_leader_id == $user->user_id) { ?>
	
	<div class="edit display-none panel panel-default">	
		<div class="panel-heading">
			<h3 class="panel-title"><?= display_glyphicon("plus-circle", "fas", "", "fa-fw") ?> Edit chapter</h3>
		</div>
		<div class="panel-body">
			<form style="margin-top: 15px;" id="edit_chapter_form" method="post" class="form-horizontal" enctype="multipart/form-data">
				<div class="form-group">
					<label for="manga_id" class="col-sm-3 control-label">Manga name</label>
					<div class="col-sm-9">
						<input required type="number" class="form-control" id="manga_id" name="manga_id" placeholder="Required" value="<?= $chapter->manga_id ?>">
					</div>
				</div>
				<div class="form-group">
					<label for="chapter_name" class="col-sm-3 control-label">Chapter name</label>
					<div class="col-sm-9">
						<input type="text" class="form-control" id="chapter_name" name="chapter_name" placeholder="Optional" value="<?= $chapter->title ?>">
					</div>
				</div>
				<div class="form-group">
					<label for="volume_number" class="col-sm-3 control-label">Volume number</label>
					<div class="col-sm-9">
						<input type="number" class="form-control" id="volume_number" name="volume_number" placeholder="Numbers only" value="<?= $chapter->volume ?>">
					</div>
				</div>
				<div class="form-group">
					<label for="chapter_number" class="col-sm-3 control-label">Chapter number</label>
					<div class="col-sm-9">
						<input type="text" class="form-control" id="chapter_number" name="chapter_number" placeholder="Alphanumeric" value="<?= $chapter->chapter ?>">
					</div>
				</div>
				<div class="form-group">
					<label for="group_id" class="col-sm-3 control-label">Group 1</label>
					<div class="col-sm-9">
						<input required type="number" class="form-control" id="group_id" name="group_id" placeholder="Required" value="<?= $chapter->group_id ?>">
					</div>
				</div>
				<div class="form-group">
					<label for="group_id_2" class="col-sm-3 control-label">Group 2</label>
					<div class="col-sm-9">
						<input required type="number" class="form-control" id="group_id_2" name="group_id_2" placeholder="Optional" value="<?= $chapter->group_id_2 ?>">
					</div>
				</div>
				<div class="form-group">
					<label for="group_id_3" class="col-sm-3 control-label">Group 3</label>
					<div class="col-sm-9">
						<input required type="number" class="form-control" id="group_id_3" name="group_id_3" placeholder="Optional" value="<?= $chapter->group_id_3 ?>">
					</div>
				</div>
				<div class="form-group">
					<label for="lang_id" class="col-sm-3 control-label">Language</label>
					<div class="col-sm-9">   
						<select class="form-control selectpicker" id="lang_id" name="lang_id" data-size="10">
							<?php 
							$languages = new Languages($db, "lang_name", "ASC");
							foreach ($languages as $language) {
								$selected = ($language->lang_id == $chapter->lang_id) ? "selected" : "";
								print "<option $selected value='$language->lang_id'>$language->lang_name</option>"; 
							}
							?>
						</select>
					</div>
				</div>
				<div class="form-group">
				<label for="file" class="col-sm-3 control-label">File</label>
					<div class="col-sm-9">
						<div class="input-group">
							<input type="text" class="form-control" placeholder="Filename" readonly name="old_file" value="">
							<span class="input-group-btn">
								<span class="btn btn-default btn-file">
									<?= display_glyphicon("folder-open", "far", "", "fa-fw") ?> <span class="span-1280">Browse</span> <input type="file" name="file" id="file">
								</span>
							</span>
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-offset-3 col-sm-9">
						<button type="submit" class="btn btn-success" id="save_edit_button"><?= display_glyphicon("pencil-alt", "fas", "", "fa-fw") ?> <span class="span-1280">Save</span></button>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-offset-3 col-sm-9 text-right">
						<div class="progress" style="height: 38px; display: none;">
							<div id="progressbar" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" style="width: 0%;" class="progress-bar progress-bar-info"></div>
						</div>
					</div>
				</div>
			</form>	
		</div>
	</div>	 


	
	<?php } ?>
	
	<?php
	
}
elseif ($chapter->chapter_deleted)
	print "<div class='alert alert-warning text-center' role='alert'>" . display_glyphicon("exclamation-triangle", "fa") . " <strong>Warning:</strong> Chapter #$id has been deleted and cannot be viewed. If this has been accidentally deleted, contact a mod to restore it.</div>";
else
	print "<div class='alert alert-danger text-center' role='alert'>" . display_glyphicon("exclamation-triangle", "fa") . " <strong>Warning:</strong> Chapter #$id does not exist.</div>";

?>
