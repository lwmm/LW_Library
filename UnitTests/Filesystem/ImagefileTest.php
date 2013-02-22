<?php

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
require_once dirname(__FILE__) . '/../Config/phpUnitConfig.php';
require_once dirname(__FILE__) . '/../../Filesystem/Directory.php';
require_once dirname(__FILE__) . '/../../Filesystem/File.php';
require_once dirname(__FILE__) . '/../../Filesystem/ImageFile.php';

/**
 * Test class for Imagefile.
 * Generated by PHPUnit on 2013-02-22 at 12:40:29.
 */
class ImagefileTest extends \PHPUnit_Framework_TestCase
{

    private $imageFile;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->config = phpUnitConfig::getConfig();

        $this->directoryObject_base = LwLibrary\Filesystem\Directory::getInstance($this->config["path"]["web_resource"]);
        $this->assertTrue(is_object($this->directoryObject_base));
        $this->assertTrue($this->directoryObject_base->check());

        $this->directoryObject2 = LwLibrary\Filesystem\Directory::getInstance($this->config["path"]["web_resource"] . "test_lw_imagefile/");
        if (!$this->directoryObject2->check()) {
            $this->assertTrue($this->directoryObject_base->add("test_lw_imagefile"));
            $this->addfile($this->directoryObject2->getPath(), "test.jpg");
        } else {
            $this->directoryObject2->delete(true);
            $this->setUp();
        }
        $this->assertTrue(is_object($this->directoryObject2));
        $this->assertTrue($this->directoryObject2->check());


        $this->imageFile = LwLibrary\Filesystem\Imagefile::getInstance($this->directoryObject2->getPath(), "test.jpg");
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->assertTrue($this->directoryObject2->delete(true));
        $this->assertFalse($this->directoryObject2->check());
    }

    /**
     * @todo Implement testGetInstance().
     */
    public function testGetInstance()
    {
        $imageFile = LwLibrary\Filesystem\Imagefile::getInstance($this->directoryObject2->getPath(), "test.jpg");
        $this->assertTrue(is_object($imageFile));
    }

    /**
     * @todo Implement testGetType().
     */
    public function testGetType()
    {
        $this->assertEquals("image", $this->imageFile->getType());
    }

    /**
     * @todo Implement testSetMaxSizes().
     */
    public function testSetMaxSizes()
    {
        $this->imageFile->setMaxSizes(100, 100);
    }

   /**
     * 
     */
    public function testResize()
    {
        //$this->setExpectedException('Exception');
        $imageMock = $this->getMock("\\lw_image", array("__construct"));
        
        $imageMock->expects($this->any())
                ->method('__construct')
                ->with($this->imageFile->getPath() . $this->imageFile->getFilename())
                ->will($this->returnValue(array()));
        
        $this->imageFile->resize(101, 101, false, false);
//          try {
//        #var_dump($imageMock);die();
//        } catch (Exception $e) {
//            $this->assertEquals($e->getMessage(), "Bildgroessen stimmen nicht");
//        }

//        $imageMock->expects($this->any())
//                ->method('__construct')
//                ->with($this->imageFile->getPath() . $this->imageFile->getFilename())
//                ->will($this->returnValue(array(
//                            "path" => $this->directoryObject2->getPath(),
//                            "errors" => array()
//                        )));
//
//        $imageMock->expects($this->any())
//                ->method('scaleImage')
//                ->with(90, 90, false, false, false)
//                ->will($this->returnValue(true));
//
//        $this->imageFile->resize(90, 90, false, false);
    }

    public function addfile($path, $filename)
    {
        $string = "test text ohne viel sinn!";
        $fileopen = fopen($path . $filename, "w+");
        $ok = fwrite($fileopen, $string);
        fclose($fileopen);
        $this->assertEquals($ok, strlen($string));
    }

}