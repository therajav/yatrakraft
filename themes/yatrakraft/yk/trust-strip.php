<?php
/**
 * Trust strip shown under checkout and thank-you content.
 *
 * @package YatraKraft
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$yk_wa = yk_whatsapp_url( yk_is_thankyou() ? 'Hi YatraKraft, I just made a booking on your website and have a question.' : 'Hi YatraKraft, I need help completing my booking.' );
?>
<aside class="yk-co-trust" aria-label="Why book with YatraKraft">
	<div class="yk-co-trust__item">
		<span class="yk-co-trust__ic" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 2a7 7 0 0 1 7 7c0 5.25-7 13-7 13S5 14.25 5 9a7 7 0 0 1 7-7Zm0 4.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5Z"/></svg></span>
		<div><strong>Local team in Munnar</strong><span>Planned by people who live here.</span></div>
	</div>
	<div class="yk-co-trust__item">
		<span class="yk-co-trust__ic" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M7 3h10a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Zm1 4v2h8V7H8Zm0 4v2h8v-2H8Zm0 4v2h5v-2H8Z"/></svg></span>
		<div><strong>Day-wise plan, made for you</strong><span>Private trips, adjusted to your pace.</span></div>
	</div>
	<a class="yk-co-trust__item yk-co-trust__item--link" href="<?php echo esc_url( $yk_wa ); ?>" target="_blank" rel="noopener">
		<span class="yk-co-trust__ic yk-co-trust__ic--wa" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12.04 2a9.9 9.9 0 0 0-8.5 14.98L2 22l5.16-1.5A9.93 9.93 0 1 0 12.04 2Zm4.5 11.96c-.25-.12-1.46-.72-1.69-.8-.23-.08-.39-.12-.56.12-.16.25-.64.8-.79.97-.14.16-.29.18-.54.06a6.7 6.7 0 0 1-3.34-2.92c-.25-.43.25-.4.72-1.34.08-.16.04-.3-.02-.43-.06-.12-.56-1.34-.76-1.84-.2-.48-.4-.41-.56-.42h-.48a.92.92 0 0 0-.66.31 2.78 2.78 0 0 0-.87 2.07 4.82 4.82 0 0 0 1.01 2.56 11.04 11.04 0 0 0 4.23 3.74c1.57.68 2.19.74 2.98.62.48-.07 1.46-.6 1.67-1.18.2-.58.2-1.08.14-1.18-.06-.1-.22-.16-.47-.28Z"/></svg></span>
		<div><strong>Talk to a person</strong><span>Call / WhatsApp +91 8304 933 066</span></div>
	</a>
</aside>
