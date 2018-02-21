<?php 
//offset
if (isset($_GET["offset"]))
	$offset = mysql_escape_mimic($_GET["offset"]);
else 
	$offset = 0;
		
$order = "upload_timestamp desc";

$array_of_manga_ids = array();

$grouped_manga_id_array = $grouped_manga_id_array ?? array();

$search = array();

if ($hentai_toggle == 0)
	$search["manga_hentai"] = 0;
elseif ($hentai_toggle == 2) 
	$search["manga_hentai"] = 1;

//lang
if (isset($_GET["lang_id"])) {
	if (!empty($_GET["lang_id"]))	//not 0
		$search["lang_id"] = $_GET["lang_id"];
	else 
		unset($search["lang_id"]); //is 0
}

$search["upload_timestamp"] = $timestamp - (60 * 60 * 24 * 7);

$search["grouped_chapters"] = $timestamp;

$search["chapter_deleted"] = 0;

$chapters = new Chapters($db, $order, 5000, 0, $search, $grouped_manga_id_array);


if (count(get_object_vars($chapters))) {

	$i = 0;
	foreach ($chapters as $chapter) {
		if (!isset($manga_name[$chapter->manga_id])) {
			$manga_array[$i] = $chapter->manga_id;
			$manga_link[$chapter->manga_id] = $chapter->manga_link;
			$manga_logo[$chapter->manga_id] = $chapter->logo;
			$manga_name[$chapter->manga_id] = $chapter->manga_name;
			$manga_hentai[$chapter->manga_id] = $chapter->manga_hentai;
			$i++;
		}
		
		$chapter_array[$chapter->manga_id][$chapter->chapter_id]["lang_name"] = $chapter->lang_name;
		$chapter_array[$chapter->manga_id][$chapter->chapter_id]["lang_flag"] = $chapter->lang_flag;
		$chapter_array[$chapter->manga_id][$chapter->chapter_id]["group_name"] = $chapter->group_name;
		$chapter_array[$chapter->manga_id][$chapter->chapter_id]["group_id"] = $chapter->group_id;
		$chapter_array[$chapter->manga_id][$chapter->chapter_id]["upload_timestamp"] = $chapter->upload_timestamp;
		$chapter_array[$chapter->manga_id][$chapter->chapter_id]["volume"] = $chapter->volume;
		$chapter_array[$chapter->manga_id][$chapter->chapter_id]["chapter"] = $chapter->chapter;
		$chapter_array[$chapter->manga_id][$chapter->chapter_id]["title"] = $chapter->title;
	}

	$limit = 50;
	$num_rows = count($manga_array);

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
	
	//$read_chapter_array = ($user->user_id) ? $user->get_read_chapters($db) : array("");
	

	?>

<div class="table-responsive">
	<table class="table table-striped table-condensed">
		<thead>
			<tr>
				<th width="110px"></th>
				<th width="25px"></th>
				<th>Latest updates</th>
				<th class="text-center" width="30px"><?= display_glyphicon("globe", "fas", "Language", "fa-fw") ?></th>
				<th>Group</th>
				<th class="text-right td-992" width="110px">Uploaded</th>
			</tr>
		</thead>
		<tbody>
		
		<?php 		

		
		

		for ($j = $offset; $j < min($num_rows, ($offset + $limit)); $j++) {
			$manga_id = $manga_array[$j];
			$manga = $chapter_array[$manga_id];
		
			//$bookmark = ($user->user_id && in_array($manga_id, $followed_manga_array)) ? display_glyphicon("bookmark", "fas", "Following", "text-success fa-fw") : "";
			$rowspan = (count($manga) >= 4 ? 5 : count($manga) + 1);
			?>
				
			<tr>
				<td rowspan="<?= $rowspan ?>"><a href="<?= $manga_link[$manga_id] ?>"><?= $manga_logo[$manga_id] ?></a></td>
				<td class="text-right"></td>
				<td colspan="4" height="31px"><?= display_glyphicon("book", "fas", "Title", "fa-fw") ?> <a class="manga_title" href="<?= $manga_link[$manga_id] ?>"><?= $manga_name[$manga_id] ?></a> <?= display_labels($manga_hentai[$manga_id]) ?></td>
			</tr>
			<?php
				$i = 1;
				foreach ($manga as $chapter_id => $chapter) {
					
					if ($i < 5) {
						$i++;
						//$key = ($user->user_id) ? array_search($chapter_id, $read_chapter_array["chapter_id"]) : "";
						//$read = ($user->user_id && in_array($chapter_id, $read_chapter_array["chapter_id"])) ? display_glyphicon("eye", "fas", "Read " . get_time_ago($read_chapter_array["timestamp"][$key]), "fa-fw") : "";
						
						?>
						<tr>
						<td class="text-right"></td>
						<td><?= display_glyphicon("file", "far", "Chapter", "fa-fw") ?> <a data-chapter-id="<?= $chapter_id ?>" data-chapter-num="<?= $chapter['chapter'] ?>" data-volume-num="<?= $chapter['volume'] ?>" data-chapter-name="<?= $chapter['title'] ?>" href="/chapter/<?= $chapter_id ?>"><?= ($chapter['volume']) ? "Vol. {$chapter['volume']} " : "" ?><?= ($chapter['chapter']) ? "Ch. {$chapter['chapter']} - " : "" ?><?= ($chapter['title']) ? mb_strimwidth($chapter['title'], 0, 60, "...") : "Read Online" ?></a></td>
						<td class="text-center"><?= display_lang_flag_v2($chapter['lang_name'], $chapter['lang_flag'], array(5, 5, 5, 5)) ?></td>
						<td><a href="/group/<?= $chapter['group_id'] ?>"><?= $chapter['group_name'] ?></a></td>
						<td class="text-right td-992" title="<?= gmdate("Y-m-d H:i:s \U\T\C", $chapter['upload_timestamp']) ?>"><time datetime="<?= gmdate("Y-m-d H:i:s \U\T\C", $chapter['upload_timestamp']) ?>"><?= get_time_ago($chapter['upload_timestamp']) ?></time></td>
						</tr>
						<?php
					}
				}
		}
		?>
		</tbody>
	</table>
</div>	
		
<p class="text-center">Showing <?= number_format($offset + 1) ?> to <?= number_format(min($num_rows, $limit * $current_page)) ?> of <?= number_format($num_rows) ?> titles</p>
<nav style="margin-bottom: 20px;" class="text-center">
	<ul style="margin: 0; cursor: pointer;" class="pagination">
		<li class="<?= $previous_class ?>" id="0"><a href="/<?= $lang_id ?>"><?= display_glyphicon("angle-double-left", "fas", "Jump to first page", "fa-fw") ?></a></li>

		<?php 
		for ($i = 2; $i >= 1; $i--) { 
			$pg = $current_page - $i;
			if ($pg > 0) {
				$o = $offset - $limit * $i;
				print "<li class='paging'><a href='/$lang_id/$o'>$pg</a></li>";
			}
		} 
		?>
		
		<li class="active"><a><?= $current_page ?></a></li>
		
		<?php 
		for ($i = 1; $i <= 2; $i++) { 
			$pg = $current_page + $i;
			if ($pg <= $last_page && ($pg - $current_page <= 2 || in_array($pg, array(4,5)))) {
				$o = $offset + $limit * $i;
				print "<li class='paging'><a href='/$lang_id/$o'>$pg</a></li>";
			}
		} 
		?>
		<li class="<?= $next_class ?>"><a href="/<?= $lang_id ?>/<?= ($last_page - 1) * $limit ?>"><?= display_glyphicon("angle-double-right", "fas", "Jump to last page", "fa-fw") ?></a></li>
	</ul>
</nav>
	
<?php
}
else 
	print "<div style='margin: 10px 0;' class='alert alert-warning text-center' role='alert'>" . display_glyphicon("exclamation-triangle", "fa") . " <strong>Warning:</strong> There are no more updates from the past week.</div>";
?>