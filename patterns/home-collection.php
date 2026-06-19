<?php
/**
 * Title: Home - collection list
 * Slug: oaf/home-collection
 * Categories: oaf
 * Inserter: no
 *
 * The four-service "Our collection" band as it appears on the home page
 * (full-width editorial rows). Service descriptions and stats are fixed copy.
 *
 * @package oaf-wp-blocktheme
 */

$oaf_svc = get_template_directory_uri() . '/assets/img/services';
?>
<!-- wp:group {"align":"full","className":"oaf-band","backgroundColor":"sand","layout":{"type":"default"}} -->
<div class="wp-block-group alignfull oaf-band has-sand-background-color has-background">
	<!-- wp:html -->
	<div class="oaf-ct">
		<div class="oaf-sechead">
			<h2>Our collection</h2>
			<span class="oaf-sechead__link"><a class="oaf-morelink" href="/collection/">Browse the collection &rarr;</a></span>
		</div>
		<p class="oaf-intro">Four free public services - the working shelves of our digital library. Each one is built and maintained by a small team and a long list of volunteer contributors.</p>
		<div class="oaf-coll-list">
			<div class="oaf-service-card"><a class="oaf-coll-row" href="/collection/"><span class="oaf-coll-band" style="background:#060f2f"><img src="<?php echo esc_url( $oaf_svc . '/pa-logo-white.svg' ); ?>" alt="Planning Alerts"></span><span class="oaf-coll-row__one">Find out what&rsquo;s being built and knocked down in your community.</span><span class="oaf-coll-row__stat" style="color:#b32d80">1.4 million alerts sent last year</span></a></div>
			<div class="oaf-service-card"><a class="oaf-coll-row" href="/collection/"><span class="oaf-coll-band" style="background:#00dcf0"><img src="<?php echo esc_url( $oaf_svc . '/rtk-logo.svg' ); ?>" alt="Right to Know"></span><span class="oaf-coll-row__one">Make Freedom of Information requests, publicly archived.</span><span class="oaf-coll-row__stat" style="color:#007a85">12,346 FOI requests archived</span></a></div>
			<div class="oaf-service-card"><a class="oaf-coll-row" href="/collection/"><span class="oaf-coll-band" style="background:#ffea8a;gap:13px"><img src="<?php echo esc_url( $oaf_svc . '/tvfy-logo.jpg' ); ?>" alt="" style="width:42px;height:42px;border-radius:3px"><span style="font-weight:700;font-size:18px;color:#060f2f;font-family:'Fira Sans',sans-serif">They Vote for You</span></span><span class="oaf-coll-row__one">See how your MP votes in Federal Parliament.</span><span class="oaf-coll-row__stat" style="color:#2f6da3">200+ divisions explained</span></a></div>
			<div class="oaf-service-card"><a class="oaf-coll-row" href="/collection/"><span class="oaf-coll-band" style="background:#f4f1eb"><img src="<?php echo esc_url( $oaf_svc . '/oa-logo.png' ); ?>" alt="OpenAustralia"></span><span class="oaf-coll-row__one">Hansard, made findable.</span><span class="oaf-coll-row__stat" style="color:#3a4e72">Every word since 2006</span></a></div>
		</div>
	</div>
	<!-- /wp:html -->
</div>
<!-- /wp:group -->
