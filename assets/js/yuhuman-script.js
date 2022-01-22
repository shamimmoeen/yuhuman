jQuery( document ).ready( function( $ ) {
	var $districtInput = $( 'select#district' );
	var district       = $districtInput.val();

	var $upazillaInput = $( 'select#upazilla' );

	if ( ! district ) {
		$upazillaInput.attr( 'disabled', 'disabled' );
	}

	$districtInput.on( 'change', function() {
		var district = $( this ).val();

		if ( ! district ) {
			$upazillaInput.attr( 'disabled', 'disabled' );
			$upazillaInput.html( '' );
		} else {
			$upazillaInput.removeAttr( 'disabled' );
			var upazillas    = yuhuman_params.upazillas[ district ];
			let upazillaHtml = '';

			$.each( upazillas, function( key, value ) {
				upazillaHtml += '<option value="' + key + '">' + value + '</option>';
			} );

			$upazillaInput.html( upazillaHtml );
		}
	} );

	// Submit the directory search form.
	$( '#yuhuman-donors-filter-bar, .yuhuman-blood-request-sort-bar' ).on( 'change', 'select', function() {
		$( this ).closest( 'form' ).submit();
	} );

	$( '#my-blood-requests-only, #show-archived-blood-requests' ).on( 'change', function() {
		$( this ).closest( 'form' ).submit();
	} );

	var fpBirthday = $( '.fieldset-birthday .wpum-datepicker' );

	if ( fpBirthday.length ) {
		fpBirthday.flatpickr( {
			dateFormat: 'Y-m-d'
		} );
	}

	if ( $( '.yuhuman-datepicker' ).length ) {
		$( '.yuhuman-datepicker:not([readonly])' ).flatpickr( {
			dateFormat: 'Y-m-d'
		} );
	}

	if ( $( '#when-need-blood' ).length ) {
		$( '#when-need-blood:not([readonly])' ).flatpickr( {
			dateFormat: 'Y-m-d'
		} );
	}

	if ( $( '.yuhuman-birthday' ).length ) {
		$( '.yuhuman-birthday:not([readonly])' ).flatpickr( {
			dateFormat: 'Y-m-d'
		} );
	}

	$( '.yuhuman-filter-toggle-wrapper' ).on( 'click', 'span', function() {
		var $elements = $( '#wpum-directory-search-form, #yuhuman-donors-filter-bar' );
		$elements.slideToggle( 300 );
	} );

	var $modal = $( '[data-remodal-id=blood-request-modal]' );

	if ( $modal.length ) {
		var modalInstance = $modal.remodal( {
			hashTracking: false,
		} );

		var $modalWrapper = $( '.yuhuman-blood-request-modal-content-wrapper' );

		$( '.view-blood-request-btn' ).on( 'click', function( e ) {
			e.preventDefault();

			var $mainWrapper = $( this ).closest( '.wpum-directory-single-user' );
			var $modalHtml   = $mainWrapper.find( '.yuhuman-blood-request-modal-content' ).clone();

			$modalWrapper.html( $modalHtml );

			modalInstance.open();
		} );

		$( document ).on( 'closed', '.blood-request-modal', function( e ) {
			$modalWrapper.html( '' );
		} );
	}

	function deleteSearchParameterFromUrl( key ) {
		var search = new URLSearchParams( window.location.search );

		if ( ! search.get( key ) ) {
			return;
		}

		search.delete( key );

		var newParameters = search.toString();
		var newUrl        = window.location.pathname;

		if ( newParameters ) {
			newUrl = `${ window.location.pathname }?${ search.toString() }`;
		}

		window.history.replaceState( {}, document.title, newUrl );
	}

	deleteSearchParameterFromUrl( 'yh-success' );
	deleteSearchParameterFromUrl( 'yh-error' );
} );
