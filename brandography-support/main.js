var formFieldIDs = [
	'name',
	'email_address',
	'message',
	'client_host',
	'wp_name',
	'wp_version',
	'wp_plugins',
	'wp_theme',
	'request_type'
];

jQuery(document).ready(function(){
	jQuery('form#brandography-support-form').on('submit', function(e){
		e.preventDefault();
		var data = new Object();
		var dataCount = 0;
		jQuery('.brandography-support-feedback').empty();
		formFieldIDs.map(function(field){
			if( jQuery(`#${field}`).val() === '' || jQuery(`#${field}`) === undefined ) {
				dataCount--;
				jQuery('.brandography-support-feedback').append(jQuery('<p class="feedback-error">' + jQuery(`#${field}`).attr('placeholder') + ' is required.</p>'));
			} else {
				dataCount++;
			}
		});
		if( dataCount === formFieldIDs.length ) { // If all required fields are present
			// Let's setup our data to send to the support server
			formFieldIDs.map(function(field){
				switch(field) {
					case 'client_wp_plugins':
					case 'wp_theme':
						data[field] = unescape( jQuery(`#${field}`).val() );
						break;
					default:
						data[field] = jQuery(`#${field}`).val();
				}
			});
			jQuery(this).find('input[type="submit"]').attr('disabled', true);
			jQuery(this).find('input[type="submit"]').fadeOut(200, function(){
				jQuery('#brandography-support-loader').css({
					'z-index': '0',
					'opacity': 1,
					'display': 'block'
				});
				jQuery('#brandography-support-error-output').val(JSON.stringify(data));
				// Let the client know the data has been received
				console.log(data);
				jQuery.ajax({
					url: BRANDO_SUPPORT_API_BASE + 'wp-json/brsu/v1/support',
					method: 'POST',
					data: data,
					success: function(result) {
	console.log(result);
						jQuery('form#brandography-support-form').fadeOut(400, function(){
							jQuery('#brandography-support-success').fadeIn();
						});
					},
					timeout: 5000,
					error: function() {
						jQuery('form#brandography-support-form').fadeOut(400, function(){
							jQuery('#brandography-support-error').fadeIn();
						});
					}
				});
			});
		}
	});

	jQuery('#brandography-support-about-link').on('click', function(e){
		e.preventDefault();
		jQuery('.brandography-support-about').fadeIn();
	});

	jQuery('.brandography-support-about-close').on('click', function(e){
		e.preventDefault();
		jQuery('.brandography-support-about').fadeOut();
	});
});
