<?php

/*

Plugin Name: WP Codemenschen Backup

Plugin URI: https://www.codemenschen.at

Description: Plugin backup folder plugins and themes fortnightly.

Version: 1.0.0

Author: Nhat Codemenschen

Author URI: https://www.codemenschen.at

*/



if ( ! defined( 'CODEMEMSCHEN_BACKUP_PATH' ) ) {

    define( 'CODEMEMSCHEN_BACKUP_PATH', plugin_dir_path( __FILE__ ) );

}



if ( ! defined( 'CODEMEMSCHEN_BACKUP_URL' ) ) {

    define( 'CODEMEMSCHEN_BACKUP_URL', plugin_dir_url( __FILE__ ) );

}



if ( ! defined( 'CODEMEMSCHEN_BACKUP' ) ) {

    define( 'CODEMEMSCHEN_BACKUP', WP_CONTENT_DIR.'/codemenschen_backup' );

}



if ( ! class_exists( 'CODEMEMSCHEN_IMPLEMENT' ) ) {



    class CODEMEMSCHEN_IMPLEMENT {



        public function __construct() {

            $this->init();

            $this->hooks();

        }



        private function init(){

            if( ini_get('max_execution_time') < 600 ){

                ini_set('max_execution_time', 600);

            }



            if( ini_get('max_input_time') < 600 ){

                ini_set('max_input_time', 600);

            }



            $includes = array(

                'helper',

                'cronjob',

                'settings',

            );



            foreach( $includes as $files ){

                require_once( CODEMEMSCHEN_BACKUP_PATH . "{$files}.php" );

            }



            if (!wp_next_scheduled('codemenschen_backup')) {

                wp_schedule_event( time(), 'fortnightly', 'codemenschen_backup' );

            }



            register_activation_hook(__FILE__, array($this, 'wp_codemenschen_install'));

            register_deactivation_hook(__FILE__, array($this, 'wp_codemenschen_uninstall'));

        }

        public function wp_codemenschen_install() {

            if(!is_dir(CODEMEMSCHEN_BACKUP)){

                mkdir(CODEMEMSCHEN_BACKUP, 0777, true);

            }

            update_option('codemenschen_backup_enable_plugins', 'yes');
            update_option('codemenschen_backup_enable_themes', 'yes');
            update_option('codemenschen_backup_times_backup', 1);
            update_option('codemenschen_backup_limit_size', 10);

        }

    



        public function wp_codemenschen_uninstall() {

        }



        private function hooks(){

            add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

        }



        public function admin_enqueue_scripts() {
            wp_enqueue_style( 'wp_codemenschen-style', CODEMEMSCHEN_BACKUP_URL . 'assets/css/style.css' );
            wp_enqueue_script( 'wp_codemenschen-js', CODEMEMSCHEN_BACKUP_URL . 'assets/js/custom.js', array( 'jquery' ) );
        }

    }

    $CODEMEMSCHEN_IMPLEMENT = new CODEMEMSCHEN_IMPLEMENT();

}