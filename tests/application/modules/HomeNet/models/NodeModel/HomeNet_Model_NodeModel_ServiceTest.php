<?php

/**
 * Test class for HomeNet_Model_NodeModel_Service.
 * Generated by PHPUnit on 2011-11-21 at 16:36:04.
 */
class HomeNet_Model_NodeModel_ServiceTest extends PHPUnit_Framework_TestCase {

    /**
     * @var HomeNet_Model_NodeModel_Service
     */
    protected $service;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->service = new HomeNet_Model_NodeModel_Service;
        $this->service->deleteAll();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        
    }
    
    private function _fillObject($object, $seed = 0) {
        $data = $this->_getTestData($seed);
        foreach ($data as $key => $value) {
            $object->$key = $value;
        }
        return $object;
    }
    private function _fillArray($array, $seed = 0) {
        if(is_object($array)){
            $array = $array->toArray();
        }
        return array_merge($array, $this->_getTestData($seed));
    }
    
    private function _getTestData($seed = 0) {
        
        $array = array('type' => HomeNet_Model_Node::SENSOR,
            'status' => HomeNet_Model_NodeModel::LIVE,
            'plugin' => 'Arduino',
            'name' => 'testModel'.$seed,
            'description' => 'test description'.$seed,
            'image' => 'test.jpg'.$seed,
            'max_devices' => 1+$seed,
            'settings' => array('key' => 'value'.$seed));
        
        if($seed % 2 == 0){
            $array['type']   = HomeNet_Model_Node::INTERNET;
            $array['status'] = HomeNet_Model_NodeModel::TESTING;
            $array['plugin'] = 'Jeenode';
        }
        return $array;
    }
    
    private function _createValidObject($seed = 0) {
        $object = new HomeNet_Model_NodeModel();
        $object = $this->_fillObject($object, $seed);
  
        $result = $this->service->create($object);

        $this->assertInstanceOf('HomeNet_Model_NodeModel_Interface', $result);
        return $result;
    }
     private function _validateResult($result, $seed = 0){
        
        $this->assertInstanceOf('HomeNet_Model_NodeModel_Interface', $result);
        $this->assertNotNull($result->id);
        
        if($seed % 2 == 0){
            $this->assertEquals(HomeNet_Model_Node::INTERNET, $result->type);
            $this->assertEquals(HomeNet_Model_NodeModel::TESTING, $result->status);
            $this->assertEquals('Jeenode', $result->plugin);
        } else {
            $this->assertEquals(HomeNet_Model_Node::SENSOR, $result->type);
            $this->assertEquals(HomeNet_Model_NodeModel::LIVE, $result->status);
            $this->assertEquals('Arduino', $result->plugin);
        }
     
        $this->assertNotNull($result->id);
        
        $this->assertEquals('testModel'.$seed, $result->name);
        $this->assertEquals('test description'.$seed, $result->description);
        $this->assertEquals('test.jpg'.$seed, $result->image);
        $this->assertEquals(1+$seed, $result->max_devices);
        $this->assertTrue(is_array($result->settings));
        $this->assertEquals('value'.$seed, $result->settings['key']);
    }


//$this->service->getMapper()///////////////////////////////////////////////////
     public function testGetMapper() {
       $this->assertInstanceOf('HomeNet_Model_NodeModel_MapperInterface', $this->service->getMapper());
    }

//$this->service->setMapper($mapper)////////////////////////////////////////////
    public function testSetMapper() {
        $mapper = new HomeNet_Model_NodeModel_MapperDbTable();
         $this->service->setMapper($mapper);
        
        $this->assertInstanceOf('HomeNet_Model_NodeModel_MapperInterface', $this->service->getMapper());
        $this->assertEquals($mapper, $this->service->getMapper());
    }
    
//$this->service->getObjectById($id)////////////////////////////////////////////
    public function testGetObjectById_valid() {
        $object = $this->_createValidObject();

        $result = $this->service->getObjectById($object->id);

        $this->_validateResult($result);
    }

    public function testGetObjectById_invalid() {
        $this->setExpectedException('NotFoundException');
        $result = $this->service->getObjectById(1000);
    }

    public function testGetObjectById_null() {
        $this->setExpectedException('InvalidArgumentException');
        $result = $this->service->getObjectById(null);
    }
//$this->service->getObjects()//////////////////////////////////////////////////
    public function testGetObjects() {
        $object = $this->_createValidObject();
        $object2 = $this->_createValidObject();

        $results = $this->service->getObjects();

        $this->assertEquals(2, count($results));

        $result = $results[0];
        $this->_validateResult($result);
    }
//$this->service->getObjectsByStatus($status)///////////////////////////////////
    public function testGetObjectsByStatus_valid() {
        $object = $this->_createValidObject();

        $results = $this->service->getObjectsByStatus(HomeNet_Model_NodeModel::TESTING);
        $this->assertEquals(1, count($results));

        $result = $results[0];
        $this->_validateResult($result);
    }
    
    public function testGetObjectsByStatus_null() {
       
        $this->setExpectedException('InvalidArgumentException');
        $this->service->getObjectsByStatus(null);
    }
    
//$this->service->create($mixed)////////////////////////////////////////////////
    public function testCreate_validObject() {
        $result = $this->_createValidObject();

        $this->assertNotNull($result->id);
        $this->_validateResult($result);
    }

    public function testCreate_validArray() {
        $array = $this->_getTestData();

        $result = $this->service->create($array);
        $this->_validateResult($result);
    }

    public function testCreate_invalidObject() {
        $this->setExpectedException('InvalidArgumentException');

        $badObject = new StdClass();
        $this->service->create($badObject);
    }
    
//$this->service->update($mixed)////////////////////////////////////////////////
    public function testUpdate_validObject() {
        //setup
        $object = $this->_createValidObject();

        //update values
        $object = $this->_fillObject($object, 1);

        $result = $this->service->update($object);

        $this->_validateResult($result, 1);
    }

    public function testUpdate_validArray() {
        //setup
        $object = $this->_createValidObject();
        $array = $object->toArray();

        //update values
        $array = $this->_fillArray($array, 1);

        $result = $this->service->update($array);

        $this->_validateResult($result, 1);
    }

    public function testUpdate_invalidObject() {
        $this->setExpectedException('InvalidArgumentException');

        $badObject = new StdClass();
        $create = $this->service->update($badObject);
    }

//$this->service->delete($mixed)////////////////////////////////////////////////    
    public function testDelete_validObject() {
        //setup
        $object = $this->_createValidObject();

        //test delete
        $id = $object->id;
        $this->service->delete($object);

        //verify that it was deleted
        $this->setExpectedException('NotFoundException');
        $result = $this->service->getObjectById($id);
    }

    public function testDelete_validArray() {
        //setup
        $object = $this->_createValidObject();

        //test delete
        $id = $object->id;
        $this->service->delete($object->toArray());

        //verify that it was deleted
        $this->setExpectedException('NotFoundException');
        $result = $this->service->getObjectById($id);
    }

    public function testDelete_validId() {
        //setup
        $object = $this->_createValidObject();

        //test delete
        $id = $object->id;
        $this->service->delete($id);

        //verify that it was deleted
        $this->setExpectedException('NotFoundException');
        $result = $this->service->getObjectById($object->id);
    }

    public function testDelete_invalidObject() {
        $this->setExpectedException('InvalidArgumentException');

        $badObject = new StdClass();
        $create = $this->service->delete($badObject);
    }
}