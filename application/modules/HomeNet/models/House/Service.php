<?php

/*
 * HouseService.php
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
 * @package HomeNet
 * @subpackage House
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class HomeNet_Model_House_Service {

    /**
     * @var HomeNet_Model_House_MapperInterface
     */
    protected $_mapper;

    /**
     * @var HomeNet_Model_House_MapperInterface
     */
    protected $_cacheMapper;

    /**
     * @var HomeNet_Model_House_MapperInterface
     */
    protected $_mapperClass = 'HomeNet_Model_House_MapperDbTable';

    /**
     * @var HomeNet_Model_House_MapperInterface
     */
    protected $_cacheMapperClass = 'HomeNet_Model_House_MapperCache';

    /**
     * @var HomeNet_Model_House_MapperInterface
     */
    protected $_cache = true;

    /**
     * Get storage mapper
     * 
     * @return HomeNet_Model_House_MapperInterface
     */
    public function getMapper() {

        if (empty($this->_mapper)) {

            if ($this->cacheEnabled()) {
                $this->_mapper = $this->getCacheMapper();
            } else {
                $this->_mapper = new $this->_mapperClass();
            }
        }

        return $this->_mapper;
    }

    /**
     * Set storage mapper
     * 
     * @param HomeNet_Model_House_MapperInterface $mapper 
     */
    public function setMapper(HomeNet_Model_House_MapperInterface $mapper) {
        $this->_mapper = $mapper;
    }

    /**
     * Get cache mapper
     * 
     * @return HomeNet_Model_HousesMapperInterface
     */
    public function getCacheMapper() {

        if (empty($this->_cacheMapper)) {
            $this->_cacheMapper = new $this->_cacheMapperClass(new $this->_mapperClass());
        }

        return $this->_cacheMapper;
    }

    /**
     * Set Cache Mapper
     * 
     * @param HomeNet_Model_House_MapperInterface $mapper 
     */
    public function setCacheMapper(HomeNet_Model_House_MapperInterface $mapper) {
        $this->_cacheMapper = $mapper;
    }

    /**
     * Enable Cache
     */
    public function enableCache() {

        $this->_cache = true;
    }

    /**
     * Disable cache
     */
    public function disableCache() {
        $this->_cache = false;
        unset($this->_mapper);
        unset($this->_cacheMapper);
    }

    /**
     * Get whether the cache is enabled
     * 
     * @return boolean
     */
    public function cacheEnabled() {

        return $this->_cache;
    }

    /**
     * Get House By Id
     * 
     * @param int $id
     * @return HomeNet_Model_House (HomeNet_Model_House_Interface)
     * @throws InvalidArgumentException 
     * @throws NotFoundException
     */
    public function getObjectById($id) {
        if (empty($id) || !is_numeric($id)) {
            throw new InvalidArgumentException('Invalid House Id', 500);
        }

        $result = $this->getMapper()->fetchObjectById($id);

        if (empty($result)) {
            throw new NotFoundException('House not found', 404);
        }
        return $result;
    }

    /**
     * Get House by Id and include Room data
     * 
     * @param int $id
     * @return HomeNet_Model_House (HomeNet_Model_House_Interface)
     * @throws InvalidArgumentException 
     * @throws NotFoundException
     */
    public function getObjectByIdWithRooms($id) {

        if (empty($id) || !is_numeric($id)) {
            throw new InvalidArgumentException('Missing Id');
        }

        $result = $this->getMapper()->fetchObjectByIdWithRooms($id);

        if (empty($result)) {
            throw new NotFoundException('House not found', 404);
        }
        return $result;
    }

    /**
     * Get multiple houses by their id's
     * 
     * @param array $ids
     * @return HomeNet_Model_House (HomeNet_Model_House_Interface)
     * @throws InvalidArgumentException 
     * @throws NotFoundException 
     */
    public function getObjectsByIds(array $ids) {
        if (empty($ids)) {
            throw new InvalidArgumentException('Missing Houses');
        }

        $results = $this->getMapper()->fetchObjectsByIds($ids);

        /* $houses = array();

          foreach($result as $key => $house){
          $houses[$house->id] = $house;
          }

          return $houses;
         */

        return $results;
    }

    /**
     * Get multiple houses by their id's and include Room data
     * 
     * @param array $ids
     * @return HomeNet_Model_House (HomeNet_Model_House_Interface)
     * @throws InvalidArgumentException 
     * @throws NotFoundException 
     */
    public function getObjectsByIdsWithRooms($ids) {

        if (empty($ids)) {
            throw new InvalidArgumentException('Missing Houses');
        }

        $houses = $this->getMapper()->fetchObjectsByIdsWithRooms($ids);

        if (empty($houses)) {
            throw new NotFoundException('Houses not found', 404);
        }
        return $houses;
    }

    /**
     * Find all the houses that belong to a user
     * 
     * @todo find a better home for this
     */
    public function getHouseIdsByUser($user = null) {
        if ($user !== null) {
            if (!is_numeric($user)) {
                throw new InvalidArgumentException('Invaild User');
            }
            $service = new HomeNet_Model_HouseUser_Service();
            $results = $service->getObjectsbyUser($user);
            $ids = array();
            foreach ($results as $value) {
                $ids[] = $value->house;
            }
            return $ids;
        }
//        //else load from session
//        if (isset($_SESSION['HomeNet']['houses'])) {
//            return $_SESSION['HomeNet']['houses'];
//        }

        $user = Core_Model_User_Manager::getUser();

        $service = new HomeNet_Model_HouseUser_Service();
        $results = $service->getObjectsbyUser($user->id);

        $ids = array();
        foreach ($results as $value) {
            $ids[] = $value->house;
        }

        return $ids;
    }

    public function getTypes() {
        return array('house' => 'House',
            'apartment' => 'Apartment',
            'condo' => 'Condo',
            'other' => 'Other',
            'na' => 'N/A');
    }

    public function getRegions(array $limit = array()) {
        
        $regions = array('1' => 'First Floor',
            '2' => 'Second Floor',
            '3' => 'Third Floor',
            '4' => 'Forth Floor',
            '5' => 'Sixth Floor',
            'B' => 'Basement',
            'A' => 'Attic',
            'O' => 'Outdoors');
        
        if(!empty($limit)){
            $regions = array_intersect_key($regions, array_flip($limit));
        }
        
        return $regions;
    }

    /**
     * Get a Houses Regions
     * 
     * @param type $id House Id
     * @return array  
     * @throws InvalidArgumentException 
     * @throws NotFoundException 
     */
    public function getRegionsById($id) {
        //@todo move to House Manager
        if (empty($id)) {
            throw new InvalidArgumentException('Missing House');
        }

        $regions = $this->getRegions();

        $r = $this->getObjectById($id)->regions;

//        if (is_string($r)) {
//            $r = unserialize($r);
//        }
//        $r = array_flip($r);
//
//        foreach ($r as $key => $region) {
//            $r[$key] = array('id' => $key, 'name' => $regions[$key]);
//        }
        return $r;
    }

    /**
     * Create a new House
     * 
     * @param HomeNet_Model_House_Interface|array $mixed
     * @return HomeNet_Model_House (HomeNet_Model_House_Interface)
     * @throws InvalidArgumentException 
     */
    public function create($mixed) {
        if ($mixed instanceof HomeNet_Model_House_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new HomeNet_Model_House(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid House');
        }
        
        if ($object->database === null) {
            $object->database = 1;
        }

        if ($object->status === null) {
            $object->status = 1;
        }

        if ($object->url === null) {
            $object->url = '';
        }

        if ($object->settings === null) {
            $object->settings = array();
        }

        $result = $this->getMapper()->save($object);
        
        //create data tables
        $service = new HomeNet_Model_Datapoint_Service($result);
        $service->createTables();
        
        

        //add to house user
        //create user perrmissions
        $service = new HomeNet_Model_HouseUser_Service();

       // $houseuserService= new HomeNet_Model_HouseUser_Service();
        $user = Core_Model_User_Manager::getUser();
       // $houseuserService->add($result->id, $user->id);

        //reset session cache
        unset($_SESSION['HomeNet']['houses']);


        //add alerts
        $messageService = new HomeNet_Model_Message_Service();
       // $url = $this->view->url(array('action' => 'index'), 'homenet-setup-index');
       // $messageService->add(HomeNet_Model_Message::NEWITEM, 'Congrates on starting your HomeNet. If you need to, you can return to the <a href="/home/' . $result->id . '/setup">Setup Wizard</a>', $user->id);
       $messageService->add(HomeNet_Model_Message::NEWITEM, '<strong>' . $user->name . '</strong> Added their home &quot;' . $result->name . '&quot; to HomeNet', null, $result->id);

        return $result;
    }

    /**
     * Update an existing House
     * 
     * @param HomeNet_Model_House_Interface|array $mixed
     * @return HomeNet_Model_House (HomeNet_Model_House_Interface)
     * @throws InvalidArgumentException 
     */
    public function update($mixed) {
        if ($mixed instanceof HomeNet_Model_House_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new HomeNet_Model_House(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid House');
        }

        $result = $this->getMapper()->save($object);
        //  $this->clearCacheById($result->id);
        return $result;
    }

    /**
     * Delete a House
     * 
     * @param HomeNet_Model_House_Interface|array|integer $mixed
     * @return boolean Success
     * @throws InvalidArgumentException 
     */
    public function delete($mixed) {
        if ($mixed instanceof HomeNet_Model_House_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new HomeNet_Model_House(array('data' => $mixed));
        } elseif (is_numeric($mixed)) {
            $object = $this->getObjectById((int) $mixed);
        } else {
            throw new InvalidArgumentException('Invalid HOuse');
        }

        $id = $object->id;

        $result = $this->getMapper()->delete($object);
        //$this->clearCacheById($id);
        return $result;
    }

    /**
     * Delete all Houses. Used for unit testing/Will not work in production 
     *
     * @return boolean Success
     * @throws NotAllowedException
     */
    public function deleteAll() {
        if (APPLICATION_ENV == 'production') {
            throw new Exception("Not Allowed");
        }
        $this->getMapper()->deleteAll();
    }

}