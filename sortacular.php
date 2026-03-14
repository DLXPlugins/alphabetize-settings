<?php // phpcs:ignore

/*
 * Plugin Name: Sortacular
 * Plugin URI: https://dlxplugins.com/plugins/sortacular/
 * Description: Sort the admin menu items alphabetically (but skip Core settings).
 * Author: DLX Plugins
 * Version: 1.0.0
 * Requires at least: 6.9
 * Requires PHP: 8.0
 * Author URI: https://dlxplugins.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: sortacular
 * Contributors: ronalfy
 */

namespace DLXPlugins\Sortacular;

define( 'SORTACULAR_VERSION', '1.0.0' );
define( 'SORTACULAR_FILE', __FILE__ );

/**
 * Default WordPress Core Settings submenu slugs (options-general.php children).
 *
 * Kept in sync with known Core; filter sortacular_core_settings_slugs for new Core pages.
 *
 * @return string[]
 */
function get_core_settings_slugs() {
	$settings_slugs = array(
		'options-general.php',
		'options-writing.php',
		'options-reading.php',
		'options-discussion.php',
		'options-media.php',
		'options-permalink.php',
		'options-privacy.php',
		'options-connectors.php', // WordPress 7.0.
	);
	/**
	 * Filter the Core settings (options-general.php children) slugs.
	 *
	 * @param string[] $settings_slugs The Core settings (options-general.php children) slugs.
	 */
	return apply_filters( 'sortacular_core_settings_slugs', $settings_slugs );
}

/**
 * Returns the single submenu entry used as the visual separator.
 *
 * @return array{0: string, 1: string, 2: string, 3: string}
 */
function get_separator_submenu_entry() {
	return array(
		'', // Menu title (empty).
		'read',
		'sortacular-separator',
		'',
	);
}

/**
 * Sorts the Settings submenu: Core first (original order), separator, then rest A–Z.
 */
function sort_options_menu_items() {
	if ( ! isset( $GLOBALS['submenu']['options-general.php'] ) ) {
		return;
	}
	$submenu_items = $GLOBALS['submenu']['options-general.php']; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

	if ( ! is_array( $submenu_items ) ) {
		return;
	}

	/**
	 * Filter the Core settings (options-general.php children) slugs.
	 *
	 * @param string[] $core_slugs The Core settings (options-general.php children) slugs.
	 */
	$core_slugs = apply_filters( 'sortacular_core_settings_slugs', get_core_settings_slugs() );
	$core_slugs = array_flip( $core_slugs );

	$core_items  = array();
	$other_items = array();

	foreach ( $submenu_items as $item ) {
		$slug = isset( $item[2] ) ? $item[2] : '';
		if ( isset( $core_slugs[ $slug ] ) ) {
			$core_items[] = $item;
		} else {
			$other_items[] = $item;
		}
	}

	usort(
		$other_items,
		function ( $a, $b ) {
			return strcasecmp( $a[0], $b[0] );
		}
	);

	$separator_entry = get_separator_submenu_entry();
	$with_separator  = count( $core_items ) > 0 && count( $other_items ) > 0;
	$final           = $with_separator
		? array_merge( $core_items, array( $separator_entry ), $other_items )
		: array_merge( $core_items, $other_items );

	$GLOBALS['submenu']['options-general.php'] = $final; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
}

/**
 * Outputs inline CSS so the separator submenu item renders as a divider.
 */
function print_separator_styles() {
	$slug = 'sortacular-separator';
	?>
	<style id="sortacular-separator-styles">
		body #adminmenu ul.wp-submenu > li > a[href*="<?php echo esc_attr( $slug ); ?>"] {
			pointer-events: none;
			cursor: default;
			border-top: 1px solid #3c434a;
			padding-top: 0;
			margin-top: 6px;
			height: 0;
			line-height: 0;
			overflow: hidden;
			text-indent: -9999px;
		}
		body #adminmenu ul.wp-submenu > li > a[href*="<?php echo esc_attr( $slug ); ?>"]:hover {
			background: transparent;
			color: inherit;
		}
	</style>
	<?php
}

// Run after menus are built; use action not filter.
add_action( 'admin_init', __NAMESPACE__ . '\sort_options_menu_items' );
add_action( 'admin_print_styles', __NAMESPACE__ . '\print_separator_styles' );
