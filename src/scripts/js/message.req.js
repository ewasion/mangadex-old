<?php if ($user->user_id) { ?>

$("#msg_reply_form").submit(function(event) {
	//validate input

	var formData = new FormData($(this)[0]);
	
	$("#reply_button").html("<?= display_glyphicon("spinner", "fa", "", "fa-pulse") ?> Replying...").attr("disabled", true);
	
	$.ajax({
		url: "/ajax/actions.ajax.php?function=msg_reply&id=<?= $id ?>",
		type: 'POST',
		data: formData,
		success: function (data) {
			location.href = "/message/<?= $id ?>";
		},
		cache: false,
		contentType: false,
		processData: false
		
	});

	event.preventDefault();
});	

<?php } ?>