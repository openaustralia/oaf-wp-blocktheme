/**
 * Editor registration for the People Grid block.
 *
 * No-build: uses the global `wp` packages declared in editor.asset.php. The
 * block is server-rendered; the sidebar lets you pick which group to show and
 * the canvas shows a live ServerSideRender preview.
 */
( function ( wp ) {
	var el = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var ServerSideRender = wp.serverSideRender;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var SelectControl = wp.components.SelectControl;
	var useSelect = wp.data.useSelect;
	var __ = wp.i18n.__;

	wp.blocks.registerBlockType( 'oaf/people-grid', {
		edit: function ( props ) {
			var terms = useSelect( function ( select ) {
				return select( 'core' ).getEntityRecords( 'taxonomy', 'oaf_person_group', { per_page: -1 } );
			}, [] );

			var options = [ { label: __( 'All groups', 'oaf-wp-blocktheme' ), value: '' } ];
			if ( terms ) {
				terms.forEach( function ( term ) {
					options.push( { label: term.name, value: term.slug } );
				} );
			}

			return el(
				Fragment,
				{},
				el(
					InspectorControls,
					{},
					el(
						PanelBody,
						{ title: __( 'People grid', 'oaf-wp-blocktheme' ) },
						el( SelectControl, {
							label: __( 'Group', 'oaf-wp-blocktheme' ),
							value: props.attributes.group,
							options: options,
							onChange: function ( value ) {
								props.setAttributes( { group: value } );
							},
						} )
					)
				),
				el(
					'div',
					useBlockProps(),
					el( ServerSideRender, {
						block: 'oaf/people-grid',
						attributes: props.attributes,
					} )
				)
			);
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp );
