<?php
/**
 * Customizer: optional header button (ported from Travel Journey).
 *
 * @package YatraKraft
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function yatrakraft_customize_register( $wp_customize ) {

	$wp_customize->add_section(
		'yatrakraft_header_button_settings',
		array(
			'priority' => 15,
			'title'    => __( 'Header Button', 'yatrakraft' ),
			'panel'    => 'travelscape_header_settings',
		)
	);

	$wp_customize->add_setting(
		'yatrakraft_header_button_label',
		array(
			'type'              => 'theme_mod',
			'transport'         => 'refresh',
			'sanitize_callback' => 'wp_filter_nohtml_kses',
		)
	);
	$wp_customize->add_control(
		'yatrakraft_header_button_label_control',
		array(
			'label'    => __( 'Header Button Label', 'yatrakraft' ),
			'section'  => 'yatrakraft_header_button_settings',
			'settings' => 'yatrakraft_header_button_label',
			'priority' => 5,
			'type'     => 'text',
		)
	);

	$wp_customize->add_setting(
		'yatrakraft_header_button_link',
		array(
			'type'              => 'theme_mod',
			'transport'         => 'refresh',
			'sanitize_callback' => 'esc_url_raw',
		)
	);
	$wp_customize->add_control(
		'yatrakraft_header_button_link_control',
		array(
			'label'       => __( 'Header Button URL', 'yatrakraft' ),
			'description' => __( 'Enter button URL/Link Here.', 'yatrakraft' ),
			'section'     => 'yatrakraft_header_button_settings',
			'settings'    => 'yatrakraft_header_button_link',
			'priority'    => 10,
			'type'        => 'url',
		)
	);
}
add_action( 'customize_register', 'yatrakraft_customize_register' );
