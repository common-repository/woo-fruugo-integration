<?php
if(!class_exists('CedfruugoUpload')){
    class CedfruugoUpload{

        private static $_instance;
        private $uploadResponse;

        /**
         * get_instance Instance.
         *
         * Ensures only one instance of CedfruugoUpload is loaded or can be loaded.
         *
         * @author CedCommerce <plugins@cedcommerce.com>
         * @since 1.0.0
         * @static
         * @return get_instance instance.
         */
        public static function get_instance() {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }
        /**
         * This function is to upload products on fruugo
         * @name upload()
         * @author CedCommerce <plugins@cedcommerce.com>
         * @link  http://www.cedcommerce.com/
         */
        function upload( $productIds=array() ) {
            if(is_array($productIds) && !empty($productIds)){
                self::prepareItems($productIds);
                return json_encode($this->final_response);
            }
        }

        public function prepareItems($productIds ,$cron = 'False' , $Offset = 'False'){
            $notice['message'] = __('Please get the latest version from our site for this feature .' , 'ced-fruugo');
            $notice['classes'] = 'notice notice-success is-dismissable';
            $this->final_response = $notice;
        }
    }
}