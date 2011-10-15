<?php

/*
 * Rest.php
 * 
 * Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * 
 * This file is part of HomeNet.
 * 
 * HomeNet is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * HomeNet is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with HomeNet.  If not, see <http ://www.gnu.org/licenses/>.
 */

/**
 * Description of Rest
 *
 * @author Matthew Doll <mdoll at homenet.me>
 */
class Content_Plugin_Element_FileManager_Rest {
    
    private $config;
    private $element;
    
    public function __construct(){
       $this->config = Zend_Registry::get('config'); 
    }
    
    public function folders($id){
        //folder
        //?method=sayHello&who=Davey&when=Day
    }
    /*  $this->options = array(
            'script_url' => $_SERVER['PHP_SELF'],
            'upload_dir' => dirname(__FILE__).'/files/',
            'upload_url' => dirname($_SERVER['PHP_SELF']).'/files/',
            'param_name' => 'files',
            // The php.ini settings upload_max_filesize and post_max_size
            // take precedence over the following max_file_size setting:
            'max_file_size' => null,
            'min_file_size' => 1,
            'accept_file_types' => '/.+$/i',
            'max_number_of_files' => null,
            'discard_aborted_uploads' => true,
            'image_versions' => array(

                'thumbnail' => array(
                    'upload_dir' => dirname(__FILE__).'/thumbnails/',
                    'upload_url' => dirname($_SERVER['PHP_SELF']).'/thumbnails/',
                    'max_width' => 80,
                    'max_height' => 80
                )
            )
        );*/
    
    public function upload(){//$id, $folder,$hash){ //$id = null
         
       // $service = new Content_Model_Field_Service();
       // $element = $service->getObjectById($id);
       $this->options = array('folder'=>'','fileType'=>'images', 'maxSize' => 8000, 'maxFileSize' => 1000000, 'minFileSize' => 0);
       
       if(!empty($this->options['folder'])){
           $this->options['folder'].='/';
       }

       // $this->options = array_merge($options, $element->options);
        
        switch($this->options['fileType']){
            case "images":
               $this->options['validExt'] = array('jpg', 'jpeg', 'png', 'gif', 'bmp');
            case "documents":
               $this->options['validExt'] = array('pdf','doc','docx');
            case "archives":    
               $this->options['validExt'] = array('zip','7z');
            case "executables":     
               $this->options['validExt'] = array('exe','msi'); 
            case "any":
            default:
               $this->options['validExt'] = array();
                
        }
        
        
        if(empty($_FILES['files'])){
            return json_encode(array('error' => 'noFile'));
        }
        
        $upload = $_FILES['files'];
        $info = array();
        
        //is multi upload
        if(is_array($upload['tmp_name'])){
            foreach ($upload['tmp_name'] as $index => $value) {
                
                $file = array(
                    'tmp_name' => $upload['tmp_name'][$index],
                    'name' => $upload['name'][$index],
                    'size' => $upload['size'][$index],
                    'type' => $upload['type'][$index],
                    'error' => $upload['error'][$index]);
               
                $info[] = $this->_uploadFile($file);
            }    
        } else {
            $file = $upload;
             if( isset($_SERVER['HTTP_X_FILE_NAME'])){
                $file['name'] = $_SERVER['HTTP_X_FILE_NAME'];
            } 
            if( isset($_SERVER['HTTP_X_FILE_SIZE'])){
                $file['size'] = $_SERVER['HTTP_X_FILE_SIZE'];
            } 
            if( isset($_SERVER['HTTP_X_FILE_TYPE'])){
                $file['type'] = $_SERVER['HTTP_X_FILE_TYPE'];
            } 
            $info[] = $this->_uploadFile($file);
        }
        
        header('Vary: Accept');
        if (isset($_SERVER['HTTP_ACCEPT']) &&
            (strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
            header('Content-type: application/json');
        } else {
            header('Content-type: text/plain');
        }
        return json_encode($info);
        
    }
        private function _validateFile($file) {
            
        if(isset($file['error']) && ($file['error'] != 0)){
            switch ($file['error']) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    return 'maxFileSize';
                case UPLOAD_ERR_PARTIAL:
                case UPLOAD_ERR_NO_FILE:
                case UPLOAD_ERR_NO_TMP_DIR:
                case UPLOAD_ERR_CANT_WRITE:
                case UPLOAD_ERR_EXTENSION:
                    return 'acceptFileTypes';
                default:
                    return 'error: '.$file['error'];
            } 
        }    
        
        $info = pathinfo($file['name']);
            
        if (in_array($file['ext'], $this->options['validExt'])) {
            return 'acceptFileTypes';
        }
        if (is_uploaded_file($file['tmp_name'])) {
            $file_size = filesize($file['tmp_name']);
        } else {
            $file_size = $_SERVER['CONTENT_LENGTH'];
        }
        if ($file_size > $this->options['maxFileSize']) {
            return 'maxFileSize';
        }
        if ($this->options['minFileSize'] &&
            $file_size < $this->options['min_file_size']) {
            return 'minFileSize';
        }
//        if (is_int($this->options['max_number_of_files']) && (
//                count($this->get_file_objects()) >= $this->options['max_number_of_files'])
//            ) {
//            return 'maxNumberOfFiles';
//        }
        return false;
    }
    
    private function _uploadFile($file) {

        $info = pathinfo($file['name']);
        $file['ext'] = $info['extension'];
        $file['name'] = cleanFilename($info['filename']).'.'.$info['extension'];
        $file['size'] = intval($file['size']);

        $error = $this->_validateFile($file);
        
        if($error){
            $file['error'] = $error;
            unset($file['tmp_name']);
            return $file;
        }
        unset($file['error']);
        $file['path'] = $this->options['folder'].$file['name'];

        $tempPath = $this->config->site->tempUploadDirectory.'/'.$file['name'].'.part';
        $fullPath = $this->config->site->uploadDirectory.'/'.$file['path'];
        clearstatcache();
        
        $append = false;
        if(file_exists($tempPath) && ($file['size'] > filesize($tempPath))){
            
            $append = true;
        }
        
        if (is_uploaded_file($file['tmp_name'])) {
            // multipart/formdata uploads (POST method uploads)
            if ($append) {
                file_put_contents($tempPath, fopen($file['tmp_name'], 'r'), FILE_APPEND );
            } else {
                move_uploaded_file($file['tmp_name'], $tempPath);
            }
        } else {
            // Non-multipart uploads (PUT method support)
            file_put_contents($tempPath, fopen('php://input', 'r'), $append ? FILE_APPEND : 0 );
        }

        $discardIncompleteUploads = false;
        $file_size = filesize($tempPath);
            
        if ($file_size === $file['size']) {

            rename($tempPath, $fullPath);
            
            if($this->options['fileType'] == 'images'){
                $helper = new CMS_View_Helper_ImagePath();

                $file['thumbnail'] = $helper->imagePath($file['path'],100,75);
                $file['preview'] =   $helper->imagePath($file['path'],480,320);
            } else {
                $file['thumbnail'] = "";
            }
            $helper = new CMS_View_Helper_AttachmentPath();
            $file['download'] = $helper->attachmentPath($file['path']);

        } elseif ($discardIncompleteUploads) {
            unlink($file_path);
            $file['error'] = 'abort';
        }
        $file['uploaded_size'] = $file_size;
       //     $file->delete_url = $this->options['script_url']
       //         .'?file='.rawurlencode($file->name);
       //     $file->delete_type = 'DELETE';
       
        
        unset($file['tmp_name']);
        
        return $file;
    }
    
    
    public function items($folder,$hash){
        
        if($hash != securityHash($folder)){
            throw new Exception('Invalid Hash',500);
        }
        $config = Zend_Registry::get('config');
        $path = $config->site->uploadDirectory .'/'.$folder;
        $files = array();
        $types = array('.jpg','.jpeg','.png','.bmp');
        
        $helper = new CMS_View_Helper_ImagePath();
        
        if(!empty($folder)){
            $folder .= '/';
        }
        
        foreach (scandir($path) as $file) {

            if (($file != '.') && ($file != '..') && !is_dir($file) && in_array(stristr($file,'.'), $types)) {
                $image = $folder.$file;
                $files[] = array('path' => $image, 
                    'thumbnail' => $helper->imagePath($image, 100, 100),
                    'preview' => $helper->imagePath($image, 480, 320),
                    'title'=>'Image Title',
                    'source'=>'Image Source',
                    'sourceUrl'=>'',
                    'copyright'=>'My Copyright');
            }
        }
        //return array('test'=>'test');
        return json_encode($files);
    }
}