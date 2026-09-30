/* YatraKraft: show the back-to-top button after scrolling, and scroll up on click. */
( function () {
	var btn = document.querySelector( '.yk-float__top' );
	if ( ! btn ) {
		return;
	}
	var shown = false;
	var ticking = false;

	function update() {
		ticking = false;
		var show = window.scrollY > Math.max( 600, window.innerHeight );
		if ( show !== shown ) {
			shown = show;
			btn.classList.toggle( 'is-visible', show );
		}
	}

	window.addEventListener( 'scroll', function () {
		if ( ! ticking ) {
			ticking = true;
			window.requestAnimationFrame( update );
		}
	}, { passive: true } );

	btn.addEventListener( 'click', function () {
		var reduce = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
		window.scrollTo( { top: 0, behavior: reduce ? 'auto' : 'smooth' } );
		var target = document.getElementById( 'masthead' ) || document.body;
		if ( target.focus ) {
			target.setAttribute( 'tabindex', '-1' );
			target.focus( { preventScroll: true } );
		}
	} );

	update();
} )();
