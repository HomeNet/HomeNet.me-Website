<?php

/**
 * Test class for HomeNet_Model_Component_Service.
 * Generated by PHPUnit on 2011-11-21 at 16:36:18.
 */
class HomeNet_Model_Component_ServiceTest extends PHPUnit_Framework_TestCase {

    /**
     * @var HomeNet_Model_Component_Service
     */
    protected $service;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->service = new HomeNet_Model_Component_Service;
        $this->service->deleteAll();
    }

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

        $array = array('status' => HomeNet_Model_ComponentModel::LIVE,
        'plugin' => 'LED',
        'name' => 'testModel',
        'description' => 'test description',
        'units' => '"',
        'settings' => array('key' => 'value'));

        $service = new HomeNet_Model_ComponentModel_Service;
        $model = $service->create($array);
        
        
        return array('device' => 1 + $seed,
        'model' => $model->id,
        'position' => 3 + $seed,
        'order' => 4 + $seed,
        'room' => 5 + $seed,
        'house' => 6 + $seed,
        'name' => 'testName'.$seed,
        'settings' => array('key' => 'value'.$seed));
    }
    
    private function _createValidObject($seed = 0) {
        $object = new HomeNet_Model_Component();
        $object = $this->_fillObject($object, $seed);
  
        $result = $this->service->create($object);

        $this->assertInstanceOf('HomeNet_Model_Component_Interface', $result);
        return $result;
    }
    
    private function _validateResult($result, $seed = 0){
        
        $this->assertInstanceOf('HomeNet_Model_Component_Interface', $result);
        $this->assertNotNull($result->id);
        $this->assertEquals(1+$seed, $result->device);
        //$this->assertEquals(2, $result->model);
        $this->assertEquals(3+$seed, $result->position);
        $this->assertEquals(4+$seed, $result->order);
        $this->assertEquals(5+$seed, $result->room);
        $this->assertEquals(6+$seed, $result->house);
        $this->assertEquals('testName'.$seed, $result->name);
        $this->assertTrue(is_array($result->settings));
        $this->assertEquals('value'.$seed, $result->settings['key']);
    }
    
//$this->service->getMapper()///////////////////////////////////////////////////
    public function testGetMapper() {
       $this->assertInstanceOf('HomeNet_Model_Component_MapperInterface', $this->service->getMapper());
    }
    
//$this->service->setMapper($mapper)////////////////////////////////////////////
    public function testSetMapper() {
        $mapper = new HomeNet_Model_Component_MapperDbTable();
         $this->service->setMapper($mapper);
        
        $this->assertInstanceOf('HomeNet_Model_Component_MapperInterface', $this->service->getMapper());
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
//$this->service->getObjectsByDevice($device)///////////////////////////////////
    public function testGetObjectsByDevice_valid() {
       $object = $this->_createValidObject();
       $results = $this->service->getObjectsByDevice($object->device);
       $this->assertEquals(1, count($results));
        $this->_validateResult($results[0]);
    }
    
    public function testGetObjectsByDevice_invalid() {
      $this->markTestIncomplete('This test has not been implemented yet.');
    }
    
    public function testGetObjectsByDevice_null() {
        $this->setExpectedException('InvalidArgumentException');
        $this->service->getObjectsByDevice(null);
    }
//$this->service->getObjectsByRoom($room)///////////////////////////////////////
    public function testGetObjectsByRoom_valid() {
        $object = $this->_createValidObject();
         $results = $this->service->getObjectsByRoom($object->room);
         $this->assertEquals(1, count($results));
        $this->_validateResult(reset($results));
    }
    public function testGetObjectsByRoom_invalid() {
        //$this->service->getObjectsByRoom(5);
        $this->markTestIncomplete('This test has not been implemented yet.');
    }
    public function testGetObjectsByRoom_null() {
        $this->setExpectedException('InvalidArgumentException');
        $this->service->getObjectsByRoom(null);
    }
//$this->service->newObjectFromModel($id)///////////////////////////////////////   
   public function testNewObjectFromModel(){
       $result = $this->service->newObjectFromModel(1);
       $this->assertInstanceOf('HomeNet_Model_Component_Abstract', $result);
   } 
    
    
//$this->service->create($mixed)////////////////////////////////////////////////
       public function testCreate_validObject() {
        $result = $this->_createValidObject();

        $this->assertNotNull($result->id);
        $this->_validateResult($result);
    }

    public function testCreate_validArray() {
        $result = $this->service->create($this->_getTestData());
        $this->_validateResult($result);
    }
    
//$this->service->update($mixed)////////////////////////////////////////////////
    public function testCreate_invalidObject() {
        $this->setExpectedException('InvalidArgumentException');

        $badObject = new StdClass();
        $this->service->create($badObject);
    }

    public function testUpdate_validObject() {
        //setup
        $object = $this->_createValidObject();

        //update values
        $object= $this->_fillObject($object, 1);

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
        $this->service->delete($object);

        //verify that it was deleted
        $this->setExpectedException('NotFoundException');
        $result = $this->service->getObjectById($object->id);
    }

    public function testDelete_validArray() {
        //setup
        $object = $this->_createValidObject();

        //test delete
        $this->service->delete($object->toArray());

        //verify that it was deleted
        $this->setExpectedException('NotFoundException');
        $result = $this->service->getObjectById($object->id);
    }

    public function testDelete_validId() {
        //setup
        $object = $this->_createValidObject();

        //test delete
        $this->service->delete($object->id);

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