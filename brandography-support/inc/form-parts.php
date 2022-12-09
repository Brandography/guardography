<?php

function brandography_support_success() { ?>
<div id="brandography-support-success">
	<div class="success-icon">
		<svg width="100px" height="100px" version="1.1" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
		 <g>
		  <path fill="#0c9969" d="m43.602 54.898l-9.1016-9.0977-8.6016 8.5977 17.703 17.703 30.5-30.5-8.6016-8.6016z"/>
		  <path fill="#0c9969" d="m50 8c-23.699 0-43 19.301-43 43s19.301 43 43 43 43-19.301 43-43-19.301-43-43-43zm0 78c-19.301 0-35-15.699-35-35s15.699-35 35-35 35 15.699 35 35-15.699 35-35 35z"/>
		 </g>
		</svg>
	</div>
	<div class="content">
		<h2><?php _e( 'Request Submitted', 'brandography-support' ); ?></h2>
		<p><?php _e( 'Your support request has been received.', 'brandography-support' ); ?><br/><?php _e( 'We will follow up with you via email.', 'brandography-support' ); ?></p>
	</div>
</div>
<? }

function brandography_support_err() { ?>
<div id="brandography-support-error">
	<div class="error-icon">
		<svg width="100px" height="100px" version="1.1" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
		 <g>
		  <path fill="rgba(217,48,40,1)" d="m50 4.168c-25.312 0-45.832 20.52-45.832 45.832s20.52 45.832 45.832 45.832 45.832-20.52 45.832-45.832-20.52-45.832-45.832-45.832zm0 83.332c-20.73 0-37.5-16.77-37.5-37.5s16.77-37.5 37.5-37.5 37.5 16.77 37.5 37.5-16.77 37.5-37.5 37.5z"/>
		  <path fill="rgba(217,48,40,1)" d="m45.832 27.082h8.332v31.25h-8.332z"/>
		  <path fill="rgba(217,48,40,1)" d="m45.832 64.582h8.332v8.332h-8.332z"/>
		 </g>
		</svg>
	</div>
	<div class="content">
		<h2><?php _e( 'Something Went Wrong', 'brandography-support' ); ?></h2>
		<p><?php _e( sprintf( 'An error was encountered while submitting your request. Copy and paste the text below, and email us at <a href="mailto:%s?subject=Support Request from %s">%s</a>', 'support@brandography.com', get_bloginfo('wpurl'), 'support@brandography.com' ), 'brandography-support' ); ?></p>
		<textarea name="message" id="brandography-support-error-output" class="large-text" rows="6"></textarea>
	</div>
</div>
<?php }


add_action('brandography_support_form', 'brandography_support_success');
add_action('brandography_support_form', 'brandography_support_err', 20);
