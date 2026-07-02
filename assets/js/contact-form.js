/**
 * Contact page: react to the step-one "What is your enquiry about?" choice.
 *
 * Shows a "contact the service team directly" link for the four services, and a
 * "we are not the government" disclaimer for every enquiry except media and
 * government / law enforcement. The option strings below must match the
 * field-select options in patterns/page-contact.php and oaf_contact_routes()
 * in inc/contact-form.php.
 */
( function () {
	var SERVICES = {
		'Right to Know': 'https://www.righttoknow.org.au/help/contact',
		'They Vote for You': 'mailto:contact@theyvoteforyou.org.au',
		PlanningAlerts: 'https://www.planningalerts.org.au/help/contact',
		'OpenAustralia.org.au': 'https://www.openaustralia.org.au/contact/'
	};
	var NO_DISCLAIMER = [ 'Media enquiry', 'Government or law enforcement' ];

	function selectedText( select ) {
		if ( ! select.value ) {
			return '';
		}
		var option = select.options[ select.selectedIndex ];
		return option ? option.textContent.trim() : select.value.trim();
	}

	function setup( form ) {
		var select = form.querySelector( 'select' );
		if ( ! select ) {
			return;
		}
		var direct = form.querySelector( '.oaf-service-direct' );
		var directLink = direct ? direct.querySelector( 'a' ) : null;
		var disclaimer = form.querySelector( '.oaf-gov-disclaimer' );

		function update() {
			var choice = selectedText( select );

			if ( direct && directLink ) {
				if ( SERVICES[ choice ] ) {
					directLink.href = SERVICES[ choice ];
					directLink.textContent = 'Contact the ' + choice + ' team directly';
					direct.classList.remove( 'oaf-hidden' );
				} else {
					direct.classList.add( 'oaf-hidden' );
				}
			}

			if ( disclaimer ) {
				var show = '' !== choice && -1 === NO_DISCLAIMER.indexOf( choice );
				disclaimer.classList.toggle( 'oaf-hidden', ! show );
			}
		}

		select.addEventListener( 'change', update );
		update();
	}

	function boot() {
		var forms = document.querySelectorAll( '.wp-block-jetpack-contact-form' );
		for ( var i = 0; i < forms.length; i++ ) {
			setup( forms[ i ] );
		}
	}

	if ( 'loading' !== document.readyState ) {
		boot();
	} else {
		document.addEventListener( 'DOMContentLoaded', boot );
	}
}() );
