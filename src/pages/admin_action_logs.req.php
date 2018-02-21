<?php
$logs = new Action_logs($db);
?>

<div class="table-responsive">
	<table class="table table-striped table-hover table-condensed">
		<thead>
			<tr>
                <th>User</th>
                <th>IP</th>
                <th>Time</th>
                <th>Type</th>
				<th>Result</th>
				<th>Details</th>
			</tr>
		</thead>
		<tbody>
		
		<?php foreach ($logs as $key => $log) { ?>
			
			<tr>
                <td><?= $log->username ?></td>
                <td><?= $log->action_ip ?></td>
                <td><?= date("Y-m-d H:i:s \U\T\C", $log->action_timestamp) ?></td>
				<td><?= $log->action_name ?></td>
				<td><?= ($log->action_result) ? "Success" : "Failure" ?></td>
				<td><?= $log->action_details ?></td>
			</tr>			
			
		<?php } ?>
		
		</tbody>
	</table>
</div>