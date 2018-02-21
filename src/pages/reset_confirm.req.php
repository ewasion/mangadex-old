<?php 
if (!$user->user_id) { 

$code = validate_md5($_GET["code"]);

?>

<script src='https://www.google.com/recaptcha/api.js'></script>

<!-- forgot_container -->
<div style="margin: 0 auto; width: 300px" id="forgot_container">
	<form method="post" id="reset_form">
		<h1 class="text-center">Reset Password</h1>
		<hr>
		
		<div class="form-group">
			<label for="reset_code" class="sr-only">Reset code</label>
			<input data-toggle="popover" data-content="Enter the reset code you received in your email." type="text" name="reset_code" id="reset_code" class="form-control" placeholder="reset_code" required value="<?= $code ?>">
		</div>
		
		<div class="g-recaptcha" data-sitekey="6LdENkAUAAAAADujxWGr1mgXKKpWKoc-2wS2IeXb"></div>
		
		<button class="btn btn-lg btn-danger btn-block" type="submit" id="reset_button"><?= display_glyphicon("sync", "fas", "", "fa-fw") ?> Reset Password</button>
	</form>
</div><!-- /container -->

<?php } else { ?>

<div style="margin: 0 auto; width: 300px" id="login_container">
	<h1 class="text-center">Login</h1>
	<hr>
	<p class="text-center">You are logged in.</p>
</div>

<?php } ?>