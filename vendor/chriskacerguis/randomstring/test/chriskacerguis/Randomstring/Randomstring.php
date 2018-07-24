<?php

namespace chriskacerguis\Randomstring\Test;

class RandomstringTest extends \PHPUnit_Framework_TestCase
{
    public $config;

    public function setUp()
    {
        // No setup needed
    }

    public function tearDown()
    {
        // No tear down needed
    }

    /**
     * Test to make sure we get a string back
     * @author Chris Kacerguis <public@cold.io>
     * @return void
     */
    public function testGenerateRandomString()
    {
        $random     = new \chriskacerguis\Randomstring\Randomstring();
        $string     = $random->generate(11);
        $resource   = is_string($string);
        $this->assertTrue($resource);
    }

    /**
     * Test to make sure we get the requested lenght of string back
     * @author Chris Kacerguis <public@cold.io>
     * @return void
     */
    public function testGenerateRandomStringLenght()
    {
        $random     = new \chriskacerguis\Randomstring\Randomstring();
        $string     = $random->generate(11);
        $this->assertEquals(11, strlen($string));
    }

    /**
     * Test to make sure we get a string with alphanumeric charecters only
     * @author Chris Kacerguis <public@cold.io>
     * @return void
     */
    public function testGenerateRandomStringAlphaNum()
    {
        $random     = new \chriskacerguis\Randomstring\Randomstring();
        $string     = $random->generate(11);
        $alphanum   = ctype_alnum($string);
        $this->assertTrue($alphanum);
    }

    /**
     * Test to make sure we get a string back with alphanumeric and special charecters
     * @author Chris Kacerguis <public@cold.io>
     * @return void
     */
    public function testGenerateRandomStringChars()
    {
        $random     = new \chriskacerguis\Randomstring\Randomstring();
        $string     = $random->generate(11, true);
        $chars      = !ctype_alnum($string);
        $this->assertTrue($chars);
    }

    /**
     * Test to make sure we get a string back, and test the wrapper functionality, 
     * so in this tests case, our string should not contain any lowercase letters
     * @author Chris Kacerguis <public@cold.io>
     * @return void
     */
    public function testGenerateRandomStringWrapper()
    {
        $random     = new \chriskacerguis\Randomstring\Randomstring();
        $string     = $random->generate(11, false, 'strtoupper');
        // check the string for any lowercase letters
        $all_upper = preg_match("'/^[^a-z]+$/'", $string);
        $this->assertEquals(0, $all_upper);
    }
}
