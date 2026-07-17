/**
 * Editor registration for the Contributors block.
 *
 * No-build: uses the global `wp` packages declared in editor.asset.php. The
 * block is server-rendered and takes no attributes; the canvas shows a live
 * ServerSideRender preview of the self-hosted contributor grid.
 */
( function ( wp ) {
	var el = wp.element.createElement;
	var ServerSideRender = wp.serverSideRender;
	var useBlockProps = wp.blockEditor.useBlockProps;

	wp.blocks.registerBlockType( 'oaf/contributors', {
		edit: function ( props ) {
			return el(
				'div',
				useBlockProps(),
				el( ServerSideRender, {
					block: 'oaf/contributors',
					attributes: props.attributes,
				} )
			);
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp );
