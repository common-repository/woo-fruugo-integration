<?php
if(!class_exists('Ced_fruugo_ajax_handler')){
	class Ced_fruugo_ajax_handler{
		
		/**
		 * construct
		 * @version 1.0.0
		 */
		public function __construct()
		{			
			add_action( 'wp_ajax_ced_fruugo_fetchCat', array ( $this, 'ced_fruugo_fetchCat' ));
			add_action( 'wp_ajax_ced_fruugo_process_fruugo_cat', array ( $this, 'ced_fruugo_process_fruugo_cat' ));
			add_action('ced_fruugo_required_fields_process_meta_simple', array($this,'ced_fruugo_required_fields_process_meta_simple'), 11, 1 );
			add_action('ced_fruugo_required_fields_process_meta_variable', array($this,'ced_fruugo_required_fields_process_meta_variable'), 11, 1 );
			add_filter( 'umb_save_additional_profile_info', array( $this, 'umb_save_additional_profile_info' ), 11, 1 );
		}
		

		public function ced_fruugo_extra_action($actions){
			$actions['update'] = "Update";
			$actions['delete'] = "Remove from fruugo";
			$actions['deactivate'] = "Deactivate";
			return $actions;
		}
		
		/**
		 * Save Profile Information
		 *
		 * @name umb_save_additional_profile_info
		 * @author CedCommerce <plugins@cedcommerce.com>
		 * @since 1.0.0
		 */
		
		public function umb_save_additional_profile_info( $profile_data ) {
			if(isset($_POST['ced_fruugo_attributes_ids_array']) && current_user_can('administrator')) {
				foreach ($_POST['ced_fruugo_attributes_ids_array'] as $key ) {
					if(isset($_POST[$key])) {
						$fieldid = isset($key) ? sanitize_text_field($key) : '';
						$fieldvalue = isset($_POST[$key]) ? sanitize_text_field($_POST[$key][0]) : null;
						$fieldattributemeta = isset($_POST[$key.'_attibuteMeta']) ? sanitize_text_field($_POST[$key.'_attibuteMeta']) : null;
						$profile_data[$fieldid] = array('default'=>$fieldvalue,'metakey'=>$fieldattributemeta);
					}
				}
			}
			return $profile_data;
		}
		/**
		 * @name ced_fruugo_fetchCat
		 * function to request for category fetching
		 *
		 * @version 1.0.0
		 */
		public function ced_fruugo_fetchCat()
		{

			$nonce = isset($_POST['_nonce']) ? sanitize_text_field($_POST['_nonce']) : "";
			$nextLevelCategories = array();
			if( $nonce == 'ced_fruugo_fetch_next_level' && current_user_can('administrator'))
			{
				/* $_POST['catDetails']  array */
				$catDetails = isset( $_POST['catDetails'] ) ? $_POST['catDetails'] : array();
				// print_r($catDetails);die;
				$catLevel = isset( $catDetails['catLevel'] ) ? $catDetails['catLevel'] : '';
				$catID = isset( $catDetails['catID'] ) ? $catDetails['catID'] : '' ;
				$catName = isset( $catDetails['catName'] ) ? $catDetails['catName'] : '' ;
				$parentCatName = isset( $catDetails['parentCatName'] ) ? $catDetails['parentCatName'] : '' ;
				if( $catID != '' )
				{
					$folderName = CED_FRUUGO_DIRPATH.'marketplaces/fruugo/lib/json/';
					$catFirstLevelFile = $folderName.'category.json';
					// print_r($catFirstLevelFile);die;
					if(file_exists($catFirstLevelFile)){
						$catFirstLevel = file_get_contents($catFirstLevelFile);
						$catFirstLevel = json_decode($catFirstLevel,true);
					}
					// print_r($catFirstLevel);die;
					$catLevel_next = $catLevel + 1;
					$lev_cat = 'level'.$catLevel_next;
					$lev_par_cat  = 'level'.$catLevel;
					foreach ($catFirstLevel as $key => $value) 
					{
						if($value[$lev_par_cat] == $catName)
						{
							$nextLevelCategories[] = $value[$lev_cat];
							$nextLevelCategories = array_unique($nextLevelCategories);
							$cat_end = $catLevel_next+1;
							$selectedCategories[$value[$lev_cat]] = $value['level'.$cat_end];
						}
					}
					$savedCategories = get_option('ced_fruugo_selected_categories');
					if( is_array( $nextLevelCategories ) && !empty( $nextLevelCategories ) )
					{
						echo json_encode( array( 'status' => '200', 'nextLevelCat' => $nextLevelCategories, 'selectedCat' => $selectedCategories ,'savedCategories' =>$savedCategories ) );
						wp_die();
					} 
				}
			}
			wp_die();
		}
		/**
		 * function to process selected categories
		 * @name ced_fruugo_process_fruugo_cat
		 * 
		 */
		public function ced_fruugo_process_fruugo_cat(){
			$nonce = isset($_POST['_nonce']) ? sanitize_text_field($_POST['_nonce']) : false;
			if($nonce == 'ced_fruugo_save' && current_user_can('administrator')){
				$cat = isset($_POST['cat']) ? $_POST['cat'] : false;
				$catID = isset($cat['catID']) ? $cat['catID'] : false;
				$catName = isset($cat['catName']) ? $cat['catName'] : false;
				$catID = trim($catName);
				$catID = preg_replace('/\s+/', '', $catID);
				$catName = preg_replace('/\s+/', '', $catName);
				if($catID && $catName){
					$savedCategories = get_option('ced_fruugo_selected_categories');
					$savedCategories = isset($savedCategories) ? $savedCategories :array();
					$savedCategories[$catID]=$catName;
					if(update_option('ced_fruugo_selected_categories', array_unique($savedCategories))){
						echo json_encode(array('status'=>'200'));die;
					}
					echo json_encode(array('status'=>'400'));die;
				}
				echo json_encode(array('status'=>'401'));die;
			}
			if($nonce == 'ced_fruugo_remove'){
				$cat = isset($_POST['cat']) ? sanitize_text_field($_POST['cat']) : false;
				$catID = isset($cat['catName']) ? trim($cat['catName']) : false;
				$catID = preg_replace('/\s+/', '', $catID);
				if($catID){
					$savedCategories = get_option('ced_fruugo_selected_categories');
					$savedCategories = isset($savedCategories) ? $savedCategories :array();
					// print_r( $savedCategories );
					if(is_array($savedCategories) && !empty($savedCategories)){
						foreach ($savedCategories as $key=>$value){
							if(trim($key) == $catID){
								unset($savedCategories[$key]);
							}
						}
					}
					if(update_option('ced_fruugo_selected_categories', array_unique($savedCategories))){
						echo json_encode(array('status'=>'200'));die;
					}
					echo json_encode(array('status'=>'400'));die;
				}
				echo json_encode(array('status'=>'401'));die;
			}
		}
		/**
		 * Process Meta data for Simple product
		 *
		 * @name ced_fruugo_required_fields_process_meta_simple
		 * @author CedCommerce <plugins@cedcommerce.com>
		 * @since 1.0.0
		 */
		
		function ced_fruugo_required_fields_process_meta_simple( $post_id ) {
			$marketPlace = 'ced_fruugo_attributes_ids_array';
			if(isset($_POST[$marketPlace])) {
				foreach ($_POST[$marketPlace] as $key => $field_name) {
					update_post_meta( $post_id, $field_name, sanitize_text_field( $_POST[$field_name][0] ) );
				}
			}
		}
		/**
		 * Process Meta data for variable product
		 *
		 * @name ced_fruugo_required_fields_process_meta_variable
		 * @author CedCommerce <plugins@cedcommerce.com>
		 * @since 1.0.0
		 */
		
		function ced_fruugo_required_fields_process_meta_variable( $postID ) {
			$marketPlace = 'ced_fruugo_attributes_ids_array';
			if(isset($_POST[$marketPlace])) {
				$attributesArray = array_unique($_POST[$marketPlace]);
				foreach ($attributesArray as $field_name) {
					foreach ($_POST['variable_post_id'] as $key => $post_id) {
						$field_name_md5  = md5( $field_name );
						if(isset($_POST[$field_name_md5][$key])) {
							update_post_meta( $post_id, $field_name, sanitize_text_field( $_POST[$field_name_md5][$key] ) );
						}
					}
				}
			}
		}
	}
}