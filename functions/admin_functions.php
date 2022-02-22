<?php
/*
	ADMIN FUNCTIONS
	===============
*/
/* ADD LIMIT METABOX TO MemberPress Product Edit */

function mepr_membership_limit_signups($membership) {
	$post = $membership;
	/* Gets any existing '_myfirst' meta box content */
	$mepr_membership_limit_signups = get_post_meta($post->ID, '_mepr_membership_limit_signups', TRUE);

	/* If there is none our variable is set to empty quotes */
	if (!$mepr_membership_limit_signups) $mepr_membership_limit_signups = '';

	/* Create the security nonce hidden input field */
	wp_nonce_field( $post->ID, 'mepr_membership_limits_signups_noncename');

	/* Now for our actual input box.  I'm using a textarea, but this could be
	whatever HTML input element you want to use and echo any existing
	content into it.*/
	?>

	<input id="_mepr_membership_limit_signups" name="_mepr_membership_limit_signups" type="number" min="0" value="<?php echo $mepr_membership_limit_signups; ?>"/>

<?php } 

function mepr_membership_limit_signups_metabox_data($membership) {
	$post_id = $membership->ID;
	/* check if this is an auto save.  If it is the form hasn't been submitted so bail early */
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
		return $post_id;

	/* verify that this came from the post edit page with proper authentication
		and  if any one of these fails the test bail and do nothing */
		if ( isset($_POST['mepr_membership_limit_signups_noncename']) && !wp_verify_nonce( $_POST['mepr_membership_limit_signups_noncename'], $post_id) ) {
			return $post_id;
		}
	
	/* Check the User Permissions for editing posts.  If not, we bail */
	if ( !current_user_can( 'edit_post', $post_id ) )
		return $post_id;

	/* If we got this far everything Authenticated so let's find and save some data */
	/* First lets get our post and put it into a variable */

	$post = get_post($post_id);

	/* Now check to make sure it is of our custom post_type */

	if ($post->post_type == 'memberpressproduct') {
		/* Now we actually update the post meta data.  If the field does not already exist
		it automatically calls "add_post_meta" instead.  The first parameter is the post_id
		the second is the actual name of our meta field in the database with leading underscore,
		the last parameter is referencing the content of our input field in HTML by name */

		update_post_meta($post_id, '_mepr_membership_limit_signups', $_POST['_mepr_membership_limit_signups']);

	}
	return $post_id;
}

function mepr_membership_custom_meta_box($membership) {
	$options = get_option( 'mpml_options' );
	if ($options['limit_label'] == '2') {
		$label = __( 'Seats' );
	} else if ( $options['limit_label'] == '1' ){
		$label = __( 'Tickets' );
	} else {
		$label = __( 'Sign-Ups' );
	}
	/* First parameter is just a name.  I used "_section" just to keep it clear that they are just arbitrary section names
	which don't seem to actually get used that I can see.  The next part is whatever title you want at the top of the
	Meta Box.  Third parameter is our custom post_type name.  Fourth is the "position" of the meta box, and lastly
	is the "priority" of how high up on the page it should be ("core" is right after the regular edit box). */
	add_meta_box( 'mepr_limit_signups_section',  __('Limit').' '.$label, 'mepr_membership_limit_signups', 'memberpressproduct','side','core');
}
// Use the MemberPress Action Hooks intended for this
add_action('mepr-membership-meta-boxes','mepr_membership_custom_meta_box');
add_action('mepr-membership-save-meta','mepr_membership_limit_signups_metabox_data');

/* 	NOTE: You can also accomplish this without using the MemberPress Action Hooks.
	All you need to do is change $membership in the function arguments to $post and $post_id,
	respectively, and delete or comment out the $post = $membership and $post_id = $membership->ID
	lines in each function, then use the following standard WordPress action hooks:
	
	add_action('admin_init', 'mepr_membership_custom_meta_box' );
	add_action('save_post','mepr_membership_limit_signups_metabox_data' );
*/

/* ADD MEMBERSHIP LIMIT DATA COLUMN TO MANAGE MEMBERSHIPS */
add_filter( 'manage_edit-memberpressproduct_columns', 'mepr_membership_signup_limit_column');

function mepr_membership_signup_limit_column( $columns ){
	$options = get_option( 'mpml_options' );
	if ($options['limit_label'] == '2') {
		$label = __( 'Seats' );
	} else if ( $options['limit_label'] == '1' ){
		$label = __( 'Tickets' );
	} else {
		$label = __( 'Sign-Ups' );
	}
	$columns['signup_limit'] = $label;
	return $columns;
}
add_action( 'manage_memberpressproduct_posts_custom_column', 'mepr_membership_signup_limit_column_data', 10, 2);

function mepr_membership_signup_limit_column_data( $column, $post_id ){
	global $post;
	switch ( $column ){
		case 'signup_limit' :
			$limit = get_post_meta($post->ID, '_mepr_membership_limit_signups', TRUE);
			$sold  = mepr_membership_has_reached_limit( $post->ID, $limit, TRUE);
			if (empty($limit)){ $limit = '∞';}
			echo $sold.' / '.$limit;
			break;
		default :
			break;
	}
}


function sanitizeSingle($string) {
	if (get_magic_quotes_gpc()){
		$string = stripslashes($string);
	}
		$string = wp_check_invalid_utf8( $string, true ); 
//		$string = wp_filter_nohtml_kses( $string );	// to strip out all html
		$string = wp_kses($string, wp_kses_allowed_html( 'post' )); // allow same html as posts 
	return $string;
}
function sanitize($string) {
	if (is_array($string)){
		foreach($string as $k => $v) {
			if (is_array($v)) {
				$string[$k] = sanitize($v);
			} else {
				$string[$k] = sanitizeSingle($v);
			}
		}
	} else {
		$string = sanitizeSingle($string);
	}
	return $string;
}
function mpml_options_validate($input) {
	$input = sanitize($input);
	return $input;
}
?>