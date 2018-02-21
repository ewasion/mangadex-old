<?php if ($user->user_id) { ?>

$("#import_form").submit(function(event) {
	//validate input

	var success_msg = "<div class='alert alert-success text-center' role='alert'><strong>Success:</strong> Your json has been imported.</div>";

	var formData = new FormData($(this)[0]);
	
	$("#import_button").html("<?= display_glyphicon("spinner", "fas", "", "fa-pulse") ?> Importing...").attr("disabled", true);
	
	$.ajax({
		url: "/ajax/actions.ajax.php?function=import_json",
		type: 'POST',
		data: formData,
		success: function (data) {
			if (!data) {
				$("#message_container").html(success_msg).show().delay(<?= FADE_DURATION ?>).fadeOut();
				location.href = "/follows";
			}
			else {
				$("#message_container").html(data).show().delay(<?= FADE_DURATION ?>).fadeOut();
				$("#import_button").html("<?= display_glyphicon("upload", "fas", "", "fa-fw") ?> Import").attr("disabled", false);
			}
			//alert(data);
		},
		cache: false,
		contentType: false,
		processData: false
	});

	event.preventDefault();
});	

<?php } ?>