/**
 * Populate and drive the People Quick Edit fields.
 *
 * Quick Edit renders the Role input and featured-image control empty, so we
 * copy the current values out of the list-table row when the editor opens, and
 * wire the photo buttons to the media library. The hidden thumbnail field uses
 * core's convention: a positive attachment id keeps/sets the photo, -1 removes
 * it, and an empty value is left untouched on save.
 */
( function ( $, wp ) {
	'use strict';

	if ( ! window.inlineEditPost ) {
		return;
	}

	var wpInlineEdit = inlineEditPost.edit;

	inlineEditPost.edit = function ( id ) {
		wpInlineEdit.apply( this, arguments );

		var postId = 0;
		if ( typeof id === 'object' ) {
			postId = parseInt( this.getId( id ), 10 );
		}
		if ( ! postId ) {
			return;
		}

		var $row     = $( '#post-' + postId );
		var $editRow = $( '#edit-' + postId );

		// Role: copy the plain text from the list cell.
		var role = $.trim( $row.find( '.column-oaf_role .oaf-role-text' ).text() );
		$editRow.find( 'input.oaf-quick-role' ).val( role );

		// Featured image: read the current attachment id and clone the preview.
		// The id span only exists when a photo is set, so empty means "no photo".
		var thumbId = $.trim( $row.find( '.column-oaf_photo .oaf-thumb-id' ).text() );
		$editRow.find( 'input.oaf-quick-photo-id' ).val( thumbId );

		var $preview = $editRow.find( '.oaf-quick-photo-preview' ).empty();
		var $img     = $row.find( '.column-oaf_photo img' ).clone();
		if ( $img.length ) {
			$preview.append( $img );
		}

		toggleRemove( $editRow );
	};

	/**
	 * Show the Remove button only when there is a photo to remove.
	 *
	 * @param {jQuery} $scope Element containing the photo control.
	 */
	function toggleRemove( $scope ) {
		var value  = $scope.find( 'input.oaf-quick-photo-id' ).val();
		var hasImg = '' !== value && '-1' !== value;
		$scope.find( '.oaf-quick-photo-remove' ).toggle( hasImg );
	}

	var frame;

	$( document ).on( 'click', '.oaf-quick-photo-set', function ( event ) {
		event.preventDefault();
		var $group = $( this ).closest( '.oaf-quick-photo' );

		frame = wp.media( {
			title:    oafQuickEditPerson.mediaTitle,
			button:   { text: oafQuickEditPerson.mediaButton },
			library:  { type: 'image' },
			multiple: false
		} );

		frame.on( 'select', function () {
			var attachment = frame.state().get( 'selection' ).first().toJSON();
			var url        = attachment.sizes && attachment.sizes.thumbnail
				? attachment.sizes.thumbnail.url
				: attachment.url;

			$group.find( 'input.oaf-quick-photo-id' ).val( attachment.id );
			$group.find( '.oaf-quick-photo-preview' )
				.html( $( '<img>', { src: url, width: 40, height: 40, alt: '' } ) );
			toggleRemove( $group );
		} );

		frame.open();
	} );

	$( document ).on( 'click', '.oaf-quick-photo-remove', function ( event ) {
		event.preventDefault();
		var $group = $( this ).closest( '.oaf-quick-photo' );

		$group.find( 'input.oaf-quick-photo-id' ).val( '-1' );
		$group.find( '.oaf-quick-photo-preview' ).empty();
		toggleRemove( $group );
	} );

}( jQuery, wp ) );
