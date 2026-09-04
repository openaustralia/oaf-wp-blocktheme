<?php
/**
 * Title: Footer (canonical)
 * Slug: oaf/footer
 * Categories: oaf
 * Block Types: core/template-part/footer
 * Inserter: no
 *
 * The OAF canonical footer: attribution, charity status + ABN, Acknowledgement
 * of Country, ACNC Registered Charity tick, sister services, social links and
 * the copyright + content licence notice.
 * This copy is mandatory across the family - keep it exact.
 *
 * @package oaf-wp-blocktheme
 */

$oaf_uri  = get_template_directory_uri();
$oaf_home = home_url( '/' );
$oaf_org  = oaf_option( 'org_name' );
$oaf_abn  = oaf_option( 'abn' );
$oaf_acnc = oaf_option( 'acnc_url' );
$oaf_abr  = oaf_option( 'abr_url' );

// Service and social links can each be blanked in the theme settings; a blank one
// is omitted rather than rendered as an empty link.
$oaf_services = oaf_service_urls();
$oaf_pa       = $oaf_services['planningalerts'];
$oaf_rtk      = $oaf_services['righttoknow'];
$oaf_tvfy     = $oaf_services['theyvoteforyou'];
$oaf_oa       = $oaf_services['openaustralia'];
$oaf_gh       = oaf_option( 'github_url' );
$oaf_bsky     = oaf_option( 'bluesky_url' );
$oaf_mast     = oaf_option( 'mastodon_url' );
$oaf_li       = oaf_option( 'linkedin_url' );

// The reuse/exceptions page is a required page an admin creates from Appearance
// -> OAF Theme, so it may not exist yet on a site that has only just updated.
// Link to it only when it is actually published; otherwise the licence sentence
// still reads correctly with the exceptions noted as plain text.
$oaf_licence_page = get_page_by_path( 'licence' );
$oaf_licence_url  = ( $oaf_licence_page instanceof WP_Post && 'publish' === $oaf_licence_page->post_status )
	? get_permalink( $oaf_licence_page )
	: '';
?>
<!-- wp:group {"align":"full","className":"oaf-footer","backgroundColor":"sand","layout":{"type":"default"}} -->
<div class="wp-block-group alignfull oaf-footer has-sand-background-color has-background">
	<!-- wp:html -->
	<div class="oaf-ct">
		<div class="oaf-foot-top">
			<div class="oaf-foot-left">
				<div class="oaf-foot-brand">
					<div class="oaf-foot-intro">Built and maintained by</div>
					<a class="oaf-foot-wordmark" href="<?php echo esc_url( $oaf_home ); ?>" aria-label="<?php echo esc_attr( $oaf_org ); ?>"><img src="<?php echo esc_url( $oaf_uri . '/assets/img/oaf-wordmark-red.svg' ); ?>" alt="<?php echo esc_attr( $oaf_org ); ?>"></a>
				</div>
				<div class="oaf-foot-charity">
					<p><a href="<?php echo esc_url( $oaf_home ); ?>"><?php echo esc_html( $oaf_org ); ?></a> is a <a href="<?php echo esc_url( $oaf_acnc ); ?>">registered charity</a> in Australia - company limited by guarantee. ABN&nbsp;<a href="<?php echo esc_url( $oaf_abr ); ?>"><?php echo esc_html( str_replace( ' ', "\u{00A0}", $oaf_abn ) ); ?></a>.</p>
				</div>
				<div class="oaf-foot-aok"><?php echo esc_html( oaf_option( 'acknowledgement' ) ); ?></div>
			</div>
			<a class="oaf-foot-tick" href="<?php echo esc_url( $oaf_acnc ); ?>"><img src="<?php echo esc_url( $oaf_uri . '/assets/img/acnc/acnc-registered-charity-colour.svg' ); ?>" alt="ACNC Registered Charity"></a>
		</div>
		<div class="oaf-foot-row">
			<div class="oaf-foot-eyebrow">Our services</div>
			<ul class="oaf-foot-services">
				<?php
				if ( '' !== $oaf_pa ) :
					?>
					<li><a href="<?php echo esc_url( $oaf_pa ); ?>">Planning Alerts</a></li><?php endif; ?>
				<?php
				if ( '' !== $oaf_rtk ) :
					?>
					<li><a href="<?php echo esc_url( $oaf_rtk ); ?>">Right to Know</a></li><?php endif; ?>
				<?php
				if ( '' !== $oaf_tvfy ) :
					?>
					<li><a href="<?php echo esc_url( $oaf_tvfy ); ?>">They Vote for You</a></li><?php endif; ?>
				<?php
				if ( '' !== $oaf_oa ) :
					?>
					<li><a href="<?php echo esc_url( $oaf_oa ); ?>">OpenAustralia.org.au</a></li><?php endif; ?>
			</ul>
		</div>
		<div class="oaf-foot-row oaf-foot-row--socials">
			<div class="oaf-foot-eyebrow">Find us</div>
			<ul class="oaf-foot-socials">
				<?php
				if ( '' !== $oaf_gh ) :
					?>
					<li><a href="<?php echo esc_url( $oaf_gh ); ?>" aria-label="OAF source on GitHub"><svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.4 3-.405 1.02.005 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12"/></svg><span class="oaf-foot-socials__label">GitHub</span></a></li><?php endif; ?>
				<?php
				if ( '' !== $oaf_bsky ) :
					?>
					<li><a href="<?php echo esc_url( $oaf_bsky ); ?>" aria-label="OAF on Bluesky"><svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 10.8c-1.087-2.114-4.046-6.053-6.798-7.995C2.566.944 1.561 1.266.902 1.565.139 1.908 0 3.08 0 3.768c0 .69.378 5.65.624 6.479.815 2.736 3.713 3.66 6.383 3.364.136-.02.275-.039.415-.056-.138.022-.276.04-.415.056-3.912.58-7.387 2.005-2.83 7.078 5.013 5.19 6.87-1.113 7.823-4.308.953 3.195 2.05 9.271 7.733 4.308 4.267-4.308 1.172-6.498-2.74-7.078a8.741 8.741 0 0 1-.415-.056c.14.017.279.036.415.056 2.67.297 5.568-.628 6.383-3.364.246-.828.624-5.79.624-6.478 0-.69-.139-1.861-.902-2.206-.659-.298-1.664-.62-4.3 1.24C16.046 4.748 13.087 8.687 12 10.8Z"/></svg><span class="oaf-foot-socials__label">Bluesky</span></a></li><?php endif; ?>
				<?php
				if ( '' !== $oaf_mast ) :
					?>
					<li><a href="<?php echo esc_url( $oaf_mast ); ?>" aria-label="OAF on Mastodon"><svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M23.268 5.313c-.35-2.578-2.617-4.61-5.304-5.004C17.51.242 15.792 0 11.813 0h-.03c-3.98 0-4.835.242-5.288.309C3.882.692 1.496 2.518.917 5.127.64 6.412.61 7.837.661 9.143c.074 1.874.088 3.745.26 5.611.118 1.24.325 2.47.62 3.68.55 2.237 2.777 4.098 4.96 4.857 2.336.792 4.849.923 7.256.38.265-.061.527-.132.786-.213.585-.184 1.27-.39 1.774-.753a.057.057 0 0 0 .023-.043v-1.809a.052.052 0 0 0-.02-.041.053.053 0 0 0-.046-.01 20.282 20.282 0 0 1-4.709.545c-2.73 0-3.463-1.284-3.674-1.818a5.593 5.593 0 0 1-.319-1.433.053.053 0 0 1 .066-.054c1.517.363 3.072.546 4.632.546.376 0 .75 0 1.125-.01 1.57-.044 3.224-.124 4.768-.422.038-.008.077-.015.11-.024 2.435-.464 4.753-1.92 4.989-5.604.008-.145.043-1.52.043-1.67.003-.512.168-3.63-.024-5.545zm-3.748 9.195h-2.561V8.29c0-1.309-.55-1.976-1.67-1.976-1.23 0-1.846.79-1.846 2.35v3.403h-2.546V8.663c0-1.56-.617-2.35-1.848-2.35-1.112 0-1.668.668-1.67 1.977v6.218H4.822V8.102c0-1.31.337-2.35 1.011-3.12.696-.77 1.608-1.164 2.74-1.164 1.311 0 2.302.5 2.962 1.498l.638 1.06.638-1.06c.66-.999 1.65-1.498 2.96-1.498 1.13 0 2.043.395 2.74 1.164.675.77 1.012 1.81 1.012 3.12z"/></svg><span class="oaf-foot-socials__label">Mastodon</span></a></li><?php endif; ?>
				<?php
				if ( '' !== $oaf_li ) :
					?>
					<li><a href="<?php echo esc_url( $oaf_li ); ?>" aria-label="OAF on LinkedIn"><svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.063 2.063 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg><span class="oaf-foot-socials__label">LinkedIn</span></a></li><?php endif; ?>
			</ul>
		</div>
		<div class="oaf-foot-row oaf-foot-row--legal">
			<p class="oaf-foot-legal">&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php echo esc_html( $oaf_org ); ?> Limited. Unless otherwise stated, you are free to reuse the content on this site under the <a rel="license" href="https://creativecommons.org/licenses/by-nc-sa/4.0/">Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International</a> licence.
			<?php if ( '' !== $oaf_licence_url ) : ?>
				<a href="<?php echo esc_url( $oaf_licence_url ); ?>">Some exceptions apply</a>.
			<?php else : ?>
				Some exceptions apply.
			<?php endif; ?>
			</p>
		</div>
	</div>
	<!-- /wp:html -->
</div>
<!-- /wp:group -->
