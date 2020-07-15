<?php
/**
 * Block list manager for Pi-hole (pages/login.php).
 *
 * @since 1.0.0
 */
$wrong_password = false;
$empty_password = false;

if ( isset( $_POST[ 'password' ] ) ) {
	if ( $_POST[ 'password' ] !== '' ) {
		$safe_password = filter_var( $_POST[ 'password' ], FILTER_SANITIZE_STRING );

		if ( $safe_password !== WEB_PASSWORD ) {
			$wrong_password = true;
		}
	} else {
		$empty_password = true;
	}
}
?>
<div class="container px-sm-0 px-lg-3 py-3">
	<div class="center-content row">
		<div class="p-3">
			<h2 class="mb-3">Sign-in to Block List Manager</h2>
			<?php
			if ( $wrong_password === true ) {
				echo '<div class="alert alert-danger" role="alert">Invalid password.</div>';
			} elseif ( $empty_password === true ) {
				echo '<div class="alert alert-danger" role="alert">A password is required.</div>';
			}
			?>
			<form action="<?php echo get_full_url( BASE_URL . 'login' ); ?>" method="POST" role="form">
				<div class="form-group">
					<label for="password" class="sr-only">Password</label>
					<input type="password" class="form-control" id="password" name="password" placeholder="Password">
				</div>
				<div class="form-group form-check">
					<input type="checkbox" class="form-check-input" id="remember" name="remember">
					<label class="form-check-label" for="remember">Remember me for 7 days</label>
				</div>
				<button type="submit" class="btn btn-primary">Log in</button>
			</form>
		</div>
	</div>
</div>
