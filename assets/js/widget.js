
jQuery(document).ready(function($) {

	$( 'body' ).on('focus', '.wu-autocomplete:not(.ui-autocomplete-input)', function() {
		var $that = $(this);

		$that.autocomplete({

			minLength: 2,

			// In the Customizer, it won't work unless appended.
			appendTo: WuWidget.is_admin ? '.setting-wrapper.autocomplete:visible' : null,

			select: function( event, ui ) {

				// Hide the JSON data in a hidden input
				$('.wu-location-data').val( JSON.stringify(ui.item.data) );

				// This is the WordPress admin
				if( WuWidget.is_admin * 1 ) {

					// Force the Customizer to trigger refresh.
					$('.setting-wrapper.autocomplete input').trigger('change').trigger('blur');

				} else {

					// If we're redirecting, get the link or the name of the location
					var city = ui.item.data.l.replace('/q/', '') || ui.item.data.name;

					// Redirect to the Wunderground site.
					window.location = 'http://'+WuWidget.subdomain+'.wunderground.com/weather-forecast/' + encodeURI(city) ;
				}

				return true;
			},
			focus: function( event, ui ) {

				// Remove the hover class from all results
				$( 'li', ui.currentTarget ).removeClass('ui-state-hover');

				// Add it back in for results
				$( 'li',ui.currentTarget ).filter(function(index, element) {
					// Only where the element text is the same as the response item label
					return $(element).text() === ui.item.label;
				}).addClass('ui-state-hover');

			},
			source: function( request , response ) {

				$.ajax({
					url: WuWidget.ajaxurl,
					dataType: "json",
					data: {
						action: 'wunderground_aq',
						query: request.term,
						_wpnonce: WuWidget._wpnonce
					},
					success: function( data ) {
						response( $.map( data.RESULTS, function( item ) {

							// This is a country or a state page. We want results,
							// not a link to a webpage about results or a list of locations.
							if(item.tz === 'MISSING') { return false; }

							return {
								label: item.name + (item.type ? ' (' + item.type + ')' : ''),
								value: item.name,
								data: item
							};
						}));
					}
				});
			}
		}); // End .autocomplete()
	}); // End .on()

});
