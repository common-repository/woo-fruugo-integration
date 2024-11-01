<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}
global $cedumbfruugohelper;
$current_page = 'umb-fruugo';

if(isset($_GET['page'])){
	$current_page = $_GET['page'];
}
?>
<div id="ced_fruugo_marketplace_loader" class="loading-style-bg" style="display: none;">
	<img src="<?php echo plugin_dir_url(__dir__);?>/images/BigCircleBall.gif">
</div>
<div id="ced_fruugo_marketplace_paid">
	<a href="https://cedcommerce.com/woocommerce-extensions/woocommerce-fruugo-integration"><img src="<?php echo plugin_dir_url(__dir__);?>/images/fruugo_image.jpg"></a>
</div>
<?php 
if($current_page =="umb-fruugo-main"){
	
	$activated_marketplaces = ced_fruugo_available_marketplace();
	if(isset($_POST['ced_fruugo_save_credentials_button']) && current_user_can('administrator')){
		if($_POST['ced_fruugo_username_string'] == '' && $_POST['ced_fruugo_password_string'] == ''){
			$validation_notice = array();
			$notice['message'] = __('Please fill details','ced-fruugo');
			$notice['classes'] = "notice notice-error";
			$validation_notice[] = $notice;
		}else{

			$validation_notice = array();
			$notice['message'] = __('Configuration Setting Saved','ced-fruugo');
			$notice['classes'] = "notice notice-success";
			$validation_notice[] = $notice;
		}

	}
	if(isset($validation_notice) && count($validation_notice)){

		$cedumbfruugohelper->umb_print_notices($validation_notice);
		unset($validation_notice);
	}	
}
?>