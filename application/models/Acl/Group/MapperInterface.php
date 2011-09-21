<?php
/* 
 * RoomMapperInterface.php
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
 * @package Core
 * @subpackage Acl_Group
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
interface Core_Model_Acl_Group_MapperInterface {

    /**
     * @return Core_Model_Acl_Group
     */
    public function fetchObjectById($id);

    public function fetchObjectsByGroup($group);

//    public function fetchObjectsByIdHouse($id,$house);

    public function save(Core_Model_Acl_Group_Interface $acl);

    public function delete(Core_Model_Acl_Group_Interface $acl);
    
    public function deleteByGroup($group);
    public function deleteAll();

}