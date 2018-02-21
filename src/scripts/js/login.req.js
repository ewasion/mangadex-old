<?php if (!$user->user_id) { ?>

$("#login_form").submit(function(event) {
	//validate input
	
	var success_msg = "<div class='alert alert-success text-center' role='alert'><strong>Success:</strong> You have logged in.</div>";

	var formData = new FormData($(this)[0]);
	
	$("#login_button").html("<?= display_glyphicon("spinner", "fa", "", "fa-pulse fa-fw") ?> Logging in...").attr("disabled", true);
	
	$.ajax({
		url: "/ajax/actions.ajax.php?function=login",
		type: 'POST',
		data: formData,
		success: function(data) {
			if (!data) {
				location.href = "<?= (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : '/' ?>";
			}
			else {
				$("#message_container").html(data).show().delay(1500).fadeOut();
				$("#login_button").html("Log in").attr("disabled", false);
			}
		},
		cache: false,
		contentType: false,
		processData: false
	});
	
	event.preventDefault();
	
});

$("#reset_form").submit(function(event) {
	//validate input

	var success_msg = "<div class='alert alert-success text-center' role='alert'><strong>Success:</strong> Check your email for your reset code.</div>";

	var formData = new FormData($(this)[0]);
	
	$("#reset_button").html("<?= display_glyphicon("spinner", "fa", "", "fa-pulse fa-fw") ?> Sending password...").attr("disabled", true);
	
	$.ajax({
		url: "/ajax/actions.ajax.php?function=reset_email",
		type: 'POST',
		data: formData,
		success: function(data) {
			if (!data) {
				$("#forgot_container").hide();
				$("#login_container").fadeIn();
				$("#message_container").html(success_msg).show().delay(1500).fadeOut();
				$("#reset_button").html("<?= display_glyphicon("spinner", "fa", "", "fa-fw") ?> Reset Password").attr("disabled", false);
			}
			else {
				$("#message_container").html(data).show().delay(1500).fadeOut();
				$("#reset_button").html("<?= display_glyphicon("spinner", "fa", "", "fa-fw") ?> Reset Password").attr("disabled", false);
			}
		},
		cache: false,
		contentType: false,
		processData: false
	});

	event.preventDefault();
});

$("#forgot_button").click(function(){
	$("#login_container").hide();
	$("#forgot_container").fadeIn();
});	

<?php } ?>