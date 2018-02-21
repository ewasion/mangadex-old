<?php if (!$user->user_id) { ?>

$("#signup_form").submit(function(event) {
	//validate input

	var success_msg = "<div class='alert alert-success text-center' role='alert'><?= display_glyphicon("ok") ?> <strong>Success:</strong> You have signed up.</div>";

	var formData = new FormData($(this)[0]);

	$("#signup_button").html("<?= display_glyphicon("spinner", "fa", "", "fa-pulse fa-fw") ?> Signing up...").attr("disabled", true);
	
	$.ajax({
		url: "/ajax/actions.ajax.php?function=signup",
		type: 'POST',
		data: formData,
		success: function(data) {
			if (!data) {
				$("#message_container").html(success_msg).show().delay(1500).fadeOut();
				location.href = "/login";
			}
			else {
				$("#message_container").html(data).show().delay(1500).fadeOut();
				$("#signup_button").html("Sign up").attr("disabled", false);
			}
		},
		cache: false,
		contentType: false,
		processData: false
	});

	event.preventDefault();
});

$("[data-toggle='popover']").popover({
	"container": "body",
	"trigger": "focus",
	"placement": "auto left"
});		

<?php } ?>