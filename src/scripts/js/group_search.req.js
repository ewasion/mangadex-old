$("#group_search_form").submit(function(event) {

	var group_name = encodeURIComponent($("#group_name").val());

	$("#search_button").html("<?= display_glyphicon("spinner", "fas", "", "fa-pulse fa-fw") ?> Searching...").attr("disabled", true);
	
	location.href = "/group_search/"+group_name;

	event.preventDefault();

});