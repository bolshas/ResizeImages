<?php
 error_reporting(E_ALL);
 ini_set("display_errors", 1);


if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
	
	if ($_REQUEST['task'] == "GetInformation") {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        
        $path = realpath('./Nuotraukos');
        
        $objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST);
        $images = new RegexIterator($objects, '/^.+\.(png)|(gif)|(bmp)|(jpg)|(jpeg)$/i');
        
        $result = array();
        
        foreach ($images as $image) {
            $result[] = $image->getPathName();
        }
        echo json_encode($result); 
	}
	
	if ($_REQUEST['task'] == "StartConvertion") {
	    
	    $old_path = $_REQUEST['file'];
        $new_place = 'mazos';
        $old_dir = dirname($old_path);
        $file = basename($old_path);
        $end_dir = str_replace(getcwd(), "", $old_dir);
        $new_dir = getcwd() . '/'. $new_place . $end_dir;
        $new_path = $new_dir . '/'. $file;
        
        if (!file_exists($new_path)) {
            if (!is_dir($new_dir)) mkdir($new_dir, 0777, true);
     
            try {
                $image = new Imagick($old_path);
            }
            catch (ImagickException $e) {
                echo "Error: " . $e->getMessage();
                exit();
            }
            
            if (!$image->resizeImage(800, 600, Imagick::FILTER_LANCZOS, 1, true)) {
                echo "Error: could not resize image " . $image->getImageFileName();
                exit();
            }
            
            if (!$image->writeImage($new_path)) {
                echo "Error: could not write image " . $image->getImageFileName() . " to path " . $new_path;
                exit();
            }
        }
        echo "ok";
    }
}