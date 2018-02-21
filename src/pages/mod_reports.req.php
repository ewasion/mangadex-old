<?php
$reports = new Chapter_reports($db, $_GET["type"]);
$report_type_array = array("Other", "All images broken", "Some images broken", "Watermarked images", "Naming rules broken", "Incorrect group", "Group policy evasion", "Official release/Raw");

?>

<div class="table-responsive">
	<table class="table table-striped table-hover table-condensed">
		<thead>
			<tr>
                <th><?= display_glyphicon("hashtag", "fas", "ID") ?></th>
                <th><?= display_glyphicon("book", "fas", "Manga") ?></th>
                <th><?= display_glyphicon("file", "far", "Chapter") ?></th>
                <th><?= display_glyphicon("clock", "far", "Time") ?></th>
                <th><?= display_glyphicon("info-circle", "fas", "Reason") ?></th>
                <th><?= display_glyphicon("comment", "far", "Info") ?></th>
                <th><?= display_glyphicon("user", "fas", "Report user") ?></th>
                <?php if ($_GET["type"] == "new") { ?>
				<th width="110px"><?= display_glyphicon("question-circle", "fas") ?></th>
				<?php } else { ?>
				<th><?= display_glyphicon("question-circle", "fas") ?></th>
				<th><?= display_glyphicon("user-md", "fas", "Mod") ?></th>
				<?php } ?>
			</tr>
		</thead>
		<tbody>
		
		<?php
		foreach ($reports as $key => $report) {
			$key++;
			?>
			
			<tr>
                <td><?= $report->report_id ?></td>
                <td><?php if ($report->manga_id) { ?><a target="_blank" href="/manga/<?= $report->manga_id ?>"><?= display_glyphicon("book", "fas", $report->manga_id) ?></a><?php } ?></td>
                <td><a target="_blank" href="/chapter/<?= $report->report_chapter_id ?>"><?= $report->report_chapter_id ?></a></td>
                <td><?= get_time_ago($report->report_timestamp) ?></td>
				<td><?= $report_type_array[$report->report_type] ?></td>
				<td><?= $report->report_info ?></td>
				<td><a href="/user/<?= $report->report_user_id ?>" target="_blank"><?= $report->reported_name ?></a></td>
				<?php if ($_GET["type"] == "new") { ?>
				<td>
					<button class="btn btn-success btn-sm report_accept" id="<?= $report->report_id ?>"><?= display_glyphicon("check", "fas", "", "fa-fw") ?></button>
					<button class="btn btn-danger btn-sm report_reject" id="<?= $report->report_id ?>"><?= display_glyphicon("times", "fas", "", "fa-fw") ?></button>
				</td>
				<?php } else { ?>
				<td><?= ($report->report_conclusion == 1) ? display_glyphicon("check", "fas", "", "text-success") : display_glyphicon("times", "fas", "", "text-danger") ?></td>
				<td><a href="/user/<?= $report->report_mod_user_id ?>" target="_blank"><?= $report->actioned_name ?></a></td>
				<?php } ?>
			</tr>			
			
			<?php
		}
		?>
			

		</tbody>
	</table>
</div>