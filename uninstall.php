<?
// Uninstall Script for ZappBar //

if( !defined( 'ABSPATH') && !defined('WP_UNINSTALL_PLUGIN') )
    exit();
 
	delete_option('mpml_options');

function uninstallMsg()
{
echo '<div class="error">
       <p>MP Membeship Limit was unable to delete some database entries</p>
    </div>';
}  

add_action('admin_notices', 'uninstallMsg');

?>
