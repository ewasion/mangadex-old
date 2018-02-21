<?php if ($user->user_id) { ?>

$('.btn-file :file').on('fileselect', function(event, numFiles, label) {
	
	var input = $(this).parents('.input-group').find(':text'),
		log = numFiles > 1 ? numFiles + ' files selected' : label;
	
	if( input.length ) {
		input.val(log);
	} else {
		if( log ) alert(log);
	}
	
	$("#upload_button").focus();
});

$("#upload_form").submit(function(evt) {
	var form = this;
 
	var success_msg = "<div class='alert alert-success text-center' role='alert'><strong>Success:</strong> Your chapter has been uploaded.</div>";
	var error_msg = "<div class='alert alert-warning text-center' role='alert'><strong>Warning:</strong> Something went wrong with your upload.</div>";
 
	$("#upload_button").html("<span class='fas fa-spinner fa-pulse' aria-hidden='true' title=''></span> Uploading...").attr("disabled", true);
	
	var formdata = new FormData(form);
	
	evt.preventDefault();
	$.ajax({
		url: "/ajax/actions_test.ajax.php?function=chapter_upload",
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
				
				form.reset();
				var restore = ['manga_id', 'group_id', 'lang_id', 'volume_number' ];
				for (var i = 0; i < restore.length; i++) {
					form.elements[restore[i]].value = formdata.get(restore[i]);
				}
				form.elements.chapter_number.value = Math.floor(parseFloat(formdata.get('chapter_number')) + 1 ) || '';
			}
			else {
				$("#message_container").html(data).show().delay(5000).fadeOut();
			}
			$("#upload_button").html("<?= display_glyphicon("upload", "fas", "", "fa-fw") ?> Upload").attr("disabled", false);
		},
 
		error: function(err) {
			console.error(err);
			$('#progressbar').parent().hide()
			$("#upload_button").html("<?= display_glyphicon("upload", "fas", "", "fa-fw") ?> Upload").attr("disabled", false);
			$("#message_container").html(error_msg).show().delay(5000).fadeOut();
		}
	});
});

<?php } ?>