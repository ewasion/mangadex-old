$(".report_accept").click(function(event){
	id = $(this).attr("id"); 
	$.ajax({
		url: "/ajax/actions.ajax.php?function=report_accept&id="+id,
		success: function(data) {
			$("#message_container").html(data).show().delay(1500).fadeOut();
			location.href = "/mod/reports/new";
		},
		cache: false,
		contentType: false,
		processData: false
	});	
	event.preventDefault();
});	

$(".report_reject").click(function(event){
	id = $(this).attr("id"); 
	$.ajax({
		url: "/ajax/actions.ajax.php?function=report_reject&id="+id,
		success: function(data) {
			$("#message_container").html(data).show().delay(1500).fadeOut();
			location.href = "/mod/reports/new";
		},
		cache: false,
		contentType: false,
		processData: false
	});	
	event.preventDefault();
});	