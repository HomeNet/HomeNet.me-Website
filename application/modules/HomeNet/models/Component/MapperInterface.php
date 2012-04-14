<?php

/*
 * ComponentMapperInterface.php
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
 * Description of ComponentMapperInterface
 *
 * @author Matthew Doll <mdoll at homenet.me>
 */
interface HomeNet_Model_Component_MapperInterface {

    public function fetchObjectById($id);

    //public function fetchObjectsByDevice($device, $status);

  //  public function fetchObjectsByRoom($room, $status);

    public function save(HomeNet_Model_Component_Interface $object);

    public function delete(HomeNet_Model_Component_Interface $object);
    
    public function deleteAll();
}