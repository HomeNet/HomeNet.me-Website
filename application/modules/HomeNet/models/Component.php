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
 * @package HomeNet
 * @subpackage Component
 * @copyright Copyright (c) 2011 Matthew Doll <mdoll at homenet.me>.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 */
class HomeNet_Model_Component extends HomeNet_Model_Component_Abstract {

//    /**
//     * @param HomeNet_Model_ComponentModelInterface $model
//     */
//    public function loadModel(HomeNet_Model_ComponentModelInterface $model) {
//        $this->model_name = $row->name;
//        $this->model = $row->id;
//    }
//
//    /**
//     * @param HomeNet_Model_Component_Abstract
//     */
//    public function getDriver() {
//         if(empty($this->driver)){
//            throw new HomeNet_Model_Exception('Missing Component Driver');
//        }
//
//        if(!class_exists($this->driver)){
//            throw new HomeNet_Model_Exception('Component Driver '.$this->driver.' Doesn\'t Exist');
//        }
//
//        return new $this->driver(array('data' => $this->toArray()));
//    }

}
