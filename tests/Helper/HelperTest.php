<?php
namespace Tests\Helper;

use PHPUnit\Framework\TestCase;
use Application\Helper\Helper;

class HelperTest extends TestCase
{
    protected $helper;

    protected function setUp()
    {
      $this->helper = new Helper();
    }

    // Test validateEmail
    public function testValidateEmailReturnsFalseIfNoEmail()
    {
        $this->assertFalse($this->helper->validateEmail());
    }

    public function testValidateEmailReturnsCorrectValue()
    {
        $this->assertFalse($this->helper->validateEmail("invalid-email"));
        $this->assertTrue($this->helper->validateEmail("email@domain.com"));
    }

    //Test getDomainFromEmail
    public function testGetDomainFromEmailReturnsCorrectValue()
    {
      $this->assertEquals($this->helper->getDomainFromEmail("example@domain.com"), "domain.com");
    }
}
