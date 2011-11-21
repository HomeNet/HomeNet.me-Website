<?php
/**
 *
 * @author mdoll
 */
interface Core_Model_Auth_Interface {
    
    /**
     * 
     */
    public function add($credentials);
    
    /**
     * @return int User Id
     */
    public function login($credentials);
    public function logout();
    public function delete($id);
    
}