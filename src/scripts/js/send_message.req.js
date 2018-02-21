<?php if ($user->user_id) { ?>

$("#msg_send_form").submit(function(event) {
	//validate input

	var formData = new FormData($(this)[0]);
	
	$("#send_button").html("<?= display_glyphicon("spinner", "fa", "", "fa-pulse") ?> Sending...").attr("disabled", true);
	
	$.ajax({
		url: "/ajax/actions.ajax.php?function=msg_send",
		type: 'POST',
		data: formData,
		success: function(data) {
			if (!data) {
				location.href = "/messages";
			}
			else {
				$("#message_container").html(data).show().delay(1500).fadeOut();
				$("#send_button").html("Send message").attr("disabled", false);
			}
		},
		cache: false,
		contentType: false,
		processData: false
		
	});

	event.preventDefault();
});	

<?php } ?>