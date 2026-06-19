/**
 * Editor registration for the Raisely Donation Form block.
 *
 * No-build: uses the global `wp` packages declared as dependencies in
 * editor.asset.php. The block is server-rendered, so the editor shows a live
 * ServerSideRender preview and saves no markup.
 */
( function ( wp ) {
	var el = wp.element.createElement;
	var ServerSideRender = wp.serverSideRender;
	var useBlockProps = wp.blockEditor.useBlockProps;

	wp.blocks.registerBlockType( 'oaf/raisely-donation-form', {
		edit: function () {
			return el(
				'div',
				useBlockProps(),
				el( ServerSideRender, { block: 'oaf/raisely-donation-form' } )
			);
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp );
