<?php 
$genres = new Genres($db);
$_GET["genres"] = isset($_GET["genres"]) ? htmlentities($_GET["genres"], ENT_QUOTES) : "";
$_GET["title"] = isset($_GET["title"]) ? htmlentities($_GET["title"], ENT_QUOTES) : "";
$_GET["author"] = isset($_GET["author"]) ? htmlentities($_GET["author"], ENT_QUOTES) : "";
$_GET["artist"] = isset($_GET["artist"]) ? htmlentities($_GET["artist"], ENT_QUOTES) : "";
?>

<div class="panel panel-default">	
	<div class="panel-heading">
		<h3 class="panel-title"><?= display_glyphicon("search-plus", "fas", "", "fa-fw") ?> Search</h3>
	</div>
	<div class="panel-body">
		<form style="margin-top: 15px;" id="search_titles_form" method="post" class="form-horizontal">
			<div class="form-group">
				<label for="manga_title" class="col-sm-3 control-label">Manga title</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" id="manga_title" name="manga_title" value="<?= $_GET["title"] ?>">
				</div>
			</div>
			<div class="form-group">
				<label for="manga_author" class="col-sm-3 control-label">Author</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" id="manga_author" name="manga_author" value="<?= $_GET["author"] ?>">
				</div>
			</div>
			<div class="form-group">
				<label for="manga_artist" class="col-sm-3 control-label">Artist</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" id="manga_artist" name="manga_artist" value="<?= $_GET["artist"] ?>">
				</div>
			</div>
			<div class="form-group">
				<label for="manga_genres" class="col-sm-3 control-label">Genres</label>
				<div class="col-sm-9">
					<select multiple class="form-control selectpicker show-tick" data-selected-text-format="count > 4" data-size="10" id="manga_genre_ids" name="manga_genre_ids" title="All genres">

						<?php 
						foreach ($genres as $genre) {
							
							$selected = in_array($genre->genre_id, explode(",", $_GET["genres"])) ? "selected" : "";
							print "<option $selected value='$genre->genre_id'>$genre->genre_name</option>"; 
						}
						?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-offset-3 col-sm-9">
					<button type="submit" class="btn btn-default" id="search_button"><?= display_glyphicon("search", "fas", "", "fa-fw") ?> <span class="span-1280">Search</span></button>
				</div>
			</div>
			
		</form>	
	</div>
</div>	 

<?php require_once(ABSPATH . "/pages/mangas.req.php"); ?>