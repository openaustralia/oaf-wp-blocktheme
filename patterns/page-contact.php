<?php
/**
 * Title: Page - Contact
 * Slug: oaf/page-contact
 * Categories: oaf
 * Block Types: core/post-content
 * Post Types: page
 * Description: Full Contact page - hero plus a Jetpack multi-step form that routes each kind of enquiry (general, service, media, government) to the right inbox, and the contact channels.
 *
 * The form is a single Jetpack multi-step Form block. Step one asks what the
 * enquiry is about; inc/contact-form.php reads that choice and routes the
 * submission to the right inbox with a subject prefix (contact_form_to /
 * contact_form_subject filters). Keep the field-select options below in sync
 * with oaf_contact_routes() in inc/contact-form.php.
 *
 * @package oaf-wp-blocktheme
 */

?>
<!-- wp:group {"align":"full","className":"oaf-pagehero","backgroundColor":"maroon","tagName":"section","layout":{"type":"default"}} -->
<section class="wp-block-group alignfull oaf-pagehero has-maroon-background-color has-background">
	<!-- wp:group {"className":"oaf-ct","layout":{"type":"default"}} -->
	<div class="wp-block-group oaf-ct">
		<!-- wp:paragraph {"className":"oaf-eyebrow"} -->
		<p class="oaf-eyebrow">Contact</p>
		<!-- /wp:paragraph -->
		<!-- wp:heading {"level":1} -->
		<h1 class="wp-block-heading">Get in touch.</h1>
		<!-- /wp:heading -->
		<!-- wp:paragraph {"className":"oaf-lead"} -->
		<p class="oaf-lead">We&rsquo;re a small team, and we read everything. Tell us what your message is about and we&rsquo;ll make sure it reaches the right person.</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->

<!-- wp:group {"align":"full","className":"oaf-band","backgroundColor":"sand","tagName":"section","layout":{"type":"default"}} -->
<section class="wp-block-group alignfull oaf-band has-sand-background-color has-background">
	<!-- wp:group {"className":"oaf-ct oaf-ct--920 oaf-grid-form","layout":{"type":"default"}} -->
	<div class="wp-block-group oaf-ct oaf-ct--920 oaf-grid-form">
		<!-- wp:group {"className":"oaf-formwrap","layout":{"type":"default"}} -->
		<div class="wp-block-group oaf-formwrap">
			<!-- wp:jetpack/contact-form {"to":"contact@oaf.org.au","subject":"[OAF Contact]"} -->
			<div class="wp-block-jetpack-contact-form">
				<!-- wp:jetpack/form-progress-indicator /-->

				<!-- wp:jetpack/form-step-container -->
				<div class="jetpack-form-steps-wrapper"><div class="wp-block-jetpack-form-step-container jetpack-form-step-container"><!-- wp:jetpack/form-step {"stepLabel":"Your enquiry"} -->
				<div class="wp-block-jetpack-form-step">
					<!-- wp:paragraph {"className":"oaf-form-hint"} -->
					<p class="oaf-form-hint">Each of our services has its own help pages and contact address, and that&rsquo;s usually the fastest way to get an answer. If your question is about one of them, please try there first: <a href="https://www.righttoknow.org.au/help/about">Right to Know</a>, <a href="https://theyvoteforyou.org.au/help/faq">They Vote for You</a>, <a href="https://www.planningalerts.org.au/faq">PlanningAlerts</a>, <a href="https://www.openaustralia.org.au/help/">OpenAustralia.org.au</a>.</p>
					<!-- /wp:paragraph -->
					<!-- wp:jetpack/field-select {"required":true,"options":["General enquiry","Right to Know","They Vote for You","PlanningAlerts","OpenAustralia.org.au","Media enquiry","Government or law enforcement"]} -->
					<div><!-- wp:jetpack/label {"label":"What is your enquiry about?"} /-->
					<!-- wp:jetpack/input {"placeholder":"Select one option","type":"dropdown"} /--></div>
					<!-- /wp:jetpack/field-select -->
					<!-- wp:html -->
					<div class="oaf-form-alert oaf-service-direct oaf-hidden" role="note"><p><a class="oaf-service-direct__link" href="#"></a></p></div>
					<div class="oaf-form-alert oaf-gov-disclaimer oaf-hidden" role="note"><p>I understand that OpenAustralia Foundation is <strong>not</strong> run by the government, and the OpenAustralia Foundation team <strong>cannot</strong> help me with personal matters relating to government services.</p></div>
					<!-- /wp:html -->
				</div>
				<!-- /wp:jetpack/form-step -->

				<!-- wp:jetpack/form-step {"stepLabel":"Your details"} -->
				<div class="wp-block-jetpack-form-step">
					<!-- wp:jetpack/field-name {"label":"Full name","required":true} /-->
					<!-- wp:jetpack/field-email {"label":"Email","required":true} /-->
					<!-- wp:jetpack/field-text {"label":"Organisation or agency (if government or law enforcement)"} /-->
					<!-- wp:jetpack/field-telephone {"label":"Phone (optional)"} /-->
				</div>
				<!-- /wp:jetpack/form-step -->

				<!-- wp:jetpack/form-step {"stepLabel":"Your message"} -->
				<div class="wp-block-jetpack-form-step">
					<!-- wp:jetpack/field-textarea {"label":"Message","required":true} /-->
					<!-- wp:jetpack/field-file -->
					<div><!-- wp:jetpack/label {"label":"Upload a file","lock":{"move":true,"remove":true}} /-->

					<!-- wp:jetpack/dropzone {"lock":{"move":true,"remove":true},"layout":{"type":"flex","justifyContent":"center","orientation":"vertical"}} -->
					<div class="wp-block-jetpack-dropzone"><!-- wp:paragraph -->
					<p>Drag and drop or click to select a file.</p>
					<!-- /wp:paragraph --></div>
					<!-- /wp:jetpack/dropzone --></div>
					<!-- /wp:jetpack/field-file -->
					<!-- wp:paragraph {"className":"oaf-fineprint"} -->
					<p class="oaf-fineprint">We don&rsquo;t sell or share your details.</p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:jetpack/form-step --></div></div>
				<!-- /wp:jetpack/form-step-container -->

				<!-- wp:jetpack/form-step-navigation {"layout":{"type":"flex","justifyContent":"right"}} -->
				<div class="wp-block-jetpack-form-step-navigation__wrapper"><div class="wp-block-jetpack-form-step-navigation"><!-- wp:button {"tagName":"button","metadata":{"name":"Previous button"},"className":"is-style-outline form-button-previous is-previous"} -->
				<div class="wp-block-button is-style-outline form-button-previous is-previous"><button type="button" class="wp-block-button__link wp-element-button">&larr; Back</button></div>
				<!-- /wp:button -->

				<!-- wp:button {"tagName":"button","metadata":{"name":"Next button"},"className":"form-button-next is-next"} -->
				<div class="wp-block-button form-button-next is-next"><button type="button" class="wp-block-button__link wp-element-button">Next &rarr;</button></div>
				<!-- /wp:button -->

				<!-- wp:button {"tagName":"button","type":"submit","metadata":{"name":"Submit button"},"className":"form-button-submit is-submit"} -->
				<div class="wp-block-button form-button-submit is-submit"><button type="submit" class="wp-block-button__link wp-element-button">Send message</button></div>
				<!-- /wp:button --></div></div>
				<!-- /wp:jetpack/form-step-navigation -->
			</div>
			<!-- /wp:jetpack/contact-form -->
		</div>
		<!-- /wp:group -->
		<!-- wp:group {"layout":{"type":"default"}} -->
		<div class="wp-block-group">
			<!-- wp:heading {"level":2,"fontSize":"x-large"} -->
			<h2 class="wp-block-heading has-x-large-font-size">Other ways to reach us</h2>
			<!-- /wp:heading -->
			<!-- wp:html -->
			<ul class="oaf-channels">
				<li><span class="oaf-channels__name">Bluesky</span><span class="oaf-channels__handle">@oaf.org.au</span></li>
				<li><span class="oaf-channels__name">Mastodon</span><span class="oaf-channels__handle">@oaf@social.oaf.org.au</span></li>
				<li><span class="oaf-channels__name">LinkedIn</span><span class="oaf-channels__handle">OpenAustralia Foundation</span></li>
				<li><span class="oaf-channels__name">GitHub</span><span class="oaf-channels__handle">github.com/openaustralia</span></li>
			</ul>
			<!-- /wp:html -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->
