<?php // phpcs:ignore

/*
 * Plugin Name: Alphabetize Settings
 * Plugin URI: https://dlxplugins.com/plugins/alphabetize-settings/
 * Description: Alphabetize the settings.
 * Author: DLX Plugins
 * Version: 1.0.0
 * Requires at least: 4.0
 * Requires PHP: 7.2
 * Author URI: https://dlxplugins.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: alphabetize-settings
 * Contributors: ronalfy
 */

namespace DLXPlugins\AlphabetizeSettings;

define( 'ALPHABETIZE_SETTINGS_VERSION', '1.0.0' );
define( 'ALPHABETIZE_SETTINGS_FILE', __FILE__ );

/**
 * Sorts the sub-menu items for options general alphabetically.
 */
function alphabetize_submenu_items() {
	// Get the sub-menu items for options general.
	if ( ! isset( $GLOBALS['submenu']['options-general.php'] ) ) {
		return;
	}
	$submenu_items = $GLOBALS['submenu'][ 'options-general.php' ]; // phpcs:ignore

	if ( null === $submenu_items ) {
		return;
	}

	// Sort the sub-menu items alphabetically.
	usort(
		$submenu_items,
		function( $a, $b ) {
			return strcasecmp( $a[0], $b[0] );
		}
	);

	// Replace options-general.php sub-menu items with the sorted items.
	$GLOBALS['submenu'][ 'options-general.php' ] = $submenu_items; // phpcs:ignore
}

// Run on init and modify globals, as there are no hooks to modify the sub-menu items.
add_filter( 'admin_init', __NAMESPACE__ . '\alphabetize_submenu_items', 10, 2 );
