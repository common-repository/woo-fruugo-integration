<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * plugin admin pages related functionality helper class.
 *
 * @since      1.0.0
 *
 * @package    Woocommerce fruugo Integration
 * @subpackage Woocommerce fruugo Integration/admin/helper
 */

if( !class_exists( 'CED_FRUUGO_menu_page_manager' ) ) :

/**
 * Admin pages related functionality.
 *
 * Manage all admin pages related functionality of this plugin.
 *
 * @since      1.0.0
 * @package    Woocommerce fruugo Integration
 * @subpackage Woocommerce fruugo Integration/admin/helper
 * @author     CedCommerce <cedcommerce.com>
 */
class CED_FRUUGO_menu_page_manager{
	
	/**
	 * The Instace of CED_FRUUGO_menu_page_manager.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      $_instance   The Instance of CED_FRUUGO_menu_page_manager class.
	 */
	private static $_instance;
	
	/**
	 * CED_FRUUGO_menu_page_manager Instance.
	 *
	 * Ensures only one instance of CED_FRUUGO_menu_page_manager is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @return CED_FRUUGO_menu_page_manager instance.
	 */
	public static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	/**
	 * Creating admin pages of Woocommerce fruugo Integration.
	 * 
	 * @since 1.0.0
	 */
	public function create_pages(){

		add_menu_page('fruugo', 'Fruugo', __('manage_woocommerce','ced-fruugo'), 'umb-fruugo-main', array( $this, 'ced_fruugo_marketplace_page' ),'', 60 );
		
		add_submenu_page('umb-fruugo-main', __('Configuration','ced-fruugo'), __('Configuration','ced-fruugo'), 'manage_woocommerce', 'umb-fruugo-main', array( $this, 'ced_fruugo_marketplace_page' ) );
		add_submenu_page('umb-fruugo-main', __('Category Mapping','ced-fruugo'), __('Category Mapping','ced-fruugo'), 'manage_woocommerce', 'umb-fruugo-cat-map', array( $this, 'ced_fruugo_category_map_page' ) );
		
		add_submenu_page('umb-fruugo-main', __('Profile','ced-fruugo'), __('Profile','ced-fruugo'), 'manage_woocommerce', 'umb-fruugo-profile', array( $this, 'ced_fruugo_profile_page' ) );
		
		add_submenu_page('umb-fruugo-main', __('Manage Products','ced-fruugo'), __('Manage Products','ced-fruugo'), 'manage_woocommerce', "umb-fruugo-pro-mgmt", array( $this, 'ced_fruugo_pro_mgmt_page' ) );
		
		add_submenu_page('umb-fruugo-main', __('Bulk Action','ced-fruugo'), __('Bulk Action','ced-fruugo'), 'manage_woocommerce', "umb-fruugo-bulk-action", array( $this, 'ced_fruugo_bulk_action' ) );
		
		add_submenu_page('umb-fruugo-main', __('Orders','ced-fruugo'), __('Orders','ced-fruugo'), 'manage_woocommerce', 'umb-fruugo-orders', array( $this, 'ced_fruugo_orders_page' ) );
	}

	/**
	 * Upload product in Bulk
	 * 
	 * @since 1.0.0
	 */
	
	public function ced_fruugo_bulk_action(){
		require_once CED_FRUUGO_DIRPATH.'admin/pages/bulk-action.php';
	}
	/**
	 * Marketplaces page.
	 * 
	 * @since 1.0.0
	 */
	public function ced_fruugo_marketplace_page()
	{
		require_once CED_FRUUGO_DIRPATH.'admin/pages/marketplaces.php';
	}

	/**
	 * Category mapping page panel.
	 * 
	 *  @since 1.0.0
	 */
	public function ced_fruugo_category_map_page(){
		
		require_once CED_FRUUGO_DIRPATH.'admin/pages/category_mapping.php';
	}
	
	/**
	 * Products management page panel.
	 *
	 *  @since 1.0.0
	 */
	public function ced_fruugo_pro_mgmt_page(){

		require_once CED_FRUUGO_DIRPATH.'admin/pages/manage_products.php';
	}
	
	/**
	 * Profile page for easy product uploading.
	 * 
	 * @since 1.0.0
	 */
	public function ced_fruugo_profile_page(){
		
		require_once CED_FRUUGO_DIRPATH.'admin/pages/profile.php';
	}
	
	/**
	 * Orders page.
	 * 
	 * @since 1.0.0
	 */
	public function ced_fruugo_orders_page(){
		
		require_once CED_FRUUGO_DIRPATH.'admin/pages/orders.php';
	}

}

endif;