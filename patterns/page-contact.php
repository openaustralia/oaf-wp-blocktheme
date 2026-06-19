<?php
/**
 * Title: Page - Contact
 * Slug: oaf/page-contact
 * Categories: oaf
 * Block Types: core/post-content
 * Post Types: page
 * Description: Full Contact page - hero, a styled placeholder form and contact channels.
 *
 * NOTE: the form is a visual placeholder only. Replace the form markup with a
 * contact-form plugin shortcode/block to make it send mail.
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
		<!-- wp:html -->
		<form class="oaf-form-card" onsubmit="return false">
			<label class="oaf-field"><span class="oaf-field__label">Your name</span><input class="oaf-input" type="text" name="oaf-name" autocomplete="name"></label>
			<label class="oaf-field"><span class="oaf-field__label">Email</span><input class="oaf-input" type="email" name="oaf-email" autocomplete="email"></label>
			<label class="oaf-field" style="margin-bottom:20px"><span class="oaf-field__label">Message</span><textarea class="oaf-input" name="oaf-message" rows="5"></textarea></label>
			<button class="oaf-submit" type="submit">Send message</button>
			<p class="oaf-fineprint">We don&rsquo;t sell or share your details. To make this form send mail, drop a contact-form plugin shortcode or block into this page and remove the placeholder form above.</p>
		</form>
		<!-- /wp:html -->
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
			<!-- wp:paragraph {"className":"oaf-fineprint"} -->
			<p class="oaf-fineprint">We&rsquo;re not on Facebook, Instagram or X.</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->
