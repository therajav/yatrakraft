<?php
/**
 * Carry Travel Journey's Customizer settings over to this theme.
 *
 * WordPress stores theme mods per theme (option "theme_mods_{stylesheet}"), so a
 * freshly activated child theme would start without the logo, header image,
 * social links, colours, copyright text, etc.
 *
 * - Before activation (theme preview) the mods are read from Travel Journey on
 *   the fly, so the preview matches the live site. Nothing is written.
 * - On activation they are copied once into theme_mods_yatrakraft. Menu
 *   locations and widgets are left to core, which maps them on theme switch
 *   (both themes use Travelscape's locations/sidebars).
 *
 * @package YatraKraft
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'YATRAKRAFT_LEGACY_THEME', 'travel-journey' );

/**
 * Travel Journey's mods, with its own keys renamed to ours.
 *
 * @return array
 */
function yatrakraft_legacy_theme_mods() {
	$mods = get_option( 'theme_mods_' . YATRAKRAFT_LEGACY_THEME );
	if ( ! is_array( $mods ) ) {
		return array();
	}

	// Core's per-theme widget stash; must not be copied between themes.
	unset( $mods['sidebars_widgets'] );

	$renamed = array(
		'travel_journey_header_button_label' => 'yatrakraft_header_button_label',
		'travel_journey_header_button_link'  => 'yatrakraft_header_button_link',
	);
	foreach ( $renamed as $old => $new ) {
		if ( array_key_exists( $old, $mods ) ) {
			$mods[ $new ] = $mods[ $old ];
			unset( $mods[ $old ] );
		}
	}

	return $mods;
}

/**
 * Preview fallback: used only while theme_mods_yatrakraft does not exist yet.
 *
 * @param mixed $default Default value.
 * @return mixed
 */
function yatrakraft_default_theme_mods( $default ) {
	$legacy = yatrakraft_legacy_theme_mods();
	return $legacy ? $legacy : $default;
}
add_filter( 'default_option_theme_mods_' . get_stylesheet(), 'yatrakraft_default_theme_mods' );

/**
 * One-time copy on activation. Values already saved for this theme win
 * (core may have written nav_menu_locations during the switch).
 */
function yatrakraft_migrate_theme_mods() {
	if ( get_option( 'yatrakraft_theme_mods_migrated' ) ) {
		return;
	}

	$option = 'theme_mods_' . get_stylesheet();

	// Read what is really stored, without the preview fallback above.
	remove_filter( 'default_option_' . $option, 'yatrakraft_default_theme_mods' );
	$current = get_option( $option );

	$merged = array_merge( yatrakraft_legacy_theme_mods(), is_array( $current ) ? $current : array() );

	if ( false === $current ) {
		add_option( $option, $merged );
	} else {
		update_option( $option, $merged );
	}
	update_option( 'yatrakraft_theme_mods_migrated', gmdate( 'c' ), false );
}
add_action( 'after_switch_theme', 'yatrakraft_migrate_theme_mods' );
