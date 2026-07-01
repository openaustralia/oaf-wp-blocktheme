<?php
/**
 * Title: Page - Contact
 * Slug: oaf/page-contact
 * Categories: oaf
 * Block Types: core/post-content
 * Post Types: page
 * Description: Full Contact page - hero plus an accordion that routes each kind of enquiry (general, service question, media, government) to the right inbox, and the contact channels.
 *
 * The forms are Jetpack Form blocks (jetpack/contact-form). Each has its own
 * recipient (to) and subject prefix, set as block attributes. Jetpack blocks
 * survive wp_kses on WordPress.com where a raw <form> would be stripped.
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
		<p class="oaf-lead">We&rsquo;re a small charity and we read everything. Whether you want to volunteer, partner, report a problem, or ask a question, here&rsquo;s how to reach us.</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->

<!-- wp:group {"align":"full","className":"oaf-band","backgroundColor":"sand","tagName":"section","layout":{"type":"default"}} -->
<section class="wp-block-group alignfull oaf-band has-sand-background-color has-background">
	<!-- wp:group {"className":"oaf-ct oaf-ct--920 oaf-grid-form","layout":{"type":"default"}} -->
	<div class="wp-block-group oaf-ct oaf-ct--920 oaf-grid-form">
		<!-- wp:group {"className":"oaf-accordion","layout":{"type":"default"}} -->
		<div class="wp-block-group oaf-accordion">
			<!-- wp:paragraph {"className":"oaf-accordion__lead"} -->
			<p class="oaf-accordion__lead">Pick what your message is about so it reaches the right people.</p>
			<!-- /wp:paragraph -->

			<!-- wp:details {"showContent":true} -->
			<details class="wp-block-details" open><summary>General enquiry</summary>
				<!-- wp:jetpack/contact-form {"to":"contact@oaf.org.au","subject":"[OAF Contact]"} -->
				<div class="wp-block-jetpack-contact-form">
					<!-- wp:jetpack/field-name {"required":true} /-->
					<!-- wp:jetpack/field-email {"required":true} /-->
					<!-- wp:jetpack/field-textarea {"label":"Message","required":true} /-->
					<!-- wp:button {"tagName":"button","type":"submit"} -->
					<div class="wp-block-button"><button type="submit" class="wp-block-button__link wp-element-button">Send message</button></div>
					<!-- /wp:button -->
				</div>
				<!-- /wp:jetpack/contact-form -->
			</details>
			<!-- /wp:details -->

			<!-- wp:details -->
			<details class="wp-block-details"><summary>A question about one of our services</summary>
				<!-- wp:paragraph -->
				<p>You&rsquo;ll often find your answer faster on the service&rsquo;s own help pages. If you still need us after checking, email the service directly:</p>
				<!-- /wp:paragraph -->
				<!-- wp:html -->
				<ul class="oaf-services">
					<li>
						<span class="oaf-services__name">Right to Know</span>
						<span class="oaf-services__links"><a href="https://www.righttoknow.org.au/help/about">Help pages</a> <a href="mailto:contact@righttoknow.org.au?subject=Right%20to%20Know%20enquiry">Email</a></span>
					</li>
					<li>
						<span class="oaf-services__name">They Vote for You</span>
						<span class="oaf-services__links"><a href="https://theyvoteforyou.org.au/help/faq">Help pages</a> <a href="mailto:contact@theyvoteforyou.org.au?subject=They%20Vote%20for%20You%20enquiry">Email</a></span>
					</li>
					<li>
						<span class="oaf-services__name">PlanningAlerts</span>
						<span class="oaf-services__links"><a href="https://www.planningalerts.org.au/faq">Help pages</a> <a href="mailto:contact@planningalerts.org.au?subject=PlanningAlerts%20enquiry">Email</a></span>
					</li>
					<li>
						<span class="oaf-services__name">OpenAustralia.org.au</span>
						<span class="oaf-services__links"><a href="https://www.openaustralia.org.au/help/">Help pages</a> <a href="mailto:contact@openaustralia.org.au?subject=OpenAustralia.org.au%20enquiry">Email</a></span>
					</li>
				</ul>
				<!-- /wp:html -->
			</details>
			<!-- /wp:details -->

			<!-- wp:details -->
			<details class="wp-block-details"><summary>Media enquiry</summary>
				<!-- wp:jetpack/contact-form {"to":"media@oaf.org.au","subject":"[Media Contact]"} -->
				<div class="wp-block-jetpack-contact-form">
					<!-- wp:jetpack/field-name {"required":true} /-->
					<!-- wp:jetpack/field-text {"label":"Outlet or publication"} /-->
					<!-- wp:jetpack/field-email {"required":true} /-->
					<!-- wp:jetpack/field-textarea {"label":"Message","required":true} /-->
					<!-- wp:button {"tagName":"button","type":"submit"} -->
					<div class="wp-block-button"><button type="submit" class="wp-block-button__link wp-element-button">Send message</button></div>
					<!-- /wp:button -->
				</div>
				<!-- /wp:jetpack/contact-form -->
			</details>
			<!-- /wp:details -->

			<!-- wp:details -->
			<details class="wp-block-details"><summary>Government or law enforcement</summary>
				<!-- wp:paragraph {"className":"oaf-notice"} -->
				<p class="oaf-notice">This form is only for use by government or law enforcement. For any other enquiry, please use one of the options above.</p>
				<!-- /wp:paragraph -->
				<!-- wp:jetpack/contact-form {"to":"exec@oaf.org.au","subject":"[OAF Contact: Gov/LEO]"} -->
				<div class="wp-block-jetpack-contact-form">
					<!-- wp:jetpack/field-name {"label":"Full name","required":true} /-->
					<!-- wp:jetpack/field-text {"label":"Organisation or agency","required":true} /-->
					<!-- wp:jetpack/field-email {"label":"Contact email","required":true} /-->
					<!-- wp:jetpack/field-telephone {"label":"Contact phone","required":true} /-->
					<!-- wp:jetpack/field-textarea {"label":"Message","required":true} /-->
					<!-- wp:jetpack/field-file -->
					<div></div>
					<!-- /wp:jetpack/field-file -->
					<!-- wp:button {"tagName":"button","type":"submit"} -->
					<div class="wp-block-button"><button type="submit" class="wp-block-button__link wp-element-button">Send message</button></div>
					<!-- /wp:button -->
				</div>
				<!-- /wp:jetpack/contact-form -->
			</details>
			<!-- /wp:details -->

			<!-- wp:paragraph {"className":"oaf-fineprint"} -->
			<p class="oaf-fineprint">We don&rsquo;t sell or share your details, and we read everything you send us.</p>
			<!-- /wp:paragraph -->
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
