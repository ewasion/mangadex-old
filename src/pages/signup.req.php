<script src='https://www.google.com/recaptcha/api.js'></script>

<div style="margin: 0 auto; width: 300px" id="signup_container">
	<form method="post" id="signup_form" >
		<h1 class="text-center">Sign up</h1>
		<hr>
		
		<div class="form-group">
			<label for="reg_username" class="sr-only">Username</label>
			<input data-toggle="popover" data-content="Alphanumeric characters only." type="text" name="reg_username" id="reg_username" class="form-control" placeholder="Username" required>
		</div>
		
		<div class="form-group">	
			<label for="reg_pass1" class="sr-only">Password</label>
			<input data-toggle="popover" data-content="Minimum length: 8 characters." type="password" name="reg_pass1" id="reg_pass1" class="form-control" placeholder="Password" required>
		</div>
		
		<div class="form-group">	
			<label for="reg_pass2" class="sr-only">Confirm Password</label>
			<input data-toggle="popover" data-content="Type your password again." type="password" name="reg_pass2" id="reg_pass2" class="form-control" placeholder="Password (again)" required>
		</div>
		
		<div class="form-group">	
			<label for="reg_email1" class="sr-only">Email Address</label>
			<input data-toggle="popover" data-content="Valid email required for activation." type="email" name="reg_email1" id="reg_email1" class="form-control" placeholder="Email Address" required>
		</div>
		
		<div class="form-group">	
			<label for="reg_email2" class="sr-only">Confirm Email Address</label>
			<input data-toggle="popover" data-content="Type your email again." type="email" name="reg_email2" id="reg_email2" class="form-control" placeholder="Email Address (again)" required>
		</div>
		
		<div class="g-recaptcha" data-sitekey="6LdENkAUAAAAADujxWGr1mgXKKpWKoc-2wS2IeXb"></div>
		
		<button class="btn btn-lg btn-default btn-block" type="submit" id="signup_button"><?= display_glyphicon("pencil-alt", "fas", "", "fa-fw") ?> Sign up</button>
		
	</form>
</div>
