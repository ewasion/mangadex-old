var page_array = [
<?php
foreach ($page_array as $value) {
	print "'$value',";
}
?>
];

var prev_chapter_id = <?= $prev_id ?>;
var next_chapter_id = <?= $next_id ?>;


function n_page(page) { //2.jpg
	var page_int = parseInt(page);  //2
		
	$("#current_page").one("load", function() {
		$("#jump_page").val(page_int).change();
		$("#current_page").attr("data-src", page_array[page_int]);
		$(window).scrollTop(0);
    }).attr("src", "/data/<?= $chapter->chapter_hash ?>/"+page_array[page_int-1]);
	
	window.history.pushState(null, null, "/chaptertest/<?= $chapter->chapter_id ?>/"+page_int); 
	
}

$("#jump_page").change(function(){
	var page_int = parseInt($(this).val()); //2
	$("#current_page").one("load", function() {
		$("#current_page").attr("data-src", page_array[page_int]);
		$(window).scrollTop(0);
    }).attr("src", "/data/<?= $chapter->chapter_hash ?>/"+page_array[page_int-1]);

	window.history.pushState(null, null, "/chaptertest/<?= $chapter->chapter_id ?>/"+page_int); 
});

$("#current_page").click(function(event){
	var next_page = $(this).attr("data-src");
	var current_page_src = $(this).attr("src");
	var current_page = current_page_src.substring(current_page_src.lastIndexOf("/") + 1);
		
	if (next_page == current_page) {
		if (next_chapter_id)
			location.href = "/chaptertest/"+next_chapter_id;
		else
			location.href = "/manga/<?= $chapter->manga_id ?>";
	}
	else {
		n_page(next_page);
	}
});	



$(document).keydown(function (e){ 
	switch (e.keyCode) {
		case 37:
		//case 65:
			var next_page = $("#current_page").attr("data-src");
			var current_page_src = $("#current_page").attr("src");
			var current_page = current_page_src.substring(current_page_src.lastIndexOf("/") + 1);
			if (next_page == current_page) { //last page
				prev_page = page_array[page_array.length - 2];
				n_page(prev_page);
			}
			else {
				var prev_page_int = parseInt($("#current_page").attr("data-src")) - 2;
				
				if (!prev_page_int) {
					if (prev_chapter_id)
						location.href = "/chaptertest/"+prev_chapter_id+"/<?= $prev_pages ?>";
					else
						location.href = "/manga/<?= $chapter->manga_id ?>";
				}
				else {
					n_page(page_array[prev_page_int-1]);
				}	
			}
			
					
			
			break;
			
		case 39:
		//case 68:
			var next_page = $("#current_page").attr("data-src");
			var current_page_src = $("#current_page").attr("src");
			var current_page = current_page_src.substring(current_page_src.lastIndexOf("/") + 1);
			
			if (next_page == current_page) {
				if (next_chapter_id)
					location.href = "/chaptertest/"+next_chapter_id;
				else
					location.href = "/manga/<?= $chapter->manga_id ?>";
			}
			else {
				n_page(next_page);
			}		
			
			break;
	}
});

$("#jump_chapter").change(function(){
	var chapter = parseInt($(this).val());
	location.href="/chaptertest/"+chapter;
	
	
});



<?php if ($user->level_id >= 15 || $chapter->user_id == $user->user_id || $group->group_leader_id == $user->user_id) { ?>
$("#edit_button").click(function(event){
	$(".edit").toggle();
	event.preventDefault();
});	
$("#cancel_edit_button").click(function(event){
	$(".edit").toggle();
	event.preventDefault();
});	

$("#edit_chapter_form").submit(function(event) {
	//validate input

	var formData = new FormData($(this)[0]);
	
	$("#save_edit_button").html("<?= display_glyphicon("spinner", "fas", "", "fa-pulse fa-fw") ?> Saving...").attr("disabled", true);
	
	$.ajax({
		url: "/ajax/actions.ajax.php?function=chapter_edit&id=<?= $id ?>",
		type: 'POST',
		data: formData,
		success: function (data) {
			$("#message_container").html(data).show().delay(1500).fadeOut();
			location.href = "/chaptertest/<?= $id ?>";
		},
		cache: false,
		contentType: false,
		processData: false
		
	});

	event.preventDefault();
});	

$("#delete_button").click(function(event){
	if (confirm("Are you sure?")) {
		$.ajax({
			url: "/ajax/actions.ajax.php?function=chapter_delete&id=<?= $id ?>",
			success: function(data) {
				$("#message_container").html(data).show().delay(100).fadeOut();
				location.href = "/manga/<?= $chapter->manga_id ?>";
			},
			cache: false,
			contentType: false,
			processData: false
		});	
	}
	event.preventDefault();
});

<?php } ?>