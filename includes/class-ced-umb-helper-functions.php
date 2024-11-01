<?php
/**
 * The file that defines the global helper functions using throughout the plugin.
 *
 * @since      1.0.0
 *
 * @package    Woocommerce fruugo Integration
 * @subpackage Woocommerce fruugo Integration/includes
 */
class CED_FRUUGO_Helper {
	
	/**
	 * The instance of CED_FRUUGO_Helper.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private static $_instance;
	
	/**
	 * CED_FRUUGO_Helper Instance.
	 *
	 * Ensures only one instance of CED_FRUUGO_Helper is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @return CED_FRUUGO_Helper - Main instance.
	 */
	public static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	/**
	 * print notices.
	 * 
	 * @since 1.0.0
	 */
	public function umb_print_notices($notices=array()){
		if(count($notices)){
			foreach($notices as $notice_array){

				$message = isset($notice_array['message']) ? esc_html($notice_array['message']) : '';
				$classes = isset($notice_array['classes']) ? esc_attr($notice_array['classes']) : 'error is-dismissable';
				if(!empty($message)){ ?>
				<div class="<?php echo $classes;?>">
					<p><?php echo $message;?></p>
				</div>
				<?php 	
			}
		}
	}
}

	/**
	 * get conditional product id.
	 * 
	 * @since 1.0.0
	 */
	public function umb_get_product_by($params){
		global $wpdb;

		$where = '';
		if(count($params)){
			$Flag = false;
			foreach($params as $meta_key=>$meta_value){
				if(!empty($meta_value) && !empty($meta_key)){
					if(!$Flag){
						$where .= 'meta_key="'.sanitize_key($meta_key).'" AND meta_value="'.$meta_value.'"';
						$Flag = true;
					}else{
						$where .= ' OR meta_key="'.sanitize_key($meta_key).'" AND meta_value="'.$meta_value.'"';
					}
				}
			}
			if($Flag){
				$product_id = $wpdb->get_var( "SELECT post_id FROM $wpdb->postmeta WHERE $where LIMIT 1" );
				if($product_id)
					return $product_id;
			}
		}
		return false;
	}
	

	/**
	 * get profile details,
	 * 
	 * @since 1.0.0
	 */
	public function ced_fruugo_profile_details( $params=array() ){
		global $wpdb;
		$profile_name = "";
		if(isset($params['id'])){
			$id = $params['id'];
			$prefix = $wpdb->prefix . CED_FRUUGO_PREFIX;
			$tablename = $prefix.'_fruugoprofiles';
			$profile_name = $wpdb->get_var("SELECT `name` FROM `$tablename` WHERE `id` = '$id'");
		}
		return $profile_name;
	}
}
?>