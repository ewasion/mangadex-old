<?php if ($user->user_id) { ?>

$("#group_add_form").submit(function(event) {
	//validate input
	
	var success_msg = "<div class='alert alert-success text-center' role='alert'><strong>Success:</strong> This group has been added.</div>";

	var formData = new FormData($(this)[0]);
	
	$("#group_add_button").html("<?= display_glyphicon("spinner", "fas", "", "fa-pulse fa-fw") ?> Adding...").attr("disabled", true);
	
	$.ajax({
		url: "/ajax/actions.ajax.php?function=group_add",
		type: 'POST',
		data: formData,
		success: function (data) {
			if (!data) {
				$("#message_container").html(success_msg).show().delay(<?= FADE_DURATION ?>).fadeOut();
				location.href = "/groups";
			}
			else {
				$("#message_container").html(data).show().delay(<?= FADE_DURATION ?>).fadeOut();
				$("#group_add_button").html("<?= display_glyphicon('plus-circle', 'fas', '', 'fa-fw') ?> Add new group").attr("disabled", false);
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