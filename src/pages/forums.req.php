<?php
if ($user->level_id >= 10) {
	
$categories = $db->get_results(" SELECT * FROM mangadex_forums WHERE forum_parent = 0 ");

	
?>
<ol class="breadcrumb">
	<li class="active">Home</li>
</ol>

<?php 
foreach ($categories as $category) {
	$forums = new Forums($db, $category->forum_id);
	?>
	
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?= $category->forum_name ?></h3>
		</div>
		<table class="table">

		<?php
		foreach ($forums as $forum) {
			?>
			<tr>		
				<td>
				<h4><strong><a href="/forum/<?= $forum->forum_id ?>"><?= $forum->forum_name ?></a></strong></h4>
				<p><?= $forum->forum_description ?></p>
				</td>
			</tr>		
			
			<?php
		}
		?>
		</table>

	</div>
	<?php
}


$online_users = $db->get_results(" SELECT user_id, username, level_colour FROM mangadex_users LEFT JOIN mangadex_user_levels ON mangadex_users.level_id = mangadex_user_levels.level_id WHERE last_seen_timestamp > $timestamp - 60 ORDER BY last_seen_timestamp DESC ");

$online_users_string = "";
foreach ($online_users as $online_user) 
	$online_users_string .= "<a style='color: #$online_user->level_colour; ' href='/user/$online_user->user_id'>$online_user->username</a>, ";

$online_users_string = rtrim($online_users_string, ", ");
?>

<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">Online users (<?= $db->num_rows ?>)</h3>
	</div>
	<div class="panel-body">
		<?= ($user->user_id) ? $online_users_string : "Not viewable by guests." ?>
	</div>
</div>

<?php
}
?>