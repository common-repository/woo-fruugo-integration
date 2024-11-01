<?php 
/**
 * main class for handling reqests.
 *
 * @since      1.0.0
 *
 * @package    Woocommerce fruugo Integration
 * @subpackage Woocommerce fruugo Integration/marketplaces/fruugo
 */

if( !class_exists( 'CED_FRUUGO_manager' ) ) :

	/**
	 * single product related functionality.
	*
	* Manage all single product related functionality required for listing product on marketplaces.
	*
	* @since      1.0.0
	* @package    Woocommerce fruugo Integration
	* @subpackage Woocommerce fruugo Integration/marketplaces/fruugo
	* @author     CedCommerce <cedcommerce.com>
	*/
	class CED_FRUUGO_manager{

		/**
		 * The Instace of CED_FRUUGO_fruugo_Manager.
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      $_instance   The Instance of CED_FRUUGO_fruugo_Manager class.
		 */
		private static $_instance;
		private static $authorization_obj;
		private static $client_obj;
		/**
		 * CED_FRUUGO_fruugo_Manager Instance.
		 *
		 * Ensures only one instance of CED_FRUUGO_fruugo_Manager is loaded or can be loaded.
		 *
		 * @author CedCommerce <plugins@cedcommerce.com>
		 * @since 1.0.0
		 * @static
		 * @return CED_FRUUGO_fruugo_Manager instance.
		 */
		public static function get_instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}
		
		public $marketplaceID = 'fruugo';
		public $marketplaceName = 'fruugo';
		
		
		/**
		 * Constructor.
		 *
		 * registering actions and hooks for fruugo.
		 *
		 * @author CedCommerce <plugins@cedcommerce.com>
		 * @since 1.0.0
		 */
		public function __construct() 
		{
			add_action('admin_init', array($this, 'ced_fruugo_required_files'));
			add_filter( 'ced_fruugo_render_marketplace_configuration_settings' , array( $this, 'ced_fruugo_render_marketplace_configuration_settings' ), 10, 3 );
			add_filter( 'ced_fruugo_required_product_fields', array( $this, 'add_fruugo_required_fields' ), 11, 2 );
			add_action('ced_fruugo_required_fields_process_meta_variable', array($this,'ced_fruugo_required_fields_process_meta_variable'), 11, 1 );
			add_action( 'ced_fruugo_render_different_input_type' , array( $this, 'ced_fruugo_render_different_input_type'), 10, 2 );
			/*loading scripts*/
			add_action( 'admin_enqueue_scripts',array($this,'load_fruugo_scripts'));
		}
		/**
		 * Marketplace Configuration Setting
		 *
		 * @name ced_fruugo_render_marketplace_configuration_settings
		 * @author CedCommerce <plugins@cedcommerce.com>
		 * @since 1.0.0
		 */
		function ced_fruugo_render_marketplace_configuration_settings( $configSettings, $marketplaceID, $saved_fruugo_details = array() )
		{
			if( $marketplaceID != $this->marketplaceID )
			{
				return $configSettings;
			}
			else
			{
				$configSettings=array();

				if(isset($_POST['ced_fruugo_save_credentials_button']) && current_user_can('administrator'))
				{
					$saved_fruugo_details = array();
					$userString = isset( $_POST['ced_fruugo_username_string'] ) ? sanitize_text_field($_POST['ced_fruugo_username_string']) : '';
					$saved_fruugo_details['userString'] = $userString;
					$passString = isset( $_POST['ced_fruugo_password_string'] ) ? sanitize_text_field($_POST['ced_fruugo_password_string']) : '';
					$saved_fruugo_details['passString'] = $passString;
					if(isset($saved_fruugo_details) )
					{
						update_option( 'ced_fruugo_details', $saved_fruugo_details);
						
					}
				}

				$ced_fruugo_save_details = get_option('ced_fruugo_details');
				$ced_fruugo_keystring = $ced_fruugo_save_details['userString'];
				$ced_fruugo_shared_string = $ced_fruugo_save_details['passString'];
				$configSettings['configSettings'] = array(
					'ced_fruugo_username_string' => array(
						'name' => __('Enter User Name', 'ced-fruugo'),
						'type' => 'text',
						'value' => $ced_fruugo_keystring
						),
					'ced_fruugo_password_string' => array(
						'name' => __('Enter Password', 'ced-fruugo'),
						'type' => 'text',
						'value' => $ced_fruugo_shared_string
						),
					'ced_fruugo_save_credentials_button' => array(
						'name' => __('Save Credentials', 'ced-fruugo'),
						'type' => 'ced_fruugo_save_credentials_button',
						'value' => ''
						),
					);
				
				$configSettings['showUpdateButton'] = false;
				$configSettings['marketPlaceName'] = $this->marketplaceName;
				return $configSettings;
			}
		}

		/**
		 * render different input types.
		 */
		function ced_fruugo_render_different_input_type( $type, $saved_fruugo_details = array() )
		{
			
			if( $type == 'ced_fruugo_save_credentials_button' ) {
				echo "<input type='submit' class='ced_fruugo_save_credentials_button button button-primary' value='Save Credentials' name='ced_fruugo_save_credentials_button'>";
			}
		}

		/**
		 * Include all required files 
		 */
		public function ced_fruugo_required_files(){
			if(is_file(CED_FRUUGO_DIRPATH.'marketplaces/fruugo/partials/class-ajax-handler.php')){
				require_once CED_FRUUGO_DIRPATH.'marketplaces/fruugo/partials/class-ajax-handler.php';
				$ajaxhandler = new Ced_fruugo_ajax_handler();
			}
			if(is_file(CED_FRUUGO_DIRPATH.'marketplaces/fruugo/partials/class-fruugo-upload.php')){
				require_once CED_FRUUGO_DIRPATH.'marketplaces/fruugo/partials/class-ajax-handler.php';
			}
		}
		/**
		 * function to enqueue scripts
		 * @name load_fruugo_scripts
		 * 
		 * @version 1.0.0
		 * 
		 */
		public function load_fruugo_scripts(){
			$screen    = get_current_screen();
			$screen_id    = $screen ? $screen->id : '';
			$param = isset($_GET['marketplaceID']) ? $_GET['marketplaceID'] : "";
			$action = isset($_GET['action']) ? $_GET['action'] : "";
			$page = isset($_GET['page']) ? $_GET['page'] : "";
			wp_enqueue_style( 'ced_fruugo_css', plugin_dir_url( __FILE__ ) . 'css/fruugo.css' );
			// print_r( $screen_id );die;
			if( $screen_id == 'toplevel_page_umb-fruugo-main' )
			{
				/**
				 ** woocommerce scripts to show tooltip :: start
				 */

				wp_register_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', array(), WC_VERSION );
				wp_enqueue_style( 'woocommerce_admin_menu_styles' );
				wp_enqueue_style( 'woocommerce_admin_styles' );

				$suffix = '';
				wp_register_script( 'woocommerce_admin', WC()->plugin_url() . '/assets/js/admin/woocommerce_admin' . $suffix . '.js', array( 'jquery', 'jquery-blockui', 'jquery-ui-sortable', 'jquery-ui-widget', 'jquery-ui-core', 'jquery-tiptip' ), WC_VERSION );
				wp_register_script( 'jquery-tiptip', WC()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip' . $suffix . '.js', array( 'jquery' ), WC_VERSION, true );
				wp_enqueue_script( 'woocommerce_admin' );

				wp_enqueue_style( 'ced-fruugo-style-jqueru-ui', plugin_dir_url( __FILE__ ) . 'css/jquery-ui.css' );
				wp_enqueue_script( 'jquery-ui-datepicker' );
				/**
				 ** woocommerce scripts to show tooltip :: end
				 */
			}
			
			wp_register_script( 'ced_fruugo_cat', plugin_dir_url( __FILE__ ) . 'js/category.js', array( 'jquery' ), time(), true );
			$localization_params = array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'plugins_url'=> CED_FRUUGO_URL,
				);
			wp_localize_script( 'ced_fruugo_cat', 'ced_fruugo_cat', $localization_params );
			wp_enqueue_script('ced_fruugo_cat');
			
			$screen    = get_current_screen();
			$screen_id    = $screen ? $screen->id : '';
			if ( in_array( $screen_id, array( 'edit-product','product' ) ) ) {
				wp_register_script( 'ced_fruugo_edit_product', plugin_dir_url( __FILE__ ) . 'js/product-edit.js',array( 'jquery' ), time(), true);
				global $post;
				if( !empty($post) )
				{
					wp_localize_script( 'ced_fruugo_edit_product', 'ced_fruugo_edit_product_script_AJAX', array(
						'ajax_url' => admin_url( 'admin-ajax.php' ),
						'product_id' => $post->ID
						));
				}
				wp_enqueue_script('ced_fruugo_edit_product');
			}
			
		}
		
		/**
		 * Function to category selection field on product single page
		 * 
		 * @name add_fruugo_required_fields
		 */
		public function add_fruugo_required_fields($fields=array(),$post=''){
			$savedCategories = array();
			$postId = isset($post->ID) ? intval($post->ID) : 0;
			$selectedfruugoCategories = get_option('ced_fruugo_selected_categories');
			$selectedfruugoCategories = (is_array($selectedfruugoCategories) && !empty($selectedfruugoCategories)) ? $selectedfruugoCategories : array();
			// $selectedfruugoCategories = $newInedx + $selectedfruugoCategories;
			$fields[] = array(
				'type' => '_select',
				'id' => '_umb_fruugo_category',
				'fields' => array(
					'id' => '_umb_fruugo_category',
					'label' => __( 'fruugo Category', 'ced-fruugo' )."<span class='ced_fruugo_wal_required'>[Required]</span>",
					'options' => $selectedfruugoCategories,
					'desc_tip' => true,
					'description' => __( 'Identify the category specification. There is only one category can be used for any single item. NOTE: Once an item is created, this information cannot be updated.', 'ced-fruugo' )
					),
				);
			return $fields;
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
						update_post_meta( $post_id, $field_name, sanitize_text_field( $_POST[$field_name][$key] ) );
					}
				}
			}
		}

		/**
		 * Upload selected products on fruugo.
		 *
		 * @author CedCommerce <plugins@cedcommerce.com>
		 * @since 1.0.0
		 * @param array $proIds
		 */
		public function upload($proIds=array(), $isWriteXML=true)
		{
			if(file_exists(CED_FRUUGO_DIRPATH.'marketplaces/fruugo/partials/class-fruugo-upload.php')){
				require CED_FRUUGO_DIRPATH.'marketplaces/fruugo/partials/class-fruugo-upload.php';
				$fruugoUploadInstance = CedfruugoUpload :: get_instance();
				$uploadRequest = $fruugoUploadInstance->upload($proIds);
				return $uploadRequest;
			}
		}
	}
	endif;
	?>