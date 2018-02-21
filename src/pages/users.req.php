<?php

$search = array();

//offset
if (isset($_GET["offset"]))
	$offset = mysql_escape_mimic($_GET["offset"]);
else 
	$offset = 0;


$order = "mangadex_users.level_id DESC, username ASC";
$limit = 100;

$users = new Users($db, $order, $limit, $offset, $search);

$num_rows = $users->num_rows($db, $search);

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

?>

<div class="table-responsive">
	<table class="table table-striped table-hover table-condensed">
		<thead>
			<tr>
				<th width="30px" class="text-center"><?= display_glyphicon("globe", "fas", "Language") ?></th>
				<th><?= display_glyphicon("user", "fas", "User") ?></th>
				<th><?= display_glyphicon("graduation-cap", "fas", "Role") ?></th>
				<th class="text-center"><?= display_glyphicon("file", "fas", "User") ?></th>
				<th class="text-center"><?= display_glyphicon("calendar-alt", "fas", "Joined") ?></th>
				<th class="text-info text-right" width="35px"><?= display_glyphicon("eye", "fas", "Total views") ?></th>
				<th width="30px" class="text-center"><?= display_glyphicon("external-link-alt", "fas", "Website") ?></th>
				<th width="30px" class="text-center"><?= display_glyphicon("rss", "fas", "RSS") ?></th>
			</tr>
		</thead>
		<tbody>
		
		<?php
		foreach ($users as $key => $view_user) {
			$key++;
			?>
			
			<tr>
				<td class="text-center"><?= display_lang_flag_v2($view_user->lang_name, $view_user->lang_flag, array(5, 5, 5, 5)) ?></td>
				<td><?= $view_user->user_link ?></td>
				<td><?= $view_user->level_name ?></td>
				<td class="text-center"><?= $view_user->user_uploads ?></td>
				<td class="text-center"><?= $view_user->joined_timestamp ?></td>
				<td class="text-info text-right"><?= number_format($view_user->user_views) ?></td>
				<td class="text-center"><?php if ($view_user->user_website) { ?>
                		<a target="_blank" href="<?= $view_user->user_website ?>"><?= display_glyphicon("external-link-square-alt", "fas", "Website", "fa-lg") ?></a>
                	<?php } else { ?>
						<?= display_glyphicon("external-link-square-alt", "fas", "Website", "fa-lg") ?>
					<?php } ?></td>
				
				<td class="text-center"><a target="_blank" href="/rss/group/<?= $view_user->group_id ?>"><?= display_glyphicon("rss-square", "fas", "RSS", "fa-lg") ?></a></td>
			</tr>			
			
			<?php
		}
		?>
			

		</tbody>
	</table>
</div>

<p class="text-center">Showing <?= number_format($offset + 1) ?> to <?= number_format(min($num_rows, $limit * $current_page)) ?> of <?= number_format($num_rows) ?> users</p>
<nav class="text-center">
	<ul style="margin: 0; cursor: pointer;" class="pagination">
		
		<li class="<?= $previous_class ?>" id="0"><a href="/<?= $page ?>"><?= display_glyphicon("angle-double-left", "fas", "Jump to first page", "fa-fw") ?></a></li>

		<?php 
		for ($i = 2; $i >= 1; $i--) { 
			$pg = $current_page - $i;
			if ($pg > 0) {
				$o = $offset - $limit * $i;
				print "<li class='paging'><a href='/$page/$o'>$pg</a></li>";
			}
		} 
		?>
		
		<li class="active"><a><?= $current_page ?></a></li>
		
		<?php 
		for ($i = 1; $i <= 2; $i++) { 
			$pg = $current_page + $i;
			if ($pg <= $last_page && ($pg - $current_page <= 2 || in_array($pg, array(4,5)))) {
				$o = $offset + $limit * $i;
				print "<li class='paging'><a href='/$page/$o'>$pg</a></li>";
			}
		} 
		?>
		<li class="<?= $next_class ?>"><a href="/<?= $page ?>/<?= ($last_page - 1) * $limit ?>"><?= display_glyphicon("angle-double-right", "fas", "Jump to last page", "fa-fw") ?></a></li>
	</ul>
</nav>