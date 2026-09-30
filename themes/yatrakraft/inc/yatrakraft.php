<?php
/**
 * YatraKraft customisations: Travel Guides index + Checkout redesign.
 *
 * @package YatraKraft
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'YATRAKRAFT_WHATSAPP_NUMBER', '918304933066' );

/**
 * WP Travel Engine checkout page ID (from WTE settings).
 */
function yatrakraft_checkout_page_id() {
	return yatrakraft_wte_page_id( 'wp_travel_engine_place_order' );
}

function yatrakraft_is_checkout() {
	$id = yatrakraft_checkout_page_id();
	return $id && is_page( $id );
}

function yatrakraft_wte_page_id( $key ) {
	$settings = get_option( 'wp_travel_engine_settings' );
	return ( is_array( $settings ) && ! empty( $settings['pages'][ $key ] ) ) ? (int) $settings['pages'][ $key ] : 0;
}

function yatrakraft_is_thankyou() {
	$id = yatrakraft_wte_page_id( 'wp_travel_engine_thank_you' );
	return $id && is_page( $id );
}

function yatrakraft_account_page_id() {
	$id = yatrakraft_wte_page_id( 'wp_travel_engine_dashboard_page' );
	return $id ? $id : 167;
}

/**
 * Profile photo URL for a user, or '' when they have none of their own.
 *
 * Uses the photo saved in the media library first (wp_user_avatar: the attachment
 * that social login / avatar plugins store), then any avatar plugin hooked into
 * get_avatar_url(). Gravatar is left to the caller so it can fall back to initials.
 *
 * @param WP_User $user User.
 * @param int     $size Pixel size.
 */
function yatrakraft_user_photo_url( $user, $size ) {
	$attachment_id = (int) get_user_meta( $user->ID, 'wp_user_avatar', true );
	$url           = $attachment_id ? wp_get_attachment_image_url( $attachment_id, array( $size, $size ) ) : '';
	return (string) apply_filters( 'yatrakraft_user_photo_url', $url ? $url : '', $user, $size );
}

/**
 * Up to two initials from the user's name, e.g. "Raja V" -> "RV".
 */
function yatrakraft_user_initials( $user ) {
	$name  = trim( $user->first_name . ' ' . $user->last_name );
	$name  = $name ? $name : $user->display_name;
	$parts = preg_split( '/\s+/u', trim( $name ) );
	$first = $parts ? mb_substr( $parts[0], 0, 1 ) : '';
	$last  = count( $parts ) > 1 ? mb_substr( end( $parts ), 0, 1 ) : '';
	return mb_strtoupper( $first . $last );
}

/**
 * Profile icon in the header (replaces "My Account" text link).
 * Logged in: the user's photo, else their Gravatar over their initials.
 */
function yatrakraft_account_icon() {
	$href = esc_url( get_permalink( yatrakraft_account_page_id() ) );
	$icon = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 12a5 5 0 1 0 0-10 5 5 0 0 0 0 10Zm0-2a3 3 0 1 1 0-6 3 3 0 0 1 0 6Zm-8 11a8 8 0 0 1 16 0h-2a6 6 0 0 0-12 0H4Z"/></svg>';

	if ( ! is_user_logged_in() ) {
		printf( '<a class="yk-account-link" href="%1$s" aria-label="Log in to your account" title="Log in to your account">%2$s</a>', $href, $icon ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above / static SVG.
		return;
	}

	$user     = wp_get_current_user();
	$label    = sprintf( 'My account (%s)', $user->display_name );
	$initials = yatrakraft_user_initials( $user );
	$photo    = yatrakraft_user_photo_url( $user, 96 );
	if ( ! $photo && get_option( 'show_avatars' ) ) {
		// "blank" = transparent image when there is no Gravatar, so the initials show through.
		$photo = get_avatar_url( $user->ID, array( 'size' => 96, 'default' => 'blank' ) );
	}

	printf(
		'<a class="yk-account-link is-logged-in" href="%1$s" aria-label="%2$s" title="%2$s">%3$s%4$s</a>',
		$href,
		esc_attr( $label ),
		$initials ? '<span class="yk-account-link__initials" aria-hidden="true">' . esc_html( $initials ) . '</span>' : $icon, // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG.
		$photo ? '<img class="yk-account-link__photo" src="' . esc_url( $photo ) . '" alt="" width="44" height="44" decoding="async">' : ''
	);
}

/**
 * Remove the "My Account" text item from nav menus (icon replaces it).
 */
function yatrakraft_hide_account_menu_item( $items ) {
	$account_id = yatrakraft_account_page_id();
	foreach ( $items as $k => $item ) {
		if ( 'page' === $item->object && (int) $item->object_id === $account_id ) {
			unset( $items[ $k ] );
		}
	}
	return $items;
}
add_filter( 'wp_nav_menu_objects', 'yatrakraft_hide_account_menu_item' );

/**
 * Enqueue a theme stylesheet, versioned by file modification time so edits bust caches.
 */
function yatrakraft_enqueue_css( $handle, $rel ) {
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
function yatrakraft_enqueue_global() {
	yatrakraft_enqueue_css( 'yk-global', '/assets/css/yk-global.css' );
}
add_action( 'wp_enqueue_scripts', 'yatrakraft_enqueue_global', 30 );

function yatrakraft_is_guides() {
	return is_home() || is_category() || is_search();
}

function yatrakraft_whatsapp_url( $text = '' ) {
	$url = 'https://wa.me/' . YATRAKRAFT_WHATSAPP_NUMBER;
	if ( $text ) {
		$url .= '?text=' . rawurlencode( $text );
	}
	return $url;
}

function yatrakraft_reading_time( $post_id = null ) {
	$words = str_word_count( wp_strip_all_tags( (string) get_post_field( 'post_content', $post_id ) ) );
	return max( 1, (int) ceil( $words / 200 ) );
}

/**
 * Styles — only on the redesigned pages.
 */
function yatrakraft_enqueue_redesign() {
	if ( ! yatrakraft_is_guides() && ! yatrakraft_is_checkout() && ! yatrakraft_is_thankyou() ) {
		return;
	}
	yatrakraft_enqueue_css( 'yk-redesign', '/assets/css/yk-redesign.css' );
}
add_action( 'wp_enqueue_scripts', 'yatrakraft_enqueue_redesign', 30 );

/**
 * Route the WTE checkout / thank-you pages to our templates.
 */
function yatrakraft_checkout_template( $template ) {
	if ( yatrakraft_is_checkout() ) {
		$custom = locate_template( 'yk-checkout.php' );
		if ( $custom ) {
			return $custom;
		}
	}
	if ( yatrakraft_is_thankyou() ) {
		$custom = locate_template( 'yk-thankyou.php' );
		if ( $custom ) {
			return $custom;
		}
	}
	return $template;
}
add_filter( 'template_include', 'yatrakraft_checkout_template', 99 );

function yatrakraft_body_class( $classes ) {
	if ( yatrakraft_is_guides() ) {
		$classes[] = 'yk-guides-page';
	}
	if ( yatrakraft_is_checkout() ) {
		$classes[] = 'yk-checkout-page';
	}
	if ( yatrakraft_is_thankyou() ) {
		$classes[] = 'yk-checkout-page';
		$classes[] = 'yk-thankyou-page';
	}
	return $classes;
}
add_filter( 'body_class', 'yatrakraft_body_class' );

/**
 * Guide card markup.
 *
 * @param bool $featured Large featured layout.
 */
function yatrakraft_guide_card( $featured = false ) {
	$is_post  = 'post' === get_post_type();
	$cats     = $is_post ? get_the_category() : array();
	$cat      = $cats ? $cats[0] : null;
	$type     = $is_post ? null : get_post_type_object( get_post_type() );
	$minutes  = yatrakraft_reading_time( get_the_ID() );
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
			<?php elseif ( $type ) : ?>
				<span class="ykg-card__cat"><?php echo esc_html( $type->labels->singular_name ); ?></span>
			<?php endif; ?>
			<h2 class="ykg-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
			<p class="ykg-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), $featured ? 34 : 22 ) ); ?></p>
			<div class="ykg-card__meta">
				<?php if ( $is_post ) : ?>
					<span><?php echo esc_html( get_the_date( 'j M Y' ) ); ?></span>
					<span aria-hidden="true">·</span>
					<span><?php echo esc_html( sprintf( '%d min read', $minutes ) ); ?></span>
				<?php endif; ?>
				<a class="ykg-card__more" href="<?php the_permalink(); ?>"><?php $is_post ? esc_html_e( 'Read guide', 'yatrakraft' ) : esc_html_e( 'View', 'yatrakraft' ); ?> <span aria-hidden="true">→</span></a>
			</div>
		</div>
	</article>
	<?php
}
