$("#unlike_button").click(function(event){
	$("#unlike_button").html("<?= display_glyphicon("spinner", "fa", "", "fa-pulse fa-fw") ?> Unliking...").attr("disabled", true);
	$.ajax({
		url: "/ajax/actions.ajax.php?function=unlike_group&id=<?= $id ?>",
		type: 'GET',
		success: function (data) {
			$("#message_container").html(data).show().delay(1500).fadeOut();
			location.href = "/group/<?= $id ?>";
		},
		cache: false,
		contentType: false,
		processData: false
		
	});
	event.preventDefault();
});				
$("#like_button").click(function(event){
	$("#like_button").html("<?= display_glyphicon("spinner", "fa", "", "fa-pulse fa-fw") ?> Liking...").attr("disabled", true);
	$.ajax({
		url: "/ajax/actions.ajax.php?function=like_group&id=<?= $id ?>",
		type: 'GET',
		success: function (data) {
			$("#message_container").html(data).show().delay(1500).fadeOut();
			location.href = "/group/<?= $id ?>";
		},
		cache: false,
		contentType: false,
		processData: false
		
	});
	event.preventDefault();
});	

<?php if ($user->user_id) { ?>

$("#comment_group_form").submit(function(event) {
	//validate input

	var formData = new FormData($(this)[0]);
	
	$("#comment_button").html("<?= display_glyphicon("spinner", "fa", "", "fa-pulse") ?> Commenting...").attr("disabled", true);
	
	$.ajax({
		url: "/ajax/actions.ajax.php?function=group_comment&id=<?= $id ?>",
		type: 'POST',
		data: formData,
		success: function (data) {
			$("#message_container").html(data).show().delay(1500).fadeOut();
			location.href = "/group/<?= $id ?>";
		},
		cache: false,
		contentType: false,
		processData: false
		
	});

	event.preventDefault();
});	
$(".delete_comment_button").click(function(event){
	if (confirm("Are you sure?")) {
		group_id = $(this).attr("id"); 
		$.ajax({
			url: "/ajax/actions.ajax.php?function=group_comment_delete&id="+group_id,
			success: function(data) {
				$("#message_container").html(data).show().delay(1500).fadeOut();
				location.href = "/group/<?= $id ?>";
			},
			cache: false,
			contentType: false,
			processData: false
		});	
	}
	event.preventDefault();
});

<?php } ?>

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

<?php } ?>

<?php if ($user->level_id >= 10 || $group->group_leader_id == $user->user_id) { //only display for relevant people ?>
$("#edit_button").click(function(event){
	$(".edit").toggle();
	event.preventDefault();
});	
$("#cancel_edit_button").click(function(event){
	$(".edit").toggle();
	event.preventDefault();
});	

$("#edit_members_button").click(function(event){
	$(".edit-members").toggle();
	event.preventDefault();
});	
$("#cancel_edit_members_button").click(function(event){
	$(".edit-members").toggle();
	event.preventDefault();
});	

<?= js_display_file_select(); ?>

<?= jquery_post("group_edit", $id, "pencil-alt", "Save", "Saving", "This group has been edited.", "group/$id"); ?>

$("#edit_group_members_form").submit(function(event) {
	//validate input

	var formData = new FormData($(this)[0]);
	
	$("#save_edit_members_button").html("<?= display_glyphicon("spinner", "fa", "", "fa-pulse fa-fw") ?> Saving...").attr("disabled", true);
	
	$.ajax({
		url: "/ajax/actions.ajax.php?function=group_add_member&id=<?= $id ?>",
		type: 'POST',
		data: formData,
		success: function (data) {
			$("#message_container").html(data).show().delay(1500).fadeOut();
			location.href = "/group/<?= $id ?>";
		},
		cache: false,
		contentType: false,
		processData: false
		
	});

	event.preventDefault();
});	

$(".group_delete_member").click(function(event){
	user_id = $(this).attr("id"); 
	$.ajax({
		url: "/ajax/actions.ajax.php?function=group_delete_member&group_id=<?= $id ?>&user_id="+user_id,
		type: 'GET',
		success: function (data) {
			$("#message_container").html(data).show().delay(1500).fadeOut();
			location.href = "/group/<?= $id ?>";
		},
		cache: false,
		contentType: false,
		processData: false
		
	});
	event.preventDefault();
});	

$("#scan_button").click(function(event){
	$.ajax({
		url: "/ajax/actions.ajax.php?function=auth_rescan&group_id=<?= $id ?>",
		type: 'GET',
		success: function (data) {
			$("#message_container").html(data).show().delay(1500).fadeOut();
			location.href = "/group/<?= $id ?>";
		},
		cache: false,
		contentType: false,
		processData: false
		
	});
	event.preventDefault();
});	

<?php } ?>

<?php if ($user->level_id >= 15) { ?>
$("#delete_button").click(function(event){
	if (confirm("Are you sure?")) {
		$.ajax({
			url: "/ajax/actions.ajax.php?function=group_delete&id=<?= $id ?>",
			success: function(data) {
				$("#message_container").html(data).show().delay(100).fadeOut();
				location.href = "/groups";
			},
			cache: false,
			contentType: false,
			processData: false
		});	
	}
	event.preventDefault();
});

$("#admin_edit_group_form").submit(function(event) {
	//validate input

	var formData = new FormData($(this)[0]);
	
	$("#admin_edit_group_button").html("<?= display_glyphicon("spinner", "fa", "", "fa-pulse fa-fw") ?> Saving...").attr("disabled", true);
	
	$.ajax({
		url: "/ajax/actions.ajax.php?function=admin_edit_group&id=<?= $id ?>",
		type: 'POST',
		data: formData,
		success: function (data) {
			$("#admin_edit_group_button").html("<?= display_glyphicon("edit", "fa", "", "fa-fw") ?> Save").attr("disabled", false);
			$("#message_container").html(data).show().delay(1500).fadeOut();
		},
		cache: false,
		contentType: false,
		processData: false
	});
	event.preventDefault();
});	 
<?php } ?>