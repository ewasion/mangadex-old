<?php
$lang_id = $_GET['lang_id'] ?? $user->language;
$_GET['lang_id'] = $lang_id;

$chapter_by_lang = $db->get_results(" SELECT * FROM mangadex_languages WHERE has_chapters = 1 ORDER BY lang_id ASC ");

				
/*if ($hentai_toggle == 1) $rss_hentai = "&h=0"; 
elseif ($hentai_toggle == 2) $rss_hentai = "&h=1"; 
else $rss_hentai = ""; */

//$followed_manga_array = ($user->user_id) ? $user->get_followed_manga_ids($db) : array("");
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
		require(ABSPATH . "/pages/grouped_chapters.req.php"); 

	?>

	</div>
	<div class="col-sm-3">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title text-center">My follows</h3>
			</div>
			<div class="panel-body">
				<?php if ($user->user_id) { ?>
				Hello! Soon you'll see your followed manga here.
				<?php } else { ?>
				Please <a href="/signup">sign up</a> to use this feature! You can also change to a dark theme with an account.
				<?php } ?>				
			</div>
		</div>
		
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title text-center">Top chapters</h3>
			</div>
			
			<ul class="nav nav-tabs nav-justified" role="tablist">
				<li role="presentation" class="active"><a href="#six-hours" aria-controls="six-hours" role="tab" data-toggle="tab">6h</a></li>
				<li role="presentation"><a href="#day" aria-controls="messages" role="tab" data-toggle="tab">24h</a></li>
				<li role="presentation"><a href="#week" aria-controls="week" role="tab" data-toggle="tab">7 days</a></li>
			</ul>
			
			<div class="tab-content">
				<div role="tabpanel" class="tab-pane active" id="six-hours"><?php readfile(ABSPATH . "/cache/top_chapters_6h_$lang_id"); ?></div>
				<div role="tabpanel" class="tab-pane" id="day"><?php readfile(ABSPATH . "/cache/top_chapters_24h_$lang_id"); ?></div>
				<div role="tabpanel" class="tab-pane" id="week"><?php readfile(ABSPATH . "/cache/top_chapters_7d_$lang_id"); ?></div>
			</div>
		</div>	
		
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title text-center">Random manga</h3>
			</div>
			<div class="panel-body">
				<?php 
				$lang_filter = ($lang_id) ? "AND mangadex_chapters.lang_id = $lang_id " : "";
				
				$manga = $db->get_row(" SELECT mangadex_mangas.manga_id, mangadex_mangas.manga_image, mangadex_mangas.manga_name 
					FROM mangadex_mangas, mangadex_chapters  
					WHERE mangadex_mangas.manga_image NOT LIKE '' 
						AND mangadex_mangas.manga_hentai = 0 
						AND mangadex_mangas.manga_last_updated > 0 
						$lang_filter 
						AND mangadex_chapters.manga_id = mangadex_mangas.manga_id 
					ORDER BY RAND() LIMIT 1 "); 
					

				if ($manga) { 
				?>	
					<a title="<?= $manga->manga_name ?>" href="/manga/<?= $manga->manga_id ?>"><img width="100%" src="/images/manga/<?= ($manga->manga_image) ? "$manga->manga_id.$manga->manga_image" : "default.png" ?>" alt="image" /></a>
				
				<?php } else { ?>
				No manga.
				<?php } ?>
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Recent forum posts</h3>
			</div>
			<div class="panel-body">
				When I get round to coding a forum >_>			
			</div>
		</div>
	</div>

</div>


	


	
