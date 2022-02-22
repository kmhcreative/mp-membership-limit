<?php
/*
	OPTIONS PAGE
	============
*/
function mpml_register_settings() {
	register_setting( 'mpml_options', 'mpml_options', 'mpml_options_validate' );
}
add_action( 'admin_init', 'mpml_register_settings' );

function mpml_settings_menu() {
	add_submenu_page( 'memberpress', __( 'MP Membership Limit Options' ), __( 'Membership Limits' ), 'manage_options', 'mpml_settings', 'mpml_settings_page', 0 );
}
add_action( 'admin_menu', 'mpml_settings_menu');
// NOTE: you can also user 'mepr_menu' here, though it doesn't make any difference

function mpml_settings_page() {
?>

	<h1>MP Membership Limit Options</h1>
		<form method="post" action="options.php" class="options_form">
			<?php 
			settings_fields( 'mpml_options' ); 
			$options = get_option( 'mpml_options' );
			?>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row">
							<label for="limit_label"><?php _e( 'Limit Label' ); ?></label>
						</th>
						<td>
							<input type="radio" name="mpml_options[limit_label]" value="0" <?php if ( isset($options['limit_label']) ){ checked('0', $options['limit_label']);}; ?>/> Sign-Up<br/>
							<input type="radio" name="mpml_options[limit_label]" value="1" <?php if ( isset($options['limit_label']) ){ checked('1', $options['limit_label']);}; ?>/> Ticket<br/>
							<input type="radio" name="mpml_options[limit_label]" value="2" <?php if ( isset($options['limit_label']) ){ checked('2', $options['limit_label']);}; ?>/> Seat
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="show_limit"><?php _e( 'Show on Form' ); ?></label>
						</th>
						<td>
							<input type="checkbox" name="mpml_options[show_limit]" value="1" <?php if (isset($options['show_limit'])){ checked('1', $options['show_limit']);} ?>> Show Limit<br/>
							<input type="checkbox" name="mpml_options[show_available]" value="1" <?php if (isset($options['show_available'])){ checked('1', $options['show_available']);} ?>>Show Available
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="transaction"><?php _e( 'Fill Rule' ); ?></label>
						</th>
						<td>
							<p>A sign-up is considered "filled" when the transaction status is:</p>
							<input type="radio" name="mpml_options[transaction]" value="pending" <?php if (isset($options['transaction'])){ checked('pending', $options['transaction']);} ?>/> Pending and Completed<br/>
							<input type="radio" name="mpml_options[transaction]" value="complete"<?php if (isset($options['transaction'])){ checked('complete', $options['transaction']);} ?>/> Completed Only<br/>
							<p><small>If you select "Pending and Completed" then both those paying online and offline will be drawing 
							from the same stock of sign-ups, and when an offline payment is marked as "complete" the overall count 
							of sign-ups won't change.<small></p>

							<p><small>If you select "Completed Only" then transactions with "Pending" status won't be counted as 
							"filled" sign-ups until they are marked "complete."  That means it is possible to accidentally oversell 
							sign-ups, though that's only a problem if you actually do have a specific limited capacity.</p></small>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="validation_msg"><?php _e( 'Validation "Sold Out" Message'); ?></label>
						</th>
						<td>
							<textarea name="mpml_options[validation_msg]" placeholder="Sorry, our signup limit has been reached. No further signups are allowed."><?php if (isset($options['validation_msg']) && !empty($options['validation_msg'])){ echo $options['validation_msg']; }; ?></textarea><br/>
							<small>It is possible two people might be filling out the sign-up form at the same time, but if it's for the last sign-up available only the person who submits their registration first will get it.  This is the message that is displayed to anyone else who submits the form after the sign-ups have already sold-out.</small>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="productpage_msg"><?php _e( 'Sign-Up Form "Sold Out" Message'); ?></label>
						</th>
						<td>
							<textarea name="mpml_options[productpage_msg]" placeholder="Sorry, registration has reached its capacity."><?php if (isset($options['productpage_msg']) && !empty($options['productpage_msg'])){ echo $options['productpage_msg']; }; ?></textarea><br/>
							<small>Instead of the sign-up form for the membership users will see this message. 
							A grayed-out dummy form will always appear after this message.</small>
						<td>
					</tr>
					<tr>
						<th scope="row"></td>
						<td>
							<h2>Multi-Tier Pricing</h2>
							<p>To make sure all users are purchasing from the same stock of available sign-ups you should 
							<strong>use the MemberPress Coupons</strong> feature to create different pricing tiers.</p>

							<p>You don't have to actually send the coupon codes out to your users because you can create a 
							link/button to a sign-up form with the coupon's discount already applied.  Then use MemberPress 
							show/hide shortcodes to selectively show a members-only link/button to your Members and hide it 
							from everyone else.</p>

							<p>If you used different membership products for pricing tiers each of them would be drawing from 
							a different stock of available sign-ups.  The only circumstance under which you'd intentionally do 
							this is to RESERVE a certain number of slots for a specific group.</p>
						</td>
					</tr>
				<tbody>
			</table>
			<?php submit_button(); ?>
		</form>

	<?php
}


?>