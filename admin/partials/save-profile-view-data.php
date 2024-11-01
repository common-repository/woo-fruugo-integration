<?php
if(!session_id()) {
	session_start();
}
//add meta keys and assign to profile
global $wpdb;
$table_name = $wpdb->prefix.CED_FRUUGO_PREFIX.'_fruugoprofiles';
if( is_array( $_POST ) && !empty( $_POST ) && current_user_can('administrator') ) {
	
	$profileid = isset($_POST['profileID']) ? sanitize_text_field($_POST['profileID']) : false;
	$profileName = isset($_POST['profile_name']) ? sanitize_text_field($_POST['profile_name']) : '';
	if($profileName==''){
		$notice['message'] = __('Please fill profile name first.','ced-fruugo');
		$notice['classes'] = "notice notice-success";
		$validation_notice[] = $notice;
		$_SESSION['ced_fruugo_validation_notice'] = $validation_notice;
		return;
		
	}
	$is_active = isset($_POST['enable']) ? '1' : '0';
	$marketplaceName = isset($_POST['marketplaceName']) ? sanitize_text_field($_POST['marketplaceName']) : 'all';
	
	$updateinfo = array();
	
	foreach ($_POST['ced_fruugo_required_common'] as $key) {
		$arrayToSave = array();
		isset($_POST[$key][0]) ? $arrayToSave['default']= sanitize_text_field($_POST[$key][0]) : $arrayToSave['default'] ='';
		if($key == '_umb_'.$marketplaceName.'_subcategory') {
			isset($_POST[$key]) ? $arrayToSave['default'] = sanitize_text_field($_POST[$key]) : $arrayToSave['default']='';
		}
		isset($_POST[$key.'_attibuteMeta']) ? $arrayToSave['metakey'] =sanitize_text_field($_POST[$key.'_attibuteMeta']) : $arrayToSave['metakey'] ='null';
		$updateinfo[$key] = $arrayToSave;
	}

	$updateinfo = apply_filters('ced_fruugo_save_additional_profile_info',$updateinfo);
	$updateinfo['selected_product_id'] = sanitize_text_field(isset($_POST['selected_product_id'])) ? sanitize_text_field($_POST['selected_product_id']) : '';
	$updateinfo['selected_product_name'] = sanitize_text_field(isset($_POST['ced_fruugo_pro_search_box'])) ? sanitize_text_field($_POST['ced_fruugo_pro_search_box']) : '';
	
	$updateinfo = json_encode($updateinfo);
	if($profileid)
	{
	//echo '<pre>';	print_r($profileName); echo '>>'; print_r($is_active); echo '>>'; print_r($updateinfo); die('>>');
		$wpdb->update($table_name, array('name'=>$profileName,'active'=>$is_active,'marketplace'=>'fruugo','profile_data'=>$updateinfo),array('id'=>$profileid));
		
		$notice['message'] = __('Profile Updated Successfully.','ced-fruugo');
		$notice['classes'] = "notice notice-success";
		$validation_notice[] = $notice;
		$_SESSION['ced_fruugo_validation_notice'] = $validation_notice;
		
	}
	else
	{
		$wpdb->insert($table_name, array('name'=>$profileName,'active'=>$is_active,'marketplace'=>'fruugo','profile_data'=>$updateinfo));
		global $wpdb;
		$prefix = $wpdb->prefix . CED_FRUUGO_PREFIX;
		$tableName = $prefix.'_fruugoprofiles';
		$sql = "SELECT * FROM `".$tableName."` ORDER BY `id` DESC";
		$queryData = $wpdb->get_results($sql,'ARRAY_A');
		$profileid = $queryData[0]['id'];
		$notice['message'] = __('Profile Created Successfully.','ced-fruugo');
		$notice['classes'] = "notice notice-success";
		$validation_notice[] = $notice;
		$_SESSION['ced_fruugo_validation_notice'] = $validation_notice;
		
		$redirectURL = get_admin_url().'admin.php?page=umb-fruugo-profile&action=edit&message=created&profileID='.$profileid;
		wp_redirect($redirectURL);
		die;
	}
}
?>