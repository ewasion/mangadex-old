<?php
$lang_id = mysql_escape_mimic($_GET['lang_id']) ?? $user->language;
$_GET['lang_id'] = $lang_id;

$chapter_by_lang = $db->get_results(" 
	SELECT COUNT(*) AS Rows, mangadex_chapters.lang_id, mangadex_languages.* 
	FROM mangadex_chapters, mangadex_languages 
	WHERE mangadex_chapters.lang_id = mangadex_languages.lang_id 
	GROUP BY mangadex_chapters.lang_id ORDER BY Rows DESC ");

$languages = new Languages($db, "lang_id", "ASC"); //lang flag

if ($hentai_toggle == 1) $rss_hentai = "&h=0"; 
elseif ($hentai_toggle == 2) $rss_hentai = "&h=1"; 
else $rss_hentai = ""; 


?>

<ul class="nav nav-tabs" style="margin-bottom: 20px;">
	<li role="presentation" class="<?= (!$lang_id) ? "active" : "" ?>"><a href="/0"><?= display_glyphicon("globe", "fas", "All languages", "fa-fw") ?></a></li>
	<?php 
	foreach ($chapter_by_lang as $lang) {
		$active = ($lang->lang_id == $lang_id) ? "active" : "";
		print "<li role='presentation' class='$active'><a href='/$lang->lang_id'>" . display_lang_flag_v2($lang->lang_name, $lang->lang_flag, array(5, 5, 5, 5)) . "</a></li>"; 
	}
	?>
	
</ul>

<div class="row">
	<div class="col-sm-9">

			
		<?php 
		$_SESSION["search"] = array();
		
		if ($hentai_toggle == 0)
			$_SESSION["search"]["manga_hentai"] = 0;
		elseif ($hentai_toggle == 2) 
			$_SESSION["search"]["manga_hentai"] = 1;

		//lang
		if (isset($_GET["lang_id"])) {
			if (!empty($_GET["lang_id"]))	//not 0
				$_SESSION["search"]["lang_id"] = $_GET["lang_id"];
			else 
				unset($_SESSION["search"]["lang_id"]); //is 0
		}
			
		$order_by[1] = "MAX(upload_timestamp)";
		$order[1] = "desc";

		$grouped_chapters = new Grouped_chapters($db, $order_by, $order, 50, $_SESSION["search"], $array_of_manga_ids);
$db->debug();
		if (count(get_object_vars($grouped_chapters))) {
			?>


		
		<div class="table-responsive">
			<table class="table table-striped table-hover table-condensed">
				<thead>
					<tr>
						<th></th>
						<th>Latest updates</th>
						<th class="text-center" width="30px"><?= display_glyphicon("globe", "fas", "Language", "fa-fw") ?></th>
						<th>Group</th>
						<th class="text-right">Uploaded</th>
					</tr>
				</thead>
				<tbody>
				
				<?php 		
				foreach ($grouped_chapters as $manga) {
					
					$order_by[1] = "upload_timestamp";
					$order[1] = "desc";
					$search['manga_id'] = $manga->manga_id;
					
					$chapters = new Chapters($db, $order_by, $order, 5, $search, $array_of_manga_ids);
					$rowspan = count(get_object_vars($chapters)) + 1;
					?>
						
					<tr>
						<td rowspan="<?= $rowspan ?>" width="100px"><img width="100%" src="/images/manga/<?= $manga->manga_image ?>" alt="image" /></td>
						<td colspan="4" height="31px"><?= display_glyphicon("book", "fas", "Title", "fa-fw") ?> <a href="/manga/<?= $manga->manga_id ?>"><?= $manga->manga_name ?></a>
					</tr>
					<?php
						foreach ($chapters as $chapter) {
							?>
							<tr>
							<td><?= display_glyphicon("file", "far", "Chapter", "fa-fw") ?> <a href="/chapter/<?= $chapter->chapter_id ?>"><?= (!$chapter->volume ?: "Vol. $chapter->volume ") . (!$chapter->chapter ?: "Ch. $chapter->chapter ") . ($chapter->title ? mb_strimwidth($chapter->title, 0, 60, "...") : "Read Online") ?></a></td>
							<td class="text-center"><?= display_lang_flag_v2($chapter->lang_name, $chapter->lang_flag, array(5, 5, 5, 5)) ?></td>
							<td><a href="/group/<?= $chapter->group_id ?>"><?= $chapter->group_name ?></a></td>
							<td class="text-right td-992" title="<?= gmdate("Y-m-d H:i:s \U\T\C", $chapter->upload_timestamp) ?>"><time datetime="<?= gmdate("Y-m-d H:i:s \U\T\C", $chapter->upload_timestamp) ?>"><?= get_time_ago($chapter->upload_timestamp) ?></time></td>
							</tr>
							<?php
						}
					
				}
				?>


				</tbody>
			</table>
		</div>			
		
		<?php
		}
		else print "<div style='margin: 10px 0;' class='alert alert-warning text-center' role='alert'>" . display_glyphicon("exclamation-triangle", "fa") . " <strong>Warning:</strong> No chapters.</div>";
		//print_r($_SESSION);
		
		?>

	</div>
	<div class="col-sm-3">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Top manga</h3>
			</div>
			
		</div>
	</div>

</div>


	


	

