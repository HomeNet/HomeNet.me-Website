<?php

/*
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
 * along with HomeNet.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * @package Content
 * @subpackage Template
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class Content_Model_Template_MapperCache implements Content_Model_Template_MapperInterface {

    private $_mapper;

    /**
     * @return Content_Model_Template_MapperInterface
     */
    public function getMapper() {

        if (empty($this->_mapper)) {
            $this->_mapper = new Content_Model_Template_MapperDbTable();
           
        }

        return $this->_mapper;
    }

    /**
     * @param Content_Model_Section_MapperInterface $mapper 
     */
    public function setMapper(Content_Model_Template_MapperInterface $mapper) {
        $this->_mapper = $mapper;
    }

    public function fetchPathBySection($section) {
        
        if(!is_numeric($section)){
            throw new Exception('Invalid Section: '.$section);
        }
        
        return APPLICATION_PATH .'/cache/content-templates/' . $section.'/';
    }
    
//    private function _getCachePath($section) {
//        return $this->_getCacheDir($section) . '/';
//    }

    /**
     * Fetch Section Content Object by Id
     * 
     * @param int $id 
     * @return Content_Model_DbTabeRow_SectionContent 
     */
    public function fetchObjectByIdRevision($id, $revision) {
        return $this->_mapper->fetchObjectByIdRevision($id, $revision);
    }

    /**
     * Fetch Section Content Objects by Section
     * 
     * @param int $section 
     * @return Content_Model_DbTabeRow_SectionContent 
     */
    public function fetchObjectsBySection($section) {

        return $this->_mapper->fetchObjectsBySection($section);
    }

    /**
     *
     * @param string $url
     * @return Content_Model_DbTabeRow_SectionContent 
     */
    public function fetchObjectBySectionUrl($section, $url) {

        return $this->_mapper->fetchObjectBySectionUrl($section, $url);
    }

    /**
     *
     * @param string $id
     * @return Content_Model_DbTabeRow_SectionContent 
     */
    public function fetchObjectById($id) {

        return $this->_mapper->fetchObjectById($id);
    }

    /**
     *
     * @param string $id
     * @return Content_Model_DbTabeRow_SectionContent 
     */
    public function fetchRevisionsById($id) {

        return $this->_mapper->fetchRevisionsById($id);
    }

//     public function fetchObjectsByIdHouse($id,$house){
//
//       $select = $this->getTable()->select()->where('id = ?',$id)
//                                ->where('house = ?',$house);
//
//       return $this->getTable()->fetchAll($select);
//    }

    private function _getPath($section, $url){
        return $this->fetchPathBySection($section) . $url . '.phtml';
    } 

    public function save(Content_Model_Template_Interface $content) {
        if($content->active == true){
        $path = $this->_getPath($content->section, $content->url);
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir);
        }
//die($path);
        $extra = '';
        if($content->layout !== null){
            $extra = '<?php 
    $_layout = Zend_Layout::getMvcInstance();
    if ($_layout->isEnabled()) {
        $_layout->setLayout(\''.$content->layout.'\');
    } 
?>
';
        }
        
        
        
        file_put_contents($path, $extra.$content->content);
        
        }
        return $this->_mapper->save($content);
    }

    public function delete(Content_Model_Template_Interface $content) {
        $path = $this->_getPath($content->section, $content->url);
        if (!file_exists($path)) {
            throw new Exception('File Doesn\'t exist');
        }
        unlink($path);
        return $this->_mapper->delete($content);
    }

    public function deleteBySection($section) {
        
        //delete dir
        
        return $this->_mapper->deleteBySection($section);
    }

    public function deleteById($id) {
        
        //get url
        //get id

        return $this->_mapper->deleteById($id);
    }

    public function deleteAll() {
        return $this->_mapper->deleteAll();
    }
}