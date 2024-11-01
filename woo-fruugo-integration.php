<?php
/**
 *
 * @since             1.0.0
 * @package           woo-fruugo-integration
 *
 * @wordpress-plugin
 * Plugin Name:       Product Lister for Fruugo
 * Description:       Configure Your Woocommerce Store to the fruugo store and sell your products easily.
 * Version:           1.0.1
 * Author:            CedCommerce <cedcommerce.com>
 * Author URI:        cedcommerce.com
 * Text Domain:       ced-fruugo
 * Domain Path:       /languages
  * WC requires at least: 3.0.0
 * WC tested up to: 3.4.2
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ced-umb-activator.php
 * @name activate_ced_fruggo
 * @author CedCommerce
 * @since 1.0.0
 */

	function activate_woocommerce_fruugo_integration() {

		require_once plugin_dir_path( __FILE__ ) . 'includes/class-ced-umb-activator.php';
		CED_FRUUGO_Activator::activate();
	}

	register_activation_hook( __FILE__, 'activate_woocommerce_fruugo_integration' );

	/**
	 * The core plugin class that is used to define internationalization,
	 * admin-specific hooks, and public-facing site hooks.
	 */
	require plugin_dir_path( __FILE__ ) . 'includes/class-ced-umb.php';

	/**
	* This file includes core functions to be used globally in plugin.
	* @author CedCommerce <plugins@cedcommerce.com>
	* @link  http://www.cedcommerce.com/
	*/
	require_once plugin_dir_path(__FILE__).'includes/ced_umb_core_functions.php';

	/**
	 * Check WooCommerce is Installed and Active.
	 *
	 * since Woocommerce fruugo Integration is extension for WooCommerce it's necessary,
	 * to check that WooCommerce is installed and activated or not,
	 * if yes allow extension to execute functionalities and if not
	 * let deactivate the extension and show the notice to admin.
	 * 
	 * @author CedCommerce
	 */
	if(ced_fruugo_check_woocommerce_active()){

		run_ced_umb_fruugo();
	}else{

		add_action( 'admin_init', 'deactivate_ced_fruugo_woo_missing' );
	}

?>