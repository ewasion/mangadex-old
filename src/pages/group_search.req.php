<?php
$languages = new Languages($db, "lang_name", "ASC");

$group_by_lang = $db->get_results(" SELECT COUNT(*) AS Rows, mangadex_groups.group_lang_id, mangadex_languages.* FROM mangadex_groups,mangadex_languages WHERE mangadex_groups.group_lang_id = mangadex_languages.lang_id GROUP BY mangadex_groups.group_lang_id ORDER BY Rows DESC ");
?>

<ul style="margin-bottom: 20px; " class="nav nav-tabs">
	<li title="All languages" role="presentation"><a href="/groups/0"><?= display_glyphicon("globe", "fas", "All languages", "fa-fw") ?></a></li>
	<?php 
	foreach ($group_by_lang as $lang) 
		print "<li role='presentation' id='$lang->group_lang_id' data-src='$lang->lang_name'><a href='/groups/$lang->group_lang_id'>" . display_lang_flag_v2($lang->lang_name, $lang->lang_flag, array(5, 5, 5, 5)) . "</a></li>"; 
	?>
	<li title="Search groups" role="presentation" class="active pull-right"><a href="/group_search"><?= display_glyphicon("search", "fas", "Search groups", "fa-fw") ?></a></li>
	<li title="Add new group" role="presentation" class="pull-right"><a href="/group_new"><?= display_glyphicon("plus-circle", "fas", "Add new group", "fa-fw") ?></a></li>
</ul>

<?php 
$_GET["group_name"] = isset($_GET["group_name"]) ? htmlentities($_GET["group_name"], ENT_QUOTES) : "";
$_GET['lang_id'] = 0;
?>

<div class="panel panel-default">	
	<div class="panel-heading">
		<h3 class="panel-title"><?= display_glyphicon("search", "fas", "", "fa-fw") ?> Search groups</h3>
	</div>
	<div class="panel-body">
		<form style="margin-top: 15px;" id="group_search_form" method="post" class="form-horizontal">
			<div class="form-group">
				<label for="group_name" class="col-sm-3 control-label">Group name</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" id="group_name" name="group_name" value="<?= $_GET["group_name"] ?>">
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-offset-3 col-sm-9">
					<button type="submit" class="btn btn-default" id="search_button"><?= display_glyphicon("search", "fas", "", "fa-fw") ?> <span class="span-1280">Search groups</span></button>
				</div>
			</div>
			
		</form>	
	</div>
</div>	 

<?php require_once(ABSPATH . "/pages/groups.req.php"); ?>