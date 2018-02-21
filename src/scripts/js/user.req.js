<?php if ($user->level_id >= 10) { ?>

$(".toggle_mass_edit_button").click(function(event){
	id = $(this).attr("id"); 
	$("#toggle_mass_edit_"+id).toggle();
	$("#chapter_"+id).toggle();
	event.preventDefault();
});	

$(".cancel_mass_edit_button").click(function(event){
	id = $(this).attr("id"); 
	$("#toggle_mass_edit_"+id).toggle();
	$("#chapter_"+id).toggle();
	event.preventDefault();
});	

$(".mass_edit_form").submit(function(event) {
	
	id = $(this).attr("id"); 
	
	var formData = new FormData($(this)[0]);
	
	$("#mass_edit_button_"+id).html("<?= display_glyphicon("spinner", "fas", "", "fa-pulse fa-fw") ?>").attr("disabled", true);
	
	$.ajax({
		url: "/ajax/actions.ajax.php?function=chapter_edit&id="+id,
		type: 'POST',
		data: formData,
		success: function(data) {
			$("#mass_edit_button_"+id).html("<?= display_glyphicon("check", "fas", "", "fa-fw") ?>").attr("disabled", false);
		},
		cache: false,
		contentType: false,
		processData: false
	});
	
	event.preventDefault();
});	

$(".mass_edit_delete_button").click(function(event){
	if (confirm("Are you sure?")) {
		id = $(this).attr("id");
		$(this).html("<?= display_glyphicon("spinner", "fas", "", "fa-pulse fa-fw") ?>").attr("disabled", true);
		$.ajax({
			url: "/ajax/actions.ajax.php?function=chapter_delete&id="+id,
			success: function(data) {
				$("#toggle_mass_edit_"+id).remove();
			},
			cache: false,
			contentType: false,
			processData: false
		});	
	}
	event.preventDefault();
});

$("#unban_button").click(function(event){
	$("#unban_button").html("<?= display_glyphicon("spinner", "fa", "", "fa-pulse fa-fw") ?> Banning...").attr("disabled", true);
	$.ajax({
		url: "/ajax/actions.ajax.php?function=unban_user&id=<?= $id ?>",
		type: 'GET',
		success: function (data) {
			$("#message_container").html(data).show().delay(1500).fadeOut();
			location.href = "/user/<?= $id ?>";
		},
		cache: false,
		contentType: false,
		processData: false
		
	});
	event.preventDefault();
});				
$("#ban_button").click(function(event){
	$("#ban_button").html("<?= display_glyphicon("spinner", "fa", "", "fa-pulse fa-fw") ?> Unbanning...").attr("disabled", true);
	$.ajax({
		url: "/ajax/actions.ajax.php?function=ban_user&id=<?= $id ?>",
		type: 'GET',
		success: function (data) {
			$("#message_container").html(data).show().delay(1500).fadeOut();
			location.href = "/user/<?= $id ?>";
		},
		cache: false,
		contentType: false,
		processData: false
		
	});
	event.preventDefault();
});	

<?php } ?>	

<?php if ($user->level_id >= 15) { ?>
$("#admin_edit_user_form").submit(function(event) {
	//validate input

	var formData = new FormData($(this)[0]);
	
	$("#admin_edit_user_button").html("<?= display_glyphicon("spinner", "fa", "", "fa-pulse fa-fw") ?> Saving...").attr("disabled", true);
	
	$.ajax({
		url: "/ajax/actions.ajax.php?function=admin_edit_user&id=<?= $id ?>",
		type: 'POST',
		data: formData,
		success: function (data) {
			$("#admin_edit_user_button").html("<?= display_glyphicon("edit", "fa", "", "fa-fw") ?> Save").attr("disabled", false);
			$("#message_container").html(data).show().delay(1500).fadeOut();
		},
		cache: false,
		contentType: false,
		processData: false
	});
	event.preventDefault();
});	 
<?php } ?>