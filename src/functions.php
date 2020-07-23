<?php
/**
 * Convert file name to nice name.
 *
 * @param string $file_name - The file name.
 *
 * @return string
 *
 * @since 1.0.0
 */
function convert_to_nice_name( $file_name ) {
	$chars     = str_replace( '--', '-&-', $file_name );
	$words     = str_replace( [ '-', '.list', '.txt' ], [ ' ', '', '' ], $chars );
	$uppercase = ucwords( $words );
	$grammar   = str_replace( [ 'Of', 'Or', 'And' ], [ 'of', 'or', 'and' ], $uppercase );

	return strip_tags( stripslashes( trim( $grammar ) ) );
}

/**
 * Filter out "#" and "" lines from array.
 *
 * @param string[] $domains - The un-filtered array.
 *
 * @return string[]
 *
 * @since 1.0.0
 */
function filter_invalid_lines( $domains ) {
	$filtered = array_filter( $domains, function ( $line ) {
		if ( strpos( $line, '#' ) !== false || $line === '' ) {
			return false;
		}

		return true;
	} );

	// Reset order of array.
	return array_values( $filtered );
}

/**
 * Get domain from url.
 *
 * @param $url - The url to convert.
 *
 * @return bool|string
 *
 * @since 1.0.0
 */
function get_domain( $url ) {
	$pieces = parse_url( $url );

	$domain = isset( $pieces[ 'host' ] ) ? $pieces[ 'host' ] : '';
	if ( preg_match( '/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs ) ) {
		return $regs[ 'domain' ];
	}

	return false;
}

/**
 * Get file type directory.
 *
 * @param string $type - Type of directory.
 *
 * @return string
 *
 * @since 1.0.0
 */
function get_file_type_directory( $type ) {
	$directory = '';

	switch ( $type ) {
		case 'block':
			$directory = BLOCK_DIRECTORY;
			break;
		case 'accept':
			$directory = ACCEPT_DIRECTORY;
			break;
		case 'watch':
			$directory = WATCH_DIRECTORY;
			break;
	}

	return $directory;
}

/**
 * Get full url.
 *
 * @param string $the_path - The path.
 *
 * @return string
 *
 * @since 1.0.0
 */
function get_full_url( $the_path = '' ) {
	$http = ( isset( $_SERVER[ 'HTTPS' ] ) && $_SERVER[ 'HTTPS' ] === 'on' ) ? 'https://' : 'http://';
	$host = ( $_SERVER[ 'HTTP_HOST' ] ) ? $_SERVER[ 'HTTP_HOST' ] : '';
	$path = ( $the_path === '' ) ? get_path() : $the_path;

	return $http . $host . $path;
}

/**
 * Get path.
 *
 * @return string
 *
 * @since 1.0.0
 */
function get_path() {
	return $_SERVER[ 'REQUEST_URI' ];
}

/**
 * Retrieve resource links for domain.
 *
 * @param string $domain - The domain name.
 *
 * @return string
 *
 * @since 1.0.0
 */
function get_link_resources( $domain ) {
	$output = '';

	$links[] = [
		'Search &quot;' . $domain . '&quot; in Google',
		'fa-search',
		'https://www.google.com/search?q=%22' . $domain . '%22'
	];
	$links[] = [
		'WHOIS Information for ' . get_domain( '//' . $domain ),
		'fa-user-secret',
		'https://www.whois.com/whois/' . get_domain( '//' . $domain )
	];
	$links[] = [
		'Security Trails',
		'fa-shield-alt',
		'https://securitytrails.com/domain/' . $domain . '/dns'
	];
	$links[] = [
		'IP Address Lookup',
		'fa-globe-americas',
		'https://whatismyipaddress.com/ip-lookup/'
	];

	foreach ( $links as $link ) {
		$output .= '<a href="' . $link[ 2 ] . '" class="resource" title="' . $link[ 0 ] . '" target="_blank"><i class="icon fas ' . $link[ 1 ] . '" aria-hidden="true"></i></a>';
	}

	return $output;
}

/**
 * Is logged in.
 *
 * @return bool
 *
 * @since 1.0.0
 */
function is_logged_in() {
	return isset( $_SESSION[ 'logged_in' ], $_SESSION[ 'remember' ], $_SESSION[ 'last_activity' ] );
}

/**
 * Is path.
 *
 * @param string $path - Path to match.
 * @param boolean $strict - Strict matching mode.
 *
 * @return boolean
 *
 * @since 1.0.0
 */
function is_path( $path, $strict = false ) {
	if ( $strict ) {
		return get_path() === $path;
	}

	return strpos( get_path(), $path ) !== false;
}

/**
 * Load files into array.
 *
 * @param string $directory - The directory where files are stored.
 * @param string[] $files - An array of file names.
 *
 * @return string[]
 *
 * @since 1.0.0
 */
function load_files_into_array( $directory, $files ) {
	$information = [];

	foreach ( $files as $file ) {
		if ( $file !== '' && is_readable( $directory . '/' . $file ) ) {
			$current_file = fopen( $directory . '/' . $file, 'r' );
			while ( ! feof( $current_file ) ) {
				array_push( $information, strip_tags( stripslashes( trim( fgets( $current_file ) ) ) ) );
			}
			fclose( $current_file );
		}
	}

	return $information;
}

/**
 * Strpos for arrays.
 *
 * @param string $haystack - The string to search in.
 * @param mixed[] $needle - The needle in an array.
 * @param int $offset - Start search beginning with number.
 *
 * @return bool
 *
 * @since 1.0.0
 */
function strpos_array( $haystack, $needle, $offset = 0 ) {
	if ( ! is_array( $needle ) ) {
		$needle = array( $needle );
	}

	foreach ( $needle as $query ) {
		// Stop on first true result.
		if ( strpos( $haystack, $query, $offset ) !== false ) {
			return true;
		}
	}

	return false;
}
