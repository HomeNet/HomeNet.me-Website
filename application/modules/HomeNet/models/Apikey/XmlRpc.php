<?php
class HomeNet_Model_Apikey_XmlRpc {
           
    /**
     * @param string $apikey
     * @return boolean
     */
    public function validate($apikey){
        $service = new HomeNet_Model_Apikey_Service();
        
        return $service->validate($apikey);
    }
    //recompile2
} 