<?php

require_once dirname(__FILE__) . '/../../../application/models/Installer.php';

/**
 * Test class for Core_Model_Installer.
 * Generated by PHPUnit on 2011-06-28 at 01:25:16.
 */
class Core_Model_InstallerTest extends PHPUnit_Framework_TestCase {


    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        //$this->object = new Core_Model_Installer;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        
    }


    public function testInstall() {
      Core_Model_Installer::install();
    }


    public function testUninstall() {
     //   Core_Model_Installer::uninstall();
    }

}