<?php if ($manga->exists) {	?>

$(".btn-spoiler").click(function(){
	$(this).next(".spoiler").toggle();
});	


<?php if (($user->level_id >= 3 && !$manga->manga_locked) || $user->level_id >= 10) { ?>

$("#edit_button").click(function(event){
	$(".edit").toggle();
	desc = $('#manga_description').val();
    desc = desc.replace(/<br \/>/g, '\n');
    desc = desc.replace(/•/g, '[*]');
	desc = desc.replace(/http(?:s)?:\/\/(?:www\.)?(?:bato\.to|batoto\.net)\/comic(?:\/_)?\/(?:comics\/)?[a-zA-Z0-9%\-]+-r([0-9]+)/g, 'https://mangadex.com/manga/$1');
    $('#manga_description').val(desc);
	event.preventDefault();
});	
$("#cancel_edit_button").click(function(event){
	$(".edit").toggle();
	event.preventDefault();
});	

<?= js_display_file_select(); ?>

<?= jquery_post("manga_edit", $id, "pencil-alt", "Save", "Saving", "This title has been edited.", "manga/$id"); ?>

<?php } ?>

<?php if ($user->user_id) { ?>

$("#upload_button").click(function(event){
	location.href = "/upload/"+<?= $manga->manga_id ?>;
	event.preventDefault();
});	


$("#unfollow_button").click(function(event){
	$("#unfollow_button").html("<?= display_glyphicon("spinner", "fa", "", "fa-pulse fa-fw") ?> Unfollowing...").attr("disabled", true);
	$.ajax({
		url: "/ajax/actions.ajax.php?function=manga_unfollow&id=<?= $id ?>",
		type: 'GET',
		success: function (data) {
			$("#message_container").html(data).show().delay(1500).fadeOut();
			location.href = "/manga/<?= $id ?>";
		},
		cache: false,
		contentType: false,
		processData: false
		
	});
	event.preventDefault();
});				
$("#follow_button").click(function(event){
	$("#follow_button").html("<?= display_glyphicon("spinner", "fa", "", "fa-pulse fa-fw") ?> Following...").attr("disabled", true);
	$.ajax({
		url: "/ajax/actions.ajax.php?function=manga_follow&id=<?= $id ?>",
		type: 'GET',
		success: function (data) {
			$("#message_container").html(data).show().delay(1500).fadeOut();
			location.href = "/manga/<?= $id ?>";
		},
		cache: false,
		contentType: false,
		processData: false
		
	});
	event.preventDefault();
});	

<?= jquery_post("manga_comment", $id, "comment", "Comment", "Commenting", "Your comment has been submitted.", "manga/$id"); ?>

$(".delete_comment_button").click(function(event){
	if (confirm("Are you sure?")) {
		comment_id = $(this).attr("id"); 
		$.ajax({
			url: "/ajax/actions.ajax.php?function=manga_comment_delete&id="+comment_id,
			success: function(data) {
				$("#message_container").html(data).show().delay(1500).fadeOut();
				location.href = "/manga/<?= $id ?>";
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
	
$("#lock_button").click(function(event){
	if (confirm("Confirm lock?")) {
		$.ajax({
			url: "/ajax/actions.ajax.php?function=mod_lock_manga&id=<?= $id ?>",
			success: function(data) {
				$("#message_container").html(data).show().delay(1500).fadeOut();
				location.href = "/manga/<?= $id ?>";
			},
			cache: false,
			contentType: false,
			processData: false
		});	
	}
	event.preventDefault();
});

$("#unlock_button").click(function(event){
	if (confirm("Confirm unlock?")) {
		$.ajax({
			url: "/ajax/actions.ajax.php?function=mod_unlock_manga&id=<?= $id ?>",
			success: function(data) {
				$("#message_container").html(data).show().delay(1500).fadeOut();
				location.href = "/manga/<?= $id ?>";
			},
			cache: false,
			contentType: false,
			processData: false
		});	
	}
	event.preventDefault();
});
<?php } ?>

<?php if ($user->level_id >= 15) { ?>
$("#delete_button").click(function(event){
	if (confirm("Are you sure?")) {
		$.ajax({
			url: "/ajax/actions.ajax.php?function=manga_delete&id=<?= $id ?>",
			success: function(data) {
				$("#message_container").html(data).show().delay(1500).fadeOut();
				location.href = "/titles";
			},
			cache: false,
			contentType: false,
			processData: false
		});	
	}
	event.preventDefault();
});

$("#admin_edit_manga_form").submit(function(event) {
	//validate input

	var formData = new FormData($(this)[0]);
	
	$("#admin_edit_manga_button").html("<?= display_glyphicon("spinner", "fa", "", "fa-pulse fa-fw") ?> Saving...").attr("disabled", true);
	
	$.ajax({
		url: "/ajax/actions.ajax.php?function=admin_edit_manga&id=<?= $id ?>",
		type: 'POST',
		data: formData,
		success: function (data) {
			$("#admin_edit_manga_button").html("<?= display_glyphicon("edit", "fa", "", "fa-fw") ?> Save").attr("disabled", false);
			$("#message_container").html(data).show().delay(1500).fadeOut();
		},
		cache: false,
		contentType: false,
		processData: false
	});
	event.preventDefault();
});	 
<?php } ?>
<?php } ?>