<?php
// Taken almost verbatim from the BuddyPress Docs testing setup.

if ( ! defined( 'BP_TESTS_DIR' ) ) {
	// define( 'BP_TESTS_DIR', dirname( __FILE__ ) . '/../../buddypress/tests/phpunit' );
	define( 'BP_TESTS_DIR', dirname( __FILE__ ) . '/../../bp-svn-bporg/tests/phpunit' );
}

if ( file_exists( BP_TESTS_DIR . '/bootstrap.php' ) ) :

	require_once( BP_TESTS_DIR . '/includes/define-constants.php' );

	if ( ! file_exists( WP_TESTS_DIR . '/includes/functions.php' ) ) {
		die( "The WordPress PHPUnit test suite could not be found.\n" );
	}

	require_once WP_TESTS_DIR . '/includes/functions.php';

	function _bootstrap_hgbp() {
		// Make sure BP is installed and loaded first
		require BP_TESTS_DIR . '/includes/loader.php';

		// Then load this plugin.
		require dirname( __FILE__ ) . '/../hierarchical-groups-for-bp.php';
	}
	tests_add_filter( 'muplugins_loaded', '_bootstrap_hgbp' );

	// We need pretty permalinks for some tests
	function _set_permalinks() {
		update_option( 'permalink_structure', '/%year%/%monthnum%/%day%/%postname%/' );
	}
	tests_add_filter( 'init', '_set_permalinks', 1 );

	// We need pretty permalinks for some tests
	function _flush() {
		flush_rewrite_rules();
	}
	tests_add_filter( 'init', '_flush', 1000 );

	require WP_TESTS_DIR . '/includes/bootstrap.php';

	// Load the BP-specific testing tools
	require BP_TESTS_DIR . '/includes/testcase.php';

	// include our testcase
	require( dirname(__FILE__) . '/hgbp-testcase.php' );

endif;
