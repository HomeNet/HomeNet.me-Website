<?php

/**
 * Description of Content
 *
 * @author mdoll
 */
class Core_Model_Content extends Core_Model_Content_Interface {
    
    public $id = null;
    public $house;
    public $user;
    public $created;
    public $permissions = '';

    public function  __construct(array $config = array()) {
        if(isset($config['data'])){
            $this->fromArray($config['data']);
        }
    }

     public function fromArray(array $array){

        $vars = get_object_vars($this);

        foreach($array as $key => $value){
            if(array_key_exists($key, $vars)){
                $this->$key = $value;
            }
        }
    }

    /**
     * @return array
     */
    public function toArray(){

        return get_object_vars($this);
    }
}