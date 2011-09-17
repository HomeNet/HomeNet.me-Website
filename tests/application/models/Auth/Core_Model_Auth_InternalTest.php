<?php

require_once dirname(__FILE__) . '/../../../../application/models/Auth/Internal.php';

/**
 * Test class for Core_Model_Auth_Internal.
 * Generated by PHPUnit on 2011-07-06 at 00:55:36.
 */
class Core_Model_Auth_InternalTest extends PHPUnit_Framework_TestCase {

    /**
     * @var Core_Model_Auth_Internal
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->object = new Core_Model_Auth_Internal;
        //Core_Model_Installer::install();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        $this->object->deleteAll(); //uninstaller already does this
        //Core_Model_Installer::uninstall();
    }

    public function testHashPassword() {
        $result = $this->object->hashPassword('testUsername','testPassword');
        $this->assertEquals(40, strlen($result));
    }

    public function testAdd() {
        $this->object->add(array('id' => 1, 'username' => 'testUser', 'password' => 'testPassword'));

        $table = new Core_Model_Auth_Internal_DbTable();
        $result = $table->find(1)->current();

        $this->assertEquals(1, $result->id);
        $this->assertEquals('testuser', $result->username);
        $this->assertEquals($this->object->hashPassword('testUser','testPassword'), $result->password);
    }
    
    public function testAddMissingCredentialsId() {
        $this->setExpectedException('InvalidArgumentException');
        $this->object->add(array( 'username' => 'testUser', 'password' => 'testPassword'));

    }
    public function testAddMissingCredentialsPassword() {
        $this->setExpectedException('InvalidArgumentException');
        $this->object->add(array('id' => 1, 'username' => 'testUser', ));

    }
    public function testAddMissingCredentialsUsername() {
        $this->setExpectedException('InvalidArgumentException');
        $this->object->add(array('id' => 1, 'password' => 'testPassword'));

    }

    public function testDuplicateAdd() {
        $this->object->add(array('id' => 1, 'username' => 'testUser', 'password' => 'testPassword'));
        $this->setExpectedException('DuplicateEntryException');
        $this->object->add(array('id' => 2, 'username' => 'testUser', 'password' => 'testPassword'));
    }

    public function testValidLoginAndLogout() {
        $this->object->add(array('id' => 1, 'username' => 'testUser', 'password' => 'testPassword'));
        $result = $this->object->login(array('username' => 'testUser', 'password' => 'testPassword'));
        $this->assertEquals(1, $result);
        $this->object->logout();
    }

    public function testInvalidLoginUsername() {
        $this->object->add(array('id' => 1, 'username' => 'testUser', 'password' => 'testPassword'));
        $this->setExpectedException('Exception', null, Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND);
        $result = $this->object->login(array('username' => 'testUserNA', 'password' => 'testPassword'));
        // $this->assertEquals(1,$result);
    }

    public function testInvalidLoginPassword() {
        $this->object->add(array('id' => 1, 'username' => 'testUser', 'password' => 'testPassword'));
        $this->setExpectedException('Exception', null, Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID);
        $result = $this->object->login(array('username' => 'testUser', 'password' => 'wrongPassword'));
        // $this->assertEquals(1,$result);
    }
    
    public function testLoginMissingCredentialsPassword() {
        $this->object->add(array('id' => 1, 'username' => 'testUser', 'password' => 'testPassword'));
        $this->setExpectedException('InvalidArgumentException');
        $this->object->login(array('username' => 'testUser', ));

    }
    public function testLoginMissingCredentialsUsername() {
        $this->object->add(array('id' => 1, 'username' => 'testUser', 'password' => 'testPassword'));
        $this->setExpectedException('InvalidArgumentException');
        $this->object->login(array('password' => 'testPassword'));

    }

    public function testDelete() {
        $this->object->add(array('id' => 1, 'username' => 'testUser', 'password' => 'testPassword'));
        $this->object->delete(1);

        $table = new Core_Model_Auth_Internal_DbTable();
        $result = $table->find(1)->current();

        $this->assertEmpty($result);
        // $this->assertEquals('testUser', $result->username);
        // $this->assertEquals($this->object->hashPassword('testPassword'), $result->password);          
    }

//    public function testLogout() {
//        $this->object->add(array('id'=>1, 'username'=>'testUser', 'password'=>'testPassword')); 
//        $this->object->login(array('username'=>'testUser', 'password'=>'testPassword')); 
//        $this->object->logout();
//    }
//    public function testDeleteAll() {
//        // Remove the following lines when you implement this test.
//        $this->markTestIncomplete(
//                'This test has not been implemented yet.'
//        );
//    }
}