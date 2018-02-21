<?php
$lang_id = $_GET['lang_id'] ?? $user->language;
$_GET['lang_id'] = $lang_id;

$search = array();

//offset
if (isset($_GET["offset"]))
	$offset = mysql_escape_mimic($_GET["offset"]);
else 
	$offset = 0;

//lang
if (isset($_GET["lang_id"])) {
	if (!empty($_GET["lang_id"]))	//not 0
		$search["group_lang_id"] = $_GET["lang_id"];
	else 
		unset($search["group_lang_id"]); //is 0
}

//lang
if (isset($_GET["group_name"])) {
	if (!empty($_GET["group_name"]))	//not 0
		$search["group_name"] = $_GET["group_name"];
	else 
		unset($search["group_name"]); //is 0
}

$order = "group_name ASC";
$limit = 100;

$groups = new Groups($db, $order, $limit, $offset, $search);

$num_rows = $groups->num_rows($db, $search);

$current_page = floor($offset / $limit) + 1; //287
$last_page = ceil($num_rows / $limit); //286

if ($current_page == 1) {
	$previous_page = "-";
	$previous_class = "disabled";
}
else {
	$previous_page = $current_page - 1;
	$previous_class = "paging";
}
if ($current_page == $last_page) {
	$next_page = "-";
	$next_class = "disabled";
}
else {
	$next_page = $current_page + 1;
	$next_class = "paging";
}

$group_by_lang = $db->get_results(" SELECT COUNT(*) AS Rows, mangadex_groups.group_lang_id, mangadex_languages.* FROM mangadex_groups,mangadex_languages WHERE mangadex_groups.group_lang_id = mangadex_languages.lang_id GROUP BY mangadex_groups.group_lang_id ORDER BY Rows DESC ");
?>

<ul class="nav nav-tabs">
	<li title="All languages" role="presentation" class="<?= (!$lang_id) ? "active" : "" ?>"><a href="/groups/0"><?= display_glyphicon("globe", "fas", "All languages", "fa-fw") ?></a></li>
	<?php 
	foreach ($group_by_lang as $lang) {
		$active = ($lang->group_lang_id == $lang_id) ? "active" : "";
		print "<li role='presentation' class='group_lang $active' id='$lang->group_lang_id' data-src='$lang->lang_name'><a href='/groups/$lang->group_lang_id'>" . display_lang_flag_v2($lang->lang_name, $lang->lang_flag, array(5, 5, 5, 5)) . "</a></li>"; 
	}
	?>
	<li title="Search groups" role="presentation" class="pull-right"><a href="/group_search"><?= display_glyphicon("search", "fas", "Search groups", "fa-fw") ?></a></li>
	<?php
	if ($user->user_id) { ?>
	<li title="Add new group" role="presentation" class="pull-right"><a href="/group_new"><?= display_glyphicon("plus-circle", "fas", "Add new group", "fa-fw") ?></a></li>
	<?php } ?>
</ul>

<div class="table-responsive">
	<table class="table table-striped table-hover table-condensed">
		<thead>
			<tr>
				<th width="30px" class="text-center"><?= display_glyphicon("globe", "fas", "Language") ?></th>
				<th width="300px"><?= display_glyphicon("users", "fas", "Group name") ?></th>
				<th class="text-center"><?= display_glyphicon("calendar-alt", "fas", "Founded") ?></th>
				<th class="text-info text-right" width="35px"><?= display_glyphicon("eye", "fas", "Total views") ?></th>
                <th width="30px" class="text-center"><?= display_glyphicon("comments", "fas", "Comments") ?></th>
				<th width="30px" class="text-center"><?= display_glyphicon("thumbs-up", "far", "Likes") ?></th>
				<th width="30px" class="text-center"><?= display_glyphicon("external-link-alt", "fas", "Website") ?></th>
				<th width="30px" class="text-center"><?= display_glyphicon("discord", "fab", "Discord") ?></th>
				<th width="30px" class="text-center"><?= display_glyphicon("hashtag", "fas", "IRC") ?></th>
				<th width="30px" class="text-center"><?= display_glyphicon("rss", "fas", "RSS") ?></th>
			</tr>
		</thead>
		<tbody>
		
		<?php
		foreach ($groups as $key => $group) {
			$key++;
			?>
			
			<tr>
				<td class="text-center"><?= display_lang_flag_v2($group->lang_name, $group->lang_flag, array(5, 5, 5, 5)) ?></td>
				<td><?= $group->group_link ?></td>
				<td class="text-center"><?= $group->group_founded ?></td>
				<td class="text-info text-right"><?= number_format($group->group_views) ?></td>
				<td class="text-center"><?= $group->group_comments ?></td>
                <td class="text-center"><?= $group->likes ?></td>
				<td class="text-center"><?php if ($group->group_website) { ?>
                		<a target="_blank" href="<?= $group->group_website ?>"><?= display_glyphicon("external-link-square-alt", "fas", "Website", "fa-lg") ?></a>
                	<?php } else { ?>
						<?= display_glyphicon("external-link-square-alt", "fas", "Website", "fa-lg") ?>
					<?php } ?></td>
				<td class="text-center"><?php if ($group->group_discord) { ?>
                		<a target="_blank" href="https://discord.gg/<?= $group->group_discord ?>"><?= display_glyphicon("discord", "fab", "Discord", "fa-lg") ?></a>
                	<?php } else { ?>
						<?= display_glyphicon("discord", "fab", "Discord", "fa-lg") ?>
					<?php } ?></td>
				<td class="text-center"><?php if ($group->group_irc_channel) { ?>
                		<a target="_blank" href="<?= $group->irc_link ?>"><?= display_glyphicon("hashtag", "fas", "IRC", "fa-lg") ?></a>
                     <?php } else { ?>
						<?= display_glyphicon("hashtag", "fas", "IRC", "fa-lg") ?>
					<?php } ?></td>
				<td class="text-center"><a target="_blank" href="/rss/group/<?= $group->group_id ?>"><?= display_glyphicon("rss-square", "fas", "RSS", "fa-lg") ?></a></td>
			</tr>			
			
			<?php
		}
		?>
			

		</tbody>
	</table>
</div>

<p class="text-center">Showing <?= number_format($offset + 1) ?> to <?= number_format(min($num_rows, $limit * $current_page)) ?> of <?= number_format($num_rows) ?> titles</p>
<nav class="text-center">
	<ul style="margin: 0; cursor: pointer;" class="pagination">
		<?php
		if (!empty($lang_id)) {
			$alpha_string = "/" . $lang_id;
		}
		else 
			$alpha_string = "/0";
		?>
		
		<li class="<?= $previous_class ?>" id="0"><a href="/<?= $page . $alpha_string ?>"><?= display_glyphicon("angle-double-left", "fas", "Jump to first page", "fa-fw") ?></a></li>

		<?php 
		for ($i = 2; $i >= 1; $i--) { 
			$pg = $current_page - $i;
			if ($pg > 0) {
				$o = $offset - $limit * $i;
				print "<li class='paging'><a href='/$page$alpha_string/$o'>$pg</a></li>";
			}
		} 
		?>
		
		<li class="active"><a><?= $current_page ?></a></li>
		
		<?php 
		for ($i = 1; $i <= 2; $i++) { 
			$pg = $current_page + $i;
			if ($pg <= $last_page && ($pg - $current_page <= 2 || in_array($pg, array(4,5)))) {
				$o = $offset + $limit * $i;
				print "<li class='paging'><a href='/$page$alpha_string/$o'>$pg</a></li>";
			}
		} 
		?>
		<li class="<?= $next_class ?>"><a href="/<?= $page . $alpha_string ?>/<?= ($last_page - 1) * $limit ?>"><?= display_glyphicon("angle-double-right", "fas", "Jump to last page", "fa-fw") ?></a></li>
	</ul>
</nav>