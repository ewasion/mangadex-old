<?php
//manga_id
if (isset($_GET["manga_id"])) {
	if (!empty($_GET["manga_id"]))	//not 0
		$search["manga_id"] = $_GET["manga_id"];
	else 
		unset($search["manga_id"]); //is 0
}

//uploader_id
if (isset($_GET["user_id"])) {
	if (!empty($_GET["user_id"]))	//not 0
		$search["user_id"] = $_GET["user_id"];
	else 
		unset($search["user_id"]); //is 0
}

//group_id
if (isset($_GET["group_id"])) {
	if (!empty($_GET["group_id"]))	//not 0
		$search["group_id"] = $_GET["group_id"];
	else 
		unset($search["group_id"]); //is 0
}


//only trusted/auth
if (!empty($_GET["a"])) {
	$search["authorised"] = 1;
}
else {
	unset($search["authorised"]); //is 0
}

if ($hentai_toggle == 0)
	$search["manga_hentai"] = 0;
elseif ($hentai_toggle == 2) 
	$search["manga_hentai"] = 1;
	
$limit = 100;

//offset
if (isset($_GET["offset"]))
	$offset = mysql_escape_mimic($_GET["offset"]);
else 
	$offset = 0;

$array_of_manga_ids = $array_of_manga_ids ?? "";

$chapters = new Chapters($db, $order, $limit, $offset, $search, $array_of_manga_ids);
$num_rows = $chapters->num_rows($db, $search, $array_of_manga_ids);


$current_page = floor($offset / $limit) + 1; //287
$last_page = ceil($num_rows / $limit); //286

if ($current_page == 1) {
	$previous_page = "-";
	$previous_class = "disabled";
}
else {
	$previous_page = $current_page - 1;
	$previous_class = "paging";
}
if ($current_page == $last_page) {
	$next_page = "-";
	$next_class = "disabled";
}
else {
	$next_page = $current_page + 1;
	$next_class = "paging";
}

if (count(get_object_vars($chapters))) { 
	?>

	
	
	<div class="table-responsive">
		<table class="table table-striped table-hover table-condensed">
			<thead>
				<tr>
					<?php if ($page != "manga") { ?><th><?= display_glyphicon("book", "fas", "Title") ?></th><?php } ?>
					<th><?= display_glyphicon("file", "far", "Chapter") ?></th>
					<th class="text-center" width="30px"><?= display_glyphicon("globe", "fas", "Language", "fa-fw") ?></th>
					<th><?= display_glyphicon("users", "fas", "Group", "fa-fw") ?></th>
					<th><?= display_glyphicon("user", "fas", "User", "fa-fw") ?></th>
					<th class="text-center text-info"><?= display_glyphicon("eye", "fas", "Views") ?></th>
					<th class="text-right"><?= display_glyphicon("clock", "far", "Age") ?></th>
					<?php if ($user->level_id >= 10) { ?><th class="text-center" width="30px"><?= display_glyphicon("pencil-alt", "fas", "Edit", "fa-fw") ?></th><?php } ?>
				</tr>
			</thead>
			<tbody>
			
			<?php 		
			$last_manga_id = "";
			foreach ($chapters as $chapter) {
				?>
					
				<tr id="chapter_<?= $chapter->chapter_id ?>">
					<?php if ($page != "manga") { ?><td><a title="<?= $chapter->manga_name ?>" href="/manga/<?= $chapter->manga_id ?>"><?= ($last_manga_id != $chapter->manga_id) ? mb_strimwidth($chapter->manga_name, 0, 40, "...") . display_labels($chapter->manga_hentai, 1) : "" ?></a></td><?php } ?>
					<td><a title="<?= $chapter->title ?>" data-chapter-id="<?= $chapter->chapter_id ?>" data-chapter-num="<?= $chapter->chapter ?>" data-volume-num="<?= $chapter->volume ?>" data-chapter-name="<?= $chapter->title ?>" href="/chapter/<?= $chapter->chapter_id ?>"><?= ($chapter->volume != "") ? "Vol. $chapter->volume " : "" ?><?= ($chapter->chapter != "") ? "Ch. $chapter->chapter - " : "" ?><?= ($chapter->title != "") ? mb_strimwidth($chapter->title, 0, 60, "...") : "Read Online" ?></a></td>
					<td class="text-center"><?= display_lang_flag_v2($chapter->lang_name, $chapter->lang_flag, array(5, 5, 5, 5)) ?></td>
					<td><a href="/group/<?= $chapter->group_id ?>"><?= $chapter->group_name ?></a></td>
					<td><a href="/user/<?= $chapter->user_id ?>"><?= $chapter->username ?></a></td>
					<td class="text-center text-info"><?= $chapter->chapter_views ?></td>
					<td class="text-right <?= ($timestamp - $chapter->upload_timestamp < 86400) ? "text-success" : "" ?>" title="<?= gmdate("Y-m-d H:i:s \U\T\C", $chapter->upload_timestamp) ?>"><time datetime="<?= gmdate("Y-m-d H:i:s \U\T\C", $chapter->upload_timestamp) ?>"><?= get_time_ago($chapter->upload_timestamp) ?></time></td>
					<?php if ($user->level_id >= 10) { ?><td><button class="btn btn-xs btn-info toggle_mass_edit_button" type="button" id="<?= $chapter->chapter_id ?>"><?= display_glyphicon("pencil-alt", "fas", "Edit", "fa-fw") ?></button></td><?php } ?>
				</tr>				
				
				<?php
				if ($user->level_id >= 10) {
					?>
					
					<tr class="display-none" id="toggle_mass_edit_<?= $chapter->chapter_id ?>">
						<td colspan="<?= ($page != "manga") ? "8" : "7" ?>">
							<form class="form-inline mass_edit_form" method="post" id="<?= $chapter->chapter_id ?>">
								<?= display_glyphicon("book", "fas", "Title") ?> <input style="width: 5%" type="text" class="form-control input-sm" name="manga_id" value="<?= $chapter->manga_id ?>" required>
								Vol <input style="width: 5%" type="text" class="form-control input-sm" name="volume_number" value="<?= $chapter->volume ?>">
								Ch <input style="width: 5%" type="text" class="form-control input-sm" name="chapter_number" value="<?= $chapter->chapter ?>">
								Title <input style="width: 25%" type="text" class="form-control input-sm" name="chapter_name" value="<?= htmlentities($chapter->title) ?>">
								<?= display_glyphicon("globe", "fas", "Language") ?> <input style="width: 5%" type="text" class="form-control input-sm" name="lang_id" value="<?= $chapter->lang_id ?>">
								<?= display_glyphicon("users", "fas", "Group") ?> <input style="width: 5%" type="text" class="form-control input-sm" name="group_id" value="<?= $chapter->group_id ?>">
								<input style="width: 5%" type="text" class="form-control input-sm" name="group_id_2" value="<?= $chapter->group_id_2 ?>">
								<input style="width: 5%" type="text" class="form-control input-sm" name="group_id_3" value="<?= $chapter->group_id_3 ?>">
								<button class="btn btn-sm btn-success" type="submit" id="mass_edit_button_<?= $chapter->chapter_id ?>"><?= display_glyphicon("pencil-alt", "fas", "Update", "fa-fw") ?></button>
								<button class="btn btn-sm btn-danger mass_edit_delete_button" type="button" id="<?= $chapter->chapter_id ?>"><?= display_glyphicon("trash", "fas", "Delete", "fa-fw") ?></button>
								<button class="btn btn-sm btn-warning cancel_mass_edit_button pull-right" type="button" id="<?= $chapter->chapter_id ?>"><?= display_glyphicon("times", "fas", "Cancel", "fa-fw") ?></button>
							</form>
						</td>
					</tr>
					<?php
				}
				
				
				$last_manga_id = $chapter->manga_id;
			}
			?>


			</tbody>
		</table>
	</div>

	<?php if ($page == "manga" || $page == "group" || $page == "user") { ?>
	<p class="text-center">Showing <?= number_format($offset + 1) ?> to <?= number_format(min($num_rows, $limit * $current_page)) ?> of <?= number_format($num_rows) ?> chapters</p>
	<nav class="text-center">
		<ul style="margin: 0; cursor: pointer;" class="pagination">
			<?php
			switch ($page) {
				case "manga":
					$string = "/$id/$manga->manga_slug";
				break;
				
				case "group":
					$string = "/$id/$group->group_slug";
				break;
				
				case "user":
					$string = "/$id/$uploader->user_slug";
				break;
				
				default:
					$string = "";
				break;
			}
			?>
			
			<li class="<?= $previous_class ?>" id="0"><a href="/<?= $page . $string ?>"><?= display_glyphicon("angle-double-left", "fas", "Jump to first page", "fa-fw") ?></a></li>

			<?php 
			for ($i = 2; $i >= 1; $i--) { 
				$pg = $current_page - $i;
				if ($pg > 0) {
					$o = $offset - $limit * $i;
					print "<li class='paging'><a href='/$page$string/$o'>$pg</a></li>";
				}
			} 
			?>
			
			<li class="active"><a><?= $current_page ?></a></li>
			
			<?php 
			for ($i = 1; $i <= 2; $i++) { 
				$pg = $current_page + $i;
				if ($pg <= $last_page && ($pg - $current_page <= 2 || in_array($pg, array(4,5)))) {
					$o = $offset + $limit * $i;
					print "<li class='paging'><a href='/$page$string/$o'>$pg</a></li>";
				}
			} 
			?>
			<li class="<?= $next_class ?>"><a href="/<?= $page . $string ?>/<?= ($last_page - 1) * $limit ?>"><?= display_glyphicon("angle-double-right", "fas", "Jump to last page", "fa-fw") ?></a></li>
		</ul>
	</nav>
	<?php } ?>
	
	<?php
}
else print "<div style='margin: 10px 0;' class='alert alert-warning text-center' role='alert'>" . display_glyphicon("exclamation-triangle", "fa") . " <strong>Warning:</strong> No chapters in your selected language(s).</div>";
//print_r($_SESSION);

?>
