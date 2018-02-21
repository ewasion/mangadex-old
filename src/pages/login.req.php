<?php if (!$user->user_id) { ?>
<script src='https://www.google.com/recaptcha/api.js'></script>

<div class="alert alert-info text-center" role="alert"><strong>Notice:</strong> If you're having trouble logging in, try clearing cookies or using the "Remember me" checkbox.</div>

<!-- login_container -->
<div style="margin: 0 auto; width: 300px" id="login_container">
	<form method="post" id="login_form" >
		<h1 class="text-center">Login</h1>
		<hr>
		<div class="form-group">
			<label for="login_username" class="sr-only">Username</label>
			<input tabindex="1" type="text" name="login_username" id="login_username" class="form-control" placeholder="Username" required>
		</div>
		
		<div class="form-group">
			<label for="login_password" class="sr-only">Password</label>
			<input tabindex="2" type="password" name="login_password" id="login_password" class="form-control" placeholder="Password" required>
		</div>

		<div class="checkbox">
			<label>
			<input type="checkbox" name="remember_me" value="1"> Remember me (1 year)
			</label>
		</div>	
		
		<button tabindex="3" class="btn btn-lg btn-success btn-block" type="submit" id="login_button"><?= display_glyphicon("sign-in-alt", "fas", "", "fa-fw") ?> Login</button>
		
		<a href="#" class="btn btn-lg btn-warning btn-block" id="forgot_button"><?= display_glyphicon("sync", "fas", "", "fa-fw") ?> Reset Password</a>
	</form>
</div>

<!-- forgot_container -->
<div style="margin: 0 auto; width: 300px" id="forgot_container" class="display-none">
	<form method="post" id="reset_form">
		<h1 class="text-center">Reset Password</h1>
		<hr>
		
		<div class="form-group">
			<label for="reset_email" class="sr-only">Email Address</label>
			<input data-toggle="popover" data-content="Enter the email address used when you registered." type="email" name="reset_email" id="reset_email" class="form-control" placeholder="Email Address" required>
		</div>
		
		<div class="g-recaptcha" data-sitekey="6LdENkAUAAAAADujxWGr1mgXKKpWKoc-2wS2IeXb"></div>
		
		<button class="btn btn-lg btn-danger btn-block" type="submit" id="reset_button"><?= display_glyphicon("sync", "fas", "", "fa-fw") ?> Send reset email</button>
	</form>
</div><!-- /container -->

<?php } else { ?>

<div style="margin: 0 auto; width: 300px" id="login_container">
	<h1 class="text-center">Login</h1>
	<hr>
	<p class="text-center">You are logged in.</p>
</div>

<?php } ?>