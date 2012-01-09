<?php

require_once dirname(__FILE__) . '/../../../../../../application/modules/HomeNet/models/Test/XmlRpc.php';

/**
 * Test class for HomeNet_Model_Test_XmlRpc.
 * Generated by PHPUnit on 2011-12-02 at 19:35:54.
 */
class HomeNet_Model_Test_XmlRpcTest extends PHPUnit_Framework_TestCase {

    /**
     * @var Zend_XmlRpc_Client
     */
    protected $client;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->client = new Zend_XmlRpc_Client('http://localhost/xmlrpc.php');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        
    }

 //homenet.test.helloWorld//////////////////////////////////////////////////////
     public function testHelloWorld_local() {

         $xmlrpc = new HomeNet_Model_Test_XmlRpc();
        $this->assertEquals('Hello World', $xmlrpc->helloWorld());

    }

    public function testValidate_remote() {

        $result = $this->client->call('homenet.test.helloWorld', array());
        $this->assertEquals('Hello World', $result);

    }


//homenet.test.ping/////////////////////////////////////////////////////////////
    public function testPing_local() {

         $xmlrpc = new HomeNet_Model_Test_XmlRpc();
        $this->assertEquals(';lakshf;alshdf;ahf;alsdhfka;sldfhsd', $xmlrpc->ping(';lakshf;alshdf;ahf;alsdhfka;sldfhsd'));

    }

    public function testPing_remote() {

        //$result = $this->client->call('homenet.test.ping', array(';lakshf;alshdf;ahf;alsdhfka;sldfhsd'));
        
       // $signatures = $this->client->getIntrospector()->getMethodSignature('homenet.test.ping');
        
        $result = $this->client->call('homenet.test.ping', array(";lakshf;alshdf;ahf;alsdhfka;sldfhsd"));
        //$this->fail(debugArray($signatures));
        $this->assertEquals(';lakshf;alshdf;ahf;alsdhfka;sldfhsd', $result);

    }
    
    public function testValidate_list() {

//        $result = $this->client->call('system.listMethods', array(''));

    }
    
    public function testValidate_method() {

        $result = $this->client->call('system.methodSignature', array('homenet.apikey.validate'));

    }

}