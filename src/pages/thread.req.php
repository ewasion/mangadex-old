<?php
$thread_id = $_GET['id'] ?? 1;

require_once (ABSPATH . "/scripts/JBBCode/Parser.php");
$parser = new JBBCode\Parser();
$parser->addCodeDefinitionSet(new JBBCode\DefaultCodeDefinitionSet());
	
$limit = 20;
$offset = 0;
$posts = new Forum_Posts($db, $thread_id, $limit, $offset);
?>

<?= $posts->get_breadcrumb($db, $thread_id) ?>

<div class="row" style="margin-bottom: 20px;">
	<div class="col-xs-6">
		<?= display_post_reply($user->level_id) ?>
	</div>
	
	<div class="col-xs-6 text-right">
	pagination
	</div>
</div>

<div class="table-responsive">
	<table class="table">
		<thead>
			<tr>
				<th>Author</th>
				<th><?= $posts->get_thread_name($db, $thread_id) ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach ($posts as $post) {

				$parser->parse($post->post_text);
				?>
				<tr>		
					<td width="110px"><?= display_glyphicon("user", "fas", "", "fa-fw") ?> <?= $post->user_link ?><br /><img alt="avatar" class="avatar" src="/images/avatars/<?= $post->logo ?>" /></td>
					<td> <?= display_glyphicon("clock", "far", "", "fa-fw") ?> <?= get_time_ago($post->post_timestamp) ?>
						<hr style="margin: 5px 0; clear: both;">
						<?= nl2br($parser->getAsHtml()) ?>
					</td>
				</tr>		
				
				<?php
			}
			?>	
		
		</tbody>
	</table>
</div>

<?php if ($user->level_id >= 3) { ?>

<div id="post_reply" class="display-none panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">Post reply</h3>
	</div>
	<div class="panel panel-body">
		<form class="form-horizontal" method="post" id="post_reply_form">
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
					<textarea rows="10" type="text" class="form-control" id="post_text" name="post_text" placeholder="BBCode allowed" required></textarea>
				</div>
			</div>
			<div class="form-group">
				<div class="col-xs-6"><button type="submit" class="btn btn-default" id="new_forum_thread_button"><?= display_glyphicon("pencil-alt", "fas", "", "fa-fw") ?> Submit</button></div>
				<div class="col-xs-6 text-right"><button title="Hide" id="back_button" type="button" class="btn btn-default"><?= display_glyphicon("undo", "fas", "", "fa-fw") ?> Back</button></div>
			</div>
		</form>
	</div>
</div>

<?php } ?>

<div class="row" style="margin-bottom: 20px;">
	<div class="col-xs-6">
		<?= display_post_reply($user->level_id) ?>
	</div>
	
	<div class="col-xs-6 text-right">
	pagination
	</div>
</div>

<?= $posts->get_breadcrumb($db, $thread_id) ?>