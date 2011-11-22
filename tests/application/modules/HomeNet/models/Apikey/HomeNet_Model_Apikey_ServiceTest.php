<?php

/**
 * Test class for HomeNet_Model_Apikey_Service.
 * Generated by PHPUnit on 2011-11-21 at 16:33:58.
 */
class HomeNet_Model_Apikey_ServiceTest extends PHPUnit_Framework_TestCase {

    /**
     * @var HomeNet_Model_Apikey_Service
     */
    protected $service;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->service = new HomeNet_Model_Apikey_Service;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        
    }

    public function testGetMapper() {
       $this->assertInstanceOf('HomeNet_Model_Apikey_MapperInterface', $this->service->getMapper());
    }

    public function testSetMapper() {
        $mapper = new HomeNet_Model_Apikey_MapperDbTable();
         $this->service->setMapper($mapper);
        
        $this->assertInstanceOf('HomeNet_Model_Apikey_MapperInterface', $this->service->getMapper());
        $this->assertEquals($mapper, $this->service->getMapper());
    }

    public function testGetObjectById() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    public function testGetObjectsByHouseUser() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    public function testGetObjectsByIdHouse() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    public function testCreateApikeyForHouse() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    public function testValidate() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    public function testCreate() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    public function testUpdate() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    public function testDelete() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

}