<?php
/*
	FRONT FUNCTIONS
	===============
*/
//	Check if full on sign-up validation
function mepr_limit_signups_for_membership($errors) {
	if (isset($_POST['mepr_product_id'])){
  		$membership_id = $_POST['mepr_product_id'];
  		$limit = get_post_meta($membership_id, '_mepr_membership_limit_signups', TRUE);
  		
  		$options = get_option( 'mpml_options' );
  		if (!empty( $options['validation_msg'] )){
  			$msg = $options['validation_msg'];
  		} else {
  			$msg = __('Sorry, our signup limit of ' . $limit . ' members has been reached. No further signups are allowed.', 'memberpress');
  		}

		if(!empty($limit) && mepr_membership_has_reached_limit($membership_id, $limit)) {
			$errors[] = $msg;
		}
	};
	return $errors;
}
add_filter('mepr-validate-signup', 'mepr_limit_signups_for_membership');


//	Override Sign-Up Form Content if FULL
function mepr_maybe_override_membership_signup_form($content) {
	global $post;
  
	$membership_id = $post->ID;
	$limit = get_post_meta($post->ID, '_mepr_membership_limit_signups', TRUE);
	
	if (!empty($limit)){ // only do this if limit isn't empty

		$options = get_option( 'mpml_options' );
		if ($options['limit_label'] == '2') {
			$label = __( 'Seats' );
		} else if ( $options['limit_label'] == '1' ){
			$label = __( 'Tickets' );
		} else {
			$label = __( 'Sign-Ups' );
		}  
	
		if (!empty( $options['validation_msg'] )){
			$msg = $options['validation_msg'];
		} else {
			if(!empty($limit)){ $plus = ' of '.$limit; } else { $plus = ''; }
			$msg = 'Sorry, registration has reached its capacity'.$plus.'.';
		}

	  if( isset($post) && $post instanceof WP_Post && mepr_membership_has_reached_limit($membership_id, $limit) ) {
		ob_start();
		?>
		  <div class="mepr_error" id="registration_full">
			<ul>
				<li><?php echo $msg; ?></li>
			</ul>
		  </div>
		  <div class="mp_wrapper" id="bogus_form" style="position:relative;opacity:.5;color:#666;">
			<div class="mp-form-row mepr_first_name">
				<div class="mp-form-label">
					<label for="user_first_name1">First Name:</label>
				</div>
				<input type="text" class="mepr-form-input">
			</div>
			<div class="mp-form-row mepr_last_name">
				<div class="mp-form-label">
					<label for="user_last_name1">Last Name:</label>
				</div>
				<input type="text"  class="mepr-form-input" value="">
			</div>
		<?php if( !is_user_logged_in() ){ ?>
			<div class="mp-form-row mepr_username">
				<div class="mp-form-label">
					<label for="user_login1">Username:*</label>
				</div>
				<input type="text"  class="mepr-form-input" value="">
			</div>
			<div class="mp-form-row mepr_email">
				<div class="mp-form-label">
					<label for="user_email1">Email:*</label>
				</div>
				<input type="email" class="mepr-form-input" value=""">
			</div>
			<div class="mp-form-row mepr_password">
				<div class="mp-form-label">
					<label for="mepr_user_password1">Password:*</label>
				</div>
				<div class="mp-hide-pw">
					<input type="password"  class="mepr-form-input mepr-password" value="">
					<button type="button" class="button mp-hide-pw hide-if-no-js" data-toggle="0" aria-label="Show password">
						<span class="dashicons dashicons-visibility" aria-hidden="true"></span>
					</button>
				</div>
			</div>
			<div class="mp-form-row mepr_password_confirm">
				<div class="mp-form-label">
					<label for="mepr_user_password_confirm1">Password Confirmation:*</label>
				</div>
				<div class="mp-hide-pw">
					<input type="password" class="mepr-form-input mepr-password-confirm" value="">
					<button type="button" class="button mp-hide-pw hide-if-no-js" data-toggle="0" aria-label="Show password">
						<span class="dashicons dashicons-visibility" aria-hidden="true"></span>
					</button>
				</div>
			</div>
		<?php }; ?>
			<div class="mepr_spacer">&nbsp;</div>
			<div class="mp-form-submit">
				  <input type="button" class="mepr-submit" value="Sign Up">
			</div>
			<div style="position:absolute;top:0;bottom:0;left:0;right:0;"></div>
		</div>
		<?php
		$content = ob_get_clean();
	  } else {
			$available = '';
			$total = '';
			if ($options['show_available'] == '1'){
				$available = $limit - mepr_membership_has_reached_limit($membership_id, $limit, '1');
			}
			if ($options['show_limit'] == '1'){
				$total = $limit;
			}
			if (!empty($available) && !empty($total)){
				$content .= '<p class="mepr_show_limit"><span class="mepr_available">'.$available.'</span> of <span class="mepr_total">'.$total.'</span> <span class="mepr_limit_label">'.$label.'</span> Available</p>';
			} else if ( !empty($available) && empty($total)){
				$content .= '<p class="mepr_show_limit"><span class="mepr_available">'.$available.'</span> <span class="mepr_limit_label">'.$label.'</span> Available</p>';
			} else if ( !empty($total) && empty($available)){
				$content .= '<p class="mepr_show_limit">Registration is limited to <span class="mepr_total">'.$total.'</span> <span class="mepr_limit_label">'.$label.'</span></p>';
			} else {
				// they're both empty so do nothing
			}
	  }; // end limit reached check
	}; // end limit set check

  return $content;
}
add_filter('the_content', 'mepr_maybe_override_membership_signup_form', 9999999, 1);


?>