<?php 
//pages
$mode = (!in_array("admin_" . $_GET["mode"] . ".req.php", $folder_array)) ? "visit_logs" : $_GET["mode"]; //redirect if $page does not exist

?>

<ul class="nav nav-tabs">
	<li role="presentation" class="<?= ($mode == "visit_logs") ? "active" : "" ?>"><a href="/admin/visit_logs">Visit logs</a></li>
	<li role="presentation" class="<?= ($mode == "rss_logs") ? "active" : "" ?>"><a href="/admin/rss_logs">RSS logs</a></li>
	<li role="presentation" class="<?= ($mode == "action_logs") ? "active" : "" ?>"><a href="/admin/action_logs">Action logs</a></li>
	<li role="presentation" class="<?= ($mode == "autodl") ? "active" : "" ?>"><a href="/admin/autodl">Auto DL</a></li>
</ul>

<?php require_once (ABSPATH . "/pages/admin_" . $mode . ".req.php"); ?>