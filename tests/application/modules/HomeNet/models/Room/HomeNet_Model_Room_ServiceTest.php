<?php

/**
 * Test class for HomeNet_Model_Room_Service.
 * Generated by PHPUnit on 2011-11-21 at 16:36:13.
 */
class HomeNet_Model_Room_ServiceTest extends PHPUnit_Framework_TestCase {

    /**
     * @var HomeNet_Model_Room_Service
     */
    protected $service;
    protected $homenetInstaller;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->service = new HomeNet_Model_Room_Service;
        $this->homenetInstaller = new HomeNet_Installer();
        $this->homenetInstaller->installTest(array('house'));
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        
    }

    private function createValidObject() {

        $object = new HomeNet_Model_Room();
        //$object->status = 1;
        $object->house = $this->homenetInstaller->house->id;
        $object->region = '2';
        $object->name = 'My Room';
        $object->description = 'My Description';
        //$object->permissions;

        $result = $this->service->create($object);

        $this->assertInstanceOf('HomeNet_Model_Room_Interface', $result);
        return $result;
    }

    private function validateResult($result) {
        $this->assertNotNull($result->id);
        //$this->assertEquals(1, $result->status);
        $this->assertEquals($this->homenetInstaller->house->id, $result->house);
        $this->assertEquals('2', $result->region);
        $this->assertEquals('My Room', $result->name);
        $this->assertEquals('My Description', $result->description);
    }
    
//$this->service->getMapper()///////////////////////////////////////////////////
    public function testGetMapper() {
        $this->assertInstanceOf('HomeNet_Model_Room_MapperInterface', $this->service->getMapper());
    }

//$this->service->setMapper($mapper)////////////////////////////////////////////
    public function testSetMapper() {
        $mapper = new HomeNet_Model_Room_MapperDbTable();
        $this->service->setMapper($mapper);

        $this->assertInstanceOf('HomeNet_Model_Room_MapperInterface', $this->service->getMapper());
        $this->assertEquals($mapper, $this->service->getMapper());
    }

//$this->service->getObjectById($id)////////////////////////////////////////////
    public function testGetObjectById_valid() {
        $object = $this->createValidObject();

        $result = $this->service->getObjectById($object->id);

        $this->validateResult($result);
    }

    public function testGetObjectById_invalid() {
        $this->setExpectedException('NotFoundException');
        $this->service->getObjectById(1000);
    }

    public function testGetObjectById_null() {
        $this->setExpectedException('InvalidArgumentException');
        $this->service->getObjectById(null);
    }

    public function testGetObjectsByHouse_valid() {
        $object = $this->createValidObject();
        $results = $this->service->getObjectsByHouse($this->homenetInstaller->house->id);

        $this->assertEquals(1, count($results));

        $this->validateResult($results[0]);
    }

    public function testGetObjectsByHouse_invalid() {
        $this->markTestIncomplete('More Validation required');
        $this->setExpectedException('NotFoundException');
        $this->service->getObjectsByHouse(1000);
    }

    public function testGetObjectsByHouse_null() {
        $this->setExpectedException('InvalidArgumentException');
        $this->service->getObjectsByHouse(null);
    }

    public function testGetObjectsByHouses_valid() {
        $object = $this->createValidObject();
        $results = $this->service->getObjectsByHouses(array($this->homenetInstaller->house->id, $this->homenetInstaller->house2->id));

        $this->assertEquals(1, count($results));

        $this->validateResult($results[0]);
    }

    public function testGetObjectsByHouses_invalid() {
        $this->markTestIncomplete('More Validation required');
        $this->setExpectedException('NotFoundException');
        $this->service->getObjectsByHouses(array(1000, 1001));
    }

    public function testGetObjectsByHouses_wrongType() {
        $this->markTestIncomplete('More Validation required');
        $this->setExpectedException('InvalidArgumentException');
        $this->service->getObjectsByHouses(array('wrongtype'));
    }

    public function testGetObjectsByHouseRegion_valid() {
        $object = $this->createValidObject();
        $results = $this->service->getObjectsByHouseRegion($object->house, $object->region);

        $this->assertEquals(1, count($results));

        $this->validateResult($results[0]);
    }

    public function testGetObjectsByHouseRegion_invalid() {
        $this->markTestIncomplete('More Validation required');
        $this->setExpectedException('NotFoundException');
        $this->service->getObjectsByHouseRegion(1000, '2');
    }

    public function testGetObjectsByHouseRegion_nullHouse() {
        $this->setExpectedException('InvalidArgumentException');
        $this->service->getObjectsByHouseRegion(null, '2');
    }
    
    public function testGetObjectsByHouseRegion_nullRegion() {
        $this->setExpectedException('InvalidArgumentException');
        $this->service->getObjectsByHouseRegion(1, null);
    }
    
//$this->service->create($mixed)////////////////////////////////////////////////
    public function testCreate_validObject() {
        $result = $this->createValidObject();

        $this->assertNotNull($result->id);
        $this->validateResult($result);
    }

    public function testCreate_validArray() {
        $array = array(
            //'status' => 1,
            'house' => 1,
            'region' => '2',
            'name' => 'My Room',
            'description' => 'My Description');

        $result = $this->service->create($array);
        $this->validateResult($result);
    }

    public function testCreate_invalidObject() {
        $this->setExpectedException('InvalidArgumentException');

        $badObject = new StdClass();
        $this->service->create($badObject);
    }
    
//$this->service->update($mixed)////////////////////////////////////////////////
    public function testUpdate_validObject() {
        //setup
        $object = $this->createValidObject();

        //update values
        //$object->status = -1;
        $object->house = $this->homenetInstaller->house2->id;
        $object->region = '3';
        $object->name = 'My Room2';
        $object->description = 'My Description2';

        $result = $this->service->update($object);

        $this->assertInstanceOf('HomeNet_Model_Room_Interface', $result);

        $this->assertNotNull($result->id);
        //$this->assertEquals(1, $result->status);
        $this->assertEquals($this->homenetInstaller->house2->id, $result->house);
        $this->assertEquals('3', $result->region);
        $this->assertEquals('My Room2', $result->name);
        $this->assertEquals('My Description2', $result->description);
    }

    public function testUpdate_validArray() {
        //setup
        $object = $this->createValidObject();
        $array = $object->toArray();

        //update values
        //$array['status'] = -1;
        $array['house'] = $this->homenetInstaller->house2->id;
        $array['region'] = '3';
        $array['name'] = 'My Room2';
        $array['description'] = 'My Description2';

        $result = $this->service->update($array);

        $this->assertInstanceOf('HomeNet_Model_Room_Interface', $result);

        $this->assertNotNull($result->id);
        //$this->assertEquals(1, $result->status);
        $this->assertEquals($this->homenetInstaller->house2->id, $result->house);
        $this->assertEquals('3', $result->region);
        $this->assertEquals('My Room2', $result->name);
        $this->assertEquals('My Description2', $result->description);
    }

    public function testUpdate_invalidObject() {
        $this->setExpectedException('InvalidArgumentException');

        $badObject = new StdClass();
        $create = $this->service->update($badObject);
    }
    
//$this->service->delete($mixed)////////////////////////////////////////////////
    public function testDelete_validObject() {
        //setup
        $object = $this->createValidObject();

        //test delete
        $id = $object->id;
        $this->service->delete($object);

        //verify that it was deleted
        $this->setExpectedException('NotFoundException');
        $result = $this->service->getObjectById($id);
    }

    public function testDelete_validArray() {
        //setup
        $object = $this->createValidObject();

        //test delete
        $id = $object->id;
        $this->service->delete($object->toArray());

        //verify that it was deleted
        $this->setExpectedException('NotFoundException');
        $result = $this->service->getObjectById($id);
    }

    public function testDelete_validId() {
        //setup
        $object = $this->createValidObject();

        //test delete
        $id = $object->id;
        $this->service->delete($object->id);

        //verify that it was deleted
        $this->setExpectedException('NotFoundException');
        $result = $this->service->getObjectById($id);
    }

    public function testDelete_invalidObject() {
        $this->setExpectedException('InvalidArgumentException');

        $badObject = new StdClass();
        $create = $this->service->delete($badObject);
    }

}