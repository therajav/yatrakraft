<?php
/**
 * YatraKraft theme functions (child of Travelscape).
 *
 * The first half ports what Travel Journey 1.0.3 did (styles, fonts, colour
 * defaults, header layout); the YatraKraft customisations live in inc/.
 *
 * @package YatraKraft
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'YATRAKRAFT_THEME_VERSION', wp_get_theme()->get( 'Version' ) );
define( 'YATRAKRAFT_THEME_DIR', get_stylesheet_directory() );

function yatrakraft_enqueue_styles() {
	wp_enqueue_style( 'yatrakraft-style', get_stylesheet_uri(), array( 'travelscape-style' ), YATRAKRAFT_THEME_VERSION );
}
add_action( 'wp_enqueue_scripts', 'yatrakraft_enqueue_styles' );

function yatrakraft_setup() {
	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'yatrakraft_custom_background_args',
			array(
				'default-color' => '#ffffff',
				'default-image' => '',
			)
		)
	);
}
add_action( 'after_setup_theme', 'yatrakraft_setup' );

/**
 * Fonts: swap Travelscape's Marcellus / DM Sans for Outfit (headings) + Inter (text).
 * wptt_get_webfont_url() (from Travelscape) downloads the Google CSS + font files
 * locally so no request goes to Google at page load.
 */
function yatrakraft_fonts() {
	wp_dequeue_style( 'travelscape-fonts' );

	$font_families = array(
		'Outfit:wght@400;500;700',
		'Inter:wght@100;300;400;500;600;700',
	);

	$fonts_url = add_query_arg(
		array(
			'family'  => implode( '&family=', $font_families ),
			'display' => 'swap',
		),
		'https://fonts.googleapis.com/css2'
	);

	$fonts = function_exists( 'wptt_get_webfont_url' ) ? wptt_get_webfont_url( esc_url_raw( $fonts_url ) ) : $fonts_url;

	wp_enqueue_style( 'yatrakraft-fonts', $fonts, array(), YATRAKRAFT_THEME_VERSION );
}
add_action( 'wp_enqueue_scripts', 'yatrakraft_fonts', 15 );
add_action( 'enqueue_block_editor_assets', 'yatrakraft_fonts', 15 );

/**
 * Default colours / fonts fed to Travelscape's design tokens.
 * Saved Customizer colours (travelscape_color_ed) still take precedence.
 */
function yatrakraft_style_defaults( $defaults ) {
	$defaults['primary-color']   = '#2f3e46';
	$defaults['secondary-color'] = '#1e7b45';
	$defaults['body-color']      = '#354f52';
	$defaults['heading-color']   = '#111638';
	$defaults['subtle-color']    = '#fafafa';
	$defaults['base-color']      = '#ffffff';

	$defaults['font-family']    = 'Inter, sans-serif';
	$defaults['font-family-h1'] = 'Outfit, sans-serif';
	$defaults['font-family-h2'] = 'Outfit, sans-serif';
	$defaults['font-family-h3'] = 'Outfit, sans-serif';
	$defaults['font-family-h4'] = 'Outfit, sans-serif';
	$defaults['font-family-h5'] = 'Outfit, sans-serif';
	$defaults['font-family-h6'] = 'Outfit, sans-serif';

	return $defaults;
}
add_filter( 'travelscape_style', 'yatrakraft_style_defaults', 15 );

require_once YATRAKRAFT_THEME_DIR . '/inc/customizer.php';
require_once YATRAKRAFT_THEME_DIR . '/inc/theme-mods.php';
require_once YATRAKRAFT_THEME_DIR . '/inc/yatrakraft.php';

/**
 * Optional header button (Customizer → Header → Header Button).
 */
function yatrakraft_header_button() {
	$label = get_theme_mod( 'yatrakraft_header_button_label' );
	$url   = get_theme_mod( 'yatrakraft_header_button_link' );
	if ( ! $label || ! $url ) {
		return;
	}
	echo '<div class="travelscape-header-button">';
	echo '<a class="header-button" href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a>';
	echo '</div>';
}

function yatrakraft_header_desktop() {
	$site_header = new Travelscape_Header();
	echo '<div class="container">';
		echo '<div class="row header-desktop">';
			$site_header->travelscape_header_logo();
			$site_header->travelscape_header_menu();
			yatrakraft_header_button();
			yk_account_icon();
		echo '</div>';
	echo '</div>';
}
add_filter( 'travelscape_header_desktop', 'yatrakraft_header_desktop', 15 );

function yatrakraft_header_mobile() {
	$site_header = new Travelscape_Header();
	echo '<div class="container">';
		echo '<div class="header-mobile">';
			echo '<div class="row top-wrap">';
				$site_header->travelscape_header_logo();
				yatrakraft_header_button();
				yk_account_icon();
				$site_header->travelscape_header_menu_toggle();
			echo '</div>';
			echo '<div class="row">';
				$site_header->travelscape_header_menu();
			echo '</div>';
		echo '</div>';
	echo '</div>';
}
add_filter( 'travelscape_header_mobile', 'yatrakraft_header_mobile', 15 );
