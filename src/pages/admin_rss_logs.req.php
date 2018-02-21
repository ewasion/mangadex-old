<?php
$visit_logs = new Visit_logs($db, "rss");
?>

<div class="table-responsive">
	<table class="table table-striped table-hover table-condensed">
		<thead>
			<tr>
                <th>User</th>
                <th>IP address</th>
                <th>Time</th>
                <th>Page</th>
                <th>Referer</th>
                <th>H</th>
                <th>User agent</th>
			</tr>
		</thead>
		<tbody>
		
		<?php
		foreach ($visit_logs as $key => $log) {
			$key++;
			?>
			
			<tr>
                <td><?= $log->username ?></td>
                <td><?= $log->visit_ip ?></td>
                <td><?= date("Y-m-d H:i:s \U\T\C", $log->visit_timestamp) ?></td>
				<td><?= $log->visit_page ?></td>
				<td><?= $log->visit_referrer ?></td>
				<td><?= $log->visit_h_toggle ?></td>
				<td><?= $log->visit_user_agent ?></td>
			</tr>			
			
			<?php
		}
		?>
			

		</tbody>
	</table>
</div>