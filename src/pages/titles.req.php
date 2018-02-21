<?php
$_GET["alpha"] = ($_GET["alpha"]) ?: "";

$manga_by_letter = $db->get_results(" SELECT substr(manga_name,1,1) as alpha, count(*) AS Rows FROM mangadex_mangas WHERE manga_name REGEXP '^[A-Za-z]' GROUP BY substr(manga_name,1,1) ");

?>

<ul class="nav nav-tabs">
	<li title="Last updated" role="presentation" class="<?= ($_GET["alpha"]) ?: "active" ?>"><a href="/titles"><?= display_glyphicon("sync", "fas", "Last updated", "fa-fw") ?></a></li>
	<li title="Other" role="presentation" class="<?= ($_GET["alpha"] == "~") ? "active" : "" ?>"><a href="/titles/~">~</a></li>
	<?php 
	foreach ($manga_by_letter as $letter) {
		$active = ($letter->alpha != $_GET["alpha"]) ?: "active";
		print "<li title='$letter->Rows titles' role='presentation' class='$active'><a href='/titles/$letter->alpha'>$letter->alpha</a></li>"; 
	}
	?>
	<li title="Advanced search" role="presentation" class="pull-right"><a href="/search"><?= display_glyphicon("search", "fas", "Advanced search", "fa-fw") ?></a></li>
	<?php
	if ($user->level_id >= 3) { ?>
	<li title="Add manga title" role="presentation" class="pull-right"><a href="/manga_new"><?= display_glyphicon("plus-circle", "fas", "", "fa-fw") ?></a></li>
	<?php } ?>
</ul>

<?php require_once(ABSPATH . "/pages/mangas.req.php"); ?>

