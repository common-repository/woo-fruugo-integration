<?php

require_once 'save-profile-view-data.php';

$profileID = (isset($_GET['profileID'])?$_GET['profileID']:'');
$profile_data = array();
if($profileID){
	$query = "SELECT * FROM `$table_name` WHERE `id`=$profileID";
	$profile_data = $wpdb->get_results($query,'ARRAY_A');
	if(is_array($profile_data)) {
		$profile_data = isset($profile_data[0]) ? $profile_data[0] : $profile_data;
		
		/* fetcing basic information */
		$profile_name = isset($profile_data['name']) ? esc_attr($profile_data['name']) : '';
		$enable = isset($profile_data['active']) ? $profile_data['active'] : false;
		$enable = ($enable) ? "yes" : "no";
		$marketplaceName = isset($profile_data['marketplace']) ? esc_attr($profile_data['marketplace']) : 'all';
		$all_marketplaces = fruugoget_enabled_marketplaces();
		array_unshift($all_marketplaces, 'all');

		$data = isset($profile_data['profile_data']) ? json_decode($profile_data['profile_data'],true) : array();
		
	}
}
else {
	/* fetcing basic information */
	$profile_name = isset($profile_data['name']) ? esc_attr($profile_data['name']) : '';
	$enable = isset($profile_data['active']) ? $profile_data['active'] : false;
	$enable = ($enable) ? "yes" : "no";
	$marketplaceName = isset($profile_data['marketplace']) ? esc_attr($profile_data['marketplace']) : 'null';
	$all_marketplaces = fruugoget_enabled_marketplaces();
	array_unshift($all_marketplaces, 'all');
}

echo '<form method="post" class="ced_fruugo_profile_save_form">';
echo '<div class="ced_fruugo_wrap ced_fruugo_wrap_opt">';
echo '<div class="back"><a href="'.get_admin_url().'admin.php?page=umb-fruugo-profile">Go Back</a></div>';
?>
<?php
global $cedumbfruugohelper;
if(!session_id()) {
	session_start();
}
if(isset($_SESSION['ced_fruugo_validation_notice'])) {
	$value = $_SESSION['ced_fruugo_validation_notice'];
	$cedumbfruugohelper->umb_print_notices($value);
	unset($_SESSION['ced_fruugo_validation_notice']);
}
?>

<div class="ced_fruugo_profile_timeline_wrapper">
	<div class="ced_fruugo_profile_timeline_heading">
		<?php _e( 'Steps to Follow', 'ced-fruugo' ); ?>
	</div>	
	<div class="ced_fruugo_profile_timeline">
		<ul class="ced_fruugo_profile_timeline_ul">
			<li title="<?php _e( 'Click to Select Metakeys', 'ced-fruugo' ); ?>" style="color: black;" data-target="ced_fruugo_select_metakeys_wrapper" class="ced_fruugo_profile_timeline_ul ced_fruugo_profile_metakeys_li active"><?php _e( 'Select MetaKeys', 'ced_fruggo' ); ?></li>
			<li data-target="ced_fruugo_profile_basic_information_wrapper" class="ced_fruugo_profile_timeline_ul ced_fruugo_profile_basic_detail_li"><?php _e( 'Basic Details', 'ced_fruggo' ); ?></li>
			<li data-target="ced_fruugo_profile_required_fields_wrapper" class="ced_fruugo_profile_timeline_ul ced_fruugo_profile_required_field_li"><?php _e( 'Required fields', 'ced_fruggo' ); ?></li>
			<li data-target="ced_fruugo_profile_required_fields_wrapper" class="ced_fruugo_profile_timeline_ul ced_fruugo_profile_category_li"><?php _e( 'Category', 'ced_fruggo' ); ?></li>
		</ul>
	</div>
	<!-- </div> -->

	<!-- <div class="ced_fruugo_profile_instruction_to_use_wrapper">
		<div class="ced_fruugo_profile_instruction_to_use_heading">
			<?php _e( 'Instruction To Use', 'ced-fruugo' ); ?>
		</div>	 -->
		<div class="ced_fruugo_profile_instruction_to_use">
			<p><?php _e( 'Profile can be created to assign similar type of values and categories to multiple products.', 'ced-fruugo' ); ?></p>
			<p class="ced_fruugo_sel_metakey"><?php _e( '1. Use "Select Product And Corresponding MetaKeys" section to select the metakeys of product you consider can be useful in mapping. Once you select the metakeys click on UPDATE. This step is not always necessary. If you have done it before, you can skip it for the next time you create a profile.', 'ced-fruugo' ); ?></p>
			<p class="ced_fruugo_basic_set"><?php _e( '2. Under "BASIC SETTINGS" tab, you have option to setup basic information for your profile. Here you can give your profile a name and enable/disable it.', 'ced-fruugo' ); ?></p>
			<p class="ced_fruugo_req_field"><?php _e( '3. Under "REQUIRED FIELDS" sections, you have to fill in all the details that are mandatory for the products.', 'ced-fruugo' ); ?></p>
			<p class="ced_fruugo_category_sel"><?php _e( '4. After Required Fields its time to select the fruugo category from "fruugo CATEGORY" section. Once you select the category you will get fields for variation products that need to be filled in if you wish to list variation products on fruugo', 'ced-fruugo' ); ?></p>
			<p><?php _e( '5. Once done with above steps you can fill in the Extra information that can be sent with the products under "EXTRA FIELDS" section.', 'ced-fruugo' ); ?></p>
			<p><?php _e( '6. If you have read above instructions carefully, you are good to go.', 'ced-fruugo' ); ?></p>
		</div>
	</div>
	<?php
	$products_IDs = array();
	$all_products = new WP_Query( 
		array(
			'post_type' => array('product', 'product_variation'),
			'post_status' => 'publish',
			'posts_per_page' => 10
			) 
		);
	$products = $all_products->posts;
	$selectedProID  = $all_products->posts['0']->ID;
	foreach ( $products as $product ) {
		$product_IDs[] = $product->ID;
	}
	
	if(isset($data['selected_product_id'])) {
		$selectedProID = $data['selected_product_id'];
		$selectedProName = $data['selected_product_name'];
	}
	else{
		$selectedProID = $product_IDs[0];
		$selectedProName = '';
	}
	
	?>
	<div class="ced_fruugo_profile_basic_information_wrapper" id="ced_fruugo_profile_basic_information_wrapper">
		<div class="ced_fruugo_profile_basic_information_heading">
			<?php _e( 'Basic Settings', 'ced-fruugo' ); ?>
		</div>
		<div class="ced_fruugo_profile_basic_information">
			<table>
				<tbody>
					<tr>
						<td>
							<span>
								<label>
									<?php _e( 'Profile Name', 'ced-fruugo' );
									?>
								</label>
								<input type="text" placeholder="<?php _e( 'Enter name for Profile', 'ced-fruugo' ); ?>" class="ced_fruugo_profile_name" name="profile_name"  value="<?php echo $profile_name; ?>"></input>
							</span>
						</td>
					</tr>
					<tr>
						<td>
							<?php $checked = ($enable == "yes") ? 'checked="checked"' : ''; ?>
							<span>
								<label>
									<?php _e( 'Enable Profile', 'ced-fruugo' );
									?>
								</label>
								<input type="checkbox" name="enable" id="ced_fruugo_enable_marketpalce" <?php echo $checked;?> >
							</span>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>


	<div class="ced_fruugo_profile_required_fields_wrapper" id="ced_fruugo_profile_required_fields_wrapper">
		<div class="ced_fruugo_profile_required_fields_heading">
			<?php _e( 'Required Fields', 'ced-fruugo' ); ?>
		</div>
		<div class="ced_fruugo_profile_required_fields">
			<?php
			$pFieldInstance = CED_FRUUGO_product_fields::get_instance();
			if(is_wp_error($pFieldInstance)){
				$message = _e('Something went wrong please try again later!','ced-fruugo');
				wp_die($message);
			}
			$fields = $pFieldInstance->get_custom_fields('required',false);
			?>
			<table>
				<tbody>
					<?php
					$requiredInAnyCase = array('_umb_id_type','_umb_id_val','_umb_brand');
					global $global_CED_FRUUGO_Render_Attributes;
					$marketPlace = "ced_fruugo_required_common";
					$productID = 0;
					$categoryID = '';
					$indexToUse = 0;
					$selectDropdownHTML= fruugorenderMetaSelectionDropdownOnProfilePage();
					// print_r($selectDropdownHTML);die('f');
					foreach ($fields as $value) {
						$isText = true;
						$field_id = trim($value['fields']['id'],'_');
						if(in_array($value['fields']['id'], $requiredInAnyCase)) {
							$attributeNameToRender = ucfirst($value['fields']['label']);
							$attributeNameToRender .= '<span class="ced_fruugo_wal_required"> [ Required ]</span>';
						}
						else {
							$attributeNameToRender = ucfirst($value['fields']['label']);
						}
						
						$default = isset($data[$value['fields']['id']]['default']) ? $data[$value['fields']['id']]['default'] : '';
						echo '<tr>';
						echo '<td>';
						if( $value['type'] == "_select" ) {
							$valueForDropdown = $value['fields']['options'];
							if($value['fields']['id'] == '_umb_id_type'){
								unset($valueForDropdown['null']);
							}
							$valueForDropdown = apply_filters('ced_fruugo_alter_data_to_render_on_profile', $valueForDropdown, $field_id);
							$global_CED_FRUUGO_Render_Attributes->renderDropdownHTML($field_id,$attributeNameToRender,$valueForDropdown,$categoryID,$productID,$marketPlace,$value['fields']['description'],$indexToUse,array('case'=>'profile','value'=>$default));
							$isText = false;
						}
						else if( $value['type'] == "_text_input" ) {
							$global_CED_FRUUGO_Render_Attributes->renderInputTextHTML($field_id,$attributeNameToRender,$categoryID,$productID,$marketPlace,$value['fields']['description'],$indexToUse,array('case'=>'profile','value'=>$default));
						}
						else {
							do_action('ced_fruugo_render_extra_data_on_profile', $value, $pFieldInstance);
							$isText = false;
						} 
						echo '</td>';
						echo '<td>';
						if($isText) {
							$previousSelectedValue = 'null';
							if( isset($data[$value['fields']['id']]['metakey']) && $data[$value['fields']['id']]['metakey'] != 'null') {
								$previousSelectedValue = $data[$value['fields']['id']]['metakey'];
							}
							$updatedDropdownHTML = str_replace('{{*fieldID}}', $value['fields']['id'], $selectDropdownHTML);
							$updatedDropdownHTML = str_replace('value="'.$previousSelectedValue.'"', 'value="'.$previousSelectedValue.'" selected="selected"', $updatedDropdownHTML);
							echo $updatedDropdownHTML;
						}
						echo '</td>';
						echo '</tr>';
					}	
					?>
				</tbody>
			</table>
		</div>
	</div>

	<div class="ced_fruugo_profile_extra_fields_wrapper">
		<div class="ced_fruugo_profile_extra_fields_heading">
			<?php _e( 'Extra Fields', 'ced-fruugo' ); ?>
		</div>
		<div class="ced_fruugo_profile_extra_fields">

			<?php
			$pFieldInstance = CED_FRUUGO_product_fields::get_instance();
			if(is_wp_error($pFieldInstance)){
				$message = _e('Something went wrong please try again later!','ced-fruugo');
				wp_die($message);
			}
			$fields = $pFieldInstance->get_custom_fields('extra_fields',false);
			?>
			<table>
				<tbody>
					<?php
					$requiredInAnyCase = array('_umb_id_type','_umb_id_val','_umb_brand');
					global $global_CED_FRUUGO_Render_Attributes;
					$marketPlace = "ced_fruugo_required_common";
					$productID = 0;
					$categoryID = '';
					$indexToUse = 0;
					$selectDropdownHTML= fruugorenderMetaSelectionDropdownOnProfilePage();
					// print_r($selectDropdownHTML);die('f');
					foreach ($fields as $value) {
						$isText = true;
						$field_id = trim($value['fields']['id'],'_');
						if(in_array($value['fields']['id'], $requiredInAnyCase)) {
							$attributeNameToRender = ucfirst($value['fields']['label']);
							$attributeNameToRender .= '<span class="ced_fruugo_wal_required"> [ Required ]</span>';
						}
						else {
							$attributeNameToRender = ucfirst($value['fields']['label']);
						}
						
						$default = isset($data[$value['fields']['id']]['default']) ? $data[$value['fields']['id']]['default'] : '';
						echo '<tr>';
						echo '<td>';
						if( $value['type'] == "_select" ) {
							$valueForDropdown = $value['fields']['options'];
							if($value['fields']['id'] == '_umb_id_type'){
								unset($valueForDropdown['null']);
							}
							$valueForDropdown = apply_filters('ced_fruugo_alter_data_to_render_on_profile', $valueForDropdown, $field_id);
							$global_CED_FRUUGO_Render_Attributes->renderDropdownHTML($field_id,$attributeNameToRender,$valueForDropdown,$categoryID,$productID,$marketPlace,$value['fields']['description'],$indexToUse,array('case'=>'profile','value'=>$default));
							$isText = false;
						}
						else if( $value['type'] == "_text_input" ) {
							$global_CED_FRUUGO_Render_Attributes->renderInputTextHTML($field_id,$attributeNameToRender,$categoryID,$productID,$marketPlace,$value['fields']['description'],$indexToUse,array('case'=>'profile','value'=>$default));
						}
						else {
							do_action('ced_fruugo_render_extra_data_on_profile', $value, $pFieldInstance);
							$isText = false;
						} 
						echo '</td>';
						echo '<td>';
						if($isText) {
							$previousSelectedValue = 'null';
							if( isset($data[$value['fields']['id']]['metakey']) && $data[$value['fields']['id']]['metakey'] != 'null') {
								$previousSelectedValue = $data[$value['fields']['id']]['metakey'];
							}
							$updatedDropdownHTML = str_replace('{{*fieldID}}', $value['fields']['id'], $selectDropdownHTML);
							$updatedDropdownHTML = str_replace('value="'.$previousSelectedValue.'"', 'value="'.$previousSelectedValue.'" selected="selected"', $updatedDropdownHTML);
							echo $updatedDropdownHTML;
						}
						echo '</td>';
						echo '</tr>';
					}	
					?>
				</tbody>
			</table>
		</div>
	</div>
	<?php
	echo '</div>';
	?>
	<div class="ced_fruugo_profile_all_wrapper">
		<div class="ced_fruugo_select_metakeys_wrapper" id="ced_fruugo_select_metakeys_wrapper">
			<div class="ced_fruugo_select_metakeys_heading">
				<?php _e('Select Product MetaKeys','ced-fruugo'); ?>	
			</div>	
			<div class="ced_fruugo_select_metakeys_section">
				<input type="hidden" name="profileID" id="profileID"value="<?php echo $profileID;?>">
				<div class="ced_fruugo_pro_search_div">
					<div class="ced_fruugo_inline_box">
						<label for="ced_fruugo_pro_search_box"><?php _e('Type Product Name Here','ced-fruugo'); ?></label>
						<div class="ced_fruugo_wrap_div">
							<input type="hidden" name="selected_product_id" id="selected_product_id" value="<?php echo $selectedProID;?>">
							<input type="text" autocomplete="off" id="ced_fruugo_pro_search_box" name="ced_fruugo_pro_search_box" placeholder="Product Name" value="<?php echo $selectedProName; ?>"/>
							<div id="ced_fruugo_suggesstion_box" style="display: none;"></div>
						</div>
						<img class="ced_fruugo_ajax_pro_search_loader" src="<?php echo CED_FRUUGO_URL.'admin/images/ajax-loader.gif'?>" style="display: none;">
					</div>	
				</div>
				<?php  fruggorenderMetaKeysTableOnProfilePage($selectedProID); ?>
			</div>
		</div>
		<div class="ced_fruugo_profile_submit_button">
			<h2><?php _e( 'SAVE PROFILE', 'ced-fruugo' ) ?></h2>
			<p class="ced_fruugo_button_right">
				<input class="button button-ced_fruggo ced_fruugo_save_profile_button" value="<?php _e('Save Profile','ced-fruugo'); ?>" name="saveProfile" type="submit">
			</p>
		</div>
	</div>
	<?php
	echo '</form>';
	?>
