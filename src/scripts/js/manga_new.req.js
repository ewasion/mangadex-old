$('.btn-file :file').on('fileselect', function(event, numFiles, label) {
		
	var input = $(this).parents('.input-group').find(':text'),
		log = numFiles > 1 ? numFiles + ' files selected' : label;
	
	if( input.length ) {
		input.val(log);
	} else {
		if( log ) alert(log);
	}
		
});
$("#manga_add_form").submit(function(event) {
	//validate input
	
	var success_msg = "<div class='alert alert-success text-center' role='alert'><strong>Success:</strong> This title has been added.</div>";

	var formData = new FormData($(this)[0]);
	
	$("#manga_add_button").html("<?= display_glyphicon("spinner", "fas", "", "fa-pulse fa-fw") ?> Adding...").attr("disabled", true);
	
	$.ajax({
		url: "/ajax/actions.ajax.php?function=manga_add",
		type: 'POST',
		data: formData,
		success: function (data) {
			if (!data) {
				$("#message_container").html(success_msg).show().delay(<?= FADE_DURATION ?>).fadeOut();
				location.href = "/titles";
			}
			else {
				$("#message_container").html(data).show().delay(<?= FADE_DURATION ?>).fadeOut();
				$("#manga_add_button").html("<?= display_glyphicon('plus-circle', 'fas', '', 'fa-fw') ?> Add new title").attr("disabled", false);
			}
			//alert(data);
		},
		cache: false,
		contentType: false,
		processData: false
	});

	event.preventDefault();
});		