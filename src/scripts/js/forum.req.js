<?php if ($user->level_id >= 3) { ?>

$(".new_thread_button").click(function(event){
	$(".toggle").toggle();
	event.preventDefault();
});	

$("#back_button").click(function(event){
	$(".toggle").toggle();
	event.preventDefault();
});	

<?= jquery_post("start_thread", $forum_id, "pencil-alt", "Submit", "Submitting", "Your thread has been posted.", "forum/$forum_id"); ?>

<?php } ?>