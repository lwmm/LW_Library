<?php

require_once dirname(__FILE__) . '/../../Validation/PasswordStrength.php';

/**
 * Test class for PasswordStrength.
 * Generated by PHPUnit on 2013-02-26 at 12:50:32.
 */
class PasswordStrengthTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var PasswordStrength
     */
    protected $password;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->password = new LwLibrary\Validation\PasswordStrength("testLogin", "strengGeheim123!!");
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        
    }

    /**
     * @todo Implement testGetPassword().
     */
    public function testGetPassword()
    {
        $this->assertEquals("strengGeheim123!!", $this->password->getPassword());
    }

    /**
     * @todo Implement testGetPasswordStrength().
     */
    public function testGetPasswordStrength()
    {
        $this->assertEquals(100,$this->password->getPasswordStrength());
    }

}