<?php
/**
 * YatraKraft thank-you page (wraps [WP_TRAVEL_ENGINE_THANK_YOU]).
 * Booking data is still rendered by WP Travel Engine — this only changes layout.
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

$yk_has_booking = ( false !== strpos( $yk_content, 'wpte-thankyou__' ) || false !== strpos( $yk_content, 'thank-you-container' ) );
$yk_guides      = get_option( 'page_for_posts' ) ? get_permalink( get_option( 'page_for_posts' ) ) : home_url( '/blog/' );
$yk_account     = get_permalink( yatrakraft_account_page_id() );
$yk_wa          = yatrakraft_whatsapp_url( 'Hi YatraKraft, I just made a booking on your website and have a question.' );
?>

<main id="primary" class="yk-checkout yk-thankyou">
	<?php if ( $yk_has_booking ) : ?>
		<section class="yk-ty-hero">
			<div class="yk-wrap">
				<div class="yk-ty-check" aria-hidden="true">
					<svg viewBox="0 0 24 24"><path d="M9.55 17.2 4.3 11.95l1.4-1.4 3.85 3.85 8.75-8.75 1.4 1.4-10.15 10.15Z"/></svg>
				</div>
				<p class="yk-eyebrow">Booking received</p>
				<h1 class="yk-ty-title">Thank you! Your trip is booked.</h1>
				<p class="yk-ty-lead">A confirmation has been sent to your email. Please keep your booking ID handy — it is the quickest way for us to find your trip if you contact us.</p>
			</div>
		</section>

		<div class="yk-wrap yk-co-body">
			<ol class="yk-ty-steps" aria-label="What happens next">
				<li class="is-done">
					<span class="yk-ty-steps__num">1</span>
					<div><strong>Booking received</strong><span>Your details and trip are saved.</span></div>
				</li>
				<li>
					<span class="yk-ty-steps__num">2</span>
					<div><strong>We review and confirm</strong><span>Our team checks your stay, vehicle and timings and gets in touch with any final details.</span></div>
				</li>
				<li>
					<span class="yk-ty-steps__num">3</span>
					<div><strong>Pack your bags</strong><span>Your day-wise plan and contacts are shared before you travel.</span></div>
				</li>
			</ol>

			<div class="yk-ty-content">
				<?php echo $yk_content; // phpcs:ignore WordPress.Security.EscapeOutput -- WP Travel Engine thank-you markup. ?>
			</div>

			<div class="yk-ty-actions">
				<a class="yk-btn yk-btn--primary" href="<?php echo esc_url( $yk_account ); ?>">View my bookings</a>
				<a class="yk-btn yk-btn--wa" href="<?php echo esc_url( $yk_wa ); ?>" target="_blank" rel="noopener">Questions? WhatsApp us</a>
				<a class="yk-btn yk-btn--outline" href="<?php echo esc_url( $yk_guides ); ?>">Read our Munnar guides</a>
			</div>
	<?php else : ?>
		<section class="yk-co-head">
			<div class="yk-wrap">
				<h1 class="yk-co-title">Your booking</h1>
			</div>
		</section>
		<div class="yk-wrap yk-co-body">
			<div class="yk-co-empty">
				<div class="yk-co-empty__icon" aria-hidden="true">
					<svg viewBox="0 0 24 24"><path d="M7 3h10a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Zm1 4v2h8V7H8Zm0 4v2h8v-2H8Zm0 4v2h5v-2H8Z"/></svg>
				</div>
				<h2>Looking for your booking?</h2>
				<p>This page shows your booking details right after checkout. You can find all your trips any time in your account, or just message us with your booking ID.</p>
				<div class="yk-co-empty__actions">
					<a class="yk-btn yk-btn--primary" href="<?php echo esc_url( $yk_account ); ?>">Go to my account</a>
					<a class="yk-btn yk-btn--wa" href="<?php echo esc_url( $yk_wa ); ?>" target="_blank" rel="noopener">WhatsApp us</a>
				</div>
			</div>
	<?php endif; ?>

			<?php get_template_part( 'yk/trust-strip' ); ?>
		</div><!-- .yk-co-body (opened in either branch above) -->
</main>

<?php
get_footer();
