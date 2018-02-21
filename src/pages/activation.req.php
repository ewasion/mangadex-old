<?php if (!$user->activated) { ?>

<div class="alert alert-info text-center" role="alert"><strong>Notice:</strong> Your activation code will be emailed to <strong><?= $user->email ?></strong>. If this is incorrect, or you are not receiving your code, email <strong>anidex.moe@gmail.com</strong> for assistance.</div>

<div style="margin: 0 auto; width: 300px" id="activation_container">
	
	<form method="post" id="activation_form" >
		<h1 class="text-center">Activation</h1>
		<hr>
		<div class="form-group">
			<label for="activation_code" class="sr-only">Activation code</label>
			<input type="text" name="activation_code" id="activation_code" class="form-control" placeholder="Activation code" required>
		</div>
		
		<button class="btn btn-lg btn-success btn-block" type="submit" id="activate_button">Activate</button>
		<a href="#" class="btn btn-lg btn-warning btn-block" id="resend_button">Resend activation code</a>
	</form>
</div>

<?php } else { ?>
	
<div class="alert alert-success text-center" role="alert"><strong>Success:</strong> Your account is activated and you have access to all of <?= TITLE ?>'s features.</div>
	
<?php } ?>