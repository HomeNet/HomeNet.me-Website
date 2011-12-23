<?php

class HomeNet_Model_Test_XmlRpc {

    /**
     * @return string 
     */
    public function helloWorld() {
        return "Hello World";
    }

    /**
     * Echo back a string
     * 
     * @param string $value
     * @return string
     */
    public function ping($value) {
        return $value;
    }
    
    /**
     * Echo back a string
     *
     * @param string $textToEcho
     * @return string
     */
    function echoTest($textToEcho)
    {
        return "You said: " . $textToEcho;
    }
//recompile2
}