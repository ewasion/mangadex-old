<?php
$array_of_manga_ids = $user->get_followed_manga_ids($db);

$lang_id = $_GET['lang_id'] ?? $user->language;
$_GET['lang_id'] = $lang_id;
?>


<!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
	<li role="presentation" class="active"><a href="#manga_followed" aria-controls="manga_followed" role="tab" data-toggle="tab"><?= display_glyphicon("book", "fas", "", "fa-fw") ?> Manga</a></li>
	<li role="presentation"><a href="#chapters" aria-controls="chapters" role="tab" data-toggle="tab"><?= display_glyphicon("file", "far", "", "fa-fw") ?> Latest updates</a></li>
	<li role="presentation"><a href="#groups_followed" aria-controls="groups_followed" role="tab" data-toggle="tab"><?= display_glyphicon("users", "fas", "", "fa-fw") ?> Groups</a></li>
	<li role="presentation"><a href="#import" aria-controls="import" role="tab" data-toggle="tab"><?= display_glyphicon("upload", "fas", "", "fa-fw") ?> Import from Batoto</a></li>
</ul>

<!-- Tab panes -->
<div class="tab-content">	
	<div role="tabpanel" class="tab-pane fade in active" id="manga_followed">
		<?php 		
		if ($array_of_manga_ids) 
			require_once(ABSPATH . "/pages/mangas.req.php"); 
		else 
			print "<div style='margin: 10px 0;' class='alert alert-warning text-center' role='alert'>" . display_glyphicon("exclamation-triangle", "fa") . " <strong>Warning:</strong> You haven't followed any manga or groups!</div>";
		?>
	</div>
	
	<div role="tabpanel" class="tab-pane fade" id="chapters">
		<?php 		
		if ($array_of_manga_ids) {
			$order = "upload_timestamp desc";
			
			if ($user->default_lang_ids)
				$search["multi_lang_id"] = $user->default_lang_ids;
			
			$search["chapter_deleted"] = 0;
			$search["grouped_chapters"] = $timestamp;
			
			require_once(ABSPATH . "/pages/chapters.req.php"); 
		}
		else 
			print "<div style='margin: 10px 0;' class='alert alert-warning text-center' role='alert'>" . display_glyphicon("exclamation-triangle", "fa") . " <strong>Warning:</strong> You haven't followed any manga or groups!</div>";
		?>
	</div>
	
	<div role="tabpanel" class="tab-pane fade" id="groups_followed">
		list of groups followed
						
	</div>
	
	<div role="tabpanel" class="tab-pane fade" id="import">
		<?php $json = $db->get_var(" SELECT json FROM mangadex_import WHERE user_id = $user->user_id ORDER BY id DESC LIMIT 1 "); ?>
		<form style="margin-top: 15px;" id="import_form" method="post" class="form-horizontal">
			<textarea rows="10" name="json" class="form-control" placeholder="Open your JSON file in notepad and paste here. No need to remove manga that you have followed on MangaDex, as those will be ignored. Your JSON will be saved to the server and processed when the site is less busy."><?= $json ?></textarea>
			
			<button type="submit" class="btn btn-default" id="import_button"><?= display_glyphicon("upload", "fas", "", "fa-fw") ?> <span class="span-1280">Upload</span></button>
			
		</form>	
	</div>
</div>