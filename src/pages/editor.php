<?php
/**
 * Block list manager for Pi-hole (pages/editor.php).
 *
 * @since 1.0.0
 */
$list_file_type = get_file_type_directory( $_GET[ 'type' ] );
$list_file_name = ( isset( $_GET[ 'list' ] ) ) ? trim( $_GET[ 'list' ] ) : '';

$list_path = $list_file_type . '/' . $list_file_name;
$list_contents  = '';

$list_nice_name = convert_to_nice_name( $list_file_name );

// Save file.
if ( isset( $_POST[ 'file-contents' ] ) && is_writable( $list_path ) ) {
	$file = fopen( $list_path, 'w' );
	fwrite( $file, strip_tags( stripslashes( trim( $_POST[ 'file-contents' ] ) ) ) . PHP_EOL );
	fclose( $file );
}

// Read file.
if ( $list_file_type !== '' && $list_file_name !== '' && is_readable( $list_path ) ) {
	$file = fopen( $list_path, 'r' );

	// If file is not empty.
	if ( filesize( $list_path ) > 0 ) {
		$list_contents = strip_tags( stripslashes( trim( fread( $file, filesize( $list_path ) ) ) ) );
	}

	fclose( $file );
}
?>
<div class="container px-sm-0 px-lg-3 py-3">
	<div class="row">
		<div class="col-lg py-3">
			<h3>List Editor</h3>
			<?php
			if ( $list_file_type === '' || $list_file_name === '' ) {
				echo '<div class="alert alert-warning" id="file-required" role="alert">An file type and file name is required to use the list editor.</div>';
			} elseif ( ! file_exists( $list_path ) ) {
				echo '<div class="alert alert-danger" id="file-not-exist" role="alert">"' . $list_file_name . '" does not exist.</div>';
			} elseif ( ! is_readable( $list_path ) ) {
				echo '<div class="alert alert-danger" id="file-not-read" role="alert">"' . $list_file_name . '" is not readable.</div>';
			} elseif ( ! is_writable( $list_path ) ) {
				echo '<div class="alert alert-danger" id="file-not-write" role="alert">"' . $list_file_name . '" is not writable.</div>';
			} else {
				echo '<div class="alert alert-success d-none" id="file-saved" role="alert">' . $list_nice_name . ' has been saved.</div>';
				echo '<div class="alert alert-primary" id="file-edit" role="alert">' . $list_nice_name . ' is being edited.</div>';
			}
			?>
			<?php if ( $list_file_type !== '' && $list_file_name !== '' && is_readable( $list_path ) && is_writable( $list_path ) ) : ?>
				<form action="<?php echo get_full_url(); ?>" method="POST" role="form">
					<div class="form-group">
						<label for="file-contents" class="sr-only">Editing <?php echo $list_nice_name; ?></label>
						<textarea class="form-control" id="file-contents" name="file-contents"><?php echo $list_contents; ?></textarea>
					</div>
					<button type="submit" class="btn btn-primary">Save List</button>
				</form>
			<?php endif; ?>
		</div>
	</div>
</div>
