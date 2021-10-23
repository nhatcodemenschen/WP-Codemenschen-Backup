<?php

class CODEMEMSCHEN_HELPERS{



    public function __construct(){

        

    }



    public function run_copy_folder($source, $dest){

        

        // if (isEnabled('shell_exec')) {

        //     if($this->isWindoww() == 'win') {

        //         $this->copy_dir_window($source, $dest);

        //     } else {

        //         $this->copy_dir_linux($source, $dest);

        //     }

        //     shell_exec($cmd);

        // } else {

        //     $this->copy_folder($source, $dest);

        // }



        $this->copy_folder($source, $dest);


        $this->write_log('Done copy: "'.$source.'" to "'.$dest.'"');

    }



    public function isEnabled($func) {

        return is_callable($func) && false === stripos(ini_get('disable_functions'), $func);

    }



    public function isWindoww() {

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {

            $type = 'win';

        } else {

            $type  = 'linux';

        }

        return $type;

    }



    public function create_folder($date = null) {

       

        if(!empty($date)) {

            $new_folder_date = CODEMEMSCHEN_BACKUP.'/'.$date;

        } else {

            $new_folder_date = CODEMEMSCHEN_BACKUP.'/'.date("Y-m-d");

        }

        if(!is_dir($new_folder_date)){

            mkdir($new_folder_date, 0777, true);

        }

        

        return $new_folder_date;

    }



    public function remove_all_folder() {

        $cdir = scandir(CODEMEMSCHEN_BACKUP);
        
       
        foreach ($cdir as $key => $folder) {

            if (!in_array($folder,array(".",".."))) {
               
                if($key < count($cdir) - get_option('codemenschen_backup_times_backup')) {
                    $path = CODEMEMSCHEN_BACKUP.'/'.$folder;

                    $this->chmod_dir($path, $mode = 0777);

                    $this->deleteDirectory($path);
                }
            }

        }

    }



    public function deleteDirectory($dirPath) {

        if (is_dir($dirPath)) {

            $objects = scandir($dirPath);

            foreach ($objects as $object) {

                if ($object != "." && $object !="..") {

                    if (filetype($dirPath . DIRECTORY_SEPARATOR . $object) == "dir") {

                        $this->deleteDirectory($dirPath . DIRECTORY_SEPARATOR . $object);

                    } else {

                        unlink($dirPath . DIRECTORY_SEPARATOR . $object);

                    }

                }

            }

            reset($objects);

            rmdir($dirPath);

        }

    }

    public function copy_dir_window($source, $dest) {

        //$cmd = 'E:; cd '.$source.'; xcopy * "'.$dest.'" /E /H /C /I';

        $cmd = 'xcopy "'.$source.'/*" "'.$dest.'" /e/i';

        return $cmd;

    }



    public function copy_dir_linux($source, $dest) {

        $cmd = "cp -r -a $source/* $dest 2>&1";

        return $cmd;

    }



    public function copy_folder($source, $dest) { 



        $dir = opendir($source); 

        @mkdir($dest); 

       

        foreach (scandir($source) as $file) { 

       

            if (( $file != '.' ) && ( $file != '..' )) { 
                
                
                
                if ( is_dir($source . '/' . $file)) { 



                    $this->copy_folder($source . '/' . $file, $dest . '/' . $file); 

       

                } else { 

                    $codemenschen_backup_limit_size = get_option('codemenschen_backup_limit_size')*1024;

                    if(filesize($source . '/' . $file) < $codemenschen_backup_limit_size) {
   
                        copy($source . '/' . $file, $dest . '/' . $file); 
                    }
                    
                } 

            } 

        } 

        closedir($dir);

    } 


    public function chmod_dir($source, $mode) {

        chmod($source, $mode);

    }

    public function getDirectorysize($path){
        $bytestotal = 0;
        $path = realpath($path);
        if($path!==false && $path!='' && file_exists($path)){
            foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)) as $object){
                $bytestotal += $object->getSize();
            }
        }
        return $bytestotal;
    }

    public function format_size($size) {
        $units = explode(' ', 'B KB MB GB TB PB');
    
        $mod = 1024;
        for ($i = 0; $size > $mod; $i++) {
            $size /= $mod;
        }

        $endIndex = strpos($size, ".")+3;
    
        return substr( $size, 0, $endIndex).' '.$units[$i];
    }

    public function show_folders() {
        $data = [];

        $cdir = scandir(CODEMEMSCHEN_BACKUP);
        foreach ($cdir as $key => $folder) {

            if (!in_array($folder,array(".",".."))) {

                $path = CODEMEMSCHEN_BACKUP.'/'.$folder;
                $data[] = [
                    'name' => $folder,
                    'path' => $path,
                    'filesize' => $this->format_size($this->getDirectorysize($path)),
                    'created_at' => date ("F d Y H:i:s.", filemtime($path)),
                ];

            }
        }
        return $data;

    }

    



    public function check_cron_job_runing() {

        $cron_jobs = get_option( 'cron' );

        foreach($cron_jobs as $job) {

            echo '<pre>';

            var_dump($job);

        }

    }



    public function write_log($log) {

        if (true === WP_DEBUG) {

            if (is_array($log) || is_object($log)) {

                error_log(print_r($log, true));

            } else {

                error_log($log);

            }

        }

    }
    

}

$GLOBALS['wp_codemenschen_helpers'] = new CODEMEMSCHEN_HELPERS();

