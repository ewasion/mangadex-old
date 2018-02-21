<?php if (count(get_object_vars($chapter)) && ($chapter->upload_timestamp < $timestamp || ($user->user_id == $chapter->user_id || $user->level_id >= 10 || $group->group_leader_id == $user->user_id))) { ?>

var prev_chapter_id = <?= $prev_id ?>;
var next_chapter_id = <?= $next_id ?>;
var prev_pages = <?= $prev_pages ?>;
var manga_id = <?= $chapter->manga_id ?>;
var chapter_id = <?= $chapter->chapter_id ?>;
var dataurl = '<?= $chapter->chapter_hash ?>';
var page_array = [
<?php
foreach ($page_array as $value) {
	print "'$value',";
}
?>
];
var server = '<?= $server ?>';



function preload_page(i) {
  if (i >= 0 && i < page_array.length) {    
    var img = new Image();
    img.src = img_url(i);
  }
}

// URL methods for simplicity's sake to prepare for possible changes in the future
function img_url(pg) {
  return server+dataurl+"/"+page_array[pg - 1];
}
function chapter_url(id, pg) {
  if (pg == null) { return "/chapter/"+id; }
  return "/chapter/"+id+"/"+pg;
}
function manga_url(id) {
  return "/manga/"+id;
}

function try_next_chapter() {
	if (next_chapter_id) {
      location.href = chapter_url(next_chapter_id);
    } else {
      location.href = manga_url(manga_id);
    }
}

function try_prev_chapter() {
	if (prev_chapter_id) {
      location.href = chapter_url(prev_chapter_id);
    } else {
      location.href = manga_url(manga_id);
    }
}

function go_page (page) {
  if (page > page_array.length) {
    if (next_chapter_id) {
      location.href = chapter_url(next_chapter_id);
    } else {
      location.href = manga_url(manga_id);
    }
  } else if (page < 1) {
    if (prev_chapter_id) {
      location.href = chapter_url(prev_chapter_id, prev_pages);
    } else {
      location.href = manga_url(manga_id);
    }
  } else {
    load_page(page);
    window.history.pushState({ page: page }, null, chapter_url(chapter_id, page));
  }
}

function load_page (page) {
  $("#current_page").attr("src", img_url(page)).one("load", function() {
    // Bootstrap selectpicker should be updated directly
    $("#jump_page").selectpicker("val", page);
    
    $("#current_page").data("page", page);
    // If scrollTop while going through history is unwanted, this should probably be moved to go_page
   $(window).scrollTop($("#current_page").offset().top - $("#top_nav").height());
	preload_page(page+1);
  });
}

<?php if (!$user->reader_mode) { ?>

// Immediately make sure jquery knows data-page is an int, and set the current history item
var page = parseInt($("#current_page").data("page"));
$("#current_page").data("page", page);
window.history.replaceState({ page: page }, null, chapter_url(chapter_id, page));
preload_page(page+1);

// Makes history work
window.onpopstate = function (evt) {
    if (evt.state != null) {
        load_page(evt.state.page);
    }
};

$("#jump_page").change(function(evt) {
  go_page(parseInt($(this).val()));
});

$("#current_page").click(function(evt) {
  go_page($(this).data("page") + 1);
});

$(document).keydown(function(evt) {
    if (evt.target.tagName === 'BODY') {
        switch (evt.keyCode) {
            case 37:
            case 65:
                return go_page($("#current_page").data("page") <?= ($user->swipe_direction) ? "-" : "+" ?> 1);
            case 39:
            case 68:
                return go_page($("#current_page").data("page") <?= ($user->swipe_direction) ? "+" : "-" ?> 1);
        }
    }
});

<?php if ($user->swipe_sensitivity > 25) { ?>
$("#current_page").swipe({
	swipeRight:function(event, direction, distance, duration, fingerCount) {
		go_page($("#current_page").data("page") <?= ($user->swipe_direction) ? "-" : "+" ?> 1);
	},
	threshold:<?= $user->swipe_sensitivity ?>
});
$("#current_page").swipe({
	swipeLeft:function(event, direction, distance, duration, fingerCount) {
		go_page($("#current_page").data("page") <?= ($user->swipe_direction) ? "+" : "-" ?> 1);
	},
	threshold:<?= $user->swipe_sensitivity ?>
});
<?php } ?>

$("#prev_page_alt").click(function(){
	go_page($("#current_page").data("page") <?= ($user->swipe_direction) ? "-" : "+" ?> 1);
});	
$("#next_page_alt").click(function(){
	go_page($("#current_page").data("page") <?= ($user->swipe_direction) ? "+" : "-" ?> 1);
});	

<?php }
elseif ($user->reader_mode) { ?>

$(".click").click(function(evt) {
	try_next_chapter(); 
});

$(document).keydown(function(evt) {
    if (evt.target.tagName === 'BODY') {
        switch (evt.keyCode) {
            case 37:
            case 65:
				<?= ($user->swipe_direction) ? "try_prev_chapter();" : "try_next_chapter();" ?>
				break;
            case 39:
            case 68:
				<?= ($user->swipe_direction) ? "try_next_chapter();" : "try_prev_chapter();" ?>
				break;
        }
    }
});

<?php } ?>

$("#jump_chapter").change(function() {
  var chapter = parseInt($(this).val());
  location.href = chapter_url(chapter);
});

$("#jump_group").change(function() {
  var chapter = parseInt($(this).val());
  location.href = chapter_url(chapter);
});

$("#prev_chapter_alt").click(function(){
	location.href = chapter_url(prev_chapter_id);
});	
$("#next_chapter_alt").click(function(){
	location.href = chapter_url(next_chapter_id);
});	


$("#minimise").click(function(){
	$(".navbar").toggle();
	$(".toggle").toggle();
	$("body").css("padding-top", "0px"); 
});	

$("#maximise").click(function(){
	$(".navbar").toggle();
	$(".toggle").toggle();
	$("body").css("padding-top", "70px"); 
});	

<?php if ($user->user_id) { ?>

$(".report_button").click(function(event){

	var type = $(this).attr('id');
	var info = prompt("Please enter additional information","");
	$.ajax({
		url: "/ajax/actions.ajax.php?function=chapter_report&id=<?= $id ?>&type="+type+"&info="+info,
		success: function(data) {
			$("#message_container").html(data).show().delay(3000).fadeOut();
		},
		cache: false,
		contentType: false,
		processData: false
	});	

	event.preventDefault();
});


<?php } ?>

<?php if ($user->level_id >= 10 || $chapter->user_id == $user->user_id || $group->group_leader_id == $user->user_id) { ?>
$("#edit_button").click(function(event){
	$(".edit").toggle();
	event.preventDefault();
});	
$("#cancel_edit_button").click(function(event){
	$(".edit").toggle();
	event.preventDefault();
});	


$('.btn-file :file').on('fileselect', function(event, numFiles, label) {
		
	var input = $(this).parents('.input-group').find(':text'),
		log = numFiles > 1 ? numFiles + ' files selected' : label;
	
	if( input.length ) {
		input.val(log);
	} else {
		if( log ) alert(log);
	}
	
	$("#save_edit_button").focus();
});

$("#edit_chapter_form").submit(function(evt) {
	var form = this;
 
	var success_msg = "<div class='alert alert-success text-center' role='alert'><strong>Success:</strong> This chapter has been edited.</div>";
	var error_msg = "<div class='alert alert-warning text-center' role='alert'><strong>Warning:</strong> Something went wrong with your upload.</div>";
 
	$("#save_edit_button").html("<?= display_glyphicon("spinner", "fas", "", "fa-pulse fa-fw") ?> Saving...").attr("disabled", true);
	
	var formdata = new FormData(form);
	
	evt.preventDefault();
	$.ajax({
		url: "/ajax/actions.ajax.php?function=chapter_edit&id=<?= $id ?>",
		type: 'POST',
		data: formdata,
		cache: false,
		contentType: false,
		processData: false,

		xhr: function() {
			var myXhr = $.ajaxSettings.xhr();
			if (myXhr.upload) {
				myXhr.upload.addEventListener('progress', function(e) {
					console.log(e)
					if (e.lengthComputable) {
						$('#progressbar').parent().show(); 
						$('#progressbar').width((Math.round(e.loaded/e.total*100) + '%'));
					}
				} , false);
			}
			return myXhr;
		},

		success: function (data) {
			$('#progressbar').parent().hide()
			$('#progressbar').width('0%');
			if (!data) {
				$("#message_container").html(success_msg).show().delay(3000).fadeOut();
				location.href = "/chapter/<?= $id ?>";
			}
			else {
				$("#message_container").html(data).show().delay(3000).fadeOut();
			}
			$("#save_edit_button").html("<?= display_glyphicon('pencil-alt', 'fas', '', 'fa-fw') ?> Save").attr("disabled", false);
		},
 
		error: function(err) {
			console.error(err);
			$('#progressbar').parent().hide()
			$("#save_edit_button").html("<?= display_glyphicon('pencil-alt', 'fas', '', 'fa-fw') ?> Save").attr("disabled", false);
			$("#message_container").html(error_msg).show().delay(3000).fadeOut();
		}
	});
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
<?php } ?>