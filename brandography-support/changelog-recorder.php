<?php

/**
 * State Maintenance Session
 */
function brandography_support_session_start() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'brandography_support';
	return $wpdb->update( $table_name, array( 'meta_value' => json_encode( brandography_support_session_info() ) ), array( 'meta_key' => 'maintenance_start' ), array( '%s' ), array( '%s' ) );
}

/**
 * End Maintenance Session
 */
function brandography_support_session_end() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'brandography_support';
	$wpdb->update( $table_name, array( 'meta_value' => json_encode( brandography_support_session_info() ) ), array( 'meta_key' => 'maintenance_end' ), array( '%s' ), array( '%s' ) );
}

/**
 * Reset Maintenance Status
 */
function brandography_support_session_reset() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'brandography_support';
	$wpdb->update( $table_name, array( 'meta_value' => NULL ), array( 'meta_key' => 'maintenance_start' ), array( NULL ), array( '%s' ) );
	$wpdb->update( $table_name, array( 'meta_value' => NULL ), array( 'meta_key' => 'maintenance_end' ), array( NULL ), array( '%s' ) );
}

/**
 * Add Options Page to Admin Menu
 */
add_action( 'admin_menu', 'brandography_support_add_admin_menu' );
function brandography_support_add_admin_menu() { 
	add_submenu_page( 'tools.php', 'Brandography Support', 'Support', 'manage_options', 'brandography_support', 'brandography_support_options_page' );
}

/**
 * Feedback Message Banner
 */
function brandography_support_notice($type='') {

}

/**
 * Options Page Content
 */
function brandography_support_options_page() {
	?>
<div class="wrap">
	<h1><?php _e('Brandography Support', 'brandography-support'); ?></h1>
	<?php
	$sessionStatus = brandography_support_session_status();
	if(isset($_POST['action'])) {
		if($_POST['action'] === 'start' && $sessionStatus === 0 ):
			brandography_support_session_start();
			?>
			<div id="message" class="notice notice-success"><p><?php _e('Maintenance session started.', 'brandography-support'); ?></p></div>
			<?php
		endif;
		// If `end` action, end the session
		if($_POST['action'] === 'end' && $sessionStatus === 1 )
			brandography_support_session_end();
		// If `send` action, reset the session
		if($_POST['action'] === 'send' && $sessionStatus === 2 ):
			brandography_support_session_reset();
			?>
			<div id="message" class="notice notice-success"><p><?php _e('Maintenance report submitted.', 'brandography-support'); ?></p></div>
			<?php
		endif;
	}
	?>
	<?php
		$sessionStatus = brandography_support_session_status();
	?>
	<form action="<?php echo admin_url('tools.php?page=' . $_GET["page"]) ?>" method="post">
	<?php if($sessionStatus === 0 ): ?>
		<p><?php _e('We use these tools during maintenance to track changes we make to your site.', 'brandography-support'); ?></p>
		<input type="hidden" name="action" value="start" />
		<input type="submit" value="Start Maintenance" class="button button-primary">
	<?php endif; ?>
	<?php if($sessionStatus === 1 ): ?>
		<p><?php _e( 'When you\'re finished with maintenance, return here to stop the session and submit the report.', 'brandography-support'); ?></p>
		<input type="hidden" name="action" value="end" />
		<input type="submit" value="End Maintenance" class="button button-primary">
	<?php endif; ?>
	<?php if($sessionStatus === 2 ): ?>
		<div id="message" class="notice notice-warning"><p><?php _e( 'Maintenance session ended. Don\'t forget to submit your report!', 'brandography-support'); ?></p></div>
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row">Name</th>
					<td><input tabindex="1" type="text" id="name" name="submitter_name" /></td>
				</tr>
				<tr>
					<th scope="row">Notes</th>
					<td>
						<textarea style="min-width: 450px;" tabindex="2" rows="5" cols="30" class="description" id="submitter_notes" name="submitter_notes"></textarea>
						<p class="description">If something on the site is not working as expected after making<br/> updates, please report the issue to a developer in Trello.</p>
					</td>
				</tr>
			</tbody>
		</table>
		<input type="hidden" name="log" value="<?php echo htmlentities( json_encode( brandography_support_session_log() ) );?>" />
		<input type="hidden" name="action" value="send" />
		<input type="submit" tabindex="2" value="Submit Maintenance Report" class="button button-primary">
	<?php endif; ?>
	</form>

	<?php if($sessionStatus === 2 ): ?>
	<hr>
	<div>
		<button id="log-toggle" class="button button-secondary" style="font-weight: normal;">View Details</button></th>
		<textarea readonly id="log" style="width: 100%; height: 500px; display: none;"><?php echo json_encode( brandography_support_session_log(), JSON_PRETTY_PRINT ); ?></textarea>
		<script>
			jQuery('input[type="submit"]').on('click', function(e) {
				e.preventDefault();
				const btn = jQuery(this);
				const form = jQuery(this).parent();
				const name =  jQuery('#name').val();
				const notes = jQuery('#submitter_notes').val();

				// Disable Button to Prevent Multiple Submissions
				btn.attr('disabled', true);

				// Build Data Object
				const data = {
						submitter: name,
						submitter_notes: escape( notes ),
						logs: JSON.parse( unescape( jQuery('input[type="hidden"][name="log"]').val() ) ),
						action: jQuery('input[type="hidden"][name="action"]').val()
				};
				// Submit the Log
				jQuery.ajax({
					url: '<?php echo BRANDO_SUPPORT_URL; ?>wp-json/brsu/v1/logs',
					method: 'POST',
					data: JSON.stringify(data),
					success: function(results) {
						form.submit();
					}
				});
			});
			jQuery("#log-toggle").on('click', function(e){
				e.preventDefault();
				jQuery('#log').toggleClass('active');
				jQuery('#log').toggle();
				if(jQuery('#log').hasClass('active')) {
					jQuery(this).text('Hide Details');
				} else {
					jQuery(this).text('Show Details');
				}
			});
		</script>
	</div>
	<?php endif; ?>
</div>
	<?php

}