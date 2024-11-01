<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}
global $wpdb, $cedumbfruugohelper;
require_once CED_FRUUGO_DIRPATH.'admin/pages/header.php';

$notices = array();
if(isset($_POST['umb_fetch_fruugo_order']) && current_user_can('administrator')){
	$message = __('Please get the latest plugin from our site','ced-fruugo');
	$classes = "error is-dismissable";
	$error = array('message'=>$message,'classes'=>$classes);
	$notices[] = $error;
}
if(count($notices)){
	$cedumbfruugohelper->umb_print_notices($notices);
	unset($notices);
}
?>
<div class="ced_fruugo_wrap">
	<h2 class="ced_fruugo_setting_header"><?php _e('Manage Orders','ced-fruugo'); ?></h2>
	<img width="" height="" src=<?php echo CED_FRUUGO_URL ?>'admin/images/order.jpg'>
</div>
