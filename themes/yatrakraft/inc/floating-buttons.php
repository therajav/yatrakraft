<?php
/**
 * Floating WhatsApp button + back-to-top button.
 *
 * @package YatraKraft
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Where the floating buttons appear. Not on checkout: nothing should pull people
 * away from paying, and that page already has a "Talk to a person" card.
 */
function yatrakraft_show_floating_buttons() {
	$show = ! is_admin() && ! yatrakraft_is_checkout();
	return (bool) apply_filters( 'yatrakraft_show_floating_buttons', $show );
}

/**
 * WhatsApp opening message for the current page.
 */
function yatrakraft_whatsapp_page_message() {
	if ( is_singular( 'trip' ) ) {
		return sprintf( "Hi YatraKraft, I'm interested in %s. Can you help me plan it?", get_the_title() );
	}
	if ( is_singular( 'post' ) ) {
		return sprintf( 'Hi YatraKraft, I was reading "%s" and would like help planning a trip.', get_the_title() );
	}
	if ( yatrakraft_is_thankyou() ) {
		return 'Hi YatraKraft, I just made a booking on your website and have a question.';
	}
	return "Hi YatraKraft, I'd like help planning a trip.";
}

function yatrakraft_enqueue_floating_buttons() {
	if ( ! yatrakraft_show_floating_buttons() ) {
		return;
	}
	$rel  = '/assets/js/yk-float.js';
	$path = get_stylesheet_directory() . $rel;
	wp_enqueue_script(
		'yk-float',
		get_stylesheet_directory_uri() . $rel,
		array(),
		file_exists( $path ) ? (string) filemtime( $path ) : YATRAKRAFT_THEME_VERSION,
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
}
add_action( 'wp_enqueue_scripts', 'yatrakraft_enqueue_floating_buttons', 30 );

function yatrakraft_floating_buttons() {
	if ( ! yatrakraft_show_floating_buttons() ) {
		return;
	}
	?>
	<div class="yk-float" aria-label="Quick actions">
		<button type="button" class="yk-float__top" aria-label="Back to top" title="Back to top">
			<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 7.6 5.7 13.9l1.4 1.4 4.9-4.9 4.9 4.9 1.4-1.4L12 7.6Z"/></svg>
		</button>
		<a class="yk-float__wa" href="<?php echo esc_url( yatrakraft_whatsapp_url( yatrakraft_whatsapp_page_message() ) ); ?>" target="_blank" rel="noopener" aria-label="Chat with us on WhatsApp">
			<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12.04 2a9.9 9.9 0 0 0-8.5 14.98L2 22l5.16-1.5A9.93 9.93 0 1 0 12.04 2Zm0 18.1a8.2 8.2 0 0 1-4.18-1.15l-.3-.18-3.07.9.92-2.99-.2-.31a8.2 8.2 0 1 1 6.83 3.73Zm4.5-6.14c-.25-.12-1.46-.72-1.69-.8-.23-.08-.39-.12-.56.12-.16.25-.64.8-.79.97-.14.16-.29.18-.54.06a6.7 6.7 0 0 1-3.34-2.92c-.25-.43.25-.4.72-1.34.08-.16.04-.3-.02-.43-.06-.12-.56-1.34-.76-1.84-.2-.48-.4-.41-.56-.42h-.48a.92.92 0 0 0-.66.31 2.78 2.78 0 0 0-.87 2.07 4.82 4.82 0 0 0 1.01 2.56 11.04 11.04 0 0 0 4.23 3.74c1.57.68 2.19.74 2.98.62.48-.07 1.46-.6 1.67-1.18.2-.58.2-1.08.14-1.18-.06-.1-.22-.16-.47-.28Z"/></svg>
			<span class="yk-float__wa-label">Chat with us</span>
		</a>
	</div>
	<?php
}
add_action( 'wp_footer', 'yatrakraft_floating_buttons', 5 );
