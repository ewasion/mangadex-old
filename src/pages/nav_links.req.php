<?php
$_GET['page'] = $_GET['page'] ?? "";
?>

<li class="<?= display_active($_GET['page'], "titles") ?>" id="titles">
	<a href="/titles"><?= display_glyphicon("book", "fas", "Manga List", "fa-fw") ?> <span class="nav-label-992">Manga</span></a>
</li>

<li class="<?= display_active($_GET['page'], "groups") ?>" id="groups">
	<a href="/groups"><?= display_glyphicon("users", "fas", "Groups", "fa-fw") ?> <span class="nav-label-1440">Groups</span></a>
</li>
<!--
<li class="<?= display_active($_GET['page'], "user") ?>" id="users">
	<a href="/users"><?= display_glyphicon("user", "fas", "Users", "fa-fw") ?> <span class="nav-label-1440">Users</span></a>
</li>
-->
<?php
if (!$user->user_id) { ?>

	<li id="login">
    	<a href="/login" title="You need to log in."><?= display_glyphicon("bookmark", "fas", "Follows", "fa-fw") ?> <span class="nav-label-1440">Follows</span></a>
    </li>
	
<?php } elseif (!$user->activated) { ?>

	<li id="activation">
    	<a href="/activation" title="You need to activate your account."><?= display_glyphicon("bookmark", "fas", "Follows", "fa-fw") ?> <span class="nav-label-1440">Follows</span></a>
    </li>
	
<?php } else { ?>

	<li class="<?= display_active($_GET['page'], "follows") ?>" id="follows">
    	<a href="/follows"><?= display_glyphicon("bookmark", "fas", "Follows", "fa-fw") ?> <span class="nav-label-1440">Follows</span></a>
    </li>
	
<?php } ?>





<li class="dropdown">
	<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
		<?= display_glyphicon("info-circle", "fas", "Info", "fa-fw") ?> <span class="nav-label-1440">Info</span> <span class="caret"></span>
    </a>
	<ul class="dropdown-menu">
		<!--<li>
			<a href="https://forums.anidex.moe" target="_blank"><?= display_glyphicon("university", "fas", "Forums", "fa-fw") ?> Forums</a>
		</li>-->
		<li class="<?= display_active($_GET['page'], "about") ?>" id="about">
        	<a href="/about"><?= display_glyphicon("info", "fas", "About", "fa-fw") ?> About</a>
        </li>
		<li class="<?= display_active($_GET['page'], "funding") ?>" id="funding">
        	<a href="/funding"><?= display_glyphicon("info", "fas", "Funding", "fa-fw") ?> Funding</a>
        </li>
		<li class="<?= display_active($_GET['page'], "changelog") ?>" id="changelog">
        	<a href="/changelog"><?= display_glyphicon("list", "fas", "Change log", "fa-fw") ?> Change log</a>
        </li>
		<li>
        	<a href="irc://irc.rizon.net/mangadex"><?= display_glyphicon("hashtag", "fas", "IRC", "fa-fw") ?> IRC</a>
        </li>
		<li>
        	<a href="https://discord.gg/Y2YKXUP" target="_blank"><?= display_glyphicon("discord", "fab", "Rules", "fa-fw") ?> Discord</a>
        </li>
		<li class="<?= display_active($_GET['page'], "rules") ?>" id="rules">
        	<a href="/rules"><?= display_glyphicon("list", "fas", "Rules", "fa-fw") ?> Rules</a>
        </li>
	</ul>
</li>