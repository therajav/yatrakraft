<?php
/**
 * YatraKraft customisations: Travel Guides index + Checkout redesign.
 *
 * @package YatraKraft
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'YK_WHATSAPP_NUMBER', '918304933066' );

/**
 * WP Travel Engine checkout page ID (from WTE settings).
 */
function yk_checkout_page_id() {
	return yk_wte_page_id( 'wp_travel_engine_place_order' );
}

function yk_is_checkout() {
	$id = yk_checkout_page_id();
	return $id && is_page( $id );
}

function yk_wte_page_id( $key ) {
	$settings = get_option( 'wp_travel_engine_settings' );
	return ( is_array( $settings ) && ! empty( $settings['pages'][ $key ] ) ) ? (int) $settings['pages'][ $key ] : 0;
}

function yk_is_thankyou() {
	$id = yk_wte_page_id( 'wp_travel_engine_thank_you' );
	return $id && is_page( $id );
}

function yk_account_page_id() {
	$id = yk_wte_page_id( 'wp_travel_engine_dashboard_page' );
	return $id ? $id : 167;
}

/**
 * Profile icon in the header (replaces "My Account" text link).
 */
function yk_account_icon() {
	$logged = is_user_logged_in();
	$label  = $logged ? 'My account' : 'Log in to your account';
	printf(
		'<a class="yk-account-link%1$s" href="%2$s" aria-label="%3$s" title="%3$s"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 12a5 5 0 1 0 0-10 5 5 0 0 0 0 10Zm0-2a3 3 0 1 1 0-6 3 3 0 0 1 0 6Zm-8 11a8 8 0 0 1 16 0h-2a6 6 0 0 0-12 0H4Z"/></svg></a>',
		$logged ? ' is-logged-in' : '',
		esc_url( get_permalink( yk_account_page_id() ) ),
		esc_attr( $label )
	);
}

/**
 * Remove the "My Account" text item from nav menus (icon replaces it).
 */
function yk_hide_account_menu_item( $items ) {
	$account_id = yk_account_page_id();
	foreach ( $items as $k => $item ) {
		if ( 'page' === $item->object && (int) $item->object_id === $account_id ) {
			unset( $items[ $k ] );
		}
	}
	return $items;
}
add_filter( 'wp_nav_menu_objects', 'yk_hide_account_menu_item' );

/**
 * Enqueue a theme stylesheet, versioned by file modification time so edits bust caches.
 */
function yk_enqueue_css( $handle, $rel ) {
	$path = get_stylesheet_directory() . $rel;
	wp_enqueue_style(
		$handle,
		get_stylesheet_directory_uri() . $rel,
		array( 'yatrakraft-style' ),
		file_exists( $path ) ? (string) filemtime( $path ) : YATRAKRAFT_THEME_VERSION
	);
}

/**
 * Global (header) styles.
 */
function yk_enqueue_global() {
	yk_enqueue_css( 'yk-global', '/assets/css/yk-global.css' );
}
add_action( 'wp_enqueue_scripts', 'yk_enqueue_global', 30 );

function yk_is_guides() {
	return is_home() || is_category();
}

function yk_whatsapp_url( $text = '' ) {
	$url = 'https://wa.me/' . YK_WHATSAPP_NUMBER;
	if ( $text ) {
		$url .= '?text=' . rawurlencode( $text );
	}
	return $url;
}

function yk_reading_time( $post_id = null ) {
	$words = str_word_count( wp_strip_all_tags( (string) get_post_field( 'post_content', $post_id ) ) );
	return max( 1, (int) ceil( $words / 200 ) );
}

/**
 * Styles — only on the redesigned pages.
 */
function yk_enqueue_redesign() {
	if ( ! yk_is_guides() && ! yk_is_checkout() && ! yk_is_thankyou() ) {
		return;
	}
	yk_enqueue_css( 'yk-redesign', '/assets/css/yk-redesign.css' );
}
add_action( 'wp_enqueue_scripts', 'yk_enqueue_redesign', 30 );

/**
 * Route the WTE checkout / thank-you pages to our templates.
 */
function yk_checkout_template( $template ) {
	if ( yk_is_checkout() ) {
		$custom = locate_template( 'yk-checkout.php' );
		if ( $custom ) {
			return $custom;
		}
	}
	if ( yk_is_thankyou() ) {
		$custom = locate_template( 'yk-thankyou.php' );
		if ( $custom ) {
			return $custom;
		}
	}
	return $template;
}
add_filter( 'template_include', 'yk_checkout_template', 99 );

function yk_body_class( $classes ) {
	if ( yk_is_guides() ) {
		$classes[] = 'yk-guides-page';
	}
	if ( yk_is_checkout() ) {
		$classes[] = 'yk-checkout-page';
	}
	if ( yk_is_thankyou() ) {
		$classes[] = 'yk-checkout-page';
		$classes[] = 'yk-thankyou-page';
	}
	return $classes;
}
add_filter( 'body_class', 'yk_body_class' );

/**
 * Guide card markup.
 *
 * @param bool $featured Large featured layout.
 */
function yk_guide_card( $featured = false ) {
	$cats     = get_the_category();
	$cat      = $cats ? $cats[0] : null;
	$minutes  = yk_reading_time( get_the_ID() );
	$classes  = $featured ? 'ykg-card ykg-card--featured' : 'ykg-card';
	$img_size = $featured ? 'large' : 'medium_large';
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( $classes ); ?>>
		<a class="ykg-card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
			<?php
			if ( has_post_thumbnail() ) {
				the_post_thumbnail( $img_size, array( 'loading' => $featured ? 'eager' : 'lazy' ) );
			} else {
				echo '<span class="ykg-card__placeholder"></span>';
			}
			?>
			<?php if ( $featured ) : ?>
				<span class="ykg-card__badge"><?php esc_html_e( 'Latest guide', 'yatrakraft' ); ?></span>
			<?php endif; ?>
		</a>
		<div class="ykg-card__body">
			<?php if ( $cat ) : ?>
				<a class="ykg-card__cat" href="<?php echo esc_url( get_category_link( $cat ) ); ?>"><?php echo esc_html( $cat->name ); ?></a>
			<?php endif; ?>
			<h2 class="ykg-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
			<p class="ykg-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), $featured ? 34 : 22 ) ); ?></p>
			<div class="ykg-card__meta">
				<span><?php echo esc_html( get_the_date( 'j M Y' ) ); ?></span>
				<span aria-hidden="true">·</span>
				<span><?php echo esc_html( sprintf( '%d min read', $minutes ) ); ?></span>
				<a class="ykg-card__more" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read guide', 'yatrakraft' ); ?> <span aria-hidden="true">→</span></a>
			</div>
		</div>
	</article>
	<?php
}
