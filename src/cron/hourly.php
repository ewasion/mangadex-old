<?php
require_once ("/home/www/mangadex.com/config.req.php"); //must be like this

require_once (ABSPATH . "/scripts/header.req.php");

$timestamp = time();

$languages = new Languages($db, "lang_id", "ASC");

foreach ($languages as $language) {
	
	$cache_file = ABSPATH . "/cache/top_chapters_6h_$language->lang_id"; // construct a cache file
	ob_start('ob_gzhandler');

	$top_chapters = $db->get_results(" SELECT mangadex_chapters.chapter_id, mangadex_chapters.chapter_views, mangadex_chapters.chapter, mangadex_chapters.manga_id, mangadex_mangas.manga_name, mangadex_mangas.manga_image, mangadex_mangas.manga_hentai 
		FROM mangadex_chapters, mangadex_mangas 
		WHERE mangadex_chapters.upload_timestamp > ($timestamp - 60*60*6) 
			AND mangadex_mangas.manga_hentai = 0 
			AND mangadex_chapters.manga_id = mangadex_mangas.manga_id 
			AND mangadex_chapters.lang_id = $language->lang_id 
		GROUP BY mangadex_chapters.manga_id 
		ORDER BY mangadex_chapters.chapter_views DESC LIMIT 10 ");

	if ($top_chapters) {
		print "<table class='table table-striped table-condensed'>";
		foreach ($top_chapters as $chapter) {
			$manga_link = "/manga/$chapter->manga_id/" . trim(preg_replace('/\W+/', '-', strtolower($chapter->manga_name)), "-");
			?>
			
			
				<tr>
					<td width="50px" rowspan="2"><a href="<?= $manga_link ?>"><img src="/images/manga/<?= $chapter->manga_id ?>.thumb.jpg" width="40px"/></a></td>
					<th height="31px" colspan="2"><?= display_glyphicon("book", "fas", "Title", "fa-fw") ?> <a title="<?= $chapter->manga_name ?>" class="manga_title" href="<?= $manga_link ?>"><?= mb_strimwidth($chapter->manga_name, 0, 33, "...") ?></a> <?= display_labels($chapter->manga_hentai, 1); ?></th>
				</tr>
				<tr>
					<td><?= display_glyphicon("file", "far", "Chapter", "fa-fw") ?> <a href="/chapter/<?= $chapter->chapter_id ?>"><?= ($chapter->chapter) ? "Chapter {$chapter->chapter}" : "Latest chapter" ?></a></td>
					<td class="text-right"><?= display_glyphicon("eye", "fas", "Total views", "fa-fw") ?> <?= $chapter->chapter_views ?></td>
				</tr>
			
			
			<?php
		}
		print "</table>";
	}
	else {
	?>

	<div class="panel-body">No chapters</div>

	<?php 
	} 

	$fp = fopen($cache_file, 'w');  //open file for writing
	fwrite($fp, ob_get_contents()); //write contents of the output buffer in Cache file
	fclose($fp); //Close file pointer

	ob_end_flush(); //Flush and turn off output buffering

}

/////////////////////////////////////////////

foreach ($languages as $language) {
	
	$cache_file = ABSPATH . "/cache/top_chapters_24h_$language->lang_id"; // construct a cache file
	ob_start('ob_gzhandler');

	$top_chapters = $db->get_results(" SELECT mangadex_chapters.chapter_id, mangadex_chapters.chapter_views, mangadex_chapters.chapter, mangadex_chapters.manga_id, mangadex_mangas.manga_name, mangadex_mangas.manga_image, mangadex_mangas.manga_hentai 
		FROM mangadex_chapters, mangadex_mangas 
		WHERE mangadex_chapters.upload_timestamp > ($timestamp - 60*60*24) 
			AND mangadex_mangas.manga_hentai = 0 
			AND mangadex_chapters.manga_id = mangadex_mangas.manga_id 
			AND mangadex_chapters.lang_id = $language->lang_id 
		GROUP BY mangadex_chapters.manga_id 
		ORDER BY mangadex_chapters.chapter_views DESC LIMIT 10 ");

	if ($top_chapters) {
		print "<table class='table table-striped table-condensed'>";
		foreach ($top_chapters as $chapter) {
			$manga_link = "/manga/$chapter->manga_id/" . trim(preg_replace('/\W+/', '-', strtolower($chapter->manga_name)), "-");
			?>
			
			
				<tr>
					<td width="50px" rowspan="2"><a href="<?= $manga_link ?>"><img src="/images/manga/<?= $chapter->manga_id ?>.thumb.jpg" width="40px"/></a></td>
					<th height="31px" colspan="2"><?= display_glyphicon("book", "fas", "Title", "fa-fw") ?> <a title="<?= $chapter->manga_name ?>" class="manga_title" href="<?= $manga_link ?>"><?= mb_strimwidth($chapter->manga_name, 0, 33, "...") ?></a> <?= display_labels($chapter->manga_hentai, 1); ?></th>
				</tr>
				<tr>
					<td><?= display_glyphicon("file", "far", "Chapter", "fa-fw") ?> <a href="/chapter/<?= $chapter->chapter_id ?>"><?= ($chapter->chapter) ? "Chapter {$chapter->chapter}" : "Latest chapter" ?></a></td>
					<td class="text-right"><?= display_glyphicon("eye", "fas", "Total views", "fa-fw") ?> <?= $chapter->chapter_views ?></td>
				</tr>
			
			
			<?php
		}
		print "</table>";
	}
	else {
	?>

	<div class="panel-body">No chapters</div>

	<?php 
	} 

	$fp = fopen($cache_file, 'w');  //open file for writing
	fwrite($fp, ob_get_contents()); //write contents of the output buffer in Cache file
	fclose($fp); //Close file pointer

	ob_end_flush(); //Flush and turn off output buffering

}


//////////////////////////////////////

$cache_file = ABSPATH . "/cache/top_chapters_6h_0"; // construct a cache file
ob_start('ob_gzhandler');

$top_chapters = $db->get_results(" SELECT mangadex_chapters.chapter_id, mangadex_chapters.chapter_views, mangadex_chapters.chapter, mangadex_chapters.manga_id, mangadex_mangas.manga_name, mangadex_mangas.manga_image, mangadex_mangas.manga_hentai 
	FROM mangadex_chapters, mangadex_mangas 
	WHERE mangadex_chapters.upload_timestamp > ($timestamp - 60*60*6) 
		AND mangadex_mangas.manga_hentai = 0 
		AND mangadex_chapters.manga_id = mangadex_mangas.manga_id 
	GROUP BY mangadex_chapters.manga_id 
	ORDER BY mangadex_chapters.chapter_views DESC LIMIT 10 ");

if ($top_chapters) {
	print "<table class='table table-striped table-condensed'>";
	foreach ($top_chapters as $chapter) {
		$manga_link = "/manga/$chapter->manga_id/" . trim(preg_replace('/\W+/', '-', strtolower($chapter->manga_name)), "-");
		?>
		
		
			<tr>
				<td width="50px" rowspan="2"><a href="<?= $manga_link ?>"><img src="/images/manga/<?= $chapter->manga_id ?>.thumb.jpg" width="40px"/></a></td>
				<th height="31px" colspan="2"><?= display_glyphicon("book", "fas", "Title", "fa-fw") ?> <a title="<?= $chapter->manga_name ?>" class="manga_title" href="<?= $manga_link ?>"><?= mb_strimwidth($chapter->manga_name, 0, 33, "...") ?></a> <?= display_labels($chapter->manga_hentai, 1); ?></th>
			</tr>
			<tr>
				<td><?= display_glyphicon("file", "far", "Chapter", "fa-fw") ?> <a href="/chapter/<?= $chapter->chapter_id ?>"><?= ($chapter->chapter) ? "Chapter {$chapter->chapter}" : "Latest chapter" ?></a></td>
				<td class="text-right"><?= display_glyphicon("eye", "fas", "Total views", "fa-fw") ?> <?= $chapter->chapter_views ?></td>
			</tr>
		
		
		<?php
	}
	print "</table>";
}
else {
?>

<div class="panel-body">No chapters</div>

<?php 
} 

$fp = fopen($cache_file, 'w');  //open file for writing
fwrite($fp, ob_get_contents()); //write contents of the output buffer in Cache file
fclose($fp); //Close file pointer

ob_end_flush(); //Flush and turn off output buffering


//////////////////////////////////////

$cache_file = ABSPATH . "/cache/top_chapters_24h_0"; // construct a cache file
ob_start('ob_gzhandler');

$top_chapters = $db->get_results(" SELECT mangadex_chapters.chapter_id, mangadex_chapters.chapter_views, mangadex_chapters.chapter, mangadex_chapters.manga_id, mangadex_mangas.manga_name, mangadex_mangas.manga_image, mangadex_mangas.manga_hentai 
	FROM mangadex_chapters, mangadex_mangas 
	WHERE mangadex_chapters.upload_timestamp > ($timestamp - 60*60*24) 
		AND mangadex_mangas.manga_hentai = 0 
		AND mangadex_chapters.manga_id = mangadex_mangas.manga_id 
	GROUP BY mangadex_chapters.manga_id 
	ORDER BY mangadex_chapters.chapter_views DESC LIMIT 10 ");

if ($top_chapters) {
	print "<table class='table table-striped table-condensed'>";
	foreach ($top_chapters as $chapter) {
		$manga_link = "/manga/$chapter->manga_id/" . trim(preg_replace('/\W+/', '-', strtolower($chapter->manga_name)), "-");
		?>
		
		
			<tr>
				<td width="50px" rowspan="2"><a href="<?= $manga_link ?>"><img src="/images/manga/<?= $chapter->manga_id ?>.thumb.jpg" width="40px"/></a></td>
				<th height="31px" colspan="2"><?= display_glyphicon("book", "fas", "Title", "fa-fw") ?> <a title="<?= $chapter->manga_name ?>" class="manga_title" href="<?= $manga_link ?>"><?= mb_strimwidth($chapter->manga_name, 0, 33, "...") ?></a> <?= display_labels($chapter->manga_hentai, 1); ?></th>
			</tr>
			<tr>
				<td><?= display_glyphicon("file", "far", "Chapter", "fa-fw") ?> <a href="/chapter/<?= $chapter->chapter_id ?>"><?= ($chapter->chapter) ? "Chapter {$chapter->chapter}" : "Latest chapter" ?></a></td>
				<td class="text-right"><?= display_glyphicon("eye", "fas", "Total views", "fa-fw") ?> <?= $chapter->chapter_views ?></td>
			</tr>
		
		
		<?php
	}
	print "</table>";
}
else {
?>

<div class="panel-body">No chapters</div>

<?php 
} 

$fp = fopen($cache_file, 'w');  //open file for writing
fwrite($fp, ob_get_contents()); //write contents of the output buffer in Cache file
fclose($fp); //Close file pointer

ob_end_flush(); //Flush and turn off output buffering
?>