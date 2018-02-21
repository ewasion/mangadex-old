<?php if ($user->user_id) { ?>

$("#activation_form").submit(function(event) {
	//validate input

	var success_msg = "<div class='alert alert-success text-center' role='alert'><?= display_glyphicon("ok") ?> <strong>Success:</strong> Activation successful.</div>";
	
	$("#activate_button").html("<?= display_glyphicon("spinner", "fa", "", "fa-pulse") ?> Activating...").attr("disabled", true);
	
	var formData = new FormData($(this)[0]);

	$.ajax({
		url: "/ajax/actions.ajax.php?function=activate",
		type: 'POST',
		data: formData,
		success: function(data) {
			if (!data) {
				location.href = "/";
			}
			else {
				$("#message_container").html(data).show().delay(1500).fadeOut();
				$("#activate_button").html("Activate").attr("disabled", false);
			}
		},
		cache: false,
		contentType: false,
		processData: false
	});

	event.preventDefault();
});

$("#resend_button").click(function(){
	$.ajax({
		url: "/ajax/actions.ajax.php?function=resend_activation_code",
		success: function(data) {
			$("#message_container").html(data).show().delay(1500).fadeOut();
		},
		cache: false,
		contentType: false,
		processData: false
	});	
});	

<?php } ?>