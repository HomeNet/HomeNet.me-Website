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
 * along with HomeNet.  If not, see <http ://www.gnu.org/licenses/>.
 */

/**
 * @package HomeNet
 * @subpackage HouseUser
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class HomeNet_Model_HouseUser_Service {

    /**
     * Storage mapper
     * 
     * @var HomeNet_Model_HouseUsersMapperInterface
     */
    protected $_mapper;

    /**
     * Get storage mapper
     * 
     * @return HomeNet_Model_HouseUsersMapperInterface
     */
    public function getMapper() {

        if (empty($this->_mapper)) {
            $this->_mapper = new HomeNet_Model_HouseUser_MapperDbTable();
        }

        return $this->_mapper;
    }

    /**
     * Set storage mapper
     * 
     * @param HomeNet_Model_HouseUser_MapperInterface $mapper 
     */
    public function setMapper(HomeNet_Model_HouseUser_MapperInterface $mapper) {
        $this->_mapper = $mapper;
    }

/**
 * Get HouseUsers by user id
 * 
 * @param integer $user
 * @return HomeNet_Model_HouseUser (HomeNet_Model_HouseUser_Interface)
 * @throws InvalidArgumentException
 * @throws NotFoundException
 */
    public function getObjectsbyUser($user){
        if(empty($user) || !is_numeric($user)){
            throw new InvalidArgumentException('Invalid User');
        }
        
        $result = $this->getMapper()->fetchObjectsByUser($user);

        if (empty($result)) {
            throw new NotFoundException('HouseUser not found', 404);
        }
        return $result;
    }

/**
 * Get HouseUsers by id
 * 
 * @param integer $user
 * @return HomeNet_Model_HouseUser (HomeNet_Model_HouseUser_Interface)
 * @throws InvalidArgumentException
 * @throws NotFoundException
 */
    public function getObjectbyId($id){
        
        if(empty($id) || !is_numeric($id)){
            throw new InvalidArgumentException('Invalid HouseUser Id');
        }
        
        $result = $this->getMapper()->fetchObjectById($id);

        if (empty($result)) {
            throw new NotFoundException('HouseUser not found', 404);
        }
        return $result;
    }
    
    public function add($house, $user, $permissions = null){
        
        if(!is_array($permissions)){
            $permissions = array($permissions);
        }
        
        $object = new HomeNet_Model_HouseUser();
        $object->house = $house;
        $object->user = $user;
        $object->permissions = $permissions;
        $this->create($object);
    }

/**
     * Create a new HouseUser
     * 
     * @param HomeNet_Model_HouseUser_Interface|array $mixed
     * @return HomeNet_Model_HouseUser (HomeNet_Model_HouseUser_Interface)
     * @throws InvalidArgumentException 
     */
    public function create($mixed) {
        if ($mixed instanceof HomeNet_Model_HouseUser_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new HomeNet_Model_HouseUser(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid HouseUser');
        }

        $result = $this->getMapper()->save($object);

        $houseService = new HomeNet_Model_House_Service();
        $house = $houseService->getObjectById($result->house);
      //  $houseService->clearCacheById($result->house);

        $types = $houseService->getTypes();
        
        $aclService = new Core_Model_Acl_User_Service();
        
        if($result->permissions !== null && is_array($result->permissions)){
            
            if(in_array(HomeNet_Model_HouseUser::PERMISSION_VIEW, $result->permissions)){
                $aclService->allow($result->user, 'homenet', null, 'index', $result->house);
            }
            
            if(in_array(HomeNet_Model_HouseUser::PERMISSION_CODE, $result->permissions)){
                $aclService->allow($result->user, 'homenet', 'node', 'code', $result->house);
            }
            
            if(in_array(HomeNet_Model_HouseUser::PERMISSION_EXPORT, $result->permissions)){
                $aclService->allow($result->user, 'homenet', 'component', 'export', $result->house);
            }
            
            if(in_array(HomeNet_Model_HouseUser::PERMISSION_ADD, $result->permissions)){
                $aclService->allow($result->user, 'homenet', null, 'add', $result->house);
                $aclService->allow($result->user, 'homenet', null, 'configure', $result->house);
            }
            
            if(in_array(HomeNet_Model_HouseUser::PERMISSION_EDIT, $result->permissions)){
                $aclService->allow($result->user, 'homenet', null, 'edit', $result->house);
            }
            
            if(in_array(HomeNet_Model_HouseUser::PERMISSION_TRASH, $result->permissions)){
                $aclService->allow($result->user, 'homenet', null, 'trash', $result->house);
                $aclService->allow($result->user, 'homenet', null, 'trashed', $result->house);
                $aclService->allow($result->user, 'homenet', null, 'untrash', $result->house);
            }
            
            if(in_array(HomeNet_Model_HouseUser::PERMISSION_DELETE, $result->permissions)){
                $aclService->allow($result->user, 'homenet', null, 'delete', $result->house);
            }
            
            if(in_array(HomeNet_Model_HouseUser::PERMISSION_ADMIN, $result->permissions)){
                $aclService->allow($result->user, 'homenet', null, null, $result->house);
            }

        }

        //$table = new HomeNet_Model_DbTable_Alerts();

        //$table->add(HomeNet_Model_Alert::NEWITEM, '<strong>' . $_SESSION['User']['name'] . '</strong> Added a new houseUser ' . $houseUser->name . ' to their ' . $types[$this->house->type] . ' ' . $this->house->name . ' to HomeNet', null, $id);
       // $table->add(HomeNet_Model_Alert::NEWITEM, '<strong>' . $_SESSION['User']['name'] . '</strong> Added a new houseUser ' . $result->name . ' to ' . $house->name . ' to HomeNet', null, $result->id);

        return $result;
    }

    /**
     * Update an existing HouseUser
     * 
     * @param HomeNet_Model_HouseUser_Interface|array $mixed
     * @return HomeNet_Model_HouseUser (HomeNet_Model_HouseUser_Interface)
     * @throws InvalidArgumentException 
     */
    public function update($mixed) {
        if ($mixed instanceof HomeNet_Model_HouseUser_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new HomeNet_Model_HouseUser(array('data' => $mixed));
        } else {
            throw new InvalidArgumentException('Invalid HouseUser');
        }

        $result = $this->getMapper()->save($object);

       // $houseService = new HomeNet_Model_House_Service();
       // $houseService->clearCacheById($result->house);

        return $result;
    }
    
     /**
     * Delete a HouseUser
     * 
     * @param HomeNet_Model_HouseUser_Interface|array|integer $mixed
     * @return boolean Success
     * @throws InvalidArgumentException 
     */
    public function delete($mixed) {
        if ($mixed instanceof HomeNet_Model_HouseUser_Interface) {
            $object = $mixed;
        } elseif (is_array($mixed)) {
            $object = new HomeNet_Model_HouseUser(array('data' => $mixed));
        } elseif (is_numeric($mixed)) {
            $object = $this->getObjectbyId((int) $mixed);
        } else {
            throw new InvalidArgumentException('Invalid UserModel');
        }
        
        

        //$result = $this->getMapper()->delete($object);
        
        $service = new Core_Model_Acl_User_Service();
        $service->deleteByUserModuleCollection($object->user, 'homenet', $object->house);

       // $houseService = new HomeNet_Model_House_Service();
       // $houseService->clearCacheById($result->house);
        $result = $this->getMapper()->delete($object);
        return $result;
    }

    /**
     * Delete all HouseUsers. Used for unit testing/Will not work in production 
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