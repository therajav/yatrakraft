<?php
/**
 * YatraKraft checkout (wraps the WP Travel Engine checkout shortcode).
 * Booking logic stays 100% WP Travel Engine — this only changes layout.
 *
 * @package YatraKraft
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$yk_content = '';
while ( have_posts() ) {
	the_post();
	ob_start();
	the_content();
	$yk_content = ob_get_clean();
}

$yk_has_cart = false !== strpos( $yk_content, 'wpte-checkout__page-layout' );
$yk_trips    = home_url( '/trips/' );
$yk_wa       = yk_whatsapp_url( 'Hi YatraKraft, I need help completing my booking.' );
?>

<main id="primary" class="yk-checkout">
	<section class="yk-co-head">
		<div class="yk-wrap">
			<div class="yk-co-head__top">
				<a class="yk-co-back" href="<?php echo esc_url( $yk_trips ); ?>" <?php echo $yk_has_cart ? 'onclick="if(history.length>1){history.back();return false;}"' : ''; ?>>
					<span aria-hidden="true">←</span> <?php echo $yk_has_cart ? 'Back to trip' : 'All trips'; ?>
				</a>
				<span class="yk-co-secure">
					<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a5 5 0 0 1 5 5v3h1a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-8a2 2 0 0 1 2-2h1V7a5 5 0 0 1 5-5Zm0 2a3 3 0 0 0-3 3v3h6V7a3 3 0 0 0-3-3Z"/></svg>
					Secure booking
				</span>
			</div>
			<h1 class="yk-co-title"><?php echo $yk_has_cart ? 'Complete your booking' : 'Your booking'; ?></h1>
			<?php if ( $yk_has_cart ) : ?>
				<p class="yk-co-lead">Add the lead traveller's details and confirm. Want to change dates, stay or vehicle? Our Munnar team can adjust it for you.</p>
			<?php endif; ?>
		</div>
	</section>

	<div class="yk-wrap yk-co-body">
		<?php if ( $yk_has_cart ) : ?>
			<?php echo $yk_content; // phpcs:ignore WordPress.Security.EscapeOutput -- WP Travel Engine checkout form markup. ?>
		<?php else : ?>
			<div class="yk-co-empty">
				<div class="yk-co-empty__icon" aria-hidden="true">
					<svg viewBox="0 0 24 24"><path d="M4 7h16l-1.2 11.2A2 2 0 0 1 16.8 20H7.2a2 2 0 0 1-2-1.8L4 7Zm4 0V6a4 4 0 1 1 8 0v1h-2V6a2 2 0 1 0-4 0v1H8Z"/></svg>
				</div>
				<h2>No trip selected yet</h2>
				<p>Pick a trip, choose your date and number of travellers, and you will land back here to confirm. Prefer a custom plan? Just message us.</p>
				<div class="yk-co-empty__actions">
					<a class="yk-btn yk-btn--primary" href="<?php echo esc_url( $yk_trips ); ?>">Browse trips</a>
					<a class="yk-btn yk-btn--wa" href="<?php echo esc_url( $yk_wa ); ?>" target="_blank" rel="noopener">Plan on WhatsApp</a>
				</div>
			</div>
		<?php endif; ?>

		<?php get_template_part( 'yk/trust-strip' ); ?>
	</div>
</main>

<?php
get_footer();
