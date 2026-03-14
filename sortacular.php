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
 * Default WordPress Core Multisite Settings submenu slugs (options-general.php children).
 *
 * Kept in sync with known Core; filter sortacular_core_multisite_settings_slugs for new Core pages.
 *
 * @return string[]
 */
function get_core_multisite_settings_slugs() {
	$settings_slugs = array(
		'settings.php',
	);
	/**
	 * Filter the Core multisite settings (settings.php children) slugs.
	 *
	 * @param string[] $settings_slugs The Core multisite settings (settings.php children) slugs.
	 */
	return apply_filters( 'sortacular_core_multisite_settings_slugs', $settings_slugs );
}

/**
 * Default WordPress Core Appearance submenu slugs (themes.php children).
 *
 * Kept in sync with known Core; filter sortacular_core_appearance_slugs for new Core pages.
 * Includes theme-editor.php so Theme File Editor (added last by Core) stays in Core group.
 * Customize, Header, and Background use full URLs; sort_appearance_menu_items() treats any
 * slug containing 'customize.php' as Core.
 *
 * @return string[]
 */
function get_core_appearance_slugs() {
	$slugs = array(
		'themes.php',
		'site-editor.php',
		'font-library.php',
		'nav-menus.php',
		'theme-editor.php',
		'widgets.php',
		'theme-install.php',
	);
	/**
	 * Filter the Core Appearance (themes.php children) slugs.
	 *
	 * @param string[] $slugs The Core Appearance submenu slugs.
	 */
	return apply_filters( 'sortacular_core_appearance_slugs', $slugs );
}

/**
 * Default WordPress Core Tools submenu slugs (tools.php children).
 *
 * Kept in sync with known Core; filter sortacular_core_tools_slugs for new Core pages.
 * Includes theme-editor.php and plugin-editor.php so editors (added last by Core for block themes) stay in Core group.
 *
 * @return string[]
 */
function get_core_tools_slugs() {
	$slugs = array(
		'tools.php',
		'import.php',
		'export.php',
		'site-health.php',
		'export-personal-data.php',
		'erase-personal-data.php',
		'ms-delete-site.php',
		'network.php',
		'theme-editor.php',
		'plugin-editor.php',
	);
	/**
	 * Filter the Core Tools (tools.php children) slugs.
	 *
	 * @param string[] $slugs The Core Tools submenu slugs.
	 */
	return apply_filters( 'sortacular_core_tools_slugs', $slugs );
}

/**
 * Default WordPress Core Dashboard submenu slugs (index.php children).
 *
 * Kept in sync with known Core; filter sortacular_core_dashboard_slugs for new Core pages.
 *
 * @return string[]
 */
function get_core_dashboard_slugs() {
	$slugs = array(
		'index.php',
		'update-core.php',
		'upgrade.php',
	);
	/**
	 * Filter the Core Dashboard (index.php children) slugs.
	 *
	 * @param string[] $slugs The Core Dashboard submenu slugs.
	 */
	return apply_filters( 'sortacular_core_dashboard_slugs', $slugs );
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
 * Sorts a submenu by Core slugs first (original order), optional separator, then rest A–Z.
 *
 * Only adds the separator when both core and non-core items exist.
 *
 * @param string   $parent_slug Parent menu slug (e.g. 'options-general.php', 'themes.php').
 * @param string[] $core_slugs  Slugs to treat as Core; order preserved. Filter before passing if needed.
 */
function sort_submenu_by_core_then_alpha( $parent_slug, array $core_slugs ) {
	if ( ! isset( $GLOBALS['submenu'][ $parent_slug ] ) ) {
		return;
	}
	$submenu_items = $GLOBALS['submenu'][ $parent_slug ]; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

	if ( ! is_array( $submenu_items ) ) {
		return;
	}

	$core_lookup = array_flip( $core_slugs );
	$core_items  = array();
	$other_items = array();

	foreach ( $submenu_items as $item ) {
		$slug = isset( $item[2] ) ? $item[2] : '';
		if ( isset( $core_lookup[ $slug ] ) ) {
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

	$GLOBALS['submenu'][ $parent_slug ] = $final; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
}

/**
 * Sorts the Settings submenu: Core first (original order), separator, then rest A–Z.
 */
function sort_options_menu_items() {
	sort_submenu_by_core_then_alpha( 'options-general.php', get_core_settings_slugs() );
}

/**
 * Sorts the Appearance submenu: Core first (original order), separator, then rest A–Z.
 *
 * Treats Customize, Header, and Background as Core by including any submenu slug
 * that contains 'customize.php' (Core uses full URLs for those items).
 */
function sort_appearance_menu_items() {
	$core_slugs = get_core_appearance_slugs();
	if ( isset( $GLOBALS['submenu']['themes.php'] ) && is_array( $GLOBALS['submenu']['themes.php'] ) ) {
		foreach ( $GLOBALS['submenu']['themes.php'] as $item ) {
			$slug = isset( $item[2] ) ? $item[2] : '';
			if ( '' !== $slug && str_contains( $slug, 'customize.php' ) && ! in_array( $slug, $core_slugs, true ) ) {
				$core_slugs[] = $slug;
			}
		}
	}
	sort_submenu_by_core_then_alpha( 'themes.php', $core_slugs );
}

/**
 * Sorts the Tools submenu: Core first (original order), separator, then rest A–Z.
 */
function sort_tools_menu_items() {
	sort_submenu_by_core_then_alpha( 'tools.php', get_core_tools_slugs() );
}

/**
 * Sorts the Dashboard submenu: Core first (original order), separator, then rest A–Z.
 */
function sort_dashboard_menu_items() {
	sort_submenu_by_core_then_alpha( 'index.php', get_core_dashboard_slugs() );
}

/**
 * Sorts the Network settings submenu: Core first (original order), separator, then rest A–Z.
 */
function sort_network_settings_menu_items() {
	if ( is_network_admin() ) {
		sort_submenu_by_core_then_alpha( 'settings.php', get_core_multisite_settings_slugs() );
	}
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
			border-top: 1px solid rgb(106, 116, 126);
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
add_action( 'admin_init', __NAMESPACE__ . '\sort_appearance_menu_items' );
add_action( 'admin_init', __NAMESPACE__ . '\sort_tools_menu_items' );
add_action( 'admin_init', __NAMESPACE__ . '\sort_dashboard_menu_items' );
add_action( 'admin_init', __NAMESPACE__ . '\sort_network_settings_menu_items' );
add_action( 'admin_print_styles', __NAMESPACE__ . '\print_separator_styles' );
