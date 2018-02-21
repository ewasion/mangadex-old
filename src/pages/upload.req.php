<?php
$id = $_GET['manga_id'] ?? 1;
$id = sanitise_id($id);

$manga = new Manga($db, $id);
$groups = new Groups($db, "group_name ASC", 3000, 0, array());; //all langs

if ($manga->exists) {
	
	$languages = new Languages($db, "lang_name", "ASC");
	?>

	<div class="panel panel-default">	
		<div class="panel-heading">
			<h3 class="panel-title"><?= display_glyphicon("upload", "fas", "", "fa-fw") ?> Upload guidelines</h3>
		</div>
		<div class="panel-body">
			<ul>
				<li>Do not upload:</li>
				<ul>
					<li>Western comics.</li>
					<li>Scans of official releases, including raws.</li>
					<ul>
						<li>Scanlations of licensed manga are allowed.</li>
					</ul>
					<li>Bulk chapters (e.g. Ch 1-10 as one chapter.)</li>
					<li>Obtrusively watermarked images.</li>
					<li>Images saved from aggregator sites, if an original source is available.</li>	
				</ul>
				<li>File limits:</li>
				<ul>
					<li>File type must be .zip.</li>
					<li>File size must be less than 100MB.</li>
					<li>The archive's directory depth must be at most one.</li>
					<li>No password protected archives.</li>
				</ul>
				<li>General:</li>
				<ul>
					<li>Do not add, edit, or remove any pages unless you are part of the original scanlator group.</li>
					<li>Select "Unknown" if you do not know which group to attribute the chapter to.</li>
					<li>Select "no group" if you do not wish to create a group for your own scanlation.</li>
					<li>If the group does not appear on the drop down list, add it to the database <a target="_blank" href="/group_new">here</a>.</li>
					<li><?= display_glyphicon("exclamation-circle ", "fas") ?> next to a group name indicates that only group members can upload to that group.</li>
				</ul>
				<li>Naming Conventions:</li>
				<ul>
					<li>Do not zeropad volume or chapter numbers. (e.g. 01, 02, ...)</li>
					<li>Include chapter names, if they are available.</li>
					<li>Add [END] to the name of the final chapter of a series.</li>
					<li>Use decimals (e.g. Ch. x.5) for bonus chapters/omake/etc.</li>
					<ul>
						<li>Use Ch. x.1, x.2, etc. if there are multiple omake after one mainline chapter. May have to be zeropadded.</li>
					</ul>
					<li>Edge Cases:</li>
					<ul>
						<li>For chapters not released in volumes, leave the volume numbers blank. These will be sorted at the top.</li>
						<li>For single-volume series, use the format: Vol. 1 Ch. x</li>
						<li>For oneshots, name the chapter "Oneshot" and call it volume 0 and chapter blank. These will be sorted at the bottom.</li>
						<li>For Webtoons, Manhwa, or Manhua, use volume field as the season number.</li>
					</ul>
				</ul>
			</ul>
		</div>
	</div>
	<div class="alert alert-success text-center" role="alert"><strong>Notice:</strong> The upload rules have been updated! Please make sure you read them!</div>
	
	<?php if ($user->user_id == 2) { ?>
		<div class="alert alert-warning text-center" role="alert"><h4><strong>Warning:</strong> Please do not upload <strong>official</strong> releases!</h4></div>
	<?php } ?>
	
	<div class="panel panel-default">	
		<div class="panel-heading">
			<h3 class="panel-title"><?= display_glyphicon("upload", "fas", "", "fa-fw") ?> Upload chapter</h3>
		</div>
		<div class="panel-body">
			<form style="margin-top: 15px;" id="upload_form" method="post" class="form-horizontal" enctype="multipart/form-data">
				<div class="form-group">
					<label for="manga_id" class="col-sm-3 control-label">Manga name</label>
					<div class="col-sm-9">
						<input type="text" class="form-control" title="To change the manga, go to the manga page." disabled value="<?= $manga->manga_name ?>">
						<input type="hidden" id="manga_id" name="manga_id" value="<?= $id ?>">
					</div>
				</div>
				<div class="form-group">
					<label for="chapter_name" class="col-sm-3 control-label">Chapter name</label>
					<div class="col-sm-9">
						<input type="text" class="form-control" id="chapter_name" name="chapter_name" placeholder="Optional">
					</div>
				</div>
				<div class="form-group">
					<label for="volume_number" class="col-sm-3 control-label">Volume number</label>
					<div class="col-sm-9">
						<input type="text" class="form-control" id="volume_number" name="volume_number" placeholder="Decimals allowed">
					</div>
				</div>
				<div class="form-group">
					<label for="chapter_number" class="col-sm-3 control-label">Chapter number</label>
					<div class="col-sm-9">
						<input type="text" class="form-control" id="chapter_number" name="chapter_number" placeholder="Decimals allowed" >
					</div>
				</div>
				<div class="form-group">
					<label for="group_delay" class="col-sm-3 control-label">Apply group delay</label>
					<div class="col-sm-9">   
						<div class="checkbox">  
							<label><input type="checkbox" name="group_delay" id="group_delay" value="1"> Use for new releases!</label> 
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="group_id" class="col-sm-3 control-label">Group 1</label>
					<div class="col-sm-9">   
						<select data-size="10" data-live-search="true" required title="Select a group" class="form-control selectpicker" id="group_id" name="group_id">
							<?php 
							foreach ($groups as $group) {
								$selected = ($group->group_id == $user->upload_group_id) ? "selected" : "";
								print "<option data-icon='glyphicon-" . ($group->group_control ? "exclamation-sign" : "ok") . "' $selected value='$group->group_id'>$group->group_name</option>"; 
							}
							?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="group_id_2" class="col-sm-3 control-label">Group 2</label>
					<div class="col-sm-9">   
						<select data-size="10" data-live-search="true" title="Select a second group" class="form-control selectpicker" id="group_id_2" name="group_id_2">
							<?php 
							foreach ($groups as $group) {
								print "<option data-icon='glyphicon-" . ($group->group_control ? "exclamation-sign" : "ok") . "' value='$group->group_id'>$group->group_name</option>"; 
							}
							?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="group_id_3" class="col-sm-3 control-label">Group 3</label>
					<div class="col-sm-9">   
						<select data-size="10" data-live-search="true" title="Select a third group" class="form-control selectpicker" id="group_id_3" name="group_id_3">
							<?php 
							foreach ($groups as $group) {
								print "<option data-icon='glyphicon-" . ($group->group_control ? "exclamation-sign" : "ok") . "' value='$group->group_id'>$group->group_name</option>"; 
							}
							?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="lang_id" class="col-sm-3 control-label">Language</label>
					<div class="col-sm-9">   
						<select required title="Select a language" class="form-control selectpicker" id="lang_id" name="lang_id" data-size="10">
							<?php 
							foreach ($languages as $language) {
								$selected = ($language->lang_id == $user->upload_lang_id) ? "selected" : "";
								print "<option $selected value='$language->lang_id'>$language->lang_name</option>"; 
							}
							?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="file" class="col-sm-3 control-label">File</label>
					<div class="col-sm-9">
						<div class="input-group">
							<input type="text" class="form-control" placeholder="Filename" readonly disabled>
							<span class="input-group-btn">
								<span class="btn btn-default btn-file">
									<?= display_glyphicon("folder-open", "far", "", "fa-fw") ?> <span class="span-1280">Browse</span> <input type="file" name="file" id="file">
								</span>
							</span>
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-6 text-left">
						<a class="btn btn-default" href="/manga/<?= $id ?>" role="button"><?= display_glyphicon("arrow-left", "fas", "", "fa-fw") ?> <span class="span-1280">Back</span></a>
					</div>
					<div class="col-sm-6 text-right">
						<button type="submit" class="btn btn-default" id="upload_button"><?= display_glyphicon("upload", "fas", "", "fa-fw") ?> <span class="span-1280">Upload</span></button>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-offset-3 col-sm-9 text-right">
						<div class="progress" style="height: 38px; display: none;">
							<div id="progressbar" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" style="width: 0%;" class="progress-bar progress-bar-info"></div>
						</div>
					</div>
				</div>
			</form>	
		</div>
	</div>	
	
<?php 
}
else print "<div class='alert alert-danger text-center' role='alert'>" . display_glyphicon("exclamation-triangle", "fa") . " <strong>Warning:</strong> Manga #$id does not exist.</div>";
?>