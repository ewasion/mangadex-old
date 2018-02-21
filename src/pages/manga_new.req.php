<?php
$_GET["alpha"] = $_GET["alpha"] ?? "";

$languages = new Languages($db, "lang_name", "ASC");
$genres = new Genres($db);

$manga_by_letter = $db->get_results(" SELECT substr(manga_name,1,1) as alpha, count(*) AS Rows FROM mangadex_mangas WHERE manga_name REGEXP '^[A-Za-z]' GROUP BY substr(manga_name,1,1) ");

?>
<ul class="nav nav-tabs" style="margin-bottom: 20px;">
	<li title="Advanced search" role="presentation"><a href="/search"><?= display_glyphicon("search-plus", "fas", "Advanced search", "fa-fw") ?></a></li>
	<li title="Last updated" role="presentation"><a href="/titles"><?= display_glyphicon("sync", "fas", "Last updated", "fa-fw") ?></a></li>
	<li title="Other" role="presentation"><a href="/titles/~">~</a></li>
	<?php 
	foreach ($manga_by_letter as $letter) {
		$active = ($letter->alpha != $_GET["alpha"]) ?: "active";
		print "<li title='$letter->Rows titles' role='presentation'><a href='/titles/$letter->alpha'>$letter->alpha</a></li>"; 
	}
	?>

	<li title="Add manga title" role="presentation" class="active pull-right"><a href="/manga_new"><?= display_glyphicon("plus-circle", "fas", "", "fa-fw") ?></a></li>
</ul>

<div class="panel panel-default">	
	<div class="panel-heading">
		<h3 class="panel-title"><?= display_glyphicon("plus-circle", "fas", "", "fa-fw") ?> Add new title</h3>
	</div>
	<div class="panel-body">
		<form style="margin-top: 15px;" id="manga_add_form" method="post" class="form-horizontal" enctype="multipart/form-data">
			<div class="form-group">
				<label for="manga_name" class="col-sm-3 control-label">Name</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" id="manga_name" name="manga_name" placeholder="(Usually English or romanized)" required>
				</div>
			</div>
			<div class="form-group">
				<label for="manga_alt_names" class="col-sm-3 control-label">Alternative name(s)</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" id="manga_alt_names" name="manga_alt_names" placeholder="(eg Japanese name)">
				</div>
			</div>
			<div class="form-group">
				<label for="manga_author" class="col-sm-3 control-label">Author</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" id="manga_author" name="manga_author" required>
				</div>
			</div>
			<div class="form-group">
				<label for="manga_artist" class="col-sm-3 control-label">Artist</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" id="manga_artist" name="manga_artist" >
				</div>
			</div>
			<div class="form-group">
				<label for="manga_lang_id" class="col-sm-3 control-label">Original language</label>
				<div class="col-sm-9">   
					<select class="form-control selectpicker" id="manga_lang_id" name="manga_lang_id">
						<?php 
						foreach ($orig_lang_array as $key => $language) {
							print "<option value='$key'>$language</option>"; 
						}
						?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label for="manga_status_id" class="col-sm-3 control-label">Status</label>
				<div class="col-sm-9">   
					<select class="form-control selectpicker" id="manga_status_id" name="manga_status_id">
						<?php 
						foreach ($status_array as $key => $status) {
							print "<option $selected value='$key'>$status</option>"; 
						}
						?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label for="manga_hentai" class="col-sm-3 control-label">R-18+</label>
				<div class="col-sm-9">
					<div class="checkbox">  
						<label><input type="checkbox" name="manga_hentai" id="manga_hentai" value="1"></label>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label for="manga_description" class="col-sm-3 control-label">Description</label>
				<div class="col-sm-9">
					<textarea class="form-control" rows="11" id="manga_description" name="manga_description" placeholder="Optional"></textarea>
				</div>
			</div>
			<div class="form-group">
				<label for="file" class="col-sm-3 control-label">Image</label>
				<div class="col-sm-9">
					<div class="input-group">
						<input type="text" class="form-control" placeholder="Filename (.png and .jpg only) [Required]" readonly disabled>
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
					<button type="submit" class="btn btn-default" id="manga_add_button"><?= display_glyphicon("plus-circle", "fas", "", "fa-fw") ?> Add new title</button>
				</div>
			</div>
		</form>	
	</div>
</div>	  
