<?php
$threads = new PM_Threads($db, $user->user_id); 

if ($threads->num_rows($db, $user->user_id)) {
	
	?>
    <form id="inbox_form" method="post">
        <table class="table table-striped table-hover ">
            <thead>
                <tr>
                    <th width="40px"></th>
                    <th width="200px">Sender</th>
                    <th>Title</th>
                    <th width="130px">Date</th>
                </tr>
            </thead>
            <tbody>
            <?php 
            
            
            
            foreach ($threads as $key => $thread) {
				$key++;
                ?>
                <tr>
                    <td class="text-center"><input style="margin: 5px;" type="checkbox"></td>
                    <td><?= display_glyphicon("user", "fas", "", "fa-fw") ?> <?= $thread->username ?></td>
                    <td><a href="/message/<?= $thread->thread_id ?>"><?= $thread->thread_subject ?></a></td>
                    <td><?= date("Y-m-d H:i", $thread->thread_timestamp) ?></td>
                </tr>
                
                <?php
            }
            
            ?>
                
            </tbody>
            <tfoot>
                <tr>
                    <th width="40px"></th>
                    <th width="200px"><button type="submit" class="btn btn-danger" id="msg_del_button" disabled title="Not functional yet"><?= display_glyphicon("trash", "fas", "", "fa-fw") ?> Delete </button></th>
                    <th></th>
                    <th width="130px"></th>
                </tr>
            </tfoot>
        </table>		
    </form>
	<?php
}
else print "<div class='alert alert-warning text-center' role='alert'>" . display_glyphicon("exclamation-triangle", "fas") . " You don't have any messages.</div>"; 
?>
