/*! Playwire - v0.1.0
 * http://wordpress.org/plugins
 * Copyright (c) 2014; * Licensed GPLv2+ */

( function( $, window, undefined ) {
	'use strict';

	var isOpen = false;

	$('.help-click').click(function(event){

		event.preventDefault();	

		if(isOpen === false){

			$('.success-instructions').after('<div class="loading-jawn">Loading Help Videos...</div>');
			$('#help-section').css('height', '100%');
			$('#help-section').css('visibility', 'visible');
			$('.help-click').text('CLOSE HELP SECTION');
			isOpen = true;

			renderPlayer();

			function renderPlayer () {

				if (typeof window.Bolt.renderPlayer != 'undefined') {
					window.Bolt.renderPlayer("help_1", function(){$('.loading-jawn').remove();});
					window.Bolt.renderPlayer("help_2");
					window.Bolt.renderPlayer("help_3");

				}else {
					console.log ('no match, trying again');
					setTimeout(renderPlayer(), 100);
				}
			}

		}else{
			$('#help_1, #help_2, #help_3').remove();
			$('#help-section').css('height', '1px');
			$('#help-section').css('visibility', 'hidden');
			$('.help-click').text('OPEN HELP SECTION');
			isOpen = false;
		}

		return false;
	
	});



	//--------------------------------------------------//
	// Adds in a jquery dialog box for the single video posts edit screen
	//--------------------------------------------------//
	if ( PlaywireObject.compare_json === 'different' ) {
		//Define the selector for the diff dialog box
		var diff_selector = 'single-video-posts-edit-dialog-different';
		//Grab the element from the dom by the selector
		var diff_element  = $( document.getElementById( diff_selector ) );

		diff_element.dialog( {
				'dialogClass'   : 'wp-dialog',
				'modal'         : false,
				'autoOpen'      : false,
				'closeOnEscape' : true,
				'buttons'       : {
					"Ignore": function() {
						$( this ).dialog( 'close' );
					},
					"Update": function() {
						var update_confirm = window.confirm( 'Warning! This will overwrite the current post what whatever new data it recieves.' );
						if ( update_confirm === true ) {
							//Confirm and update post with new values via ajax
							var diff_data_element = $( document.getElementById( 'single-video-posts-edit-dialog-different-data-element' ) );
							var nonce             = diff_data_element.data( 'nonce' );
							var id                = diff_data_element.data( 'id' );

							var diff_request  = $.ajax( {
								dataType : 'json',
								type     : 'post',
								url      : PlaywireObject.ajaxurl,
								data: {
									action : 'ajax_update_post',
									nonce  : nonce,
									id     : id
								}
							} );

							//Success/done callback
							diff_request.done( function( response )  {
								if ( response === true ) {
									diff_element.dialog(  'close'  );
									location.reload();
								}
							} );

						} else  {
							//Do nothing
							return false;
						}
					},
					"Go Back": function() {
						window.location.href = PlaywireObject.adminurl;
						return false;
					}
				}
		} );
	}


	//--------------------------------------------------//
	// Adds in a jquery dialog box for the single video
	// posts edit screen if removed
	//--------------------------------------------------//
	if ( PlaywireObject.compare_json === 'removed' ) {
		//Define the selector for the removed dialog box
		var removed_selector = 'single-video-posts-edit-dialog-removed';
		//Grab the element from the dom by the selector
		var removed_element  = $( document.getElementById( removed_selector ) );
		var removed_dialog   = $( document.getElementById( 'single-video-posts-edit-dialog-removed-data-element' ) );
		removed_element.dialog( {
				'dialogClass'   : 'wp-dialog',
				'modal'         : true,
				'autoOpen'      : true,
				'closeOnEscape' : true,
				'buttons'       : {
					"Ignore": function() {
						//Confirm and update post with new values via ajax
						var removed_data_element = $( removed_dialog );
						var nonce                = removed_data_element.data( 'nonce' );
						var id                   = removed_data_element.data( 'id' );
						var removed_request      = $.ajax( {
								dataType : 'json',
								type     : 'post',
								url      : PlaywireObject.ajaxurl,
								data: {
									action : 'ajax_delete_post_meta',
									nonce  : nonce,
									id     : id
								}
							} );

						//Success/done callback
						removed_request.done( function( response )  {
							if ( response === true ) {
								location.reload();
							}
						} );
					},
					"Delete": function() {
						var remove_confirm = window.confirm( 'Warning! This will delete the current post and any data associated with it in WordPress.' );
						if ( remove_confirm === true ) {
							//Confirm and update post with new values via ajax
							var removed_data_element = $( document.getElementById( 'single-video-posts-edit-dialog-removed-data-element' ) );
							var nonce                = removed_data_element.data( 'nonce' );
							var id                   = removed_data_element.data( 'id' );
							var remove_post_request  = $.ajax( {
									dataType : 'json',
									type     : 'post',
									url      : PlaywireObject.ajaxurl,
									data: {
										action : 'ajax_delete_post',
										nonce  : nonce,
										id     : id
									}
								} );

							//Success/done callback
							remove_post_request.done( function( response )  {
								if ( response === true ) {
									location.reload();
									window.location.href = PlaywireObject.adminurl;
								}
							} );
						} else  {
							//Do nothing
							return false;
						}
					},
					"Go Back": function() {
						window.location.href = PlaywireObject.adminurl;
						return false;
					}
				}
		} );
	}


	//--------------------------------------------------//
	// Controls the radio buttons on the quickedit screen
	//--------------------------------------------------//
	var the_list = document.getElementById( 'the-list' );
	if ( the_list ) {
		the_list.addEventListener( 'click', function( event ) {
			if ( event.target && event.target.getAttribute( 'class' ) === 'editinline' ) {
				//Reset
				inlineEditPost.revert();

				//Get the post ID
				var post_id = inlineEditPost.getId( event.target );

				var rowData = $( document.getElementById( String( 'inline_' + post_id ) ) );

				//Hierarchical taxonomies (we're treating all radio taxes as hierarchical)
				$( document.getElementsByClassName( 'post_category' ), rowData ).each( function() {

					var taxonomy;
					var term_ids = $( this ).text();
					term_ids     = term_ids.trim() !== '' ? term_ids.trim() : '0';
					var term_id  = term_ids.split( "," );
					term_id      = term_id ? term_id[0] : '0';
					taxonomy     = $( this ).attr( 'id' ).replace( String( '_'+post_id ), '' );
					//Selects the proper list item in the quickedit :w
					//taxonomy screen
					$( document.getElementById( String( taxonomy + '-' + term_id ) ) ).first().find( 'input:radio' ).prop( 'checked', true );
				} );
			}
		} );
	}


	//--------------------------------------------------//
	// Inserts Shortcode on post/page from tinymce
	//--------------------------------------------------//
	//First check if we are in an iFrame because we are using an iFrame for the Playlist library template
	if ( window.self !== window.top ) {
		var playlist = document.getElementsByClassName( 'playlist' );
		//To keep backwards compatability with browsers we are leaving this event listener with jQuery
		$( playlist ).click( function() {
			var playlist_post_id = $( this ).data( 'playlist-post-id' );
			var win = window.dialogArguments || window.opener || window.parent || window.top;
			win.send_to_editor( String( '[playwire_playlist playlist_post_id=' + playlist_post_id + ']' ) );
		} );
	}

	//First check if we are in an iFrame because we are using an iFrame for the Playlist library template
	if ( window.self !== window.top ) {
		var video = document.getElementsByClassName( 'video' );
		//To keep backwards compatability with browsers we are leaving this event listener with jQuery
		$( video ).click( function() {
			var video_post_id = $( this ).data( 'video-post-id' );
			var win = window.dialogArguments || window.opener || window.parent || window.top;
			win.send_to_editor( String( '[playwire_video video_post_id=' + video_post_id + ']' ) );
		} );
	}

	//--------------------------------------------------//
	// Filters out playlist results using data-attribute
	//--------------------------------------------------//
	$( document.getElementById( playwire_object.search_input ) ).fastLiveFilter( document.getElementById( playwire_object.search_list ) );


	//--------------------------------------------------//
	// Adds the "Add New XXX" button to the videos and
	// playlists post type edit screens
	// @note: This is only because WP doesn't currently
	// offer an action/filter to do this.
	//--------------------------------------------------//
	//If proper edit screen then create the button and insert into dom
	if ( parseInt( playwire_object.is_edit_screen, 10 ) === 1 ) {
	//playwire_object.edit_screen_text
		//playwire_object.edit_screen_url
		$( '#wpbody-content h2' ).append(
			$('<a/>', {
				'class' : 'add-new-h2',
				href    : playwire_object.edit_screen_url,
				text    : playwire_object.edit_screen_text
			} )
		);
	}


	//--------------------------------------------------//
	// Updates selected playlist and form in wp-admin
	//--------------------------------------------------//
	//Setup id's and elements for the settings selectors
	var playwire_aspect_ratio_select        = '#playwire_aspect_ratio_select';
	var playlist_preview_meta_box           = '#playlist_preview_meta_box';
	var playwire_gallery_pagination_options = document.getElementById( 'playwire_gallery_pagination_options' );
	var playwire_single_video_element       = document.getElementById( 'playwire_single_video' );
	var playwire_video_playlist_element     = document.getElementById( 'playwire_video_playlist' );

	$( "#post select, #post input" ).on( "change", function( event ) {
		//We'll just lay out the variables since there aren't that many.
		var playwire_metabox_nonce        = $( "input[name='playwire_metabox_nonce']" ).val();
		var template                      = $( "input[name='playwire_video_layout']:checked" ).data( 'template' );
		var current_playlist              = $( "select[name='playwire_video_playlist']" ).find( ':selected' ).val();
		var current_single_video          = $( "select[name='playwire_single_video']" ).find( ':selected' ).data( 'current-single-video' );
		var current_ratio                 = $( "input[name='playwire_aspect_ratio']:checked" ).val();
		var current_ratio_select          = $( String( "select" + '.' + current_ratio ) ).find( ':selected' ).val();
		var current_ratio_height          = $( String( "select" + '.' + current_ratio ) ).find( ':selected' ).data( 'height' );
		var current_ratio_width           = $( String( "select" + '.' + current_ratio ) ).find( ':selected' ).data( 'width' );
		var current_gallery_pagination    = $( "input[name='playwire_gallery_pagination_options']:checked" ).val();

		var data = {
			action:                      'ajax_preview',
			playwire_metabox_nonce:      playwire_metabox_nonce,
			template:                    template,
			current_playlist:            current_playlist,
			current_single_video:        current_single_video,
			current_ratio:               current_ratio,
			current_ratio_select:        current_ratio_select,
			current_ratio_height:        current_ratio_height,
			current_gallery_pagination:  current_gallery_pagination
		};


		if ( data.template === 'single' ) {
			//$( playwire_single_video ).show();
			//$( playwire_video_playlist ).hide();
		} else {
			//$( playwire_single_video ).hide();
			//$( playwire_video_playlist ).show();
		}

		if ( data.template === 'gallery') {
			//$( playwire_gallery_pagination_options ).show();
		} else {
			//$( playwire_gallery_pagination_options ).hide();
		}

		//We are using a <space> below instead of a &nbsp; because the concatonated string will be converted to a literal string
		if ( data.current_ratio === 'custom' ) {
			$( String( playwire_aspect_ratio_select + ' ' + 'ul' ) ).show();
			$( String( playwire_aspect_ratio_select + ' ' + 'select' ) ).hide();
		} else if (data.current_ratio === 'classic' ) {
			$( String( playwire_aspect_ratio_select + ' ' + 'select.classic' ) ).show();
			$( String( playwire_aspect_ratio_select + ' ' + 'ul' ) ).hide();
			$( String( playwire_aspect_ratio_select + ' ' + 'select.widescreen' ) ).hide();
		} else if (data.current_ratio === 'widescreen' ) {
			$( String( playwire_aspect_ratio_select + ' ' + 'select.widescreen' ) ).show();
			$( String( playwire_aspect_ratio_select + ' ' + 'ul' ) ).hide();
			$( String( playwire_aspect_ratio_select + ' ' + 'select.classic' ) ).hide();
		}

		$( String( playlist_preview_meta_box + ' ' + '.inside' ) ).fadeOut();
		$.post(ajaxurl, data, function( response ) {
			$( String( playlist_preview_meta_box + ' ' + '.inside' ) ).fadeIn().html( response );
		} );

	} );


	//--------------------------------------------------//
	// Adds pseudo pagination to the gallery
	//--------------------------------------------------//
	//Add wild id selector to loop through galleryies
	var playwire_gallery = '[id^=playwire_gallery]';
	var show_per_page    = playwire_object.gallery_limit; //To comply with the current css standard of 3 items per row this number must be divisible by 3 the variable for this is setup in the main Playwire class

	$( playwire_gallery ).each( function( index ) {
		//Add an index in case of multiple instances of same gallery exist on page
		var new_class = $( this ).attr( 'id' ) + '_' + index;
		$( this ).addClass( new_class );
		var number_of_items = $( this ).children( 'a' ).size();
		//If the number of items is lower than the show_per_page no need to continue on.
		if ( number_of_items <= show_per_page ) {
			return;
		}

		var number_of_pages = Math.ceil( number_of_items / show_per_page );

		//Adding in a properly built html element using jQuery
		$( this ).append(
			$('<div/>', {
				'class': 'controls'
			} ),
			$('<input/>', {
				type: 'hidden',
				'class': 'current_page'
			} ),
			$( '<input/>', {
				'class': 'show_per_page',
				type: 'hidden'
			} )
		);

		$( '.current_page' ).val( 0 );
		$( '.show_per_page' ).val( parseInt( show_per_page, 10 ) );

		//if pagination
		if ( $( this ).attr( 'id' ).match( '_pagination$' ) ) {
			var navigation_html_page = '<a class="prev">Prev</a>';
			var current_link = 0;
			while ( number_of_pages > current_link ) {
				navigation_html_page += String( '<a class="page" longdesc="' + current_link + '">' + ( current_link + 1 ) + '</a>' );
				current_link++;
			}
			navigation_html_page += '<a class="next">Next</a>';
			$( '.controls', this ).html( navigation_html_page );
		}

		$( '.controls a.page:first', this ).addClass( 'active' );

		//if show More Videos button
		if ( $( this ).attr( 'id' ).match( '_more$' ) ) {
			var navigation_html_more = String( '<a class="more-videos">More Videos</a>' );
			$( '.controls', this ).html( navigation_html_more );
		}

		if ( $( this ).attr( 'id' ).match( '_pagination$' ) || $( this ).attr( 'id' ).match( '_more$' ) ) {
			$( this ).children( 'a' ).css( 'display', 'none' );
			$( this ).children( 'a' ).slice( 0, show_per_page ).css( 'display', 'inline-block' );
		}
	} );


	//--------------------------------------------------//
	// Controls for the gallery pagination
	//--------------------------------------------------//
	$( '.controls' ).on( 'click',  'a', function() {
		var page_num      = $( this ).attr( 'longdesc' );
		var gallery_class = $( this ).parent().parent().attr( 'class' );
		var show_per_page = parseInt( $( this ).parent().parent().find( '.show_per_page' ).val(), 0 );
		var current_page  = $( this ).parent().parent().find( '.current_page' ).val();

		if ( $( this ).hasClass( 'next' ) && $( this ).siblings( '.active' ).next( '.page' ).length == true ) {
			var page_num = parseInt( current_page, 0 ) + 1;
			playwire_go_to_page( page_num, gallery_class, show_per_page, current_page );
		} else if ( $( this ).hasClass( 'prev' ) && $( this ).siblings( '.active' ).prev( '.page' ).attr( 'longdesc' ) >= 0 ) {
			var page_num = parseInt( current_page, 0 ) - 1;
			playwire_go_to_page( page_num, gallery_class, show_per_page, current_page );
		}

		if ( $( this ).hasClass( 'more-videos' ) ) {
			var videos_parent = $( this ).parent().parent().attr( 'class' );
			var child         =  $( String( '.' + videos_parent ) ).find( "> a:hidden" ).first();
			var items         =  $( String( '.' + videos_parent + ' a' ) ).size() - 1;
			var shown         =  $( String( '.' + videos_parent + ' a:visible' ) ).size() - 1;
			var show_per_page = parseInt( shown, 10 ) + parseInt( show_per_page, 10 );
			if ( items > shown ) {
				$( String( '.' + videos_parent + ' a:lt(' + show_per_page + ')' ) ).show();
			} else {
				this.style.display = 'none';
			}
		}

		if ( $( this ).hasClass( 'page' ) ) {
			playwire_go_to_page( page_num, gallery_class, show_per_page, current_page );
		}

	} );


	//--------------------------------------------------//
	// Controls the icon for the gallery layout
	//--------------------------------------------------//
	$( "div[id^='playwire_gallery'] a .vertical-video-alignment, .flexslider-control ul.slides li" ).hover(
		function() {
			$( this ).children( '.playwire_play_icon' ).show();
		},
		function() {
			$( this ).children( '.playwire_play_icon' ).hide();
		}
	);

	//--------------------------------------------------//
	// Allows pagination offset for gallery pagination
	//--------------------------------------------------//
	function playwire_go_to_page( page_num, gallery_class, show_per_page, current_page ) {
		var start_from = page_num * show_per_page;
		var end_on = start_from + show_per_page;
		$( document.getElementsByClassName( gallery_class ) ).children( 'a' ).css( 'display', 'none' ).slice( start_from, end_on ).css( 'display', 'inline-block' );
		$( document.getElementsByClassName( gallery_class ) ).find( '.page[longdesc=' + page_num + ']' ).addClass( 'active' ).siblings( '.active' ).removeClass( 'active' );
		$( document.getElementsByClassName( gallery_class ) ).find( '.current_page' ).val( page_num );
	}

} )( jQuery, this );