<?php
if (isset($_GET['id'])) 
	$id = $_GET['id'];
else 
	die();

$id = sanitise_id($id);

$thread = new PM_Thread($db, $id); 

if ($thread->sender_id == $user->user_id || $thread->recipient_id == $user->user_id) {
	
	$msgs = new PM_Msgs($db, $id); 

	if ($thread->sender_id == $user->user_id) 
		$db->query(" UPDATE mangadex_pm_threads SET sender_read = 1 WHERE thread_id = $id LIMIT 1 ");
	else 
		$db->query(" UPDATE mangadex_pm_threads SET recipient_read = 1 WHERE thread_id = $id LIMIT 1 ");

	//BBCode
	require_once (ABSPATH . "/scripts/JBBCode/Parser.php");
	$parser = new JBBCode\Parser();
	$parser->addCodeDefinitionSet(new JBBCode\DefaultCodeDefinitionSet());

	?>

	<table class="table table-striped table-hover ">
		<thead>
			<tr>
				<th class="text-right">Title: </th>
				<td><?= $thread->thread_subject ?></td>
			</tr>
		</thead>
		<tbody>
		
			<?php
			foreach ($msgs as $msg) {
				
				$msg_user = new User($db, $msg->user_id, "user_id");
				$parser->parse($msg->msg_text);
				?>
			<tr>
				<td width="110px"><img alt="avatar" width="100px" src="/images/avatars/<?= $msg_user->logo ?>" /></td>
				<td><?= display_glyphicon("user", "fas", "", "fa-fw") ?> <?= $msg_user->user_link ?> <span class="pull-right"><?= display_glyphicon("clock", "far", "", "fa-fw") ?> <?= gmdate("Y-m-d H:i:s \U\T\C", $msg->msg_timestamp) ?></span>
					<hr style="margin: 5px 0;  clear: both;">
					<?= nl2br($parser->getAsHtml()) ?>
				</td>
			</tr>
			<?php } ?>	
			
		</tbody>
	</table>	

	<form style="margin: 0 20px;" id="msg_reply_form" name="msg_reply_form" class="form-horizontal">
		<div class="form-group">
			<textarea required id="reply" name="reply" class="form-control" rows="3" placeholder="Post a reply"></textarea>
		</div>
		<div class="form-group text-center">
			<button id="reply_button" type="submit" class="btn btn-default">Submit reply</button>
		</div>
	</form>	
	
<?php 
}
else print "<div class='alert alert-danger text-center' role='alert'>" . display_glyphicon("exclamation-triangle", "fas") . " <strong>Warning:</strong> You are not the sender nor recipient of this thread.</div>";
?>