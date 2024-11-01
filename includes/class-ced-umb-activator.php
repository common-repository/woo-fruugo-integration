<?php

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Woocommerce fruugo Integration
 * @subpackage Woocommerce fruugo Integration/includes
 */
class CED_FRUUGO_Activator {

	/**
	 * Activation actions.
	 *
	 * All required actions on plugin activation.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		self::create_tables();
		self::register();
	}
	
	/**
	 * Tables necessary for this plugin.
	 * 
	 * @since 1.0.0
	 */
	private static function create_tables(){
		
		global $wpdb;
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		
		define( 'CED_FRUUGO_TABLE_PREFIX' , 'ced_fruugo' );
		$prefix = $wpdb->prefix . CED_FRUUGO_TABLE_PREFIX;
		$table_name = "{$prefix}_fruugoprofiles";
		// profile table
		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            $create_profile = "CREATE TABLE {$prefix}_fruugoprofiles (id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,`name` VARCHAR(255) NOT NULL DEFAULT '',active bool NOT NULL DEFAULT true,marketplace VARCHAR(255) DEFAULT 'fruugo',profile_data longtext DEFAULT NULL,profile_required_attribute longtext DEFAULT NULL,PRIMARY KEY (id));";
            dbDelta( $create_profile );
        }
		
		update_option('ced_fruugo_database_version',CED_FRUUGO_VERSION);
	}
	
	private static function register()
    {
    	$domain_register = get_option( 'register_fru_domain', null );
    	if(empty($domain_register)){
    		update_option('register_fru_domain' , 'yes');
	    	$admin_email = get_option( 'admin_email', null );
	    	$domain = $_SERVER['HTTP_HOST'];
	    	$data =  array('domain'=>$domain,'email'=>$admin_email,'framework'=>'Woocommerce');
	       $url = 'http://admin.apps.cedcommerce.com/magento-fruugo-info/create?'.http_build_query($data);
	       $args = array(
				'method' => 'GET',
				'timeout' => 45,
				'redirection' => 5,
				'httpversion' => '1.0',
				'headers' => array(
					'Content-Type' => 'application/json',
					),
				'body' =>  $data,
				'sslverify' => false
				);      
			$response      = wp_remote_post( $url, $args );
			
	       return $response;
    	}
    }


}
?>