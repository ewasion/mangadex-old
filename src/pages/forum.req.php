<?php
$forum_id = $_GET['id'] ?? 1;

$limit = 20;
$offset = 0;
$threads = new Forum_Threads($db, $forum_id, $limit, $offset);
?>

<?= $threads->get_breadcrumb($db, $forum_id) ?>

<div class="toggle">
	<?php 
	$subforums = new Forums($db, $forum_id);

	if ($subforums->num_rows($db, $forum_id)) {
		?>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Child Boards</h3>
			</div>
			<table class="table">

				<?php
				foreach ($subforums as $subforum) {
				?>
				<tr>		
					<td>
					<h4><strong><a href="/forum/<?= $subforum->forum_id ?>"><?= $subforum->forum_name ?></a></strong></h4>
					<p><?= $subforum->forum_description ?></p>
					</td>
				</tr>		
				
				<?php
				}
			?>
			</table>

		</div>
		<?php
	}
	?>

	<div class="row" style="margin-bottom: 20px;">
		<div class="col-xs-6">
			<?= display_new_thread($user->level_id) ?>
		</div>
		
		<div class="col-xs-6 text-right">
		pagination
		</div>
	</div>

	<?php
	if ($threads->num_rows($db, $forum_id)) {
	?>

	<div class="table-responsive">
		<table class="table">
			<thead>
				<tr>
					<th>Thread</th>
					<th>Started by</th>
					<th>Replies</th>
					<th>Views</th>
					<th>Last post</th>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach ($threads as $thread) {
				?>
				<tr>		
					<td><a href="/thread/<?= $thread->thread_id ?>"><?= $thread->thread_name ?></a></td>
					<td><a href="/user/<?= $thread->user_id ?>"><?= $thread->username ?></a></td>
					<td class="text-center"><?= $thread->thread_posts ?></td>
					<td class="text-center"><?= $thread->thread_views ?></td>
					<td class="text-right"><?= get_time_ago($thread->last_post_timestamp) ?> by <?= $thread->last_post_user_id ?></td>
				</tr>		
				
				<?php
				}
				?>	
			
			</tbody>
		</table>
	</div>
	<?php
	}
	else print "<div class='alert alert-info text-center' role='alert'>" . display_glyphicon("info-circle", "fas") . " There are no threads in this forum.</div>";
	?>

	<div class="row" style="margin-bottom: 20px;">
		<div class="col-xs-6">
			<?= display_new_thread($user->level_id) ?>
		</div>
		
		<div class="col-xs-6 text-right">
		pagination
		</div>
	</div>

	
</div>

<?php if ($user->level_id >= 3) { ?>

<div class="toggle display-none panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">Start new thread</h3>
	</div>
	<div class="panel panel-body">
		<form class="form-horizontal" method="post" id="start_thread_form">
			<div class="form-group">
				<div class="col-xs-12">
					<input type="text" class="form-control" id="subject" name="subject" placeholder="Thread name" required>
				</div>
			</div>
			<div class="form-group">
				<div class="col-xs-12">
					<?php
					foreach ($bbcode_array_id as $key => $text) 
						print "<button title='$bbcode_array_title[$key]' id='$text' type='button' class='btn btn-sm btn-default'>" . display_glyphicon("$text", "fas", "", "fa-fw") . "</button>";
					?>
				</div>
			</div>
			<div class="form-group">
				<div class="col-xs-12">
					<textarea rows="20" type="text" class="form-control" id="post_text" name="post_text" placeholder="BBCode allowed" required></textarea>
				</div>
			</div>
			<div class="form-group">
				<div class="col-xs-6"><button type="submit" class="btn btn-default" id="start_thread_button"><?= display_glyphicon("pencil-alt", "fas", "", "fa-fw") ?> Submit</button></div>
				<div class="col-xs-6 text-right"><button title="Back to forum" id="back_button" type="button" class="btn btn-default"><?= display_glyphicon("undo", "fas", "", "fa-fw") ?> Back</button></div>
			</div>
		</form>
	</div>
</div>

<?php } ?>

