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
 * @subpackage Category
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
interface Core_Model_Menu_MapperInterface {

    public function fetchObjects();
    
    public function fetchObjectById($id);

//    public function fetchObjectsBySection($section);

//    public function fetchObjectsByIdHouse($id,$house);

    public function save(Core_Model_Menu_Interface $categorySet);

    public function delete(Core_Model_Menu_Interface $categorySet);
    
    public function deleteAll();

}