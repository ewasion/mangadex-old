<li class="dropdown">
	<a href="/user/<?= $user->user_id ?>" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
	<?= (!$user->user_id) ? display_glyphicon("user-times", "fas", "", "fa-fw") : display_glyphicon("user", "fas", "", "fa-fw")	?>
	<span class="nav-label-1440"><?= $user->username ?></span> <span class="caret"></span></a>
	<ul class="dropdown-menu">
	
		<?php if (!$user->user_id) { ?>
		<li class="<?= display_active($_GET['page'], "login") ?>" id="login"><a href="/login"><?= display_glyphicon("sign-in-alt", "fas", "", "fa-fw") ?> Log in</a></li> 
		<li class="<?= display_active($_GET['page'], "signup") ?>" id="signup"><a href="/signup"><?= display_glyphicon("pencil-alt", "fas", "", "fa-fw") ?> Sign up</a></li>
		<?php } 
		
		else { 
			if ($user->level_id >= 10) { ?>
			<li><a href="/mod"><?= display_glyphicon("wrench", "fas", "", "fa-fw") ?> Moderation</a></li>
			<?php } 
			
			if ($user->level_id >= 15) { ?>
			<li><a href="/admin"><?= display_glyphicon("wrench", "fas", "", "fa-fw") ?> Admin</a></li>
			<?php } ?>
			
		<li><a href="/user/<?= $user->user_id ?>/<?= $user->user_slug ?>"><?= display_glyphicon("user", "fas", "", "fa-fw") ?> Profile</a></li>
        <li class="<?= display_active($_GET['page'], "settings") ?>" id="settings"><a href="/settings"><?= display_glyphicon("cog", "fas", "", "fa-fw") ?> Settings</a></li>
		<li id="logout"><a href="#"><?= display_glyphicon("sign-out-alt", "fas", "", "fa-fw") ?> Log out</a></li>
		<?php } ?>
       
	</ul>
</li>
