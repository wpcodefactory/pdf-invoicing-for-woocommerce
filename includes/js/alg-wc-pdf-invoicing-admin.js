/**
 * PDF Invoicing for WooCommerce - Admin JS
 *
 * @version 2.4.0
 * @since   2.4.0
 *
 * @author  WPFactory
 */

jQuery( document ).ready( function ( $ ) {

	// Add button to each element with the class 'alg-wc-shortcode-field'.
	$( '.alg-wc-shortcode-field' ).each( function () {
		// Find the closest ancestor element and add a class to it.
		const shortcode_closest_element = $( this ).parents().first().addClass( 'alg-wc-shortcode-wrap' );

		// Create the link element.
		let link = $( '<a>', {
			href: '#',
			title: alg_wc_pdf_invoicing_admin_js.shortcodes_text,
			class: 'alg-wc-shortcode-button button button-secondary',
			text: alg_wc_pdf_invoicing_admin_js.shortcodes_text
		} );

		// Create the span element for the icon.
		let icon = $( '<span>', {
			class: 'dashicons dashicons-arrow-down-alt2',
		} );

		// Append the icon to the link.
		link.append( icon );

		// Append the link to the selected element.
		shortcode_closest_element.append( link );
	} );

	// Define the content to append.
	let shortcodes_list = alg_wc_pdf_invoicing_admin_js.shortcodes;

	const shortcode_list_class = '.alg-wc-shortcode-list';

	$( document ).on( 'click', '.alg-wc-shortcode-button', function ( e ) {
		e.preventDefault();

		let container = $( this ).closest( '.alg-wc-shortcode-wrap' );

		$( shortcode_list_class ).not( container.find( shortcode_list_class ) ).hide();

		if ( container.find( shortcode_list_class ).length ) {
			container.find( shortcode_list_class ).toggle();
		} else {
			container.append( `${shortcodes_list}` );
			container.find( shortcode_list_class ).toggle();
		}

		e.stopPropagation();
	} );

	// Click event for hiding shortcodes list when clicking outside.
	$( document ).on( 'click', function ( e ) {
		const shortcode_lists = $( shortcode_list_class );

		if ( ! shortcode_lists.is( e.target ) && 0 === shortcode_lists.has( e.target ).length ) {
			shortcode_lists.hide();
		}
	} );

	// Click and append shortcodes to the field or TinyMCE editor.
	$( document ).on( 'click', '.alg-wc-shortcode-list li', function () {
		const shortcode = $( this ).data( 'shortcode' );
		const field_container = $( this ).closest( '.alg-wc-shortcode-wrap' );
		const field_id = field_container.find( '.alg-wc-shortcode-field' ).attr( 'id' );
		const field = $( `[id="${field_id}"]` );

		if ( ! field.length ) {
			return;
		}

		field.focus();

		// Get current cursor position.
		const cursor_pos = field.prop( 'selectionStart' );

		// Use execCommand to insert text.
		try {
			document.execCommand( 'insertText', false, shortcode );
		} catch ( error ) {
			// Fallback method if execCommand fails.
			const field_value = field.val();
			field.val( field_value.substring( 0, cursor_pos ) + shortcode + field_value.substring( cursor_pos ) );
		}

		// Update cursor position after inserting the shortcode.
		field.prop( 'selectionStart', cursor_pos + shortcode.length );
		field.prop( 'selectionEnd', cursor_pos + shortcode.length );

		// For TinyMCE editor (Visual Editor).
		if ( typeof tinyMCE !== "undefined" ) {
			const editor = tinyMCE.get( field_id );

			if ( editor ) {
				// Insert the shortcode into the TinyMCE editor.
				editor.execCommand( 'mceInsertContent', false, shortcode );
			}
		}
	} );

	// Filter items in the dropdown shortcode list.
	$( document ).on( 'keyup', '.alg-wc-shortcode-search', function () {
		let filter = $( this ).val().toLowerCase();
		$( this ).closest( '.alg-wc-shortcode-list' ).find( 'li' ).filter( function () {
			$( this ).toggle( $( this ).text().toLowerCase().indexOf( filter ) > - 1 );
		} );
	} );

	// Bind changes for editor (TinyMCE or textarea).
	function bindEditorChanges( editorContainer ) {
		const editorId = editorContainer.find( '.wp-editor-area' ).attr( 'id' );
		const $textarea = $( `#${editorId}` );

		// TinyMCE Visual Editor.
		if ( typeof tinyMCE !== 'undefined' ) {
			const editor = tinyMCE.get( editorId );
			if ( editor ) {
				editor.on( 'change keyup', function () {
					editor.save(); // sync content to textarea.
					$textarea.trigger( 'input' ).trigger( 'change' );
				} );
			}
		}
	}

	// Initialize all editors.
	$( '.alg-wc-text-editor' ).each( function () {
		bindEditorChanges( $( this ) );
	} );

	// Handle dynamically initialized TinyMCE editors.
	$( document ).on( 'tinymce-editor-init', function ( e, editor ) {
		const container = $( `#${editor.id}` ).closest( '.alg-wc-text-editor' );
		if ( container.length ) {
			bindEditorChanges( container );
		}
	} );

	// Trigger changes on editor tab switch (Visual/Text).
	$( document ).on( 'click', '.wp-switch-editor', function () {
		const container = $( this ).closest( '.alg-wc-text-editor' );
		const editorId = container.find( '.wp-editor-area' ).attr( 'id' );

		// Trigger change for listeners.
		$( `#${editorId}` ).trigger( 'input' ).trigger( 'change' );
	} );
} );
