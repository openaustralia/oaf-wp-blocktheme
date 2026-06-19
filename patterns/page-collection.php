<?php
/**
 * Title: Page - Collection
 * Slug: oaf/page-collection
 * Categories: oaf
 * Block Types: core/post-content
 * Post Types: page
 * Description: Full Collection page - hero and the four services as editorial rows.
 *
 * @package oaf-wp-blocktheme
 */

$oaf_svc = get_template_directory_uri() . '/assets/img/services';
$oaf_pa  = esc_url( oaf_option( 'planningalerts_url' ) );
$oaf_rtk = esc_url( oaf_option( 'righttoknow_url' ) );
$oaf_tvfy = esc_url( oaf_option( 'theyvoteforyou_url' ) );
$oaf_oa  = esc_url( oaf_option( 'openaustralia_url' ) );
?>
<!-- wp:group {"align":"full","className":"oaf-pagehero","backgroundColor":"maroon","tagName":"section","layout":{"type":"default"}} -->
<section class="wp-block-group alignfull oaf-pagehero has-maroon-background-color has-background">
	<!-- wp:group {"className":"oaf-ct","layout":{"type":"default"}} -->
	<div class="wp-block-group oaf-ct">
		<!-- wp:paragraph {"className":"oaf-eyebrow"} -->
		<p class="oaf-eyebrow">The collection</p>
		<!-- /wp:paragraph -->
		<!-- wp:heading {"level":1} -->
		<h1 class="wp-block-heading">Four collections, open to everyone.</h1>
		<!-- /wp:heading -->
		<!-- wp:paragraph {"className":"oaf-lead"} -->
		<p class="oaf-lead">Like any library, we lend freely. Each collection is a free public service - built, maintained and kept online by a small charity and a long list of volunteer contributors.</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
</section>
<!-- /wp:group -->

<!-- wp:group {"align":"full","className":"oaf-band","backgroundColor":"white","tagName":"section","layout":{"type":"default"}} -->
<section class="wp-block-group alignfull oaf-band has-white-background-color has-background">
	<!-- wp:html -->
	<div class="oaf-ct oaf-coll-full">
		<div class="oaf-service-card"><div class="oaf-coll-row"><span class="oaf-coll-band" style="background:#060f2f"><img src="<?php echo esc_url( $oaf_svc . '/pa-logo-white.svg' ); ?>" alt="Planning Alerts"></span><div class="oaf-coll-full__body"><h3><span class="oaf-coll-full__dot" style="background:#ca3f94"></span>Planning Alerts</h3><p class="oaf-coll-full__long">Planning Alerts emails you details of planning applications near you, as soon as they&rsquo;re published. You&rsquo;d probably want to know if your neighbour was going to knock their house down - now you can.</p></div><div class="oaf-coll-full__meta"><span class="oaf-coll-row__stat" style="color:#b32d80">1.4 million alerts sent last year</span><a class="oaf-coll-full__visit" href="<?php echo $oaf_pa; ?>">Visit &rarr;</a></div></div></div>
		<div class="oaf-service-card"><div class="oaf-coll-row"><span class="oaf-coll-band" style="background:#00dcf0"><img src="<?php echo esc_url( $oaf_svc . '/rtk-logo.svg' ); ?>" alt="Right to Know"></span><div class="oaf-coll-full__body"><h3><span class="oaf-coll-full__dot" style="background:#00b0bf"></span>Right to Know</h3><p class="oaf-coll-full__long">Right to Know helps you make Freedom of Information requests and publishes the answers for everyone to read. The government is funded by public money - you have a right to know how it&rsquo;s spent.</p></div><div class="oaf-coll-full__meta"><span class="oaf-coll-row__stat" style="color:#007a85">12,346 FOI requests archived</span><a class="oaf-coll-full__visit" href="<?php echo $oaf_rtk; ?>">Visit &rarr;</a></div></div></div>
		<div class="oaf-service-card"><div class="oaf-coll-row"><span class="oaf-coll-band" style="background:#ffea8a;gap:13px"><img src="<?php echo esc_url( $oaf_svc . '/tvfy-logo.jpg' ); ?>" alt="" style="width:48px;height:48px;border-radius:3px"><span style="font-weight:700;font-size:19px;color:#060f2f;font-family:'Fira Sans',sans-serif">They Vote for You</span></span><div class="oaf-coll-full__body"><h3><span class="oaf-coll-full__dot" style="background:#428bca"></span>They Vote for You</h3><p class="oaf-coll-full__long">They Vote for You turns the parliamentary record into plain English, so you can see how your MP has actually voted on the issues you care about - not how they say they vote.</p></div><div class="oaf-coll-full__meta"><span class="oaf-coll-row__stat" style="color:#2f6da3">200+ divisions explained</span><a class="oaf-coll-full__visit" href="<?php echo $oaf_tvfy; ?>">Visit &rarr;</a></div></div></div>
		<div class="oaf-service-card"><div class="oaf-coll-row"><span class="oaf-coll-band" style="background:#f4f1eb"><img src="<?php echo esc_url( $oaf_svc . '/oa-logo.png' ); ?>" alt="OpenAustralia.org.au"></span><div class="oaf-coll-full__body"><h3><span class="oaf-coll-full__dot" style="background:#3a4e72"></span>OpenAustralia.org.au</h3><p class="oaf-coll-full__long">OpenAustralia.org.au makes the official record of Federal Parliament - Hansard - searchable and easy to follow. Find what your representatives said, and get an email when they speak about what matters to you.</p></div><div class="oaf-coll-full__meta"><span class="oaf-coll-row__stat" style="color:#3a4e72">Every word since 2006</span><a class="oaf-coll-full__visit" href="<?php echo $oaf_oa; ?>">Visit &rarr;</a></div></div></div>
	</div>
	<!-- /wp:html -->
</section>
<!-- /wp:group -->

<!-- wp:pattern {"slug":"oaf/donate-band"} /-->
