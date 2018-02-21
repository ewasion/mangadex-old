<?php
$search = array();

if ($hentai_toggle == 0)
	$search["manga_hentai"] = 0;
elseif ($hentai_toggle == 2) 
	$search["manga_hentai"] = 1;
	
//genre
if (isset($_GET["genres"])) {
	if (!empty($_GET["genres"])) //not ""
		$search["manga_genres"] = $_GET["genres"];
	else 
		unset($search["manga_genre"]); //is ""
}
	
//title
if (isset($_GET["title"])) {
	if (!empty($_GET["title"])) //not ""
		$search["manga_name"] = $_GET["title"];
	else 
		unset($search["manga_name"]); //is ""
}

//author
if (isset($_GET["author"])) {
	if (!empty($_GET["author"])) //not ""
		$search["manga_author"] = $_GET["author"];
	else 
		unset($search["manga_author"]); //is ""
}

//artist
if (isset($_GET["artist"])) {
	if (!empty($_GET["artist"])) //not ""
		$search["manga_artist"] = $_GET["artist"];
	else 
		unset($search["manga_artist"]); //is ""
}

//alpha
if (isset($_GET["alpha"])) {
	if (!empty($_GET["alpha"])) {//not ""
		$search["manga_alpha"] = $_GET["alpha"];
		$order_by = "manga_name";
		$order = "ASC";
	}
	else {
		unset($search["manga_alpha"]); //is ""
		$order_by = "manga_last_updated";
		$order = "DESC";
	}
	
}
else {
	$order_by = "manga_last_updated";
	$order = "DESC";
}

//offset
if (isset($_GET["offset"]))
	$offset = mysql_escape_mimic($_GET["offset"]);
else 
	$offset = 0;

$limit = 100;

$array_of_manga_ids = $array_of_manga_ids ?? "";
$mangas = new Mangas($db, $order_by, $order, $limit, $offset, $search, $array_of_manga_ids); //do not change $array_of_manga_ids

$num_rows = $mangas->num_rows($db, $search, $array_of_manga_ids);

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
if (count(get_object_vars($mangas))) { ?>

	<div class="table-responsive">
		<table class="table table-striped table-hover table-condensed">
			<thead>
				<tr>
					<th width="30px" class="text-center"><?= display_glyphicon("globe", "fas", "Language") ?></th>
					<th width="500px"><?= display_glyphicon("book", "fas", "Title") ?></th>
					<th><?= display_glyphicon("pencil-alt", "fas", "Author/Artist") ?></th>
					<th width="30px" class="text-center"><?= display_glyphicon("comments", "fas", "Comments") ?></th>
					<th width="30px" class="text-center"><?= display_glyphicon("star", "fas", "Rating") ?></th>
					<th width="30px" class="text-info text-center"><?= display_glyphicon("eye", "fas", "Views") ?></th>
					<th width="30px" class="text-center"><?= display_glyphicon("bookmark", "fas", "Follows") ?></th>
					<th width="150px" class="text-right"><?= display_glyphicon("sync", "fas", "Last update") ?></th>
				</tr>
			</thead>
			<tbody>
			
			<?php
			foreach ($mangas as $manga) {
				?>
				
				<tr>
					<td class="text-center"><?= display_lang_flag_v2($manga->lang_name, $manga->lang_flag, array(5, 5, 5, 5)) ?></td>
					<td><?= $manga->manga_link ?></td>
					<td><a href="/?page=titles&author=<?= $manga->manga_author ?>"><?= $manga->manga_author ?></a></td>
					<td class="text-center"><?= $manga->manga_comments ?></td>
					<td class="text-center"><?= $manga->manga_rating ?></td>
					<td class="text-center text-info"><?= $manga->manga_views ?></td>
					<td class="text-center"><?= $manga->manga_follows ?></td>
					<td class="text-right" title="<?= gmdate("Y-m-d H:i:s \U\T\C", $manga->manga_last_updated) ?>"><time datetime="<?= gmdate("Y-m-d H:i:s \U\T\C", $manga->manga_last_updated) ?>"><?= get_time_ago($manga->manga_last_updated) ?></time></td>

				</tr>			
				
				<?php
			}
			?>
				

			</tbody>
		</table>
	</div>
	
	<?php if ($page == "titles" || $page == "follows") { ?>
	<p class="text-center">Showing <?= number_format($offset + 1) ?> to <?= number_format(min($num_rows, $limit * $current_page)) ?> of <?= number_format($num_rows) ?> titles</p>
	<nav class="text-center">
		<ul style="margin: 0; cursor: pointer;" class="pagination">
			<?php
			if (!empty($_GET["alpha"])) {
				$alpha_string = "/" . $_GET["alpha"];
			}
			else 
				$alpha_string = "";
			?>
			
			<li class="<?= $previous_class ?>" id="0"><a href="/<?= $page . $alpha_string ?>"><?= display_glyphicon("angle-double-left", "fas", "Jump to first page", "fa-fw") ?></a></li>

			<?php 
			for ($i = 2; $i >= 1; $i--) { 
				$pg = $current_page - $i;
				if ($pg > 0) {
					$o = $offset - $limit * $i;
					print "<li class='paging'><a href='/$page$alpha_string/$o'>$pg</a></li>";
				}
			} 
			?>
			
			<li class="active"><a><?= $current_page ?></a></li>
			
			<?php 
			for ($i = 1; $i <= 2; $i++) { 
				$pg = $current_page + $i;
				if ($pg <= $last_page && ($pg - $current_page <= 2 || in_array($pg, array(4,5)))) {
					$o = $offset + $limit * $i;
					print "<li class='paging'><a href='/$page$alpha_string/$o'>$pg</a></li>";
				}
			} 
			?>
			<li class="<?= $next_class ?>"><a href="/<?= $page . $alpha_string ?>/<?= ($last_page - 1) * $limit ?>"><?= display_glyphicon("angle-double-right", "fas", "Jump to last page", "fa-fw") ?></a></li>
		</ul>
	</nav>
	<?php } ?>

<?php
}
else print "<div style='margin: 10px 0;' class='alert alert-warning text-center' role='alert'>" . display_glyphicon("exclamation-triangle", "fa") . " <strong>Warning:</strong> No titles.</div>";


?>