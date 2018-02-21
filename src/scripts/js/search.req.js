$("#search_titles_form").submit(function(event) {

	var search_title = encodeURIComponent($("#manga_title").val());
	var search_author = encodeURIComponent($("#manga_author").val());
	var search_artist = encodeURIComponent($("#manga_artist").val());
	var manga_genre_ids = commaMultipleSelect("manga_genre_ids");
	
	$("#search_button").html("<?= display_glyphicon("spinner", "fas", "", "fa-pulse fa-fw") ?> Searching...").attr("disabled", true);
	
	location.href = "/?page=search&title="+search_title+"&author="+search_author+"&artist="+search_artist+"&genres="+manga_genre_ids;

	event.preventDefault();

});