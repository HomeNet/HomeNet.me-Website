<?php

/*
 * Inteface.php
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
 *
 * @author Matthew Doll <mdoll at homenet.me>
 */
interface Content_Model_Plugin_Interface {
    
    /**
     * Get the name of the plugin
     * 
     * @return string 
     */
    function getName();
    
    /**
     * Get the version of the plugin
     * 
     * @return string 
     */
    function getVersion();
    
    /**
     * Get the date this version was released
     * 
     * @return Zend_Date 
     */
    function getDate();
    
    /**
     * Get the id of the Publisher
     * 
     * @return string 
     */
    function getPublisherId();
    
    /**
     * Get the author of the plugin
     * 
     * @return string 
     */
    function getPublisher();
    /**
     * Get the url of the authors website (without http://) 
     * 
     * @return string 
     */
    function getPublisherUrl();
    
    /**
     * Get the url of the authors website for support (without http://) 
     * 
     * @return string 
     */
    function getSupportUrl();
    
    /**
     * Get the security hash of the plugin, used to determine if a plugin offically verified
     * 
     * @return string 
     */
    function getSignature();

}