<?php
/**
 * Title: Page - Licence
 * Slug: oaf/page-licence
 * Categories: oaf
 * Block Types: core/post-content
 * Post Types: page
 * Description: Full "Licence" page - the Creative Commons licence covering site content, how to attribute it, what the licence does not cover, and the separate GPL licence on the theme's code.
 *
 * NOTE: this page is the target of the footer's "Some exceptions apply" link.
 * Its slug (`licence`) is looked up by patterns/footer.php, so renaming the
 * page's URL will silently drop that link back to plain text.
 *
 * @package oaf-wp-blocktheme
 */

?>
<!-- wp:group {"align":"full","className":"oaf-pagehero","backgroundColor":"maroon","tagName":"section","layout":{"type":"default"}} -->
<section class="wp-block-group alignfull oaf-pagehero has-maroon-background-color has-background">
	<!-- wp:group {"className":"oaf-ct","layout":{"type":"default"}} -->
	<div class="wp-block-group oaf-ct">
		<!-- wp:paragraph {"className":"oaf-eyebrow"} -->
		<p class="oaf-eyebrow">Reuse</p>
		<!-- /wp:paragraph -->
		<!-- wp:heading {"level":1} -->
		<h1 class="wp-block-heading">Licence.</h1>
		<!-- /wp:heading -->
		<!-- wp:paragraph {"className":"oaf-lead"} -->
		<p class="oaf-lead">We want what we publish to be useful to other people, so nearly everything on this site can be reused. This page sets out the licence that applies, how to credit us, and the few things the licence cannot cover.</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->

<!-- wp:group {"align":"full","className":"oaf-band","backgroundColor":"white","tagName":"section","layout":{"type":"default"}} -->
<section class="wp-block-group alignfull oaf-band has-white-background-color has-background">
	<!-- wp:group {"className":"oaf-ct oaf-ct--narrow","layout":{"type":"default"}} -->
	<div class="wp-block-group oaf-ct oaf-ct--narrow">
		<!-- wp:group {"className":"oaf-prose","layout":{"type":"default"}} -->
		<div class="wp-block-group oaf-prose">
			<!-- wp:paragraph {"className":"oaf-eyebrow oaf-eyebrow--dark"} -->
			<p class="oaf-eyebrow oaf-eyebrow--dark">What you can reuse</p>
			<!-- /wp:paragraph -->
			<!-- wp:heading -->
			<h2 class="wp-block-heading">Our content is Creative Commons licensed.</h2>
			<!-- /wp:heading -->
			<!-- wp:paragraph -->
			<p>Unless otherwise stated, the content on this site is available under the <a rel="license" href="https://creativecommons.org/licenses/by-nc-sa/4.0/">Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International</a> licence. That means you are free to copy it, republish it, translate it and build on it, provided you credit us, do not use it commercially, and release anything you build on it under the same licence. The <a href="https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode.en">full legal text</a> is the authoritative version.</p>
			<!-- /wp:paragraph -->
			<!-- wp:paragraph -->
			<p>This page covers this website. It does not set the terms for material published by our Collections, which carry information from other sources under their own arrangements.</p>
			<!-- /wp:paragraph -->

			<!-- wp:paragraph {"className":"oaf-eyebrow oaf-eyebrow--dark"} -->
			<p class="oaf-eyebrow oaf-eyebrow--dark">How to credit us</p>
			<!-- /wp:paragraph -->
			<!-- wp:heading -->
			<h2 class="wp-block-heading">Name us, link back, and name the licence.</h2>
			<!-- /wp:heading -->
			<!-- wp:paragraph -->
			<p>The licence asks for reasonable credit. In practice that means naming the OpenAustralia Foundation as the source, linking back to the page you took the material from, and saying which licence applies. If you changed anything, say so. Something like this is enough:</p>
			<!-- /wp:paragraph -->
			<!-- wp:quote -->
			<blockquote class="wp-block-quote"><!-- wp:paragraph -->
			<p>"Why we built Planning Alerts" by the OpenAustralia Foundation, used under CC BY-NC-SA 4.0. Edited for length.</p>
			<!-- /wp:paragraph --></blockquote>
			<!-- /wp:quote -->

			<!-- wp:paragraph {"className":"oaf-eyebrow oaf-eyebrow--dark"} -->
			<p class="oaf-eyebrow oaf-eyebrow--dark">The exceptions</p>
			<!-- /wp:paragraph -->
			<!-- wp:heading -->
			<h2 class="wp-block-heading">Some things are not ours to license.</h2>
			<!-- /wp:heading -->
			<!-- wp:paragraph -->
			<p>A few things on this site sit outside the Creative Commons licence, either because they are trademarks we need to keep control of, or because they belong to someone else.</p>
			<!-- /wp:paragraph -->
			<!-- wp:list -->
			<ul class="wp-block-list"><!-- wp:list-item -->
			<li><strong>Our name and logos.</strong> The OpenAustralia Foundation name and wordmark, and the names and logos of our Collections, are our trademarks. You may not use them in a way that suggests we produce, endorse or are affiliated with your work.</li>
			<!-- /wp:list-item -->
			<!-- wp:list-item -->
			<li><strong>The ACNC Registered Charity Tick.</strong> Copyright in the Tick belongs to the Commonwealth of Australia, and we use it under a licence we cannot pass on. Registered charities can obtain their own from the ACNC and must follow the ACNC's <a href="https://www.acnc.gov.au/charity/registered-charity-tick/terms-and-conditions">terms and conditions</a>.</li>
			<!-- /wp:list-item -->
			<!-- wp:list-item -->
			<li><strong>Photographs of people.</strong> Portraits of our team, board and contributors are published here with their permission and are not covered by the licence.</li>
			<!-- /wp:list-item -->
			<!-- wp:list-item -->
			<li><strong>The typefaces.</strong> Fira Sans and Merriweather are licensed separately under the <a href="https://openfontlicense.org/">SIL Open Font License 1.1</a>.</li>
			<!-- /wp:list-item -->
			<!-- wp:list-item -->
			<li><strong>Anything individually marked.</strong> Where a page, image or dataset carries its own licence or credit, that one applies instead.</li>
			<!-- /wp:list-item --></ul>
			<!-- /wp:list -->

			<!-- wp:paragraph {"className":"oaf-eyebrow oaf-eyebrow--dark"} -->
			<p class="oaf-eyebrow oaf-eyebrow--dark">The code</p>
			<!-- /wp:paragraph -->
			<!-- wp:heading -->
			<h2 class="wp-block-heading">The software is open source, under a different licence.</h2>
			<!-- /wp:heading -->
			<!-- wp:paragraph -->
			<p>The Creative Commons licence above covers content, not code. The theme that runs this site is published on <a href="https://github.com/openaustralia/oaf-wp-blocktheme">GitHub</a> under the GNU General Public License v2 or later, along with the rest of our work. The trademark and Tick exceptions above apply to the code too, so a fork needs to replace our name and logos and remove the Tick.</p>
			<!-- /wp:paragraph -->

			<!-- wp:paragraph -->
			<p>If you are unsure whether something is covered, or you want to do something the licence does not allow, <a href="/contact/">get in touch</a> and we will do our best to say yes.</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->
