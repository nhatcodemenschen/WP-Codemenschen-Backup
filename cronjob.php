<?php

class CODEMEMSCHEN_CRONJOBS{



    public function __construct(){

        add_action ( 'codemenschen_backup', array( $this,'codemenschen_backup_func') );

    }



    public function codemenschen_backup_func(){

        global $wp_codemenschen_helpers;



        // Remove all folder

        $wp_codemenschen_helpers->remove_all_folder();



        // Create new folder for cronjob 

        $dest = $wp_codemenschen_helpers->create_folder();



        $folders = ['themes', 'plugins'];

        foreach($folders as $folder) {



            $dest_path = $dest.'/'.$folder;



            if(!is_dir($dest_path)){

                mkdir($dest_path, 0777, true);

            }



            // Run feature copy dir themes
            
            $source_path = WP_CONTENT_DIR.'/'.$folder;

            $wp_codemenschen_helpers->run_copy_folder($source_path, $dest_path);

        }

        $wp_codemenschen_helpers->chmod_dir($dest, $mode = 0400);

        //Send mail
        if(get_option('codemenschen_backup_enable_send_mail') == 'yes') {
            
            $to = 'jptesting3@gmail.com';
            $subject = 'Codemenschen Backup - '.$dest;
            $body = 'Plugin "Codemenschen Backup" run backup in '.$dest;
            $headers = array('Content-Type: text/html; charset=UTF-8');
            
            wp_mail( $to, $subject, $body, $headers );
        }

    }

}

new CODEMEMSCHEN_CRONJOBS();

