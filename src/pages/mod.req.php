<?php 
//pages
$mode = (!in_array("mod_" . $_GET["mode"] . ".req.php", $folder_array)) ? "reports" : $_GET["mode"]; //redirect if $page does not exist
$_GET["type"] = ($mode == "reports" && !isset($_GET["type"])) ? "new" : $_GET["type"];
?>

<ul class="nav nav-tabs">
	<li role="presentation" class="dropdown <?= ($mode == "reports") ? "active" : "" ?>">
		<a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Reports <span class="caret"></span></a>
		<ul class="dropdown-menu">
			<li role="presentation" class="<?= ($mode == "reports" && $_GET["type"] == "new") ? "active" : "" ?>"><a href="/mod/reports/new">New reports</a></li>
			<li role="presentation" class="<?= ($mode == "reports" && $_GET["type"] == "old") ? "active" : "" ?>"><a href="/mod/reports/old">Old reports</a></li>
		</ul>
	</li>
	<li role="presentation" class=""><a href="#">stuff</a></li>
</ul>

<?php require_once (ABSPATH . "/pages/mod_" . $mode . ".req.php"); ?>