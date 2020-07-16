<?php
/**
 * Pi-hole configuration.
 *
 * @since 1.0.0
 */
define( 'PI_HOLE_URL', 'http://192.168.1.2:80' );
define( 'PI_HOLE_TOKEN', '' );

/**
 * Script configuration.
 *
 * @since 1.0.0
 */
define( 'BASE_URL', '/' );
define( 'WEB_PASSWORD', '' );

/**
 * Block lists file location.
 *
 * @since 1.0.0
 */
define( 'BLOCK_DIRECTORY', realpath( './lists/block' ) );
define( 'BLOCK_LIST_FILES', [ 'my-list.list', 'my-list.txt' ] );

/**
 * Accept lists file location.
 *
 * @since 1.0.0
 */
define( 'ACCEPT_DIRECTORY', realpath( './lists/accept' ) );
define( 'ACCEPT_LIST_FILES', [ 'my-list.list' ] );

/**
 * Watch lists file location.
 *
 * @since 1.0.0
 */
define( 'WATCH_DIRECTORY', realpath( './lists/watch' ) );
define( 'WATCH_LIST_FILES', [ 'my-list.list' ] );
