<?php
/*
Plugin Name: MP Membership Limit
Plugin URI:  https://github.com/kmhcreative/mp-membership-limit
Description: Adds ability to limit MemberPress Membership sign-ups.
Version: 	 0.1
Author: 	 K.M. Hansen
Author URI:  http://www.kmhcreative.com
License: 	 GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
GitHub Plugin URI: https://github.com/kmhcreative/mpml
GitHub Branch: master

Copyright 2022  K.M. Hansen  (email : software@kmhcreative.com)

==== Beta Version Disclaimer =====

This plugin should be considered experimental.
Use it on a production website at your own risk.

===================================
*/

if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}


/*
	ACTIVATION SETTINGS
*/

function mpml_activate($reset = false) {
	// version check - if not using minimum WP version, bail!
	$wp_version = get_bloginfo('version');
	if ($wp_version < 4.8) {
		global $pagenow;
		if ( is_admin() && $pagenow=="plugins.php" ) {
		echo "<div class='error'><p><b>ERROR:</b> MP Membership Limit is <em>activated</em> but requires <b>WordPress 4.9</b> or greater to work.  You are currently running <em>Wordpress <span style='color:red;'>".$wp_version."</span>,</em> please upgrade.</p></div>";
		}
		return;
	};
	// still here? Then lets set defaults!
	if ( $reset===true ) {
		delete_option('mpml_options');
	}

	$mpml_options = get_option('mpml_options');
	if (empty($mpml_options) || $reset == 'mpml_options' ) {
		$mpml_options = array(
			'limit_label'		=>	'0',
			'show_limit' 		=>	'0',
			'show_available'	=>	'0',
			'transaction'		=>	'complete',
			'validation_msg'	=>  '',
			'productpage_msg'	=> ''
		);
		update_option('mpml_options' , $mpml_options);
	}

};
register_activation_hook(__FILE__, 'mpml_activate');


// Plugin Info Function
function mpml_pluginfo($whichinfo = null) {
	global $mpml_pluginfo;
	if (empty($mpml_pluginfo) || $whichinfo == 'reset') {
		// Important to assign pluginfo as an array to begin with.
		$mpml_pluginfo = array();
		$mpml_addinfo = array(
				// plugin directory/url
				'plugin_file' => __FILE__,
				'plugin_url' => plugin_dir_url(__FILE__),
				'plugin_path' => plugin_dir_path(__FILE__),
				'plugin_basename' => plugin_basename(__FILE__),
				'version' => '0.1'
		);
		// Combine em.
		$mpml_pluginfo = array_merge($mpml_pluginfo, $mpml_addinfo);
	}
	if ($whichinfo) {
		if (isset($mpml_pluginfo[$whichinfo])) {
			return $mpml_pluginfo[$whichinfo];
		} else return false;
	}
	return $mpml_pluginfo;
}


/*	Only do stuff if MemberPress is installed and activated,
	in which case the following class will exist:
*/
if (class_exists( 'MeprBaseCtrl' )){
	/* 	ADD CHECK FOR LIMIT ON SIGN-UPS
		if $sold is provided it returns number of membership sign-ups sold	(integer)
		if $sold is omitted it returns whether or not membership is sold out (true|false)
	*/
	function mepr_membership_has_reached_limit( $membership_id, $limit, $sold = null ) {
		  global $wpdb;
  
		  $options = get_option( 'mpml_options' );
		  if ($options['transaction'] == 'complete'){
		  	$status = "'complete', 'confirmed'";
		  } else {
		  	$status = "'pending', 'complete', 'confirmed'";
		  }

		  $query = "SELECT count(DISTINCT user_id)
					  FROM {$wpdb->prefix}mepr_transactions
					  WHERE status IN({$status})
						AND (
						  expires_at IS NULL
						  OR expires_at = '0000-00-00 00:00:00'
						  OR expires_at >= NOW()
						)
						AND product_id = {$membership_id}";

		  $count = $wpdb->get_var($query);
		  if ($sold) {
			// inquiry if for number sold
			return $count;
		  } else {	
			// inquiry is for whether it is filled or not
			return ($count >= $limit);
		  }  
	}
	/* Load other parts depending on where we are */
	if ( is_admin() ) {
		// We are on the back end
		@require('functions/admin_functions.php');
		@require('options/mpml_options.php');
		@require('plugin-update-checker/plugin-update-checker.php');
			$mpmlUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
			'https://github.com/kmhcreative/mp-membership-limit',
				__FILE__,'mp-membership-limit'
			);
			$mpmlUpdateChecker->getVcsApi()->enableReleaseAssets();	
	} else {
		// We are on the front end
		@require('functions/front_functions.php');
	}
} else {
	echo "<div class='error'><p><b>ERROR:</b> MP Membership Limit is <em>activated</em> but requires <b>MemberPress</b> to work, which was not found.  Please activate the MemberPress plugin.</p></div>";
};

?>