<?php if ($user->level_id >= 3) { ?>

$(".post_reply_button").click(function(event){
	$("#post_reply").toggle();
	event.preventDefault();
});	

$("#back_button").click(function(event){
	$("#post_reply").toggle();
	event.preventDefault();
});	

<?= jquery_post("post_reply", $thread_id, "pencil-alt", "Submit", "Submitting", "Your reply has been posted.", "thread/$thread_id"); ?>

<?php } ?>