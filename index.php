<?php
/**
 * Block list manager for Pi-hole (index.php).
 *
 * @since 1.0.0
 */
require_once( 'config.php' );
require_once( 'src/functions.php' );

session_start();
session_regenerate_id( true );

// Login helper.
if ( is_path( BASE_URL . 'login', true ) ) {
	if ( isset( $_POST[ 'password' ] ) ) {
		$safe_password = filter_var( $_POST[ 'password' ], FILTER_SANITIZE_STRING );
		$safe_remember = isset( $_POST[ 'remember' ] );

		// If password is correct, login and redirect.
		if ( $safe_password === WEB_PASSWORD ) {
			$_SESSION[ 'logged_in' ]     = true;
			$_SESSION[ 'remember' ]      = $safe_remember;
			$_SESSION[ 'last_activity' ] = time();

			header( 'Location: ' . get_full_url( BASE_URL ) );
			exit();
		}
	} elseif ( is_logged_in() ) {
		// Redirect to homepage if user is already logged in.
		header( 'Location: ' . get_full_url( BASE_URL ) );
		exit();
	}
} elseif ( WEB_PASSWORD !== '' && ! is_logged_in() ) {
	// Redirect to login page if WEB_PASSWORD is set and if user is not logged in.
	header( 'Location: ' . get_full_url( BASE_URL . 'login' ) );
	exit();
}

// Logout helper.
if ( is_path( BASE_URL . 'logout', true ) ) {
	if ( is_logged_in() ) {
		session_unset();
		session_destroy();

		header( 'Location: ' . get_full_url( BASE_URL ) );
		exit();
	}
}

// End invalid/expired sessions or renew current ones.
if ( is_logged_in() ) {
	if (
		$_SESSION[ 'logged_in' ] !== true
		|| ( $_SESSION[ 'remember' ] === false && ( time() - $_SESSION[ 'last_activity' ] ) > 1800 )  // 30 minutes.
		|| ( $_SESSION[ 'remember' ] === true && ( time() - $_SESSION[ 'last_activity' ] ) > 604800 ) // 7 days.
	) {
		header( 'Location: ' . get_full_url( BASE_URL . 'logout' ) );
		exit();
	} elseif ( $_SESSION[ 'remember' ] === false ) {
		// Reset session timer if "Remember me" is not set.
		$_SESSION[ 'last_activity' ] = time();
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<title>Block List Manager for Pi-hole</title>
	<meta name="robots" content="noindex, nofollow" />
	<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
	<link rel="stylesheet" href="src/css/bootstrap.css" />
	<link rel="stylesheet" href="src/css/style.css" />
	<link rel="stylesheet" href="src/css/tablesorter.css" />
	<link rel="stylesheet" href="src/fonts/css/all.css" />
</head>
<body>
<div id="global-header">
	<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
		<div class="container">
			<a class="navbar-brand" href="<?php echo get_full_url( BASE_URL ); ?>">Block List Manager</a>
			<?php if (
				( WEB_PASSWORD !== '' && is_logged_in() ) // If WEB_PASSWORD is set and logged in.
				|| ( WEB_PASSWORD === '' )                // If WEB_PASSWORD is empty and logged in/out.
			) : ?>
				<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#nav-bar" aria-controls="nav-bar" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>
				<div class="collapse navbar-collapse" id="nav-bar" role="navigation">
					<ul class="navbar-nav mr-auto">
						<li class="nav-item<?php echo ( is_path( BASE_URL, true ) || is_path( BASE_URL . 'overview', true ) ) ? ' active' : ''; ?>">
							<a class="nav-link" href="<?php echo get_full_url( BASE_URL . 'overview' ); ?>">
								<span>Overview</span>
								<?php echo ( is_path( BASE_URL, true ) || is_path( BASE_URL . 'overview', true ) ) ? '<span class="sr-only">(current)</span>' : ''; ?>
							</a>
						</li>
						<li class="nav-item dropdown<?php echo ( is_path( BASE_URL . 'editor', false ) ) ? ' active' : ''; ?>">
							<a class="nav-link dropdown-toggle" href="#" id="nav-bar-list-editor" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<span>List Editor</span>
								<?php echo ( is_path( BASE_URL . 'editor', false ) ) ? '<span class="sr-only">(current)</span>' : ''; ?>
							</a>
							<div class="dropdown-menu" aria-labelledby="nav-bar-list-editor">
								<?php
								foreach ( BLOCK_LIST_FILES as $file ) {
									if ( $file !== '' && file_exists( BLOCK_DIRECTORY . '/' . $file ) ) {
										$name   = convert_to_nice_name( $file );
										$active = ( is_path( BASE_URL . 'editor?type=block&list=' . $file, true ) ) ? ' active' : '';

										echo '<a class="dropdown-item' . $active . '" href="' . get_full_url( BASE_URL . 'editor' ) . '?type=block&list=' . $file . '">' . $name . '</a>';
									}
								}

								if ( count( ACCEPT_LIST_FILES ) > 0 ) {
									echo '<div class="dropdown-divider"></div>';
								}

								foreach ( ACCEPT_LIST_FILES as $file ) {
									if ( $file !== '' && file_exists( ACCEPT_DIRECTORY . '/' . $file ) ) {
										$name   = convert_to_nice_name( $file );
										$active = ( is_path( BASE_URL . 'editor?type=accept&list=' . $file, true ) ) ? ' active' : '';

										echo '<a class="dropdown-item' . $active . '" href="' . get_full_url( BASE_URL . 'editor' ) . '?type=accept&list=' . $file . '">' . $name . '</a>';
									}
								}

								if ( count( ACCEPT_LIST_FILES ) > 0 ) {
									echo '<div class="dropdown-divider"></div>';
								}

								foreach ( WATCH_LIST_FILES as $file ) {
									if ( $file !== '' && file_exists( WATCH_DIRECTORY . '/' . $file ) ) {
										$name   = convert_to_nice_name( $file );
										$active = ( is_path( BASE_URL . 'editor?type=watch&list=' . $file, true ) ) ? ' active' : '';

										echo '<a class="dropdown-item' . $active . '" href="' . get_full_url( BASE_URL . 'editor' ) . '?type=watch&list=' . $file . '">' . $name . '</a>';
									}
								}
								?>
							</div>
						</li>
					</ul>
					<ul class="navbar-nav ml-auto">
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle" href="#" id="nav-bar-pihole" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<span>My Pi-hole</span>
							</a>
							<div class="dropdown-menu dropdown-menu-right" aria-labelledby="nav-bar-pihole">
								<a class="dropdown-item" href="http://192.168.10.100:3737/admin/settings.php" target="_blank">
									<i class="icon fas fa-cogs" aria-hidden="true"></i>
									<span class="ml-1">System Settings</span>
								</a>
								<a class="dropdown-item" href="<?php echo PI_HOLE_URL . '/admin/settings.php?tab=api' ?>" target="_blank">
									<i class="icon fas fa-code" aria-hidden="true"></i>
									<span class="ml-1">API Settings</span>
								</a>
								<a class="dropdown-item" href="<?php echo PI_HOLE_URL . '/admin/groups-adlists.php' ?>" target="_blank">
									<i class="icon fas fa-shield-alt" aria-hidden="true"></i>
									<span class="ml-1">Configure Ad Lists</span>
								</a>
								<a class="dropdown-item" href="<?php echo PI_HOLE_URL . '/admin/gravity.php' ?>" target="_blank">
									<i class="icon fas fa-arrow-circle-down" aria-hidden="true"></i>
									<span class="ml-1">Update Ad Lists</span>
								</a>
							</div>
						</li>
						<?php if ( is_logged_in() ) : ?>
							<li class="nav-item">
								<a class="nav-link" href="<?php echo get_full_url( BASE_URL . 'logout' ); ?>">
									<span>Logout</span>
								</a>
							</li>
						<?php endif; ?>
					</ul>
				</div>
			<?php endif; ?>
		</div>
	</nav>
</div>
<div id="global-body">
	<?php
	if ( WEB_PASSWORD !== '' && ! is_logged_in() ) {
		// Load login page.
		include_once( './src/pages/login.php' );
	} elseif ( ( is_path( BASE_URL, true ) || is_path( BASE_URL . 'overview', true ) ) ) {
		// Load overview page.
		include_once( './src/pages/overview.php' );
	} elseif ( is_path( BASE_URL . 'editor', false ) ) {
		// Load file editor page.
		include_once( './src/pages/editor.php' );
	} else {
		// Load 404 page.
		include_once( './src/pages/not-found.php' );
	}
	?>
</div>
<script src="src/js/jquery.js"></script>
<script src="src/js/bootstrap.js"></script>
<script src="src/js/jquery.tablesorter.js"></script>
<?php
// Load overview tools.
if ( is_path( BASE_URL, true ) || is_path( BASE_URL . 'overview', true ) ) {
	include_once( './src/tools/overview.php' );
}

// Load editor tools.
if ( is_path( BASE_URL . 'editor', false ) ) {
	include_once( './src/tools/editor.php' );
}
?>
</body>
</html>
