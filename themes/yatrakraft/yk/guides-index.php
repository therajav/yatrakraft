<?php
/**
 * YatraKraft Travel Guides index (blog home, category archives and search results).
 *
 * @package YatraKraft
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wp_query;

$yk_is_cat   = is_category();
$yk_search   = is_search();
$yk_paged    = max( 1, (int) get_query_var( 'paged' ) );
$yk_blog_url = get_option( 'page_for_posts' ) ? get_permalink( get_option( 'page_for_posts' ) ) : home_url( '/' );
$yk_cats     = get_categories(
	array(
		'hide_empty' => true,
		'orderby'    => 'count',
		'order'      => 'DESC',
		'number'     => 8,
		'exclude'    => array( (int) get_option( 'default_category' ) ),
	)
);
$yk_current  = $yk_is_cat ? get_queried_object_id() : 0;

if ( $yk_search ) {
	$yk_query = get_search_query();
	$yk_found = (int) $wp_query->found_posts;
	$yk_title = $yk_query ? sprintf( 'Results for “%s”', $yk_query ) : 'Search our travel guides';
	if ( $yk_found ) {
		$yk_lead = sprintf( 1 === $yk_found ? '%d guide matches your search.' : '%d guides match your search.', $yk_found );
	} else {
		$yk_lead = 'Nothing matched that search. Try a shorter phrase, pick a topic below, or ask us directly.';
	}
} elseif ( $yk_is_cat ) {
	$yk_title = single_cat_title( '', false );
	$yk_desc  = wp_strip_all_tags( category_description() );
	$yk_lead  = $yk_desc ? $yk_desc : 'Practical, local advice to help you plan your trip with confidence.';
} else {
	$yk_title = 'Kerala travel guides, written from Munnar';
	$yk_lead  = 'Honest, up-to-date advice on when to go, how long to stay and what is actually worth your time — from a travel team that grew up in these hills.';
}

$yk_hero = '';
if ( ! empty( $wp_query->posts ) ) {
	$yk_hero = get_the_post_thumbnail_url( $wp_query->posts[0], 'full' );
}

$yk_wa = yatrakraft_whatsapp_url( 'Hi YatraKraft, I read your travel guide and would like help planning a trip.' );
?>

<section class="yk-guides-hero"<?php echo $yk_hero ? ' style="--yk-hero:url(' . esc_url( $yk_hero ) . ')"' : ''; ?>>
	<div class="yk-wrap">
		<nav class="yk-crumbs" aria-label="Breadcrumb">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a>
			<span aria-hidden="true">/</span>
			<?php if ( $yk_is_cat || $yk_search ) : ?>
				<a href="<?php echo esc_url( $yk_blog_url ); ?>">Travel Guides</a>
				<span aria-hidden="true">/</span>
				<span aria-current="page"><?php echo esc_html( $yk_title ); ?></span>
			<?php else : ?>
				<span aria-current="page">Travel Guides</span>
			<?php endif; ?>
		</nav>
		<p class="yk-eyebrow"><?php echo $yk_search ? 'Search' : ( $yk_is_cat ? 'Travel Guides' : 'By locals, for travellers' ); ?></p>
		<h1 class="yk-guides-hero__title"><?php echo esc_html( $yk_title ); ?></h1>
		<p class="yk-guides-hero__lead"><?php echo esc_html( $yk_lead ); ?></p>

		<form class="yk-search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<label class="screen-reader-text" for="yk-guide-search">Search travel guides</label>
			<svg class="yk-search__icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M10.5 3a7.5 7.5 0 0 1 5.96 12.06l4.24 4.24-1.4 1.4-4.24-4.24A7.5 7.5 0 1 1 10.5 3Zm0 2a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11Z"/></svg>
			<input id="yk-guide-search" type="search" name="s" placeholder="Search guides — e.g. Eravikulam, monsoon, 2 days" value="<?php echo esc_attr( get_search_query() ); ?>">
			<input type="hidden" name="post_type" value="post">
			<button type="submit">Search</button>
		</form>
	</div>
</section>

<?php if ( $yk_cats ) : ?>
<div class="yk-chips-bar">
	<div class="yk-wrap">
		<ul class="yk-chips">
			<li><a class="<?php echo ( $yk_is_cat || $yk_search ) ? '' : 'is-active'; ?>" href="<?php echo esc_url( $yk_blog_url ); ?>">All guides</a></li>
			<?php foreach ( $yk_cats as $yk_c ) : ?>
				<li>
					<a class="<?php echo ( $yk_current === (int) $yk_c->term_id ) ? 'is-active' : ''; ?>" href="<?php echo esc_url( get_category_link( $yk_c ) ); ?>">
						<?php echo esc_html( $yk_c->name ); ?> <span><?php echo (int) $yk_c->count; ?></span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
<?php endif; ?>

<main id="primary" class="yk-guides">
	<div class="yk-wrap">
		<?php if ( have_posts() ) : ?>
			<?php
			$yk_i         = 0;
			$yk_grid_open = false;
			while ( have_posts() ) :
				the_post();
				if ( 0 === $yk_i && 1 === $yk_paged && ! $yk_is_cat && ! $yk_search ) {
					yatrakraft_guide_card( true );
				} else {
					if ( ! $yk_grid_open ) {
						echo '<div class="yk-grid">';
						$yk_grid_open = true;
					}
					yatrakraft_guide_card( false );
				}
				$yk_i++;
			endwhile;
			if ( $yk_grid_open ) {
				echo '</div>';
			}

			the_posts_pagination(
				array(
					'class'     => 'yk-pagination',
					'mid_size'  => 1,
					'prev_text' => '← Newer',
					'next_text' => 'Older →',
				)
			);
			?>
		<?php else : ?>
			<div class="yk-empty">
				<?php if ( $yk_search ) : ?>
					<h2>No guides found</h2>
					<p>Try fewer words (for example “Munnar”, “monsoon” or “tea”), browse all guides, or message us and we will answer it for you.</p>
				<?php else : ?>
					<h2>No guides here yet</h2>
					<p>We are writing new guides every week. Meanwhile, ask us anything about your trip.</p>
				<?php endif; ?>
				<div class="yk-empty__actions">
					<?php if ( $yk_search ) : ?>
						<a class="yk-btn yk-btn--primary" href="<?php echo esc_url( $yk_blog_url ); ?>">All guides</a>
					<?php endif; ?>
					<a class="yk-btn yk-btn--wa" href="<?php echo esc_url( $yk_wa ); ?>" target="_blank" rel="noopener">Ask on WhatsApp</a>
				</div>
			</div>
		<?php endif; ?>

		<section class="ykg-cta" aria-labelledby="yk-cta-title">
			<div class="ykg-cta__copy">
				<p class="yk-eyebrow">Plan it with a local</p>
				<h2 id="yk-cta-title">Reading about Munnar is the easy part.</h2>
				<p>Tell us your dates and who is travelling. We will send you a day-wise plan with stays, vehicle and a clear price.</p>
			</div>
			<div class="ykg-cta__actions">
				<a class="yk-btn yk-btn--wa" href="<?php echo esc_url( $yk_wa ); ?>" target="_blank" rel="noopener">
					<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12.04 2a9.9 9.9 0 0 0-8.5 14.98L2 22l5.16-1.5A9.93 9.93 0 1 0 12.04 2Zm0 18.1a8.2 8.2 0 0 1-4.18-1.15l-.3-.18-3.07.9.92-2.99-.2-.31a8.2 8.2 0 1 1 6.83 3.73Zm4.5-6.14c-.25-.12-1.46-.72-1.69-.8-.23-.08-.39-.12-.56.12-.16.25-.64.8-.79.97-.14.16-.29.18-.54.06a6.7 6.7 0 0 1-3.34-2.92c-.25-.43.25-.4.72-1.34.08-.16.04-.3-.02-.43-.06-.12-.56-1.34-.76-1.84-.2-.48-.4-.41-.56-.42h-.48a.92.92 0 0 0-.66.31 2.78 2.78 0 0 0-.87 2.07 4.82 4.82 0 0 0 1.01 2.56 11.04 11.04 0 0 0 4.23 3.74c1.57.68 2.19.74 2.98.62.48-.07 1.46-.6 1.67-1.18.2-.58.2-1.08.14-1.18-.06-.1-.22-.16-.47-.28Z"/></svg>
					Chat on WhatsApp
				</a>
				<a class="yk-btn yk-btn--ghost" href="<?php echo esc_url( home_url( '/trips/' ) ); ?>">Browse trips</a>
			</div>
		</section>
	</div>
</main>
