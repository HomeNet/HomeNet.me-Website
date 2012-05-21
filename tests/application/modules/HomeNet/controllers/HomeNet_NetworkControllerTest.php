<?php

/**
 * Test class for HomeNet_RoomController.
 * Generated by PHPUnit on 2012-04-27 at 14:47:35.
 */
class HomeNet_NetworkControllerTest extends CMS_Test_PHPUnit_ControllerTestCase {

    private $installer;
    private $homenetInstaller;
    private $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->installer = new Core_Installer();
        $this->installer->installTest();
        $this->installer->loginAsSuperAdmin();


        $this->bootstrap = new Zend_Application('testing', APPLICATION_PATH . '/configs/application.ini'); //
        $this->view = Zend_Registry::get('view');

        $this->homenetInstaller = new HomeNet_Installer();
        $this->homenetInstaller->installTest(array('house'));
        
        $this->object = $this->homenetInstaller->room;

        //$this->service = new Content_Model_Category_Service();
        parent::setUp();


        $this->setModule('HomeNet');
        $this->setController('Network');
        $this->getRequest()->setParam('house', $this->homenetInstaller->house->id);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        
    }

    protected function _getTestData($seed = 0) {

        $array = array(
            //  'house' => $this->homenetInstaller->house->id,
            // 'status' => HomeNet_Model_Room::STATUS_LIVE,
            'region' => 1,
            'name' => 'My Room' . $seed,
            'description' => 'My Description' . $seed);
        return $array;
    }

    protected function _getBadTestData($version = 0) {

        $array = $this->_getTestData();
        if ($version == 0) {
            $array['name'] = '';
        }
        return $array;
    }

    /**
     * @todo Implement testIndexAction().
     */
    public function testIndexAction() {
        //setup
        $this->setAction('Index');
        $this->getRequest()->setParam('id', $this->object->id);

        //run
        $this->dispatch();

        $this->assertACM();
    }


//newAction()///////////////////////////////////////////////////////////////////
    public function testNewAction_firstView() {
        //setup
        $this->setAction('New');

        //run
        $this->dispatch();

        $this->assertACM();
        $this->assertNotRedirect();
    }

    public function testNewAction_submitInvalid() {
        //setup
        $this->setAction('New');

        $this->getRequest()->setMethod('POST')
                ->setPost($this->_getBadTestData());

        //run
        $this->dispatch();

        $this->assertACM();
        $this->assertNotRedirect();
    }

    public function testNewAction_submitValid() {
        //setup
        $this->setAction('New');
        $this->getRequest()->setMethod('POST')
                ->setPost($this->_getTestData());

        //run
        $this->dispatch();

        $this->assertACM();
        $this->assertRedirect();
    }
//editAction()///////////////////////////////////////////////////////////////////
    public function testEditAction_firstView() {
        //setup
        $this->setAction('Edit');
        $this->getRequest()->setParam('id', $this->object->id);

        //run
        $this->dispatch();

        $this->assertACM();
        $this->assertContains($this->object->name, $this->response->outputBody()); //make sure data is in the form
        $this->assertNotRedirect();
    }

    public function testEditAction_submitInvalid() {
        //setup
        $this->setAction('Edit');
        $this->getRequest()->setParam('id', $this->object->id);
        $this->getRequest()->setMethod('POST')
                ->setPost($this->_getBadTestData());
        //run
        $this->dispatch();

        $this->assertACM();
        $this->assertNotRedirect();
    }

    public function testEditAction_submitValid() {
        //setup
        $this->setAction('Edit');
        $this->getRequest()->setParam('id', $this->object->id);
        $this->getRequest()->setMethod('POST')
                ->setPost($this->_getTestData(1));

        //run
        $this->dispatch();

        $this->assertACM('Edit');
        $this->assertRedirect();
    }
//deleteAction()///////////////////////////////////////////////////////////////////
    public function testDeleteAction_firstView() {
        //setup
        $this->setAction('Delete');
        $this->getRequest()->setParam('id', $this->object->id);

        //show form
        $this->dispatch();

        $this->assertACM();
        $this->assertContains($this->object->name, $this->response->outputBody()); //make sure data is in the form
        $this->assertNotRedirect();
    }

    public function testDeleteAction_submitCancel() {
        //setup
        $this->setAction('Delete');
        $this->getRequest()->setParam('id', $this->object->id);
        $this->getRequest()->setMethod('POST')
                ->setPost(array('cancel' => 'cancel'));

        //run
        $this->dispatch();

        $this->assertACM();
        $this->assertRedirect();
    }

    public function testDeleteAction_submitDelete() {
        //setup
        $this->setAction('Delete');
        $this->getRequest()->setParam('id', $this->object->id);
        $this->getRequest()->setMethod('POST')
                ->setPost(array('confirm' => 'confirm'));

        //run
        $this->dispatch();

        $this->assertACM();
        $this->assertRedirect();
    }

}