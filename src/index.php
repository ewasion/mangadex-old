<?php
if (in_array($_SERVER["HTTP_CF_CONNECTING_IP"], array("82.118.236.79", "49.150.43.112", "88.207.195.180", "95.19.93.179"))) die("Your IP has been banned due to abnormal behaviour which may indicate that you are a bot. Contact anidex.moe@gmail.com if that is not the case.");

require_once ($_SERVER["DOCUMENT_ROOT"] . "/config.req.php");

require_once (ABSPATH . "/scripts/header.req.php");

$timestamp = time();

session_start();


$_SESSION["token"] = $_SESSION["token"] ?? "";
$token = $_COOKIE['mangadex'] ?? $_SESSION["token"];
$user = new User($db, $token, "token");
				
//pages
$folder_array = array_diff(scandir(ABSPATH . "/pages"), array("..", "."));

$_GET["page"] = $_GET["page"] ?? "";
$page = in_array($_GET["page"] . ".req.php", $folder_array) ? $_GET["page"] : "main"; //redirect if $page does not exist

$lang_id = $_GET['lang_id'] ?? $user->language;
$_GET['lang_id'] = $lang_id;

if (!$user->user_id) {
	$logged_in_pages = array("follows", "upload", "settings", "messages", "message", "send_message", "activation", "admin", "mod", "group_new", "manga_new"); //pages which require login
	if (in_array($page, $logged_in_pages)) 
		$page = "login";
}
elseif (($user->level_id < 15 && $page == "admin") || ($user->level_id < 10 && $page == "mod")) 
	$page = "main";

if ($user->user_id)
	$db->query(" UPDATE mangadex_users SET last_seen_timestamp = $timestamp WHERE user_id = $user->user_id LIMIT 1 "); //update last_seen
	

visit_log_cumulative($db, $ip, $table = "visit");

visit_log($db, $_SERVER, $ip, $user->user_id, $user->hentai_mode); //$hentai_toggle set in header.req.php

switch ($page) {
	case "main":
		if (empty($_GET['lang_id']))
			$title = "Latest updates - " . TITLE;
		else {
			$lang_name = $db->get_var(" SELECT lang_name FROM mangadex_languages WHERE lang_id = '{$_GET['lang_id']}'; ");
			$title = "Latest updates ($lang_name) - " . TITLE;
		}
		$og_image = "/images/user_logos/default.png";
		break;

	case "chapter":
		$chapter = $db->get_row(" SELECT mangadex_mangas.manga_name, mangadex_mangas.manga_image, mangadex_chapters.volume, mangadex_chapters.chapter, mangadex_chapters.title 
			FROM mangadex_mangas, mangadex_chapters 
			WHERE mangadex_chapters.manga_id = mangadex_mangas.manga_id 
				AND mangadex_chapters.chapter_id = '{$_GET['id']}'; ");
		if ($chapter)
			$title = (($chapter->volume) ? "Vol. $chapter->volume " : "" ).(($chapter->chapter) ? "Ch. $chapter->chapter " : "").((!$chapter->volume && !$chapter->chapter) ? "$chapter->title " : "" )."($chapter->manga_name) - " . TITLE;
		else 
			$title = TITLE;
		
		$filename = get_ext($chapter->manga_image, 0);
		$og_image = "/images/manga/$filename.thumb.jpg";
		break;
		
	case "manga":
		$manga = $db->get_row(" SELECT manga_name, manga_id FROM mangadex_mangas WHERE manga_id = '{$_GET['id']}'; ");
		$title = $manga->manga_name . " (Manga) - " . TITLE;
		$og_image = "/images/manga/$manga->manga_id.thumb.jpg";
		break;
		
	case "user":
		$username = $db->get_var(" SELECT username FROM mangadex_users WHERE user_id = '{$_GET['id']}'; ");
		$title = $username . " (User) - " . TITLE;
		$og_image = "/images/user_logos/default.png";
		break;
		
	case "group":
		$group_name = $db->get_var(" SELECT group_name FROM mangadex_groups WHERE group_id = '{$_GET['id']}'; ");
		$title = $group_name . " (Group) - " . TITLE;
		$og_image = "/images/user_logos/default.png";
		break;
		
	case "groups":
		if (empty($_GET['lang_id']))
			$title = "Groups (All) - " . TITLE;
		else {
			$lang_name = $db->get_var(" SELECT lang_name FROM mangadex_languages WHERE lang_id = '{$_GET['lang_id']}'; ");
			$title = "Groups ($lang_name) - " . TITLE;
		}
		$og_image = "/images/user_logos/default.png";
		break;

	case "titles":
		$_GET['alpha'] = $_GET['alpha'] ?? "";
		$letter = ($_GET['alpha']) ?: "All";
		$title = "Manga titles ($letter) - " . TITLE;
		$og_image = "/images/user_logos/default.png";
		break;
		
	default:
		$title = ($page) ? ucfirst($page) . " - " . TITLE : TITLE;
		$og_image = "/images/user_logos/default.png";
		break;
}

$ignore_pages   = array("login", "torrent", "group", "signup");

$dynamic_url    = $_SERVER["QUERY_STRING"] . "-$lang_id-" . $hentai_toggle; // requested dynamic page (full url)
$cache_file     = ABSPATH . "/cache/" . $dynamic_url; // construct a cache file
//$ignore = strpos_arr($dynamic_url, $ignore_pages); //check if url is in ignore list

?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
	<meta name="author" content="MangaDex">
	<meta name="description" content="<?= DESCRIPTION ?>">
	<meta name="keywords" content="manga, reader, mangadex, scanlation">

	<meta property="og:site_name" content="<?= TITLE ?>">
	<meta property="og:title" content="<?= $title ?>">
	<meta property="og:image" content="<?= $og_image ?>">

	<link rel="icon" href="/favicon.ico">

	<title><?= $title ?> </title>

	<!-- Bootstrap core CSS -->
	<link href="/bootstrap/css/bootstrap.<?= ($user->style) ?: 1 ?>.css" rel="stylesheet">
	
	<!-- Bootstrap select CSS -->
	<link href="/bootstrap/css/bootstrap-select.min.css?v=1" rel="stylesheet">
	
	<!-- Bootstrap checkbox CSS -->
	<link href="/bootstrap/css/bootstrap-checkbox.css" rel="stylesheet">
	
	<!-- Custom styles for this template -->
	<link href="/scripts/css/theme.css?v=5" rel="stylesheet">

	<!-- Fontawesone glyphicons -->
	<link href="/fontawesome/web-fonts-with-css/css/fontawesome-all.min.css" rel="stylesheet">
</head>

<body>
<?php require_once(ABSPATH . "/scripts/analytics.req.php"); ?>

	<!-- Fixed navbar -->
	<nav id="top_nav" class="navbar navbar-default navbar-fixed-top">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" id="home_button" href="/"><?= TITLE ?></a>
			</div>
			<div id="navbar" class="navbar-collapse collapse">
				<ul class="nav navbar-nav" id="nav_links"><?php require_once(ABSPATH . "/pages/nav_links.req.php"); ?></ul>
				
                <ul class="nav navbar-nav navbar-right" id="username"><?php require_once(ABSPATH . "/pages/username.req.php"); ?></ul>
				
                <ul class="nav navbar-nav navbar-right" id="pm">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
							<?= display_glyphicon("envelope", "fas", "Messages", "fa-fw") ?> <span class="nav-label-1440">Mail <?= $user->get_unread_threads($db) ?></span> <span class="caret"></span>
						</a>
                        <ul class="dropdown-menu">
                            <li class="" id="messages"><a href="/messages"><?= display_glyphicon("envelope", "fas", "Messages", "fa-fw") ?> Messages</a></li>
                            <li class="" id="send_message"><a href="/send_message"><?= display_glyphicon("pencil-alt", "fas", "", "fa-fw") ?> Send</a></li>
                        </ul>
                    </li>
				</ul>
				
				<?php if ($user->hentai_mode) { ?>
				<form id="h_toggle_form" class="navbar-form navbar-right">
					<select class="form-control selectpicker show-tick" data-width="100px" id="h_toggle" name="h_toggle">
						<option value="0" <?= (!$hentai_toggle) ? "selected" : "" ?> data-content="Hide <span class='label label-danger'>H</span>">Hide H</option>
						<option value="1" <?= ($hentai_toggle == 1) ? "selected" : "" ?>>Show all</option>
						<option value="2" <?= ($hentai_toggle == 2) ? "selected" : "" ?> data-content="Only <span class='label label-danger'>H</span>">Only H</option>
					</select>
				</form>
				<?php } ?>
				
  				<form id="quick_search_form" role="search" class="navbar-form navbar-right" action="/?page=titles">
					<div class="input-group">	
						<input type="text" class="form-control quick_search_input" placeholder="Quick search" name="manga_name" id="quick_search_input" >
						<span class="input-group-btn">
							<button class="btn btn-default" type="submit" id="quick_search_button"><?= display_glyphicon("search", "fas") ?></button>
						</span>
					</div>
				</form>              
                
			</div><!--/.nav-collapse -->
		</div>
	</nav>

	<div class="container" role="main">
		<?php if (!$user->activated) { ?>
		<div class="alert alert-warning text-center" role="alert"><strong>Warning:</strong> Your account is currently unactivated. Please enter your activation code <a href="/activation">here</a> for access to all of <?= TITLE ?>'s features.</div>
		<?php } ?>
		
		<?php if ((!$user->read_announcement || !$user->user_id) && $page != "chapter") { ?>
		<div id="announcement" class="alert alert-success alert-dismissible text-center" role="alert">
        	<?php if ($user->user_id) { ?>
				<button id="read_announcement_button" type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<?php } ?>
			<strong>Update (21-Feb)</strong> <strong>Notice:</strong> New server is being prepared. </div>
		<?php } ?>
		
		<div id="content">
			<?php 
			$time = explode(" ", microtime());
			$start = $time[1] + $time[0];
			
			if (!$_SERVER["QUERY_STRING"] && file_exists($cache_file) && time() - CACHE_TIME < filemtime($cache_file) && $caching) { //check Cache exist and it's not expired.
				ob_start('ob_gzhandler'); //Turn on output buffering, "ob_gzhandler" for the compressed page with gzip.
				readfile($cache_file); //read Cache file
				
				print "<p class='text-center'><samp>Cached " . get_time_ago(filemtime($cache_file)) . ".</samp></p>";
				
				ob_end_flush(); //Flush and turn off output buffering
				exit(); //no need to proceed further, exit the flow.
			}
			//Turn on output buffering with gzip compression.
			ob_start('ob_gzhandler');
			######## Your Website Content Starts Below #########

			require_once (ABSPATH . "/pages/" . $page . ".req.php"); 
			
			?>
			<?php
				
				$time = explode(' ', microtime());
				$finish = $time[1] + $time[0];
				$total_time = round(($finish - $start), 3) * 1000;
				print "<!--<p class='text-center'><samp>Page generated in $total_time ms.</samp></p>-->";
			?>
		</div>
		
	</div> <!-- /container -->

	<!-- message_container -->
	<div id="message_container" class="display-none"></div>
	<!-- /container -->	
	
	<footer class="footer">
		<p class="text-center text-muted">Copyright &copy; <?= date("Y") ?> <a href="/">MangaDex</a> - <a href="https://hologfx.com" target="_blank" title="Project AniDex Portal">Project AniDex</a></p>
	</footer>
	

	<!-- Bootstrap core JavaScript
	================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
	<script src="/scripts/jquery.min.js"></script>
	<script src="/scripts/jquery.touchSwipe.min.js"></script>
	<script src="/bootstrap/js/bootstrap.min.js"></script>
	<script src="/bootstrap/js/bootstrap-select.min.js"></script>
	<script type="text/javascript">
	var $ = jQuery;
	
	$(document).on('change', '.btn-file :file', function() {
		var input = $(this),
			numFiles = input.get(0).files ? input.get(0).files.length : 1,
			label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
		input.trigger('fileselect', [numFiles, label]);
	});

	function capitalizeFirstLetter(string) {
		return string.charAt(0).toUpperCase() + string.slice(1);
	}
	
	function commaMultipleSelect(id) {
		var list = document.getElementById(id);
		var selected = new Array();
	 
		for (i = 0; i < list.options.length; i++) {
			if (list.options[ i ].selected) {
				 selected.push(list.options[ i ].value);
			}
		}
	 
		return selected.join(',');
	}
	
	$(document).ready(function(){
		var query = location.search;
		
		$("#read_announcement_button").click(function(event){
			$.ajax({
				url: "/ajax/actions.ajax.php?function=read_announcement",
				type: 'GET',
				success: function (data) {
					$("#announcement").hide();
				},
				cache: false,
				contentType: false,
				processData: false
			});
			event.preventDefault();
		});	
		
		$("#logout").click(function(event){
			$.ajax({
				type: "POST",
				url: "/ajax/actions.ajax.php?function=logout",
				success: function(data) {
					$("#message_container").html(data).show().delay(1500).fadeOut();
					location.reload();
				}
			});
			event.preventDefault();
		});	

		$("#h_toggle").change(function() {
			val = $("#h_toggle").val();
			$.ajax({
				url: "/ajax/actions.ajax.php?function=hentai_toggle&mode="+val,
				type: 'GET',
				async: false,
				success: function (data) {
					$("#message_container").html(data).show().delay(1500).fadeOut();
					location.href = "/";
					//alert(data);
				},
				cache: false,
				contentType: false,
				processData: false
				
			});
			location.reload();
		});
		
		$("#quick_search_form").submit(function(event) {

			var search_title = encodeURIComponent($("#quick_search_input").val());
			
			$("#quick_search_button").html("<?= display_glyphicon("spinner", "fas", "", "fa-pulse") ?>").attr("disabled", true);
			
			location.href = "/?page=search&title="+search_title;

			event.preventDefault();

		});

		<?php 
		$folder_array = array_diff(scandir(ABSPATH . "/scripts/js"), array("..", "."));

		if (in_array($page . ".req.js", $folder_array)) 
			require_once (ABSPATH . "/scripts/js/" . $page . ".req.js"); 
		?>
		
	});
	
	</script>
</body>
</html>

<?php

######## Your Website Content Ends here #########

if(!$_SERVER["QUERY_STRING"] && $caching){
    $fp = fopen($cache_file, 'w');  //open file for writing
    fwrite($fp, ob_get_contents()); //write contents of the output buffer in Cache file
    fclose($fp); //Close file pointer
}
ob_end_flush(); //Flush and turn off output buffering

?>
