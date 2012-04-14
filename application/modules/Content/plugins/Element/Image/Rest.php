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
class Content_Plugin_Element_Image_Rest {
    public function folders($item,$hash){
        //folder
        //?method=sayHello&who=Davey&when=Day
    }
    public function items($id){
        $service = new Content_Model_Field_Service();
       $element = $service->getObjectById($id);
//        if($hash != securityHash($folder)){
//            throw new Exception('Invalid Hash',500);
//        }
       
       $options = array('folder'=>'','fileType'=>'images', 'maxSize' => 8000, 'maxFileSize' => 1000000, 'minFileSize' => 0);
       
       

        $options = array_merge($options, $element->options);
       
        $config = Zend_Registry::get('config');
        $path = $config->site->uploadDirectory .'/'.$options['folder'];
        $files = array();
        $types = array('.jpg','.jpeg','.png','.bmp');
        
        $helper = new CMS_View_Helper_ImagePath();
        $count = 0;
        foreach (scandir($path) as $file) {

            if (($file != '.') && ($file != '..') && !is_dir($file) && in_array(stristr($file,'.'), $types)) {
                $image = $folder.'/'.$file;
                $files[] = array('path' => $image, 
                    'thumbnail' => $helper->imagePath($image, 100, 100),
                    'preview' => $helper->imagePath($image, 480, 320),
                    'title'=>'Title '.$count,
                    'description'=>'description '.$count,
                    'source'=>'Source '.$count,
                    'url'=>'',
                    'copyright'=>'My Copyright '.$count);
                $count++;
            }
        }
        //return array('test'=>'test');
        return json_encode($files);
    }
}