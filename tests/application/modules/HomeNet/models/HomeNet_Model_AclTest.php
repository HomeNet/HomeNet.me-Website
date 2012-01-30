<?php

/**
 * Test class for HomeNet_Model_Acl.
 * Generated by PHPUnit on 2012-01-20 at 00:03:04.
 */
class HomeNet_Model_AclTest extends PHPUnit_Framework_TestCase {

    /**
     * @var HomeNet_Model_Acl
     */
    protected $acl;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        
        
        $this->installer = new Installer();
        $this->installer->installTest();
        $this->installer->loginAsMember();

        $this->homenetInstaller = new HomeNet_Installer();
        $this->homenetInstaller->installTest(array('house'));
        
        
        $service = new HomeNet_Model_HouseUser_Service();
        $service->add($this->homenetInstaller->house->id, $this->installer->user->member->id, array(HomeNet_Model_HouseUser::PERMISSION_VIEW));
        
        $this->acl = new HomeNet_Model_Acl($this->homenetInstaller->house);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        
    }

    /**
     * @todo Implement testIsAllowed().
     */
    public function testIsAllowed() {
        $this->assertTrue($this->acl->isAllowed('house', 'index'));
        $this->assertFalse($this->acl->isAllowed('house', 'edit'));
    }

}

?>